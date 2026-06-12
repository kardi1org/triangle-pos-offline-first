<?php

use Modules\FeatureManager\Entities\FeatureManagement;
use Illuminate\Support\Facades\Auth;

if (!function_exists('isFeatureEnabled')) {
    function isFeatureEnabled($key)
    {
        // 1. Pastikan user sudah login
        if (!Auth::check()) {
            return false;
        }

        $user = Auth::user();

        // 2. Ambil code paket milik user (misal hasilnya: 'free', 'premium', atau angka '1', '2')
        // Pastikan kolom 'codepaket' ini memang menyimpan nilai yang sesuai dengan nama kolom di DB
        $codePaket = $user->codepaket;

        // Jika codepaket kosong di user, set default (sesuaikan dengan default sistem Anda, misal '1' atau 'free')
        if (!$codePaket) {
            $codePaket = '1';
        }

        // 3. Query ke Database Utama ('mysql') membedah tabel FeatureManagement
        return \Modules\FeatureManager\Entities\FeatureManagement::on('db_pos')
            ->where('feature_key', $key)
            ->where('package_' . $codePaket, 1) // Menghasilkan: package_1, package_free, dll.
            ->exists();
    }
}

if (!function_exists('settings')) {
    function settings()
    {
        $settings = cache()->remember('settings', 24 * 60, function () {
            return \Modules\Setting\Entities\Setting::firstOrFail();
        });

        return $settings;
    }
}

if (!function_exists('format_currency')) {
    function format_currency($value, $format = true)
    {
        if (!$format) {
            return $value;
        }

        $settings = settings();
        $position = $settings->default_currency_position;
        $symbol = $settings->currency->symbol;
        $decimal_separator = $settings->currency->decimal_separator;
        $thousand_separator = $settings->currency->thousand_separator;

        if ($position == 'prefix') {
            $formatted_value = $symbol . ' ' . number_format((float) $value, 2, $decimal_separator, $thousand_separator);
        } else {
            $formatted_value = number_format((float) $value, 2, $decimal_separator, $thousand_separator) . $symbol;
        }

        return $formatted_value;
    }
}

if (!function_exists('make_reference_id')) {
    function make_reference_id($prefix, $number)
    {
        $padded_text = $prefix . '-' . str_pad($number, 5, 0, STR_PAD_LEFT);

        return $padded_text;
    }
}

if (!function_exists('array_merge_numeric_values')) {
    function array_merge_numeric_values()
    {
        $arrays = func_get_args();
        $merged = array();
        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                if (!is_numeric($value)) {
                    continue;
                }
                if (!isset($merged[$key])) {
                    $merged[$key] = $value;
                } else {
                    $merged[$key] += $value;
                }
            }
        }

        return $merged;
    }
}
