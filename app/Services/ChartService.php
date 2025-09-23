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

        // Selalu ambil 3 tahun terakhir
        $years = range($currentYear - 2, $currentYear);

        $averages = [];
        $targetsPerYear = [];

        foreach ($years as $year) {
            $query = DefectInput::whereYear('defect_inputs.tgl', $year)
                ->join('sub_workstations', 'defect_inputs.line', '=', 'sub_workstations.subsect_name')
                ->join('workstations', 'sub_workstations.id_workstation', '=', 'workstations.id')
                ->join('departments', 'workstations.id_dept', '=', 'departments.id');

            if ($departmentId) {
                $query->where('departments.id', $departmentId);
            }

            $monthlySums = $query->groupByRaw('MONTH(defect_inputs.tgl)')
                ->selectRaw('MONTH(defect_inputs.tgl) as month, SUM(defect_inputs.total_ng) as sum_ng')
                ->get();

            $numMonthsWithData = $monthlySums->where('sum_ng', '>', 0)->count();
            $totalNg = $monthlySums->sum('sum_ng');
            $average = $numMonthsWithData > 0 ? round($totalNg / $numMonthsWithData) : 0;
            $averages[$year] = $average;

            $targetQuery = Target::where('start_year', '<=', $year)
                ->where('end_year', '>=', $year);

            if ($departmentId) {
                $targetQuery->where('department_id', $departmentId);
            }

            $target = $targetQuery->first();
            $targetsPerYear[$year] = $target ? $target->target_value : 0;
        }

        // Data bulanan tahun berjalan
        $monthlyFindings = [];
        for ($month = 1; $month <= 12; $month++) {
            $query = DefectInput::whereYear('defect_inputs.tgl', $currentYear)
                ->whereMonth('defect_inputs.tgl', $month)
                ->join('sub_workstations', 'defect_inputs.line', '=', 'sub_workstations.subsect_name')
                ->join('workstations', 'sub_workstations.id_workstation', '=', 'workstations.id')
                ->join('departments', 'workstations.id_dept', '=', 'departments.id');

            if ($departmentId) {
                $query->where('departments.id', $departmentId);
            }

            $sum = $query->sum('defect_inputs.total_ng');
            $monthlyFindings[$month] = $sum;
        }

        // 🔥 Hitung persentase naik/turun (dibanding bulan sebelumnya)
        $comparison = null;
        $thisMonthValue = $monthlyFindings[$currentMonth] ?? 0;
        $prevMonthValue = $currentMonth > 1 ? ($monthlyFindings[$currentMonth - 1] ?? 0) : null;

        if ($currentMonth > 1) {
            if ($prevMonthValue > 0) {
                $comparison = round((($thisMonthValue - $prevMonthValue) / $prevMonthValue) * 100, 2);
            } elseif ($thisMonthValue > 0) {
                $comparison = 100; // naik 100% karena sebelumnya nol
            } else {
                $comparison = 0;
            }
        }

        // Nama bulan (pakai locale Indo)
        Carbon::setLocale('id');
        $thisMonthName = Carbon::now()->translatedFormat('F');
        $prevMonthName = $currentMonth > 1
            ? Carbon::now()->subMonth()->translatedFormat('F')
            : null;

        // Labels
        $labels = [];
        foreach ($years as $year) {
            $labels[] = $year;
        }

        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                       'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        foreach ($monthNames as $name) {
            $labels[] = $name;
        }

        // Bar data
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

        // Warna bar
        $barColors = [];
        foreach ($years as $year) {
            $barColors[] = '#FFD700';
        }
        for ($i = 1; $i <= 12; $i++) {
            $barColors[] = '#cbcbcbff';
        }

        // Line data
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
            // 🔽 tambahan buat card comparison
            'comparison'     => $comparison,
            'thisMonthValue' => $thisMonthValue,
            'prevMonthValue' => $prevMonthValue,
            'thisMonthName'  => $thisMonthName,
            'prevMonthName'  => $prevMonthName,
        ];
    }
}
