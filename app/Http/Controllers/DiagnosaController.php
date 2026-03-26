<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DiagnosaController extends Controller
{
    public function index(Request $request)
    {
        $category = strtolower(trim((string) $request->query('category', '')));

        if (!in_array($category, ['hardware', 'software'])) {
            $category = '';
        }

        $symptomsQuery = DB::table('symptoms')->orderBy('code');

        if ($category !== '') {
            $symptomsQuery->whereRaw('LOWER(category) = ?', [$category]);
        }

        $symptoms = $symptomsQuery->get();

        return view('diagnosa.form', compact('symptoms', 'category'));
    }

    public function process(Request $request)
    {
        $request->validate([
            'category' => ['required', 'in:hardware,software'],
            'symptoms' => ['required', 'array', 'min:1'],
        ], [
            'category.required' => 'Kategori harus dipilih.',
            'category.in' => 'Kategori tidak valid.',
            'symptoms.required' => 'Pilih minimal 1 gejala.',
            'symptoms.array' => 'Format gejala tidak valid.',
            'symptoms.min' => 'Pilih minimal 1 gejala.',
        ]);

        $category = strtolower(trim((string) $request->input('category')));
        $selected = (array) $request->input('symptoms', []);
        $selected = array_values(array_unique(array_map('intval', $selected)));

        if (empty($selected)) {
            return back()->withInput()->with('error', 'Pilih minimal 1 gejala.');
        }

        $selectedLookup = array_flip($selected);

        $selectedDetails = DB::table('symptoms')
            ->whereIn('id', $selected)
            ->whereRaw('LOWER(category) = ?', [$category])
            ->orderBy('code')
            ->get();

        if ($selectedDetails->isEmpty()) {
            return back()->withInput()->with('error', 'Gejala yang dipilih tidak sesuai dengan kategori.');
        }

        $cases = DB::table('case_bases as c')
            ->join('damages as d', 'd.id', '=', 'c.damage_id')
            ->select(
                'c.id',
                'c.case_code',
                'c.damage_id',
                'c.note',
                'd.name',
                'd.solution',
                'd.category'
            )
            ->whereRaw('LOWER(d.category) = ?', [$category])
            ->orderBy('c.case_code')
            ->get();

        if ($cases->isEmpty()) {
            return back()->withInput()->with('error', 'Belum ada case untuk kategori ' . $category . '.');
        }

        $caseIds = $cases->pluck('id')->toArray();

        $allCaseSymptoms = DB::table('case_symptoms as cs')
            ->join('symptoms as s', 's.id', '=', 'cs.symptom_id')
            ->select(
                'cs.case_base_id',
                'cs.symptom_id',
                'cs.weight',
                's.code',
                's.name',
                's.category'
            )
            ->whereIn('cs.case_base_id', $caseIds)
            ->whereRaw('LOWER(s.category) = ?', [$category])
            ->orderBy('s.code')
            ->get()
            ->groupBy('case_base_id');

        $results = [];

        foreach ($cases as $case) {
            $caseSymptoms = $allCaseSymptoms[$case->id] ?? collect();

            if ($caseSymptoms->isEmpty()) {
                continue;
            }

            $matchWeight = 0.0;
            $totalWeight = 0.0;
            $matchedIds = [];
            $matchedDetails = [];

            foreach ($caseSymptoms as $cs) {
                $weight = (float) $cs->weight;
                $totalWeight += $weight;

                if (isset($selectedLookup[$cs->symptom_id])) {
                    $matchWeight += $weight;
                    $matchedIds[] = (int) $cs->symptom_id;
                    $matchedDetails[] = [
                        'id' => (int) $cs->symptom_id,
                        'code' => $cs->code,
                        'name' => $cs->name,
                        'category' => strtolower((string) $cs->category),
                    ];
                }
            }

            $matchedCount = count($matchedIds);
            $totalSelected = count($selected);

            $caseScore = $totalWeight > 0 ? ($matchWeight / $totalWeight) : 0;
            $userScore = $totalSelected > 0 ? ($matchedCount / $totalSelected) : 0;

            $similarity = round((($caseScore * 0.7) + ($userScore * 0.3)) * 100, 2);

            $results[] = [
                'case_id' => $case->id,
                'similarity' => $similarity,
                'matchWeight' => round($matchWeight, 2),
                'totalWeight' => round($totalWeight, 2),
                'matched' => $matchedIds,
                'matchedDetails' => $matchedDetails,
                'detail' => [
                    'id' => $case->id,
                    'case_code' => $case->case_code,
                    'name' => $case->name,
                    'solution' => $case->solution,
                    'category' => strtolower((string) ($case->category ?? '')),
                ],
            ];
        }

        usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        $topResults = array_slice($results, 0, 3);
        $best = $topResults[0] ?? null;

        $setting = DB::table('cbr_settings')->orderByDesc('id')->first();
        $threshold = $setting ? (float) $setting->similarity_threshold : 70.00;

        $bestSimilarity = $best['similarity'] ?? 0;
        $needsReview = $bestSimilarity < $threshold;

        $pendingId = null;

        if ($needsReview) {
            $pendingId = DB::table('pending_cases')->insertGetId([
                'user_id' => Auth::id(),
                'selected_symptom_ids' => json_encode($selected),
                'best_case_id' => $best['case_id'] ?? null,
                'best_similarity' => $bestSimilarity,
                'top_results' => json_encode($topResults),
                'status' => 'pending',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $historyId = DB::table('diagnosis_histories')->insertGetId([
            'user_id' => Auth::id(),
            'selected_symptom_ids' => json_encode($selected),
            'best_case_id' => $best['case_id'] ?? null,
            'best_similarity' => $bestSimilarity,
            'top_results' => json_encode($topResults),
            'threshold_used' => $threshold,
            'needs_review' => $needsReview ? 1 : 0,
            'pending_case_id' => $pendingId,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        session([
            'diagnosa.category' => $category,
            'diagnosa.topResults' => $topResults,
            'diagnosa.selectedDetails' => $selectedDetails,
            'diagnosa.retainInfo' => [
                'threshold' => $threshold,
                'needs_review' => $needsReview,
                'pending_case_id' => $pendingId,
                'history_id' => $historyId,
            ],
        ]);

        return redirect()->route('diagnosa.result');
    }

    public function result()
    {
        $category = session('diagnosa.category');
        $topResults = session('diagnosa.topResults');
        $selectedDetails = session('diagnosa.selectedDetails');
        $retainInfo = session('diagnosa.retainInfo');

        if (!$topResults || !$selectedDetails || !$retainInfo) {
            return redirect()->route('diagnosa.form')
                ->with('error', 'Tidak ada hasil diagnosa untuk ditampilkan.');
        }

        return view('diagnosa.result', compact(
            'category',
            'topResults',
            'selectedDetails',
            'retainInfo'
        ));
    }
}