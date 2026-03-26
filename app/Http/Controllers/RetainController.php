<?php

namespace App\Http\Controllers;

use App\Models\CaseBase;
use App\Models\CaseSymptom;
use App\Models\Damage;
use App\Models\PendingCase;
use App\Models\Symptom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class RetainController extends Controller
{
    public function index()
    {
        $items = DB::table('pending_cases as p')
            ->leftJoin('users as u', 'u.id', '=', 'p.user_id')
            ->leftJoin('case_bases as c', 'c.id', '=', 'p.best_case_id')
            ->leftJoin('damages as d', 'd.id', '=', 'c.damage_id')
            ->select([
                'p.*',
                'u.name as user_name',
                'c.case_code as case_code',
                'd.name as damage_name',
                'd.category as damage_category',
            ])
            ->orderByDesc('p.id')
            ->get();

        return view('retain.index', compact('items'));
    }

    public function show($id)
    {
        $pending = DB::table('pending_cases as p')
            ->leftJoin('users as u', 'u.id', '=', 'p.user_id')
            ->leftJoin('case_bases as c', 'c.id', '=', 'p.best_case_id')
            ->leftJoin('damages as d', 'd.id', '=', 'c.damage_id')
            ->select([
                'p.*',
                'u.name as user_name',
                'c.case_code as case_code',
                'd.name as damage_name',
                'd.solution as damage_solution',
                'd.category as damage_category',
            ])
            ->where('p.id', $id)
            ->first();

        abort_if(!$pending, 404);

        $topResults = json_decode($pending->top_results ?? '[]', true) ?? [];
        $selectedIds = json_decode($pending->selected_symptom_ids ?? '[]', true) ?? [];

        $selectedSymptoms = count($selectedIds)
            ? DB::table('symptoms')
                ->whereIn('id', $selectedIds)
                ->orderBy('code')
                ->get()
            : collect();

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

        $damages = Damage::orderBy('code')->get();

        return view('retain.show', compact('pending', 'topResults', 'selectedSymptoms', 'damages'));
    }

    public function approve(Request $request, $id)
    {
        $pending = PendingCase::find($id);
        abort_if(!$pending, 404);

        if ($pending->status !== 'pending') {
            return back()->with('error', 'Data ini sudah diproses.');
        }

        $damageId = $request->input('damage_id');

        if (!$damageId && $pending->best_case_id) {
            $damageId = CaseBase::where('id', $pending->best_case_id)->value('damage_id');
        }

        if (!$damageId) {
            return back()->with('error', 'damage_id belum ada. Pilih kerusakan dulu.');
        }

        $damage = Damage::find($damageId);
        if (!$damage) {
            return back()->with('error', 'Kerusakan tidak valid.');
        }

        $last = CaseBase::orderByDesc('id')->value('case_code');
        $num = 0;

        if ($last && preg_match('/C(\d+)/', $last, $m)) {
            $num = (int) $m[1];
        }

        $newCode = 'C' . str_pad($num + 1, 3, '0', STR_PAD_LEFT);

        $newCase = CaseBase::create([
            'case_code' => $newCode,
            'damage_id' => $damageId,
            'note' => 'Retain from pending #' . $pending->id,
        ]);

        $selectedIds = json_decode($pending->selected_symptom_ids ?? '[]', true) ?? [];

        foreach ($selectedIds as $sid) {
            $symptom = Symptom::find((int) $sid);

            if ($symptom && strtolower((string)$symptom->category) === strtolower((string)$damage->category)) {
                CaseSymptom::create([
                    'case_base_id' => $newCase->id,
                    'symptom_id' => (int) $sid,
                    'weight' => 1,
                ]);
            }
        }

        $pending->update([
            'status' => 'approved',
            'review_note' => $request->input('review_note'),
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        DB::table('diagnosis_histories')
            ->where('pending_case_id', $pending->id)
            ->update([
                'needs_review' => 0,
                'updated_at' => now(),
            ]);

        return redirect('/retain')->with('success', 'Approved! Case baru dibuat: ' . $newCode);
    }

    public function reject(Request $request, $id)
    {
        $pending = PendingCase::find($id);
        abort_if(!$pending, 404);

        if ($pending->status !== 'pending') {
            return back()->with('error', 'Data ini sudah diproses.');
        }

        $pending->update([
            'status' => 'rejected',
            'review_note' => $request->input('review_note'),
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        return redirect('/retain')->with('success', 'Pending case ditolak.');
    }

    public function userPendingIndex()
    {
        $uid = Auth::id();

        $items = DB::table('pending_cases as p')
            ->leftJoin('case_bases as c', 'c.id', '=', 'p.best_case_id')
            ->leftJoin('damages as d', 'd.id', '=', 'c.damage_id')
            ->select([
                'p.id',
                'p.created_at',
                'p.status',
                'p.best_similarity',
                'c.case_code',
                'd.name as damage_name',
                'd.category as damage_category',
            ])
            ->where('p.user_id', $uid)
            ->orderByDesc('p.id')
            ->paginate(15)
            ->withQueryString();

        return view('user.pending.index', compact('items'));
    }

    public function userPendingShow($id)
    {
        $uid = Auth::id();

        $pending = DB::table('pending_cases as p')
            ->leftJoin('case_bases as c', 'c.id', '=', 'p.best_case_id')
            ->leftJoin('damages as d', 'd.id', '=', 'c.damage_id')
            ->leftJoin('users as reviewer', 'reviewer.id', '=', 'p.reviewed_by')
            ->select([
                'p.*',
                'c.case_code as case_code',
                'd.name as damage_name',
                'd.solution as damage_solution',
                'd.category as damage_category',
                'reviewer.name as reviewer_name',
            ])
            ->where('p.id', $id)
            ->where('p.user_id', $uid)
            ->first();

        abort_if(!$pending, 404);

        $topResults = json_decode($pending->top_results ?? '[]', true) ?? [];
        $selectedIds = json_decode($pending->selected_symptom_ids ?? '[]', true) ?? [];

        $selectedSymptoms = count($selectedIds)
            ? DB::table('symptoms')->whereIn('id', $selectedIds)->orderBy('code')->get()
            : collect();

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
            }
        }
        unset($item);

        return view('user.pending.show', compact('pending', 'topResults', 'selectedSymptoms'));
    }
}