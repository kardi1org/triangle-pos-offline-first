<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SelectOutletController extends Controller
{
    // public function index()
    // {
    //     // Ambil outlets melalui relasi yang sudah kita arahkan ke DB pusat
    //     $outlets = auth()->user()->outlets;

    //     if ($outlets->count() === 0) {
    //         Auth::logout();
    //         return redirect('/login')->withErrors(['email' => 'Akses outlet Anda telah dicabut.']);
    //     }

    //     return view('auth.select-outlet', compact('outlets'));
    // }
    public function index()
    {
        // 🎯 Ambil ID user yang sedang login
        $userId = auth()->id();

        // 🎯 EMBED NAMA DATABASE PUSAT ('db_pos.') LANGSUNG PADA NAMA TABEL
        // Ini memaksa MySQL cross-database query, tidak peduli koneksi defaultnya ke db_pos2
        $outlets = DB::connection('mysql')
            ->table('db_pos.outlets')
            ->join('db_pos.outlet_user', 'db_pos.outlets.id', '=', 'db_pos.outlet_user.outlet_id')
            ->where('db_pos.outlet_user.user_id', $userId)
            ->select('db_pos.outlets.*')
            ->get();

        // Mengubah array objek mentah menjadi Eloquent Model agar view 'auth.select-outlet' tidak error saat membaca property/method model
        $outlets = \Modules\User\Entities\Outlet::hydrate($outlets->toArray());

        if ($outlets->count() === 0) {
            Auth::logout();
            return redirect('/login')->withErrors(['email' => 'Akses outlet Anda telah dicabut.']);
        }

        return view('auth.select-outlet', compact('outlets'));
    }
    public function cancel(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }

    public function select(Request $request)
    {
        // 🎯 Cukup pastikan id terisi (required). Keberadaan ID akan dicek langsung lewat query DB di bawahnya.
        $request->validate([
            'outlet_id' => 'required'
        ]);

        $userId = auth()->id();

        // Query manual mencari ke database pusat
        $outlet = DB::connection('mysql')
            ->table('db_pos.outlets')
            ->join('db_pos.outlet_user', 'db_pos.outlets.id', '=', 'db_pos.outlet_user.outlet_id')
            ->where('db_pos.outlet_user.user_id', $userId)
            ->where('db_pos.outlets.id', $request->outlet_id)
            ->select('db_pos.outlets.*')
            ->first();

        if (!$outlet) {
            // Jika tidak ditemukan di DB pusat, otomatis memicu error ini ke halaman blade
            return back()->withErrors(['outlet_id' => 'Anda tidak memiliki akses ke outlet ini.']);
        }

        session([
            'selected_outlet_id' => $outlet->id,
            'selected_outlet_name' => $outlet->name
        ]);

        return redirect()->route('home');
    }
}
