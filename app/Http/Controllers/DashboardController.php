<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class DashboardController extends Controller
{
    public function root()
    {
        return Auth::check()
            ? redirect('/dashboard')
            : redirect('/login');
    }

    public function redirectDashboard()
    {
        if (!Auth::check()) {
            return redirect('/login');
        }

        $role = Auth::user()->role ?? 'user';

        return match ($role) {
            'admin'   => redirect('/admin'),
            'teknisi' => redirect('/teknisi'),
            default   => redirect('/user'),
        };
    }

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

    public function admin(Request $request)
    {
        [$range, $from, $to] = $this->dateRange($request, 'query');

        $diagnosisBase = DB::table('diagnosis_histories')
            ->whereBetween('created_at', [$from, $to]);

        $stats = [
            'total_diagnosa'   => (clone $diagnosisBase)->count(),
            'valid'            => (clone $diagnosisBase)->where('needs_review', 0)->count(),
            'perlu_review'     => (clone $diagnosisBase)->where('needs_review', 1)->count(),

            'pending'          => DB::table('pending_cases')->whereBetween('created_at', [$from, $to])->where('status', 'pending')->count(),
            'approved'         => DB::table('pending_cases')->whereBetween('created_at', [$from, $to])->where('status', 'approved')->count(),
            'rejected'         => DB::table('pending_cases')->whereBetween('created_at', [$from, $to])->where('status', 'rejected')->count(),

            'total_case'       => DB::table('case_bases')->count(),
            'total_gejala'     => DB::table('symptoms')->count(),
            'total_kerusakan'  => DB::table('damages')->count(),
        ];

        $recent = DB::table('diagnosis_histories as h')
            ->leftJoin('users as u', 'u.id', '=', 'h.user_id')
            ->leftJoin('case_bases as c', 'c.id', '=', 'h.best_case_id')
            ->leftJoin('damages as d', 'd.id', '=', 'c.damage_id')
            ->select([
                'h.id','h.created_at','h.best_similarity','h.threshold_used','h.needs_review',
                'u.name as user_name','c.case_code','d.name as damage_name',
            ])
            ->whereBetween('h.created_at', [$from, $to])
            ->orderByDesc('h.id')
            ->limit(10)
            ->get();

        $topDamages = DB::table('diagnosis_histories as h')
            ->leftJoin('case_bases as c', 'c.id', '=', 'h.best_case_id')
            ->leftJoin('damages as d', 'd.id', '=', 'c.damage_id')
            ->select([
                'd.id as damage_id',
                'd.name as damage_name',
                DB::raw('COUNT(*) as total'),
            ])
            ->whereBetween('h.created_at', [$from, $to])
            ->whereNotNull('d.id')
            ->groupBy('d.id', 'd.name')
            ->orderByDesc('total')
            ->limit(7)
            ->get();

        $totalForDamageChart = $topDamages->sum('total');

        $symptomCounter = [];
        $rows = DB::table('diagnosis_histories')
            ->select('selected_symptom_ids')
            ->whereBetween('created_at', [$from, $to])
            ->get();

        foreach ($rows as $row) {
            $ids = json_decode($row->selected_symptom_ids ?? '[]', true);
            if (!is_array($ids)) continue;

            foreach ($ids as $sid) {
                $sid = (int)$sid;
                if ($sid <= 0) continue;
                $symptomCounter[$sid] = ($symptomCounter[$sid] ?? 0) + 1;
            }
        }

        arsort($symptomCounter);
        $topSymptomIds = array_slice(array_keys($symptomCounter), 0, 10);

        $symptomMap = [];
        if (!empty($topSymptomIds)) {
            $symptoms = DB::table('symptoms')->whereIn('id', $topSymptomIds)->get();
            foreach ($symptoms as $s) {
                $symptomMap[$s->id] = $s;
            }
        }

        $topSymptoms = [];
        foreach ($topSymptomIds as $sid) {
            $s = $symptomMap[$sid] ?? null;
            $topSymptoms[] = [
                'id'    => $sid,
                'code'  => $s->code ?? ('ID ' . $sid),
                'name'  => $s->name ?? '-',
                'total' => $symptomCounter[$sid] ?? 0,
            ];
        }

        $totalForSymptomChart = array_sum(array_column($topSymptoms, 'total'));

        $trendValid = DB::table('diagnosis_histories')
            ->whereBetween('created_at', [$from, $to])
            ->where('needs_review', 0)
            ->selectRaw('DATE(created_at) as d, COUNT(*) as total')
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->keyBy('d');

        $trendReview = DB::table('diagnosis_histories')
            ->whereBetween('created_at', [$from, $to])
            ->where('needs_review', 1)
            ->selectRaw('DATE(created_at) as d, COUNT(*) as total')
            ->groupBy('d')
            ->orderBy('d')
            ->get()
            ->keyBy('d');

        $trendLabels = [];
        $trendCountsValid = [];
        $trendCountsReview = [];

        $cursor = $from->copy()->startOfDay();
        $end = $to->copy()->startOfDay();

        while ($cursor->lte($end)) {
            $key = $cursor->toDateString();
            $trendLabels[] = $cursor->format('d-m');
            $trendCountsValid[] = (int)($trendValid[$key]->total ?? 0);
            $trendCountsReview[] = (int)($trendReview[$key]->total ?? 0);
            $cursor->addDay();
        }

        return view('admin', compact(
            'stats',
            'recent',
            'range',
            'from',
            'to',
            'topDamages',
            'totalForDamageChart',
            'topSymptoms',
            'totalForSymptomChart',
            'trendLabels',
            'trendCountsValid',
            'trendCountsReview'
        ));
    }

    public function teknisi()
    {
        $stats = [
            'total_diagnosa'   => DB::table('diagnosis_histories')->count(),
            'valid'            => DB::table('diagnosis_histories')->where('needs_review', 0)->count(),
            'perlu_review'     => DB::table('diagnosis_histories')->where('needs_review', 1)->count(),

            'pending'          => DB::table('pending_cases')->where('status', 'pending')->count(),
            'approved'         => DB::table('pending_cases')->where('status', 'approved')->count(),
            'rejected'         => DB::table('pending_cases')->where('status', 'rejected')->count(),

            'total_case'       => DB::table('case_bases')->count(),
            'total_gejala'     => DB::table('symptoms')->count(),
            'total_kerusakan'  => DB::table('damages')->count(),
        ];

        $pendingLatest = DB::table('pending_cases as p')
            ->leftJoin('users as u', 'u.id', '=', 'p.user_id')
            ->leftJoin('case_bases as c', 'c.id', '=', 'p.best_case_id')
            ->leftJoin('damages as d', 'd.id', '=', 'c.damage_id')
            ->select([
                'p.id','p.created_at','p.best_similarity','p.status',
                'u.name as user_name','c.case_code','d.name as damage_name',
            ])
            ->orderByDesc('p.id')
            ->limit(10)
            ->get();

        return view('teknisi', compact('stats', 'pendingLatest'));
    }

    public function user()
    {
        $uid = Auth::id();

        $stats = [
            'total_diagnosa'  => DB::table('diagnosis_histories')->where('user_id', $uid)->count(),
            'valid'           => DB::table('diagnosis_histories')->where('user_id', $uid)->where('needs_review', 0)->count(),
            'perlu_review'    => DB::table('diagnosis_histories')->where('user_id', $uid)->where('needs_review', 1)->count(),
        ];

        $recent = DB::table('diagnosis_histories as h')
            ->leftJoin('case_bases as c', 'c.id', '=', 'h.best_case_id')
            ->leftJoin('damages as d', 'd.id', '=', 'c.damage_id')
            ->select([
                'h.id','h.created_at','h.best_similarity','h.threshold_used','h.needs_review',
                'c.case_code','d.name as damage_name',
            ])
            ->where('h.user_id', $uid)
            ->orderByDesc('h.id')
            ->limit(10)
            ->get();

        return view('user', compact('stats', 'recent'));
    }

    public function exportPdf(Request $request)
    {
        [$range, $from, $to] = $this->dateRange($request, 'input');
        $chartImage = $request->input('chart_image');

        $diagnosisBase = DB::table('diagnosis_histories')
            ->whereBetween('created_at', [$from, $to]);

        $stats = [
            'total_diagnosa'   => (clone $diagnosisBase)->count(),
            'valid'            => (clone $diagnosisBase)->where('needs_review', 0)->count(),
            'perlu_review'     => (clone $diagnosisBase)->where('needs_review', 1)->count(),
            'pending'          => DB::table('pending_cases')->whereBetween('created_at', [$from, $to])->where('status', 'pending')->count(),
            'approved'         => DB::table('pending_cases')->whereBetween('created_at', [$from, $to])->where('status', 'approved')->count(),
            'rejected'         => DB::table('pending_cases')->whereBetween('created_at', [$from, $to])->where('status', 'rejected')->count(),
        ];

        $recent = DB::table('diagnosis_histories as h')
            ->leftJoin('users as u', 'u.id', '=', 'h.user_id')
            ->leftJoin('case_bases as c', 'c.id', '=', 'h.best_case_id')
            ->leftJoin('damages as d', 'd.id', '=', 'c.damage_id')
            ->select([
                'h.id','h.created_at','h.best_similarity','h.threshold_used','h.needs_review',
                'u.name as user_name','c.case_code','d.name as damage_name',
            ])
            ->whereBetween('h.created_at', [$from, $to])
            ->orderByDesc('h.id')
            ->limit(15)
            ->get();

        $topDamages = DB::table('diagnosis_histories as h')
            ->leftJoin('case_bases as c', 'c.id', '=', 'h.best_case_id')
            ->leftJoin('damages as d', 'd.id', '=', 'c.damage_id')
            ->select([
                'd.name as damage_name',
                DB::raw('COUNT(*) as total'),
            ])
            ->whereBetween('h.created_at', [$from, $to])
            ->whereNotNull('d.id')
            ->groupBy('d.name')
            ->orderByDesc('total')
            ->limit(7)
            ->get();

        $symptomCounter = [];
        $rows = DB::table('diagnosis_histories')
            ->select('selected_symptom_ids')
            ->whereBetween('created_at', [$from, $to])
            ->get();

        foreach ($rows as $row) {
            $ids = json_decode($row->selected_symptom_ids ?? '[]', true);
            if (!is_array($ids)) continue;
            foreach ($ids as $sid) {
                $sid = (int)$sid;
                if ($sid <= 0) continue;
                $symptomCounter[$sid] = ($symptomCounter[$sid] ?? 0) + 1;
            }
        }

        arsort($symptomCounter);
        $topSymptomIds = array_slice(array_keys($symptomCounter), 0, 10);

        $symptomMap = [];
        if (!empty($topSymptomIds)) {
            $symptoms = DB::table('symptoms')->whereIn('id', $topSymptomIds)->get();
            foreach ($symptoms as $s) {
                $symptomMap[$s->id] = $s;
            }
        }

        $topSymptoms = [];
        foreach ($topSymptomIds as $sid) {
            $s = $symptomMap[$sid] ?? null;
            $topSymptoms[] = [
                'code'  => $s->code ?? ('ID ' . $sid),
                'name'  => $s->name ?? '-',
                'total' => $symptomCounter[$sid] ?? 0,
            ];
        }

        $validPercent  = $stats['total_diagnosa'] > 0 ? round(($stats['valid'] / $stats['total_diagnosa']) * 100, 2) : 0;
        $reviewPercent = $stats['total_diagnosa'] > 0 ? round(($stats['perlu_review'] / $stats['total_diagnosa']) * 100, 2) : 0;

        $filename = 'dashboard-admin-' . Carbon::now()->format('Ymd-His') . '.pdf';

        $pdf = Pdf::loadView('admin_pdf', compact(
            'range',
            'from',
            'to',
            'stats',
            'recent',
            'topDamages',
            'topSymptoms',
            'validPercent',
            'reviewPercent',
            'chartImage'
        ))->setPaper('A4', 'portrait');

        return $pdf->download($filename);
    }
}