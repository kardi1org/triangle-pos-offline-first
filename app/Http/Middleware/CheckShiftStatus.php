<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Modules\Shift\Entities\Shift;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckShiftStatus
{
    public function handle(Request $request, Closure $next)
    {
        // 1. Izinkan akses jika ini adalah request internal Livewire
        if ($request->is('livewire/*')) {
            return $next($request);
        }

        $settings = DB::table('settings')->first();

        if ($settings && $settings->is_shift === 'aktif') {
            $activeShift = \Modules\Shift\Entities\Shift::where('user_id', Auth::id())
                ->where('status', 'open')
                ->first();

            if (!$activeShift) {
                if ($request->ajax()) {
                    return response()->json(['error' => 'Shift belum dibuka'], 403);
                }
                toast('Open a shift first!', 'error');
                return redirect()->route('shift.index')->with('error', 'Buka shift dulu!');
            }
        }

        return $next($request);
    }
}
