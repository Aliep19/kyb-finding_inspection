<?php

namespace App\Services;

use App\Models\DefectInput;
use Carbon\Carbon;

class ParetoFindingsService
{
    public function getParetoFindingsByLine($departmentId = null)
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                       'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Ambil daftar line unik (sub_workstation)
        $lines = DefectInput::join('sub_workstations', 'defect_inputs.line', '=', 'sub_workstations.subsect_name')
            ->join('workstations', 'sub_workstations.id_workstation', '=', 'workstations.id')
            ->join('departments', 'workstations.id_dept', '=', 'departments.id')
            ->when($departmentId, function ($q) use ($departmentId) {
                $q->where('departments.id', $departmentId);
            })
            ->distinct()
            ->pluck('defect_inputs.line')
            ->toArray();

        $datasets = [];

        foreach ($lines as $line) {
            $lineData = [];

            for ($month = 1; $month <= 12; $month++) {
                $query = DefectInput::join('sub_workstations', 'defect_inputs.line', '=', 'sub_workstations.subsect_name')
                    ->join('workstations', 'sub_workstations.id_workstation', '=', 'workstations.id')
                    ->join('departments', 'workstations.id_dept', '=', 'departments.id')
                    ->whereYear('defect_inputs.tgl', $currentYear)
                    ->whereMonth('defect_inputs.tgl', $month)
                    ->where('defect_inputs.line', $line);

                if ($departmentId) {
                    $query->where('departments.id', $departmentId);
                }

                $total = $query->sum('defect_inputs.total_ng');

                if ($month <= $currentMonth) {
                    $lineData[] = (int) $total;
                } else {
                    $lineData[] = null;
                }
            }

            $datasets[] = [
                'type' => 'bar',
                'label' => $line,
                'data' => $lineData,
                'backgroundColor' => $this->randomColor(),
                'yAxisID' => 'y',
            ];
        }

        return [
            'labels' => $monthNames,
            'datasets' => $datasets,
        ];
    }

    private function randomColor()
    {
        // Gunakan warna dasar saja
        $basicColors = [
            '#FF0000', // Merah
            '#00FF00', // Hijau
            '#0000FF', // Biru
            '#FFFF00', // Kuning
            '#FFA500', // Oranye
            '#800080', // Ungu
            '#00FFFF', // Cyan
            '#FFC0CB', // Pink
            '#808080', // Abu-abu
            '#000000', // Hitam
        ];
        return $basicColors[array_rand($basicColors)];
    }
}
