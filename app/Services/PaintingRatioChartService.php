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
                $paintingData[] = (int) $painting;
                $notPaintingData[] = (int) $notPainting;
            } else {
                $paintingData[] = null;
                $notPaintingData[] = null;
            }
        }

        return [
            'labels' => $monthNames,
            'datasets' => [
                               // 🔹 Trend line Painting (tanpa datalabels)
                [
                    'type' => 'line',
                    'label' => '',
                    'data' => $paintingData,
                    'borderColor' => '#FF4500',
                    'backgroundColor' => 'transparent',
                    'borderWidth' => 2,
                    'tension' => 0.4,
                    'fill' => false,
                    'pointRadius' => 4,
                    'pointBackgroundColor' => '#FFFFFF', // titik lingkaran warna putih
                    'yAxisID' => 'y',
                    'datalabels' => [ 'display' => false ] // ❌ matikan angka
                ],
                // 🔹 Trend line Not Painting (tanpa datalabels)
                [
                    'type' => 'line',
                    'label' => '',
                    'data' => $notPaintingData,
                    'borderColor' => '#006400',
                    'backgroundColor' => 'transparent',
                    'borderWidth' => 2,
                    'tension' => 0.4,
                    'fill' => false,
                    'pointRadius' => 4,
                    'pointBackgroundColor' => '#FFFFFF',
                    'yAxisID' => 'y',
                    'datalabels' => [ 'display' => false ] // ❌ matikan angka
                ],
                [
                    'type' => 'bar',
                    'label' => 'Painting',
                    'data' => $paintingData,
                    'backgroundColor' => '#FFA500',
                    'yAxisID' => 'y',
                ],
                [
                    'type' => 'bar',
                    'label' => 'Not Painting',
                    'data' => $notPaintingData,
                    'backgroundColor' => '#90EE90',
                    'yAxisID' => 'y',
                ],

            ],
        ];
    }
}
