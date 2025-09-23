<?php

namespace App\Services;

use App\Models\DefectInput;
use Carbon\Carbon;

class RatioChartService
{
    public function getRatioChartData($departmentId = null)
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // Selalu ambil tahun berjalan saja
        $years = [$currentYear];

        // Inisialisasi array untuk average ratios tahunan
        $avgDefectRatios = [];
        $avgRepairRatios = [];
        $avgRejectRatios = [];

        foreach ($years as $year) {
            // Query dasar dengan join ke departments
            $query = DefectInput::whereYear('defect_inputs.tgl', $year)
                ->join('sub_workstations', 'defect_inputs.line', '=', 'sub_workstations.subsect_name')
                ->join('workstations', 'sub_workstations.id_workstation', '=', 'workstations.id')
                ->join('departments', 'workstations.id_dept', '=', 'departments.id');

            if ($departmentId) {
                $query->where('departments.id', $departmentId);
            }

            // Hitung ratio per bulan untuk tahun ini
            $monthlyData = $query->groupByRaw('MONTH(defect_inputs.tgl)')
                ->selectRaw('MONTH(defect_inputs.tgl) as month,
                             SUM(defect_inputs.total_ng) as sum_ng,
                             SUM(defect_inputs.repair) as sum_repair,
                             SUM(defect_inputs.reject) as sum_reject,
                             SUM(defect_inputs.total_check) as sum_check')
                ->get();

            $monthlyDefectRatios = [];
            $monthlyRepairRatios = [];
            $monthlyRejectRatios = [];

            foreach ($monthlyData as $data) {
                $totalCheck = $data->sum_check > 0 ? $data->sum_check : 1; // Hindari division by zero
                $monthlyDefectRatios[$data->month] = round(($data->sum_ng / $totalCheck) * 100, 2); // Dalam persen
                $monthlyRepairRatios[$data->month] = round(($data->sum_repair / $totalCheck) * 100, 2);
                $monthlyRejectRatios[$data->month] = round(($data->sum_reject / $totalCheck) * 100, 2);
            }

            // Hitung average tahunan (average dari monthly ratios yang ada datanya)
            $numMonthsWithData = count($monthlyData);
            $avgDefect = $numMonthsWithData > 0 ? round(array_sum($monthlyDefectRatios) / $numMonthsWithData, 2) : 0;
            $avgRepair = $numMonthsWithData > 0 ? round(array_sum($monthlyRepairRatios) / $numMonthsWithData, 2) : 0;
            $avgReject = $numMonthsWithData > 0 ? round(array_sum($monthlyRejectRatios) / $numMonthsWithData, 2) : 0;

            $avgDefectRatios[$year] = $avgDefect;
            $avgRepairRatios[$year] = $avgRepair;
            $avgRejectRatios[$year] = $avgReject;
        }

        // Data bulanan untuk tahun berjalan
        $monthlyDefectRatiosCurrent = [];
        $monthlyRepairRatiosCurrent = [];
        $monthlyRejectRatiosCurrent = [];

        for ($month = 1; $month <= 12; $month++) {
            $query = DefectInput::whereYear('defect_inputs.tgl', $currentYear)
                ->whereMonth('defect_inputs.tgl', $month)
                ->join('sub_workstations', 'defect_inputs.line', '=', 'sub_workstations.subsect_name')
                ->join('workstations', 'sub_workstations.id_workstation', '=', 'workstations.id')
                ->join('departments', 'workstations.id_dept', '=', 'departments.id');

            if ($departmentId) {
                $query->where('departments.id', $departmentId);
            }

            $data = $query->selectRaw('SUM(defect_inputs.total_ng) as sum_ng,
                                       SUM(defect_inputs.repair) as sum_repair,
                                       SUM(defect_inputs.reject) as sum_reject,
                                       SUM(defect_inputs.total_check) as sum_check')
                          ->first();

            $totalCheck = $data->sum_check > 0 ? $data->sum_check : 1;
            $monthlyDefectRatiosCurrent[$month] = round(($data->sum_ng / $totalCheck) * 100, 2);
            $monthlyRepairRatiosCurrent[$month] = round(($data->sum_repair / $totalCheck) * 100, 2);
            $monthlyRejectRatiosCurrent[$month] = round(($data->sum_reject / $totalCheck) * 100, 2);
        }

        // Hitung persentase perbandingan untuk Defect Ratio
        $defectComparison = null;
        $thisMonthDefect = $monthlyDefectRatiosCurrent[$currentMonth] ?? 0;
        $prevMonthDefect = $currentMonth > 1 ? ($monthlyDefectRatiosCurrent[$currentMonth - 1] ?? 0) : null;

        if ($currentMonth > 1) {
            if ($prevMonthDefect > 0) {
                $defectComparison = round((($thisMonthDefect - $prevMonthDefect) / $prevMonthDefect) * 100, 2);
            } elseif ($thisMonthDefect > 0) {
                $defectComparison = 100; // Naik 100% jika bulan sebelumnya nol
            } else {
                $defectComparison = 0;
            }
        }

        // Hitung persentase perbandingan untuk Repair Ratio
        $repairComparison = null;
        $thisMonthRepair = $monthlyRepairRatiosCurrent[$currentMonth] ?? 0;
        $prevMonthRepair = $currentMonth > 1 ? ($monthlyRepairRatiosCurrent[$currentMonth - 1] ?? 0) : null;

        if ($currentMonth > 1) {
            if ($prevMonthRepair > 0) {
                $repairComparison = round((($thisMonthRepair - $prevMonthRepair) / $prevMonthRepair) * 100, 2);
            } elseif ($thisMonthRepair > 0) {
                $repairComparison = 100; // Naik 100% jika bulan sebelumnya nol
            } else {
                $repairComparison = 0;
            }
        }

        // Hitung persentase perbandingan untuk Reject Ratio
        $rejectComparison = null;
        $thisMonthReject = $monthlyRejectRatiosCurrent[$currentMonth] ?? 0;
        $prevMonthReject = $currentMonth > 1 ? ($monthlyRejectRatiosCurrent[$currentMonth - 1] ?? 0) : null;

        if ($currentMonth > 1) {
            if ($prevMonthReject > 0) {
                $rejectComparison = round((($thisMonthReject - $prevMonthReject) / $prevMonthReject) * 100, 2);
            } elseif ($thisMonthReject > 0) {
                $rejectComparison = 100; // Naik 100% jika bulan sebelumnya nol
            } else {
                $rejectComparison = 0;
            }
        }

        // Nama bulan dalam bahasa Indonesia
        Carbon::setLocale('id');
        $thisMonthName = Carbon::now()->translatedFormat('F');
        $prevMonthName = $currentMonth > 1 ? Carbon::now()->subMonth()->translatedFormat('F') : null;

        // Labels: tahun + bulan
        $labels = [];
        foreach ($years as $year) {
            $labels[] = $year;
        }
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        foreach ($monthNames as $name) {
            $labels[] = $name;
        }

        // Data untuk defect ratio
        $defectData = [];
        foreach ($years as $year) {
            $defectData[] = $avgDefectRatios[$year];
        }
        for ($month = 1; $month <= 12; $month++) {
            $defectData[] = $month <= $currentMonth ? $monthlyDefectRatiosCurrent[$month] : null;
        }

        // Data untuk repair ratio
        $repairData = [];
        foreach ($years as $year) {
            $repairData[] = $avgRepairRatios[$year];
        }
        for ($month = 1; $month <= 12; $month++) {
            $repairData[] = $month <= $currentMonth ? $monthlyRepairRatiosCurrent[$month] : null;
        }

        // Data untuk reject ratio
        $rejectData = [];
        foreach ($years as $year) {
            $rejectData[] = $avgRejectRatios[$year];
        }
        for ($month = 1; $month <= 12; $month++) {
            $rejectData[] = $month <= $currentMonth ? $monthlyRejectRatiosCurrent[$month] : null;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'type' => 'bar',
                    'label' => 'Defect Ratio (%)',
                    'data' => $defectData,
                    'backgroundColor' => '#FFD700', // Kuning
                    'yAxisID' => 'y',
                ],
                [
                    'type' => 'bar',
                    'label' => 'Repair Ratio (%)',
                    'data' => $repairData,
                    'backgroundColor' => '#01265bff', // Hijau
                    'yAxisID' => 'y',
                ],
                [
                    'type' => 'bar',
                    'label' => 'Reject Ratio (%)',
                    'data' => $rejectData,
                    'backgroundColor' => '#FF0000', // Merah
                    'yAxisID' => 'y',
                ],
            ],
            // Tambahan untuk card comparison
            'defect' => [
                'comparison' => $defectComparison,
                'thisMonthValue' => $thisMonthDefect,
                'prevMonthValue' => $prevMonthDefect,
                'thisMonthName' => $thisMonthName,
                'prevMonthName' => $prevMonthName,
            ],
            'repair' => [
                'comparison' => $repairComparison,
                'thisMonthValue' => $thisMonthRepair,
                'prevMonthValue' => $prevMonthRepair,
                'thisMonthName' => $thisMonthName,
                'prevMonthName' => $prevMonthName,
            ],
            'reject' => [
                'comparison' => $rejectComparison,
                'thisMonthValue' => $thisMonthReject,
                'prevMonthValue' => $prevMonthReject,
                'thisMonthName' => $thisMonthName,
                'prevMonthName' => $prevMonthName,
            ],
        ];
    }
}
