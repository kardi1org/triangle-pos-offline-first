<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('payments')->insert([
            'code' => 1,
            'Cash' => 'Y',
            'DebitCard' => 'Y',
            'Gopay' => 'Y',
            'CreditCard' => 'Y',
            'OVO' => 'Y',
            'ShopeePay' => 'Y',
            'Kredivo' => 'Y',
            'Dana' => 'Y',
            'GrabPay' => 'Y',
            'QRIS' => 'Y',
        ]);
    }
}
