<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CbrSettingController extends Controller
{
    public function index()
    {
        $setting = DB::table('cbr_settings')->orderByDesc('id')->first();
        $threshold = $setting ? (float)$setting->similarity_threshold : 70.00;

        $history = DB::table('cbr_settings as s')
            ->leftJoin('users as u', 'u.id', '=', 's.updated_by')
            ->select('s.*', 'u.name as updater_name')
            ->orderByDesc('s.id')
            ->limit(10)
            ->get();

        return view('settings.cbr', compact('threshold', 'history'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'similarity_threshold' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        DB::table('cbr_settings')->insert([
            'similarity_threshold' => (float)$request->similarity_threshold,
            'updated_by' => Auth::id(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect('/settings/cbr')->with('success', 'Threshold berhasil diperbarui.');
    }
}