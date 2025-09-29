<?php

namespace App\Services;

use App\Models\DefectInput;
use App\Models\DefectInputDetail;
use App\Models\Workstation;
use Carbon\Carbon;

class ParetoProblemService
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

    public function getParetoProblems($departmentId = null)
{
    $currentYear = Carbon::now()->year;
    $currentMonth = Carbon::now()->month;

    $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
                   'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

    $thisMonthNameEn = $monthNames[$currentMonth - 1];

    // Get total check
    $totalCheckQuery = DefectInput::join('sub_workstations', 'defect_inputs.line', '=', 'sub_workstations.subsect_name')
        ->join('workstations', 'sub_workstations.id_workstation', '=', 'workstations.id')
        ->join('departments', 'workstations.id_dept', '=', 'departments.id')
        ->when($departmentId, function ($q) use ($departmentId) {
            $q->where('departments.id', $departmentId);
        })
        ->whereYear('defect_inputs.tgl', $currentYear)
        ->whereMonth('defect_inputs.tgl', $currentMonth)
        ->sum('defect_inputs.total_check');

    $totalCheck = (int) $totalCheckQuery;

    // Get repair and reject totals for pie
    $repairTotal = DefectInputDetail::join('defect_inputs', 'defect_input_details.defect_input_id', '=', 'defect_inputs.id')
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
        ->where('defect_input_details.keterangan', 'repair')
        ->sum('defect_input_details.jumlah_defect');

    $rejectTotal = DefectInputDetail::join('defect_inputs', 'defect_input_details.defect_input_id', '=', 'defect_inputs.id')
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
        ->where('defect_input_details.keterangan', 'reject')
        ->sum('defect_input_details.jumlah_defect');

    $totalNG = $repairTotal + $rejectTotal;

    $repairRatio = $totalNG > 0 ? round(($repairTotal / $totalNG) * 100) : 0;
    $rejectRatio = $totalNG > 0 ? round(($rejectTotal / $totalNG) * 100) : 0;

    // Get defect data per defect per ws for main ws determination
    $defectPerWs = DefectInputDetail::join('defect_inputs', 'defect_input_details.defect_input_id', '=', 'defect_inputs.id')
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
                     defect_categories.defect_name as category_name,
                     workstations.sect_name,
                     defect_input_details.keterangan,
                     SUM(defect_input_details.jumlah_defect) as total_per_ws')
        ->groupBy('defect_subs.jenis_defect', 'defect_categories.defect_name', 'workstations.sect_name', 'defect_input_details.keterangan')
        ->get();

    // Aggregate overall per defect
    $overallDefects = [];
    foreach ($defectPerWs as $item) {
        $defectName = $item->defect_name;
        if (!isset($overallDefects[$defectName])) {
            $overallDefects[$defectName] = [
                'total' => 0,
                'keterangan' => $item->keterangan, // Assume same for all entries of same defect
                'category' => $item->category_name, // Assume same for all entries of same defect
                'ws_contributions' => [],
            ];
        }
        $overallDefects[$defectName]['total'] += (int) $item->total_per_ws;
        $overallDefects[$defectName]['ws_contributions'][$item->sect_name] = (int) $item->total_per_ws;
    }

    // For each defect, determine main ws (max contribution)
    foreach ($overallDefects as $defectName => &$data) {
        arsort($data['ws_contributions']);
        $data['main_ws'] = key($data['ws_contributions']);
    }
    unset($data);  // Break reference

    // Sort overall defects by total desc
    uasort($overallDefects, function ($a, $b) {
        return $b['total'] <=> $a['total'];
    });

    // Get top 10
    $topProblems = array_slice($overallDefects, 0, 10, true);
    $othersTotal = array_sum(array_column(array_slice($overallDefects, 10), 'total'));

    // Prepare table data
    $tableData = [];
    $rank = 1;
    foreach ($topProblems as $defectName => $data) {
        $status = strtoupper(substr($data['keterangan'], 0)); // rp or rj
        $tableData[] = [
            'rank' => $rank,
            'problem' => $defectName,
            'qty' => $data['total'],
            'status' => $status,
        ];
        $rank++;
    }
    // Add others
    $tableData[] = [
        'rank' => '',
        'problem' => 'Others',
        'qty' => $othersTotal,
        'status' => '',
    ];

    // For list and grouping (top 6)
    $top6 = array_slice($overallDefects, 0, 6, true);
    $groups = [];
    $rank = 1;
    foreach ($top6 as $defectName => $data) {
        $category = strtoupper($data['category']); // Use category for grouping instead of main_ws
        $suffix = ($rank == 1) ? 'st' : (($rank == 2) ? 'nd' : (($rank == 3) ? 'rd' : 'th'));
        $groups[$category][] = $rank . $suffix;
        $rank++;
    }

    // Format groups
    $formattedGroups = [];
    foreach ($groups as $category => $ranks) {
        $formattedGroups[] = $category . ': ' . implode(', ', $ranks);
    }

    // Pie data
    $pieData = [
        'repair' => $repairTotal,
        'reject' => $rejectTotal,
        'repair_ratio' => $repairRatio,
        'reject_ratio' => $rejectRatio,
        'total_ng' => $totalNG,
    ];

    // List data
    $listData = [];
    $rank = 1;
    foreach ($top6 as $defectName => $data) {
        $listData[] = $rank . '. ' . $defectName;
        $rank++;
    }

    return [
        'month' => strtoupper($thisMonthNameEn),
        'table_data' => $tableData,
        'pie_data' => $pieData,
        'list_data' => $listData,
        'groups' => $formattedGroups,
        'total_check' => $totalCheck,
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
