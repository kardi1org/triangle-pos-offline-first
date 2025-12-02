<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Meja;

class MejaSeeder extends Seeder
{
    public function run(): void
    {
        Meja::insert([
            ['no_meja' => 1, 'name' => 'Table 1', 'qty_pax' => 4, 'location' => 'Indoor', 'shape' => 'Square', 'status' => 0],
            ['no_meja' => 2, 'name' => 'Table 2', 'qty_pax' => 2, 'location' => 'Outdoor', 'shape' => 'Round', 'status' => 0],
        ]);
    }
}
