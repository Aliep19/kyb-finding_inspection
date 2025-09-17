<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubworkstationSeeder extends Seeder
{
    public function run(): void
    {
        $SubWorkstations = [
            // [
            //     'id_workstation' => 8,
            //     'subsect_name' => 'SAS',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            //             [
            //     'id_workstation' => 8,
            //     'subsect_name' => 'CLEANING CENTER',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'id_workstation' => 8,
            //     'subsect_name' => 'OSC 1',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'id_workstation' => 8,
            //     'subsect_name' => 'OSC 2',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'id_workstation' => 8,
            //     'subsect_name' => 'WLC',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'id_workstation' => 8,
            //     'subsect_name' => 'INSPECTOR SUPPORT',
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
        ];

        DB::table('sub_workstations')->insert($SubWorkstations);
    }
}
