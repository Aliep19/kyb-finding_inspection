<?php

namespace App\Services;

use App\Models\DefectInputDetail;
use Carbon\Carbon;

class PaintingRatioChartService
{
    public function getPaintingRatioChartData($departmentId = null)
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                       'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $paintingData = [];
        $notPaintingData = [];

        // Query base untuk mengambil data defect
        for ($month = 1; $month <= 12; $month++) {
            $queryBase = DefectInputDetail::join('defect_inputs', 'defect_input_details.defect_input_id', '=', 'defect_inputs.id')
                ->join('defect_categories', 'defect_input_details.defect_category_id', '=', 'defect_categories.id')
                ->join('sub_workstations', 'defect_inputs.line', '=', 'sub_workstations.subsect_name')
                ->join('workstations', 'sub_workstations.id_workstation', '=', 'workstations.id')
                ->join('departments', 'workstations.id_dept', '=', 'departments.id')
                ->whereYear('defect_inputs.tgl', $currentYear)
                ->whereMonth('defect_inputs.tgl', $month);

            if ($departmentId) {
                $queryBase->where('departments.id', $departmentId);
            }

            $painting = (clone $queryBase)->where('defect_categories.jenis_defect', 1)
                ->sum('defect_input_details.jumlah_defect');

            $notPainting = (clone $queryBase)->where('defect_categories.jenis_defect', 0)
                ->sum('defect_input_details.jumlah_defect');

            if ($month <= $currentMonth) {
                $paintingData[$month] = (int) $painting;
                $notPaintingData[$month] = (int) $notPainting;
            } else {
                $paintingData[$month] = null;
                $notPaintingData[$month] = null;
            }
        }

        // Hitung persentase perbandingan untuk Painting
        $paintingComparison = null;
        $thisMonthPainting = $paintingData[$currentMonth] ?? 0;
        $prevMonthPainting = $currentMonth > 1 ? ($paintingData[$currentMonth - 1] ?? 0) : null;

        if ($currentMonth > 1) {
            if ($prevMonthPainting > 0) {
                $paintingComparison = round((($thisMonthPainting - $prevMonthPainting) / $prevMonthPainting) * 100, 2);
            } elseif ($thisMonthPainting > 0) {
                $paintingComparison = 100; // Naik 100% jika bulan sebelumnya nol
            } else {
                $paintingComparison = 0;
            }
        }

        // Hitung persentase perbandingan untuk Not Painting
        $notPaintingComparison = null;
        $thisMonthNotPainting = $notPaintingData[$currentMonth] ?? 0;
        $prevMonthNotPainting = $currentMonth > 1 ? ($notPaintingData[$currentMonth - 1] ?? 0) : null;

        if ($currentMonth > 1) {
            if ($prevMonthNotPainting > 0) {
                $notPaintingComparison = round((($thisMonthNotPainting - $prevMonthNotPainting) / $prevMonthNotPainting) * 100, 2);
            } elseif ($thisMonthNotPainting > 0) {
                $notPaintingComparison = 100; // Naik 100% jika bulan sebelumnya nol
            } else {
                $notPaintingComparison = 0;
            }
        }

        // Nama bulan dalam bahasa Indonesia
        Carbon::setLocale('id');
        $thisMonthName = Carbon::now()->translatedFormat('F');
        $prevMonthName = $currentMonth > 1
            ? Carbon::now()->subMonth()->translatedFormat('F')
            : null;

        // Data untuk chart
        $chartData = [
            'labels' => $monthNames,
            'datasets' => [
                // Trend line Painting (tanpa datalabels)
                [
                    'type' => 'line',
                    'label' => '',
                    'data' => array_values($paintingData),
                    'borderColor' => '#FF4500',
                    'backgroundColor' => 'transparent',
                    'borderWidth' => 2,
                    'tension' => 0.4,
                    'fill' => false,
                    'pointRadius' => 4,
                    'pointBackgroundColor' => '#FFFFFF',
                    'yAxisID' => 'y',
                    'datalabels' => ['display' => false]
                ],
                // Trend line Not Painting (tanpa datalabels)
                [
                    'type' => 'line',
                    'label' => '',
                    'data' => array_values($notPaintingData),
                    'borderColor' => '#006400',
                    'backgroundColor' => 'transparent',
                    'borderWidth' => 2,
                    'tension' => 0.4,
                    'fill' => false,
                    'pointRadius' => 4,
                    'pointBackgroundColor' => '#FFFFFF',
                    'yAxisID' => 'y',
                    'datalabels' => ['display' => false]
                ],
                [
                    'type' => 'bar',
                    'label' => 'Painting',
                    'data' => array_values($paintingData),
                    'backgroundColor' => '#FFA500',
                    'yAxisID' => 'y',
                ],
                [
                    'type' => 'bar',
                    'label' => 'Not Painting',
                    'data' => array_values($notPaintingData),
                    'backgroundColor' => '#90EE90',
                    'yAxisID' => 'y',
                ],
            ],
            // Tambahan untuk card comparison
            'painting' => [
                'comparison' => $paintingComparison,
                'thisMonthValue' => $thisMonthPainting,
                'prevMonthValue' => $prevMonthPainting,
                'thisMonthName' => $thisMonthName,
                'prevMonthName' => $prevMonthName,
            ],
            'notPainting' => [
                'comparison' => $notPaintingComparison,
                'thisMonthValue' => $thisMonthNotPainting,
                'prevMonthValue' => $prevMonthNotPainting,
                'thisMonthName' => $thisMonthName,
                'prevMonthName' => $prevMonthName,
            ],
        ];

        return $chartData;
    }
}
