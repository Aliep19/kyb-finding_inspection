<?php

namespace Database\Seeders;

use App\Models\DefectCategory;
use Illuminate\Database\Seeder;

class DefectCategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'PAINTING',
            'END BOLT',
            'RUBBER BUSHING',
            'EYE',
            'OUTER COVER',
            'O/SHEL',
            'SPOT WELDING',
            'SEAM WELDING',
            'REINFORCE WELDING',
            'BRACKET WELDING',
            'HOLE BRACKET',
            'SPRING SEAT',
            'HOSE BRACKET',
            'SENSOR BRACKET',
            'ROTARY WELDING',
            'WELDING',
            'LOWER CAP',
            'ORING',
            'MARKING STAMP',
            'ID COLOR',
            'FUNCTION',
            'PISTON ROD',
            'STICKER',
            'MOUNTING',
            'OTHER',
        ];

        foreach ($categories as $name) {
            DefectCategory::create(['defect_name' => $name]);
        }
    }
}

