<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class CheckShiftStatus
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->is('livewire/*') || !Auth::check()) {
            return $next($request);
        }

        $settings = DB::table('settings')->first();

        if ($settings && $settings->is_shift === 'aktif') {
            $user = Auth::user();

            // 1. Cari apakah ada shift yang berstatus 'open'
            $activeShift = \Modules\Shift\Entities\Shift::where('user_id', $user->id)
                ->where('status', 'open')
                ->first();

            // 2. JIKA TIDAK ADA SHIFT AKTIF
            if (!$activeShift) {
                // Jangan redirect jika sudah di halaman shift, halaman logout, atau pilih outlet
                // agar tidak terjadi loop (muter-muter) redirect
                if (
                    $request->routeIs('shift.*') ||
                    $request->is('logout') ||
                    $request->routeIs('auth.select-outlet')
                ) {
                    return $next($request);
                }

                // Redirect ke halaman buka shift
                return $this->redirectOpenShift($request);
            }

            // 3. LOGIKA ANTI TERKUNCI (MULTI-TAB / DEVICE)
            $currentSessionId = Session::getId();

            if ($activeShift->session_token !== $currentSessionId) {
                $isJustLoggedIn = Session::has('just_logged_in');
                $isFreshLogin = $user->last_login_at && $user->last_login_at->diffInMinutes(now()) <= 2;

                if ($isJustLoggedIn || $isFreshLogin) {
                    $activeShift->update(['session_token' => $currentSessionId]);
                    Session::forget('just_logged_in');
                    return $next($request);
                }

                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')->with('message', 'Sesi Anda telah berakhir karena akun digunakan di browser/perangkat lain.');
            }
        }

        return $next($request);
    }

    private function redirectOpenShift($request)
    {
        if ($request->ajax()) {
            return response()->json(['error' => 'Shift belum dibuka'], 403);
        }
        toast('Buka Shift (Session) terlebih dahulu!', 'error');
        return redirect()->route('shift.index');
    }
}
