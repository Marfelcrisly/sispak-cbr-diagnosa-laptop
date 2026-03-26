<?php

namespace App\Http\Controllers;

use App\Models\CaseBase;
use App\Models\CaseSymptom;
use App\Models\Damage;
use App\Models\Symptom;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CaseController extends Controller
{
    public function publicIndex()
    {
        $cases = DB::table('case_bases')
            ->join('damages', 'damages.id', '=', 'case_bases.damage_id')
            ->select(
                'case_bases.id',
                'case_bases.case_code',
                'case_bases.note',
                'damages.code as damage_code',
                'damages.name as damage_name',
                'damages.category as damage_category'
            )
            ->orderBy('case_bases.case_code')
            ->get();

        return view('cases.index', compact('cases'));
    }

    public function publicShow($id)
    {
        $case = DB::table('case_bases')
            ->join('damages', 'damages.id', '=', 'case_bases.damage_id')
            ->select(
                'case_bases.id',
                'case_bases.case_code',
                'case_bases.note',
                'damages.code as damage_code',
                'damages.name as damage_name',
                'damages.category as damage_category',
                'damages.solution'
            )
            ->where('case_bases.id', $id)
            ->first();

        abort_if(!$case, 404);

        $symptoms = DB::table('case_symptoms')
            ->join('symptoms', 'symptoms.id', '=', 'case_symptoms.symptom_id')
            ->select(
                'symptoms.code',
                'symptoms.name',
                'symptoms.category',
                'case_symptoms.weight'
            )
            ->where('case_symptoms.case_base_id', $id)
            ->orderBy('symptoms.code')
            ->get();

        return view('cases.show', compact('case', 'symptoms'));
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $query = DB::table('case_bases as c')
            ->join('damages as d', 'd.id', '=', 'c.damage_id')
            ->select([
                'c.id',
                'c.case_code',
                'c.note',
                'd.code as damage_code',
                'd.name as damage_name',
                'd.category as damage_category',
            ])
            ->orderBy('c.case_code');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('c.case_code', 'like', "%{$q}%")
                  ->orWhere('d.code', 'like', "%{$q}%")
                  ->orWhere('d.name', 'like', "%{$q}%")
                  ->orWhere('d.category', 'like', "%{$q}%");
            });
        }

        $cases = $query->paginate(10)->withQueryString();

        return view('admin.cases.index', compact('cases', 'q'));
    }

    public function create()
    {
        $lastCase = CaseBase::orderByDesc('id')->first();

        if ($lastCase && preg_match('/^C(\d+)$/', strtoupper($lastCase->case_code), $match)) {
            $nextNumber = ((int) $match[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        $nextCaseCode = 'C' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);

        $damages = Damage::orderBy('code')->get();
        $symptoms = Symptom::orderBy('code')->get();

        return view('admin.cases.create', compact('damages', 'symptoms', 'nextCaseCode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'case_code' => ['required', 'string', 'max:20'],
            'damage_id' => ['required', 'integer'],
        ]);

        $caseCode = strtoupper(trim($request->case_code));

        if (CaseBase::where('case_code', $caseCode)->exists()) {
            return back()->withInput()->with('error', 'Case code sudah ada. Gunakan kode lain.');
        }

        $damage = Damage::find($request->damage_id);
        if (!$damage) {
            return back()->withInput()->with('error', 'Kerusakan tidak valid.');
        }

        $case = CaseBase::create([
            'case_code' => $caseCode,
            'damage_id' => (int) $request->damage_id,
            'note' => $request->note ? trim($request->note) : 'Manual input',
        ]);

        $symInputs = $request->input('symptoms', []);
        if (is_array($symInputs)) {
            foreach ($symInputs as $symId => $weight) {
                $w = (int) $weight;
                if ($w > 0) {
                    $symptom = Symptom::find((int) $symId);

                    if ($symptom && strtolower($symptom->category) === strtolower($damage->category)) {
                        CaseSymptom::create([
                            'case_base_id' => $case->id,
                            'symptom_id' => (int) $symId,
                            'weight' => $w,
                        ]);
                    }
                }
            }
        }

        return redirect('/admin/cases')->with('success', 'Case berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $case = CaseBase::find($id);
        abort_if(!$case, 404);

        $damages = Damage::orderBy('code')->get();
        $symptoms = Symptom::orderBy('code')->get();

        $existing = CaseSymptom::where('case_base_id', $id)
            ->pluck('weight', 'symptom_id')
            ->toArray();

        return view('admin.cases.edit', compact('case', 'damages', 'symptoms', 'existing'));
    }

    public function update(Request $request, $id)
    {
        $case = CaseBase::find($id);
        abort_if(!$case, 404);

        $request->validate([
            'case_code' => ['required', 'string', 'max:20'],
            'damage_id' => ['required', 'integer'],
        ]);

        $caseCode = strtoupper(trim($request->case_code));

        if (CaseBase::where('case_code', $caseCode)->where('id', '!=', $id)->exists()) {
            return back()->withInput()->with('error', 'Case code sudah dipakai case lain.');
        }

        $damage = Damage::find($request->damage_id);
        if (!$damage) {
            return back()->withInput()->with('error', 'Kerusakan tidak valid.');
        }

        $case->update([
            'case_code' => $caseCode,
            'damage_id' => (int) $request->damage_id,
            'note' => $request->note ? trim($request->note) : $case->note,
        ]);

        CaseSymptom::where('case_base_id', $id)->delete();

        $symInputs = $request->input('symptoms', []);
        if (is_array($symInputs)) {
            foreach ($symInputs as $symId => $weight) {
                $w = (int) $weight;
                if ($w > 0) {
                    $symptom = Symptom::find((int) $symId);

                    if ($symptom && strtolower($symptom->category) === strtolower($damage->category)) {
                        CaseSymptom::create([
                            'case_base_id' => (int) $id,
                            'symptom_id' => (int) $symId,
                            'weight' => $w,
                        ]);
                    }
                }
            }
        }

        return redirect('/admin/cases')->with('success', 'Case berhasil diupdate.');
    }

    public function delete($id)
    {
        CaseSymptom::where('case_base_id', $id)->delete();
        CaseBase::where('id', $id)->delete();

        return back()->with('success', 'Case berhasil dihapus.');
    }
}