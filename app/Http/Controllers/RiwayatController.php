<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RiwayatController extends Controller
{
    public function index()
    {
        $role = Auth::user()->role ?? 'user';

        $query = DB::table('diagnosis_histories as h')
            ->leftJoin('users as u', 'u.id', '=', 'h.user_id')
            ->leftJoin('case_bases as c', 'c.id', '=', 'h.best_case_id')
            ->leftJoin('damages as d', 'd.id', '=', 'c.damage_id')
            ->select([
                'h.*',
                'u.name as user_name',
                'c.case_code',
                'd.name as damage_name',
                'd.category as damage_category', // 🔥 TAMBAHAN
            ])
            ->orderByDesc('h.id');

        if ($role === 'user') {
            $query->where('h.user_id', Auth::id());
        }

        $histories = $query->get();

        return view('riwayat.index', compact('histories'));
    }

    public function show($id)
    {
        $history = DB::table('diagnosis_histories as h')
            ->leftJoin('users as u', 'u.id', '=', 'h.user_id')
            ->leftJoin('case_bases as c', 'c.id', '=', 'h.best_case_id')
            ->leftJoin('damages as d', 'd.id', '=', 'c.damage_id')
            ->select([
                'h.*',
                'u.name as user_name',
                'c.case_code',
                'd.name as damage_name',
                'd.solution as damage_solution',
                'd.category as damage_category', // 🔥 TAMBAHAN
            ])
            ->where('h.id', $id)
            ->first();

        abort_if(!$history, 404);

        // 🔒 SECURITY
        $role = Auth::user()->role ?? 'user';
        if ($role === 'user' && (int)$history->user_id !== (int)Auth::id()) {
            abort(403);
        }

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
                        'd.category as damage_category', // 🔥 TAMBAHAN
                    ])
                    ->where('c.id', $caseId)
                    ->first();

                $item['detail'] = [
                    'id' => $detail->id ?? $caseId,
                    'case_code' => $detail->case_code ?? '-',
                    'name' => $detail->damage_name ?? '-',
                    'solution' => $detail->damage_solution ?? null,
                    'category' => strtolower($detail->damage_category ?? ''), // 🔥
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

            // 🔧 FIX kompatibilitas lama
            $item['matchWeight'] = (float)($item['matchWeight'] ?? 0);
            $item['totalWeight'] = (float)($item['totalWeight'] ?? ($item['totalCaseWeight'] ?? 0));

            // 🔧 matchedDetails fallback
            $matchedIds = $item['matched'] ?? [];

            if (!isset($item['matchedDetails']) || !is_array($item['matchedDetails'])) {
                $item['matchedDetails'] = !empty($matchedIds)
                    ? DB::table('symptoms')
                        ->whereIn('id', $matchedIds)
                        ->orderBy('code')
                        ->get()
                        ->map(fn($s) => [
                            'id' => $s->id,
                            'code' => $s->code,
                            'name' => $s->name,
                        ])
                        ->toArray()
                    : [];
            }
        }
        unset($item);

        $selectedSymptoms = count($selectedIds)
            ? DB::table('symptoms')->whereIn('id', $selectedIds)->orderBy('code')->get()
            : collect();

        return view('riwayat.show', compact(
            'history',
            'topResults',
            'selectedSymptoms'
        ));
    }
}