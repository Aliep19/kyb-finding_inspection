<?php

namespace App\Services;

use App\Models\DefectInput;
use App\Models\DefectInputDetail;
use App\Models\Workstation;
use Carbon\Carbon;

class ParetoFindingsService
{
    protected $workstationColors = [];
    protected $defectColorMap = [
        'repair'     => '#abbbf0ff',
        'reject'     => '#f65a5af4',
    ];

    public function __construct()
    {
        // Ambil semua workstation -> kasih warna dinamis
        $workstations = Workstation::pluck('sect_name')->toArray();

        foreach ($workstations as $ws) {
            $this->workstationColors[$ws] = $this->randomColor();
        }
    }

    public function getParetoFindingsByLine($departmentId = null)
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                       'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        // Ambil daftar workstation yang punya data defect
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

                $wsData[] = $month <= $currentMonth ? (int) $total : null;
            }

            $datasets[] = [
                'type' => 'bar',
                'label' => $ws,
                'data' => $wsData,
                'backgroundColor' => $this->workstationColors[$ws] ?? $this->randomColor(),
                'yAxisID' => 'y',
            ];
        }

        // Ambil top 3 workstation bulan ini
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

        // Nama bulan
        Carbon::setLocale('id');
        $thisMonthName = Carbon::now()->translatedFormat('F');

        // Data Pareto defect
        $defectData = DefectInputDetail::join('defect_inputs', 'defect_input_details.defect_input_id', '=', 'defect_inputs.id')
            ->join('defect_subs', 'defect_input_details.defect_sub_id', '=', 'defect_subs.id')
            ->join('defect_categories', 'defect_input_details.defect_category_id', '=', 'defect_categories.id')
            ->join('sub_workstations', 'defect_inputs.line', '=', 'sub_workstations.subsect_name')
            ->join('workstations', 'sub_workstations.id_workstation', '=', 'workstations.id')
            ->join('departments', 'workstations.id_dept', '=', 'departments.id')
            ->when($departmentId, function ($q) use ($departmentId) {
                $q->where('departments.id', $departmentId);
            })
            ->whereYear('defect_inputs.tgl', $currentYear)
            ->whereMonth('defect_inputs.tgl', $currentMonth)
            ->selectRaw('defect_subs.jenis_defect as defect_name,
            SUM(defect_input_details.jumlah_defect) as total,
            workstations.sect_name,
            defect_input_details.keterangan')

            ->groupBy('defect_input_details.defect_category_id', 'defect_input_details.defect_sub_id', 'workstations.sect_name', 'defect_input_details.keterangan')
            ->orderByDesc('total')
            ->get();

        $labels = [];
        $data = [];
        $backgroundColors = [];
        $borderColors = [];

        foreach ($defectData as $item) {
            $labels[] = $item->defect_name;
            $data[] = (int) $item->total;

            $backgroundColors[] = $this->getDefectColor($item->keterangan);
            $borderColors[] = $this->workstationColors[$item->sect_name] ?? '#000000';
        }

        $N = array_sum($data);
        $thisMonthNameEn = Carbon::now()->format('M Y');
        $title = "FI 4W Finding - PARETO " . strtoupper($thisMonthNameEn) . " [N=" . $N . "]";

        $paretoDefectChart = [
            'title' => $title,
            'labels' => $labels,
            'data' => $data,
            'backgroundColors' => $backgroundColors,
            'borderColors' => $borderColors,


        ];

        // Legend Defects
        $legendDefects = [];
        foreach ($this->defectColorMap as $name => $color) {
            $legendDefects[] = [
                'text' => ucfirst($name),
                'fillStyle' => $color,
            ];
        }

        // Legend Workstations
        $legendWorkstations = [];
        foreach ($this->workstationColors as $ws => $color) {
            $legendWorkstations[] = [
                'text' => $ws,
                'fillStyle' => $color,
            ];
        }

        return [
            'labels' => $monthNames,
            'datasets' => $datasets,
            'topWorkstations' => [
                'monthName' => $thisMonthName,
                'workstations' => $topWorkstations,
            ],
            'paretoDefects' => $paretoDefectChart,
            'legend' => [
                'defects' => $legendDefects,
                'workstations' => $legendWorkstations,
            ],
        ];
    }

    private function getDefectColor($keterangan)
    {
        $key = strtolower(trim($keterangan));
        return $this->defectColorMap[$key] ?? '#808080';
    }

    private function randomColor()
    {
        $basicColors = [
            '#FFFF00', '#FFA500', '#800080',
            '#00FFFF', '#FFC0CB', '#808080', '#ADD8E6',
        ];
        return $basicColors[array_rand($basicColors)];
    }
}
