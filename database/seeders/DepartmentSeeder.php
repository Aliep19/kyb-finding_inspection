<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [];

        for ($i = 1; $i <= 5; $i++) {
            $departments[] = [
                'dept_name' => 'PROD' . $i,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        DB::table('departments')->insert($departments);
    }
    
}
