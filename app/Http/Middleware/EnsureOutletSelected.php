<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureOutletSelected
{
    public function handle(Request $request, Closure $next)
    {
        // Jika user sudah login
        if (Auth::check()) {
            // Kecuali untuk rute pemilihan outlet dan logout
            if (
                !$request->session()->has('selected_outlet_id') &&
                !$request->is('select-outlet*') &&
                !$request->is('logout')
            ) {

                return redirect()->route('auth.select-outlet')
                    ->with('warning', 'Silakan pilih outlet terlebih dahulu untuk melanjutkan.');
            }
        }

        return $next($request);
    }
}
