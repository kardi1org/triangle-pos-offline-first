<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrderSummarySettingsSeeder extends Seeder
{
    public function run()
    {
        $settings = [
            [
                'feature_key'         => 'order_tax',
                'feature_name'        => 'Pajak PB1 / PPN',
                'formula_description' => 'Pajak pemerintah yang dihitung dari subtotal atau subtotal+service.',
                'tax_position'        => 'after', // Pajak biasanya di posisi akhir perhitungan
                'default_value'       => 11.00,
                'is_active'           => true,
            ],
            [
                'feature_key'         => 'service_charge',
                'feature_name'        => 'Service Charge',
                'formula_description' => 'Biaya layanan (makan di tempat). Dihitung berdasarkan persentase dari subtotal.',
                'tax_position'        => 'before', // Dihitung SEBELUM pajak agar pajak juga mengenakan service (standar resto)
                'default_value'       => 5.00,
                'is_active'           => true,
            ],
            [
                'feature_key'         => 'delivery_fee',
                'feature_name'        => 'Biaya Pengantaran',
                'formula_description' => 'Biaya tetap (fixed) untuk pesanan pesan antar.',
                'tax_position'        => 'after',
                'default_value'       => 0.00,
                'is_active'           => true,
            ],
            [
                'feature_key'         => 'lain_a',
                'feature_name'        => 'Biaya Tambahan A',
                'formula_description' => 'Biaya operasional tambahan lainnya.',
                'tax_position'        => 'after',
                'default_value'       => 0.00,
                'is_active'           => true,
            ],
            [
                'feature_key'         => 'lain_b',
                'feature_name'        => 'Biaya Tambahan B',
                'formula_description' => 'Biaya operasional tambahan lainnya.',
                'tax_position'        => 'after',
                'default_value'       => 0.00,
                'is_active'           => true,
            ],
            [
                'feature_key'         => 'discount_global',
                'feature_name'        => 'Diskon Umum',
                'formula_description' => 'Potongan harga dalam persentase yang mengurangi subtotal.',
                'tax_position'        => 'before',
                'default_value'       => 0.00,
                'is_active'           => true,
            ],
        ];

        foreach ($settings as $setting) {
            DB::table('order_summary_settings')->updateOrInsert(
                ['feature_key' => $setting['feature_key']],
                $setting
            );
        }
    }
}
