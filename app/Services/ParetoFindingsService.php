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

        // Ambil daftar workstation berdasarkan sect_name
        $workstations = DefectInput::join('sub_workstations', 'defect_inputs.line', '=', 'sub_workstations.subsect_name')
            ->join('workstations', 'sub_workstations.id_workstation', '=', 'workstations.id')
            ->join('departments', 'workstations.id_dept', '=', 'departments.id')
            ->when($departmentId, function ($q) use ($departmentId) {
                $q->where('departments.id', $departmentId);
            })
            ->distinct()
            ->pluck('workstations.sect_name')
            ->toArray();

        $datasets = [];

        foreach ($workstations as $ws) {
            $wsData = [];

            for ($month = 1; $month <= 12; $month++) {
                $query = DefectInput::join('sub_workstations', 'defect_inputs.line', '=', 'sub_workstations.subsect_name')
                    ->join('workstations', 'sub_workstations.id_workstation', '=', 'workstations.id')
                    ->join('departments', 'workstations.id_dept', '=', 'departments.id')
                    ->whereYear('defect_inputs.tgl', $currentYear)
                    ->whereMonth('defect_inputs.tgl', $month)
                    ->where('workstations.sect_name', $ws);

                if ($departmentId) {
                    $query->where('departments.id', $departmentId);
                }

                $total = $query->sum('defect_inputs.total_ng');

                if ($month <= $currentMonth) {
                    $wsData[] = (int) $total;
                } else {
                    $wsData[] = null;
                }
            }

            $datasets[] = [
                'type' => 'bar',
                'label' => $ws,
                'data' => $wsData,
                'backgroundColor' => $this->randomColor(),
                'yAxisID' => 'y',
            ];
        }

        // Ambil top 3 workstations untuk bulan ini
        $topWorkstationsQuery = DefectInput::join('sub_workstations', 'defect_inputs.line', '=', 'sub_workstations.subsect_name')
            ->join('workstations', 'sub_workstations.id_workstation', '=', 'workstations.id')
            ->join('departments', 'workstations.id_dept', '=', 'departments.id')
            ->whereYear('defect_inputs.tgl', $currentYear)
            ->whereMonth('defect_inputs.tgl', $currentMonth)
            ->when($departmentId, function ($q) use ($departmentId) {
                $q->where('departments.id', $departmentId);
            })
            ->groupBy('workstations.sect_name')
            ->selectRaw('workstations.sect_name as workstation, SUM(defect_inputs.total_ng) as total_ng')
            ->orderByDesc('total_ng')
            ->take(3)
            ->get();

        $topWorkstations = $topWorkstationsQuery->map(function ($item) {
            return [
                'workstation' => $item->workstation,
                'total_ng' => (int) $item->total_ng,
            ];
        })->toArray();

        // Nama bulan dalam bahasa Indonesia
        Carbon::setLocale('id');
        $thisMonthName = Carbon::now()->translatedFormat('F');

        return [
            'labels' => $monthNames,
            'datasets' => $datasets,
            'topWorkstations' => [
                'monthName' => $thisMonthName,
                'workstations' => $topWorkstations,
            ],
        ];
    }

    private function randomColor()
    {
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
