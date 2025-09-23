<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubworkstationSeeder extends Seeder
{
    public function run(): void
    {
        $SubWorkstations = [
            [
                'id_workstation' => 4,
                'subsect_name' => 'Mounting SA1',
                'created_at' => now(),
                'updated_at' => now(),
            ],
                        [
                'id_workstation' => 4,
                'subsect_name' => 'Mounting SA3',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_workstation' => 4,
                'subsect_name' => 'Mounting SA6',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_workstation' => 4,
                'subsect_name' => 'Mounting SA7',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'id_workstation' => 4,
                'subsect_name' => 'Mounting SA9',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ];

        DB::table('sub_workstations')->insert($SubWorkstations);
    }
}
