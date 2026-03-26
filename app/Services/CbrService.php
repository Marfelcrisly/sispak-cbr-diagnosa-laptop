<?php

namespace App\Services;

use App\Models\CaseBase;

class CbrService
{
    public function calculate(array $selected): array
    {
        $selected = array_values(array_unique(array_map('intval', $selected)));
        $selectedLookup = array_flip($selected);

        // Ambil semua case beserta relasi damage dan gejalanya
        $cases = CaseBase::with(['damage', 'symptoms.symptom'])->get();

        $results = [];

        foreach ($cases as $case) {
            $caseSymptoms = $case->symptoms;

            if ($caseSymptoms->isEmpty()) {
                continue;
            }

            $matchWeight = 0.0;
            $totalWeight = 0.0;
            $matchedIds = [];
            $matchedDetails = [];
            $matchedCount = 0;

            foreach ($caseSymptoms as $cs) {
                $w = (float) $cs->weight;
                $totalWeight += $w;

                if (isset($selectedLookup[$cs->symptom_id])) {
                    $matchWeight += $w;
                    $matchedCount++;
                    $matchedIds[] = (int) $cs->symptom_id;

                    if ($cs->symptom) {
                        $matchedDetails[] = [
                            'id' => $cs->symptom->id,
                            'code' => $cs->symptom->code,
                            'name' => $cs->symptom->name,
                        ];
                    }
                }
            }

            $totalSelected = count($selected);

            $caseScore = $totalWeight > 0 ? ($matchWeight / $totalWeight) : 0;
            $userScore = $totalSelected > 0 ? ($matchedCount / $totalSelected) : 0;

            // Hybrid similarity:
            // 70% coverage terhadap case
            // 30% coverage terhadap input user
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
                    'name' => optional($case->damage)->name ?? '-',
                    'solution' => optional($case->damage)->solution ?? null,
                ],
            ];
        }

        usort($results, fn($a, $b) => $b['similarity'] <=> $a['similarity']);

        return array_slice($results, 0, 3);
    }
}