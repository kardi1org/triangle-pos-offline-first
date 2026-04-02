<?php

namespace App\Http\Controllers\Auth;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Traits\ManagesTenantConnection;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    */

    use AuthenticatesUsers, ManagesTenantConnection;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    // protected function authenticated(Request $request, $user)
    // {
    //     $mytime = Carbon::now();

    //     // 1. LOGIKA OTORISASI EKSISTING (Pengecekan is_active & valid_date)
    //     if (($user->is_active != 1) || ($user->valid_date < $mytime->format('Y-m-d')) && ($user->id != 1)) {
    //         Auth::logout();

    //         if (($user->is_active == 2) && ($user->id > 1)) {
    //             $valid = '';
    //         } else {
    //             $valid = '<br> End Valid ' . $user->valid_date;
    //         }

    //         return back()->with([
    //             'account_deactivated' => 'Your account is deactivated! Please contact with Super Admin.' . $valid
    //         ]);
    //     }

    //     // =======================================================
    //     // LOGIKA VERIFIKASI KONEKSI DATABASE (Pengecekan Kualitas)
    //     // =======================================================

    //     // Kumpulkan konfigurasi lengkap dari Model User untuk pengujian
    //     $tenantConfig = [
    //         'database' => $user->tenant_database,
    //         'host' => $user->tenant_host,
    //         'port' => $user->tenant_port,
    //         'username' => $user->tenant_username,
    //         'password' => $user->tenant_password,
    //     ];

    //     // Lakukan tes koneksi dengan konfigurasi lengkap. (Perkiraan Lokasi Line 76)
    //     if (!$this->setAndTestTenantConnection($tenantConfig)) {

    //         // JIKA GAGAL: Batalkan Login yang baru saja dibuat
    //         $this->guard()->logout();
    //         $request->session()->invalidate();
    //         $request->session()->regenerateToken();

    //         // Arahkan kembali ke login dengan pesan error koneksi DB
    //         return redirect('/login')
    //             ->withInput($request->only('email', 'remember'))
    //             ->withErrors([
    //                 'email' => 'Gagal terhubung ke database tenant **' . $user->DB . '** Anda. Silakan hubungi administrator.',
    //             ]);
    //     }
    //     // =======================================================

    //     // JIKA OTORISASI DAN KONEKSI DB BERHASIL: Lanjutkan ke dashboard
    //     return redirect()->intended(RouteServiceProvider::HOME);
    // }

    protected function authenticated(Request $request, $user)
    {
        $mytime = Carbon::now();

        // 1. CEK STATUS AKTIF & MASA BERLAKU
        if (($user->is_active != 1) || ($user->valid_date < $mytime->format('Y-m-d')) && ($user->id != 1)) {
            Auth::logout();
            $valid = ($user->is_active == 2) ? '' : '<br> End Valid ' . $user->valid_date;
            return back()->with(['account_deactivated' => 'Your account is deactivated! Please contact Admin.' . $valid]);
        }

        // 2. CEK KONEKSI DATABASE TENANT
        $tenantConfig = [
            'database' => $user->tenant_database,
            'host' => $user->tenant_host,
            'port' => $user->tenant_port,
            'username' => $user->tenant_username,
            'password' => $user->tenant_password,
        ];

        if (!$this->setAndTestTenantConnection($tenantConfig)) {
            $this->guard()->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/login')->withInput($request->only('email', 'remember'))
                ->withErrors(['email' => 'Gagal terhubung ke database tenant **' . $user->DB . '** Anda. Silakan hubungi administrator.']);
        }

        // 3. LOGIKA PEMILIHAN OUTLET
        $userOutlets = $user->outlets; // Pastikan relasi 'outlets' sudah ada di model User
        $count = $userOutlets->count();

        if ($count === 0 && $user->id != 1) { // User ID 1 biasanya Super Admin (opsional bypass)
            Auth::logout();
            return redirect('/login')->withErrors(['email' => 'Anda belum memiliki otorisasi akses ke outlet manapun.']);
        }

        if ($count > 1) {
            // Jika lebih dari 1 outlet, arahkan ke halaman pilihan
            // Simpan status bahwa user sedang dalam proses memilih outlet
            return redirect()->route('auth.select-outlet');
        }

        // Jika hanya 1 outlet, langsung set ke session dan masuk
        $outlet = $userOutlets->first();
        session(['selected_outlet_id' => $outlet->id]);
        session(['selected_outlet_name' => $outlet->name]);

        return redirect()->intended(RouteServiceProvider::HOME);
    }
}
