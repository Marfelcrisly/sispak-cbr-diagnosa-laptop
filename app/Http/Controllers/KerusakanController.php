<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class KerusakanController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string)$request->query('q', ''));

        $query = DB::table('damages')->orderBy('code');

        if ($q !== '') {
            $query->where(function ($w) use ($q) {
                $w->where('code', 'like', "%{$q}%")
                  ->orWhere('name', 'like', "%{$q}%")
                  ->orWhere('solution', 'like', "%{$q}%");
            });
        }

        $damages = $query->paginate(15)->withQueryString();

        return view('admin.damages.index', compact('damages', 'q'));
    }

    public function create()
    {
        return view('admin.damages.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => ['required','string','max:20'],
            'name' => ['required','string','max:255'],
            'category' => ['required','in:hardware,software'], // 🔥 WAJIB
            'solution' => ['nullable','string'],
        ]);

        $code = strtoupper(trim($request->code));

        // CEK DUPLIKAT
        if (DB::table('damages')->where('code', $code)->exists()) {
            return back()->withInput()->with('error', 'Kode kerusakan sudah ada.');
        }

        DB::table('damages')->insert([
            'code' => $code,
            'name' => trim($request->name),
            'category' => $request->category, // 🔥
            'solution' => $request->solution ? trim($request->solution) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/admin/damages')->with('success', 'Kerusakan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $damage = DB::table('damages')->where('id', $id)->first();
        abort_if(!$damage, 404);

        return view('admin.damages.edit', compact('damage'));
    }

    public function update(Request $request, $id)
    {
        $damage = DB::table('damages')->where('id', $id)->first();
        abort_if(!$damage, 404);

        $request->validate([
            'code' => ['required','string','max:20'],
            'name' => ['required','string','max:255'],
            'category' => ['required','in:hardware,software'], // 🔥
            'solution' => ['nullable','string'],
        ]);

        $code = strtoupper(trim($request->code));

        // CEK DUPLIKAT (KECUALI DIRI SENDIRI)
        if (DB::table('damages')
            ->where('code', $code)
            ->where('id','!=',$id)
            ->exists()) {
            return back()->withInput()->with('error', 'Kode sudah dipakai.');
        }

        DB::table('damages')->where('id', $id)->update([
            'code' => $code,
            'name' => trim($request->name),
            'category' => $request->category, // 🔥
            'solution' => $request->solution ? trim($request->solution) : null,
            'updated_at' => now(),
        ]);

        return redirect('/admin/damages')->with('success', 'Kerusakan berhasil diupdate.');
    }

    public function delete($id)
    {
        // CEK RELASI KE CASE
        if (DB::table('case_bases')->where('damage_id', $id)->exists()) {
            return back()->with('error', 'Kerusakan ini dipakai di Data Case.');
        }

        DB::table('damages')->where('id', $id)->delete();

        return back()->with('success', 'Kerusakan berhasil dihapus.');
    }
}