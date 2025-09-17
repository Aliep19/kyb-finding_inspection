<?php

namespace App\Services;

use App\Models\DefectInput;
use App\Models\Target;
use Carbon\Carbon;

class ChartService
{
    public function getChartData($departmentId = null)
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // Selalu ambil 3 tahun terakhir (walau DB kosong)
        $years = range($currentYear - 2, $currentYear);

        $averages = [];
        $targetsPerYear = [];

        foreach ($years as $year) {
            // Hitung rata-rata NG per tahun, dengan join ke relasi departments
            $query = DefectInput::whereYear('defect_inputs.tgl', $year)
                ->join('sub_workstations', 'defect_inputs.line', '=', 'sub_workstations.subsect_name') // Asumsi line = subsect_name
                ->join('workstations', 'sub_workstations.id_workstation', '=', 'workstations.id')
                ->join('departments', 'workstations.id_dept', '=', 'departments.id');

            if ($departmentId) {
                $query->where('departments.id', $departmentId); // Filter berdasarkan department
            }

            $monthlySums = $query->groupByRaw('MONTH(defect_inputs.tgl)')
                ->selectRaw('MONTH(defect_inputs.tgl) as month, SUM(defect_inputs.total_ng) as sum_ng')
                ->get();

            $numMonthsWithData = $monthlySums->where('sum_ng', '>', 0)->count();
            $totalNg = $monthlySums->sum('sum_ng');
            $average = $numMonthsWithData > 0 ? round($totalNg / $numMonthsWithData) : 0;
            $averages[$year] = $average;

            // Ambil target sesuai tahun (sudah ada filter departmentId)
            $targetQuery = Target::where('start_year', '<=', $year)
                ->where('end_year', '>=', $year);

            if ($departmentId) {
                $targetQuery->where('department_id', $departmentId);
            }

            $target = $targetQuery->first();
            $targetsPerYear[$year] = $target ? $target->target_value : 0; // kalau ga ada target → 0
        }

        // Data bulanan tahun berjalan, dengan join ke relasi departments
        $monthlyFindings = [];
        for ($month = 1; $month <= 12; $month++) {
            $query = DefectInput::whereYear('defect_inputs.tgl', $currentYear)
                ->whereMonth('defect_inputs.tgl', $month)
                ->join('sub_workstations', 'defect_inputs.line', '=', 'sub_workstations.subsect_name') // Asumsi line = subsect_name
                ->join('workstations', 'sub_workstations.id_workstation', '=', 'workstations.id')
                ->join('departments', 'workstations.id_dept', '=', 'departments.id');

            if ($departmentId) {
                $query->where('departments.id', $departmentId); // Filter berdasarkan department
            }

            $sum = $query->sum('defect_inputs.total_ng');
            $monthlyFindings[$month] = $sum;
        }

        // Labels (tahun + bulan Jan–Des) → sama seperti sebelumnya
        $labels = [];
        foreach ($years as $year) {
            $labels[] = $year;
        }

        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                       'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        foreach ($monthNames as $name) {
            $labels[] = $name;
        }

        // Bar data → sama seperti sebelumnya
        $barData = [];
        foreach ($years as $year) {
            $barData[] = $averages[$year];
        }
        for ($month = 1; $month <= 12; $month++) {
            if ($month <= $currentMonth) {
                $barData[] = $monthlyFindings[$month];
            } else {
                $barData[] = null;
            }
        }

        // Warna bar → sama
        $barColors = [];
        foreach ($years as $year) {
            $barColors[] = '#FFD700'; // kuning utk tahun
        }
        for ($i = 1; $i <= 12; $i++) {
            $barColors[] = '#808080'; // abu utk bulan
        }

        // Line data → sama
        $lineData = [];
        foreach ($years as $year) {
            $lineData[] = $targetsPerYear[$year];
        }
        for ($i = 1; $i <= 12; $i++) {
            $lineData[] = $targetsPerYear[$currentYear];
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'type' => 'line',
                    'label' => 'Target in Years/Month',
                    'data' => $lineData,
                    'borderColor' => 'red',
                    'backgroundColor' => 'transparent',
                    'yAxisID' => 'y',
                    'tension' => 0.1,
                ],
                [
                    'type' => 'bar',
                    'label' => 'Finding',
                    'data' => $barData,
                    'backgroundColor' => $barColors,
                    'yAxisID' => 'y',
                ],
            ],
        ];
    }
}
