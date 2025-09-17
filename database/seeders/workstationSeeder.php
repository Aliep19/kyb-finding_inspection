<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class WorkstationSeeder extends Seeder
{
    public function run(): void
    {
        $Workstations = [
            // [
            //     'id_dept' => 5,
            //     'sect_name' => 'SA GAS',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'id_dept' => 5,
            //     'sect_name' => 'SA OLI',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'id_dept' => 5,
            //     'sect_name' => 'SDA+M',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'id_dept' => 5,
            //     'sect_name' => 'PAINTING 5',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'id_dept' => 5,
            //     'sect_name' => 'MOUNTING SA',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'id_dept' => 5,
            //     'sect_name' => 'MOUNTING SA 3',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'id_dept' => 5,
            //     'sect_name' => 'PACKAGING SA 3',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'id_dept' => 5,
            //     'sect_name' => 'SUPPORT SA',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
        ];

        DB::table('workstations')->insert($Workstations);
    }
}
