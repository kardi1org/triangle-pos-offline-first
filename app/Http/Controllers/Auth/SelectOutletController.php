<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SelectOutletController extends Controller
{
    public function index()
    {
        // Ambil outlets melalui relasi yang sudah kita arahkan ke DB pusat
        $outlets = auth()->user()->outlets;

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
        // Gunakan 'mysql' sebagai database target pengecekan exists
        $request->validate([
            'outlet_id' => 'required|exists:mysql.outlets,id'
        ]);

        // Cari outlet tanpa menggunakan prefix 'mysql.' di dalam where
        // Laravel sudah tahu harus mencari di mana karena relasi di model User sudah benar
        $outlet = auth()->user()->outlets()
            ->where('outlets.id', $request->outlet_id)
            ->first();

        if (!$outlet) {
            return back()->withErrors(['outlet_id' => 'Anda tidak memiliki akses ke outlet ini.']);
        }

        session([
            'selected_outlet_id' => $outlet->id,
            'selected_outlet_name' => $outlet->name
        ]);

        return redirect()->route('home');
    }
}
