<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class EvaluasiController extends Controller
{
    private function dateRange(Request $request, string $mode = 'query'): array
    {
        $get = fn(string $key, $default = null) =>
            $mode === 'input'
                ? $request->input($key, $default)
                : $request->query($key, $default);

        $range = $get('range', '30d');

        $from = Carbon::now()->subDays(30)->startOfDay();
        $to   = Carbon::now()->endOfDay();

        if ($range === 'today') {
            $from = Carbon::today()->startOfDay();
            $to   = Carbon::today()->endOfDay();
        } elseif ($range === '7d') {
            $from = Carbon::now()->subDays(7)->startOfDay();
            $to   = Carbon::now()->endOfDay();
        } elseif ($range === '30d') {
            $from = Carbon::now()->subDays(30)->startOfDay();
            $to   = Carbon::now()->endOfDay();
        } elseif ($range === 'custom') {
            $fromInput = $get('from');
            $toInput   = $get('to');

            $from = $fromInput ? Carbon::parse($fromInput)->startOfDay() : null;
            $to   = $toInput ? Carbon::parse($toInput)->endOfDay() : null;

            if ($from && !$to) $to = Carbon::now()->endOfDay();
            if (!$from && $to) $from = Carbon::parse($toInput)->subDays(30)->startOfDay();

            if (!$from && !$to) {
                $range = '30d';
                $from = Carbon::now()->subDays(30)->startOfDay();
                $to   = Carbon::now()->endOfDay();
            }
        } else {
            $range = '30d';
            $from  = Carbon::now()->subDays(30)->startOfDay();
            $to    = Carbon::now()->endOfDay();
        }

        return [$range, $from, $to];
    }

    public function index(Request $request)
    {
        [$range, $from, $to] = $this->dateRange($request, 'query');

        $histories = DB::table('diagnosis_histories as h')
            ->leftJoin('users as u', 'u.id', '=', 'h.user_id')
            ->leftJoin('case_bases as c', 'c.id', '=', 'h.best_case_id')
            ->leftJoin('damages as d', 'd.id', '=', 'c.damage_id')
            ->leftJoin('diagnosis_validations as v', 'v.history_id', '=', 'h.id')
            ->leftJoin('damages as ed', 'ed.id', '=', 'v.expert_damage_id')
            ->select([
                'h.id',
                'h.created_at',
                'h.best_similarity',
                'h.threshold_used',
                'h.needs_review',
                'u.name as user_name',
                'c.case_code',
                'd.id as predicted_damage_id',
                'd.name as predicted_damage_name',
                'd.category as predicted_damage_category',
                'v.expert_damage_id',
                'ed.name as expert_damage_name',
                'ed.category as expert_damage_category',
                'v.validated_at',
            ])
            ->whereBetween('h.created_at', [$from, $to])
            ->orderByDesc('h.id')
            ->paginate(15)
            ->withQueryString();

        $validatedQuery = DB::table('diagnosis_histories as h')
            ->leftJoin('case_bases as c', 'c.id', '=', 'h.best_case_id')
            ->leftJoin('damages as d', 'd.id', '=', 'c.damage_id')
            ->join('diagnosis_validations as v', 'v.history_id', '=', 'h.id')
            ->whereBetween('h.created_at', [$from, $to]);

        $totalValidated = (clone $validatedQuery)->count();

        $totalCorrect = (clone $validatedQuery)
            ->whereColumn('v.expert_damage_id', 'd.id')
            ->count();

        $accuracy = $totalValidated > 0
            ? round(($totalCorrect / $totalValidated) * 100, 2)
            : 0;

        $matrix = (clone $validatedQuery)
            ->select([
                'd.name as predicted',
                'v.expert_damage_id',
                DB::raw('COUNT(*) as total'),
            ])
            ->leftJoin('damages as ed', 'ed.id', '=', 'v.expert_damage_id')
            ->addSelect('ed.name as expert')
            ->groupBy('d.name', 'v.expert_damage_id', 'ed.name')
            ->orderByDesc('total')
            ->limit(30)
            ->get();

        return view('evaluasi.index', compact(
            'histories',
            'range',
            'from',
            'to',
            'totalValidated',
            'totalCorrect',
            'accuracy',
            'matrix'
        ));
    }

    public function show($id)
    {
        $history = DB::table('diagnosis_histories as h')
            ->leftJoin('users as u', 'u.id', '=', 'h.user_id')
            ->leftJoin('case_bases as c', 'c.id', '=', 'h.best_case_id')
            ->leftJoin('damages as d', 'd.id', '=', 'c.damage_id')
            ->leftJoin('diagnosis_validations as v', 'v.history_id', '=', 'h.id')
            ->leftJoin('damages as ed', 'ed.id', '=', 'v.expert_damage_id')
            ->select([
                'h.*',
                'u.name as user_name',
                'c.case_code',
                'd.id as predicted_damage_id',
                'd.name as predicted_damage_name',
                'd.solution as predicted_solution',
                'd.category as predicted_damage_category',
                'v.expert_damage_id',
                'ed.name as expert_damage_name',
                'ed.category as expert_damage_category',
                'v.note as validation_note',
                'v.validated_at',
            ])
            ->where('h.id', $id)
            ->first();

        abort_if(!$history, 404);

        $topResults = json_decode($history->top_results ?? '[]', true) ?? [];
        $selectedIds = json_decode($history->selected_symptom_ids ?? '[]', true) ?? [];

        foreach ($topResults as &$item) {
            $caseId = $item['case_id'] ?? null;

            if ($caseId) {
                $detail = DB::table('case_bases as c')
                    ->leftJoin('damages as d', 'd.id', '=', 'c.damage_id')
                    ->select([
                        'c.id',
                        'c.case_code',
                        'd.name as damage_name',
                        'd.solution as damage_solution',
                        'd.category as damage_category',
                    ])
                    ->where('c.id', $caseId)
                    ->first();

                $item['detail'] = [
                    'id' => $detail->id ?? $caseId,
                    'case_code' => $detail->case_code ?? '-',
                    'name' => $detail->damage_name ?? '-',
                    'solution' => $detail->damage_solution ?? null,
                    'category' => strtolower((string)($detail->damage_category ?? '')),
                ];
            } else {
                $item['detail'] = [
                    'id' => null,
                    'case_code' => '-',
                    'name' => '-',
                    'solution' => null,
                    'category' => '',
                ];
            }
        }
        unset($item);

        $selectedSymptoms = count($selectedIds)
            ? DB::table('symptoms')->whereIn('id', $selectedIds)->orderBy('code')->get()
            : collect();

        $damages = DB::table('damages')->orderBy('code')->get();

        return view('evaluasi.show', compact(
            'history',
            'topResults',
            'selectedSymptoms',
            'damages'
        ));
    }

    public function store(Request $request, $id)
    {
        $request->validate([
            'expert_damage_id' => ['required', 'integer'],
            'note' => ['nullable', 'string'],
        ]);

        abort_if(!DB::table('diagnosis_histories')->where('id', $id)->exists(), 404);

        DB::table('diagnosis_validations')->updateOrInsert(
            ['history_id' => $id],
            [
                'expert_damage_id' => (int) $request->expert_damage_id,
                'validated_by' => Auth::id(),
                'note' => $request->note,
                'validated_at' => now(),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return redirect('/evaluasi/' . $id)->with('success', 'Validasi pakar berhasil disimpan.');
    }
}