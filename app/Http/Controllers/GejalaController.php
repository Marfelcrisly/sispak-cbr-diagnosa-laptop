<?php

namespace App\Http\Controllers;

use App\Models\Symptom;
use Illuminate\Http\Request;

class GejalaController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $query = Symptom::query()->orderBy('code');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('code', 'like', "%{$q}%")
                  ->orWhere('name', 'like', "%{$q}%")
                  ->orWhere('category', 'like', "%{$q}%");
            });
        }

        $symptoms = $query->paginate(15)->withQueryString();

        return view('admin.symptoms.index', compact('symptoms', 'q'));
    }

    public function create()
    {
        $lastSymptom = Symptom::orderByDesc('id')->first();

        if ($lastSymptom && preg_match('/^G(\d+)$/', strtoupper($lastSymptom->code), $match)) {
            $nextNumber = ((int) $match[1]) + 1;
        } else {
            $nextNumber = 1;
        }

        $nextCode = 'G' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);

        return view('admin.symptoms.create', compact('nextCode'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => ['required', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:hardware,software'],
        ]);

        $code = strtoupper(trim($request->code));
        $name = trim($request->name);

        if (Symptom::where('code', $code)->exists()) {
            return back()->withInput()->with('error', 'Kode gejala sudah ada. Gunakan kode lain.');
        }

        Symptom::create([
            'code' => $code,
            'name' => $name,
            'category' => $request->category,
        ]);

        return redirect('/admin/symptoms')->with('success', 'Gejala berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $symptom = Symptom::find($id);
        abort_if(!$symptom, 404);

        return view('admin.symptoms.edit', compact('symptom'));
    }

    public function update(Request $request, $id)
    {
        $symptom = Symptom::find($id);
        abort_if(!$symptom, 404);

        $request->validate([
            'code' => ['required', 'string', 'max:20'],
            'name' => ['required', 'string', 'max:255'],
            'category' => ['required', 'in:hardware,software'],
        ]);

        $code = strtoupper(trim($request->code));
        $name = trim($request->name);

        if (Symptom::where('code', $code)->where('id', '!=', $id)->exists()) {
            return back()->withInput()->with('error', 'Kode gejala sudah dipakai gejala lain.');
        }

        $symptom->update([
            'code' => $code,
            'name' => $name,
            'category' => $request->category,
        ]);

        return redirect('/admin/symptoms')->with('success', 'Gejala berhasil diupdate.');
    }

    public function delete($id)
    {
        $symptom = Symptom::withCount('caseSymptoms')->find($id);
        abort_if(!$symptom, 404);

        if (($symptom->case_symptoms_count ?? 0) > 0) {
            return back()->with('error', 'Gejala ini sedang dipakai di Data Case. Hapus relasinya dulu.');
        }

        $symptom->delete();

        return back()->with('success', 'Gejala berhasil dihapus.');
    }
}