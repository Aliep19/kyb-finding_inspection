<?php

namespace App\Services;

use App\Models\DefectInputDetail;
use App\Models\Workstation;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ParetoAssemblingService
{
    protected $defectColors = [
        '#808080', // Dark gray for first
        '#000000', // Black for second
        '#90EE90', // Light green for third
    ];

    public function getTop3ParetoAssembling($departmentId = null)
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // Ambil TOP 3 workstation berdasarkan SUM(jumlah_defect)
        $workstations = Workstation::join('sub_workstations', 'workstations.id', '=', 'sub_workstations.id_workstation')
            ->join('defect_inputs', 'defect_inputs.line', '=', 'sub_workstations.subsect_name')
            ->join('defect_input_details', 'defect_inputs.id', '=', 'defect_input_details.defect_input_id')
            ->when($departmentId, function ($q) use ($departmentId) {
                $q->where('workstations.id_dept', $departmentId);
            })
            ->whereYear('defect_inputs.tgl', $currentYear)
            ->whereMonth('defect_inputs.tgl', $currentMonth)
            ->groupBy('workstations.sect_name')
            ->select('workstations.sect_name', DB::raw('SUM(defect_input_details.jumlah_defect) as defect_count'))
            ->orderByDesc('defect_count')
            ->limit(3)
            ->get();

        $chartData = [];
        foreach ($workstations as $workstation) {
            $sectionName = $workstation->sect_name;
            $chartTitle = 'TOP 3 Pareto ' . strtoupper($sectionName);

            // Query untuk ambil top 3 defects per workstation - EXTENDED: Tambah GROUP_CONCAT untuk pica images
            $topDefects = DefectInputDetail::join('defect_inputs', 'defect_input_details.defect_input_id', '=', 'defect_inputs.id')
                ->join('defect_subs', 'defect_input_details.defect_sub_id', '=', 'defect_subs.id')
                ->join('sub_workstations', 'defect_inputs.line', '=', 'sub_workstations.subsect_name')
                ->join('workstations', 'sub_workstations.id_workstation', '=', 'workstations.id')
                ->join('departments', 'workstations.id_dept', '=', 'departments.id')
                ->when($departmentId, function ($q) use ($departmentId) {
                    $q->where('departments.id', $departmentId);
                })
                ->where('workstations.sect_name', $sectionName)
                ->whereYear('defect_inputs.tgl', $currentYear)
                ->whereMonth('defect_inputs.tgl', $currentMonth)
                ->select(
                    'defect_subs.jenis_defect as defect_name',
                    DB::raw('SUM(defect_input_details.jumlah_defect) as total_defect'),
                    DB::raw('GROUP_CONCAT(DISTINCT defect_input_details.pica SEPARATOR ",") as picas')  // Asumsi kolom 'pica' berisi path gambar, comma-separated untuk multiple
                )
                ->groupBy('defect_subs.jenis_defect')
                ->orderByDesc('total_defect')
                ->limit(3)
                ->get();

            $data = [
                'title' => $chartTitle,
                'labels' => [],
                'values' => [],
                'colors' => [],
                'images' => [],  // Tambahan: Array gambar per defect (bisa multiple, atau ambil satu pertama)
            ];

            foreach ($topDefects as $index => $defect) {
                $data['labels'][] = $defect->defect_name;
                $data['values'][] = (int) $defect->total_defect;
                $data['colors'][] = $this->defectColors[$index] ?? $this->randomColor();

                // Parse picas: Ambil array path gambar (misal, tampilkan satu pertama, atau semua)
                $picaPaths = $defect->picas ? explode(',', $defect->picas) : [];
                $data['images'][] = array_map(function ($path) {
                    return asset('storage/pica/' . basename($path)); // Gunakan asset untuk path publik
                }, array_filter($picaPaths));
            }

            $chartData[$sectionName] = $data;
        }

        return $chartData;
    }

    public function getTop3DefectDetails($departmentId = null)
    {
        $currentYear = Carbon::now()->year;
        $currentMonth = Carbon::now()->month;

        // Ambil TOP 3 workstation yang sama kayak di Pareto
        $workstations = Workstation::join('sub_workstations', 'workstations.id', '=', 'sub_workstations.id_workstation')
            ->join('defect_inputs', 'defect_inputs.line', '=', 'sub_workstations.subsect_name')
            ->join('defect_input_details', 'defect_inputs.id', '=', 'defect_input_details.defect_input_id')
            ->when($departmentId, function ($q) use ($departmentId) {
                $q->where('workstations.id_dept', $departmentId);
            })
            ->whereYear('defect_inputs.tgl', $currentYear)
            ->whereMonth('defect_inputs.tgl', $currentMonth)
            ->groupBy('workstations.sect_name')
            ->select('workstations.sect_name', DB::raw('SUM(defect_input_details.jumlah_defect) as defect_count'))
            ->orderByDesc('defect_count')
            ->limit(3)
            ->get();

        $defectDetails = [];
        foreach ($workstations as $workstation) {
            $sectionName = $workstation->sect_name;

            // Ambil top 3 defect untuk workstation ini
            $topDefects = DefectInputDetail::join('defect_inputs', 'defect_input_details.defect_input_id', '=', 'defect_inputs.id')
                ->join('defect_subs', 'defect_input_details.defect_sub_id', '=', 'defect_subs.id')
                ->join('sub_workstations', 'defect_inputs.line', '=', 'sub_workstations.subsect_name')
                ->join('workstations', 'sub_workstations.id_workstation', '=', 'workstations.id')
                ->join('departments', 'workstations.id_dept', '=', 'departments.id')
                ->when($departmentId, function ($q) use ($departmentId) {
                    $q->where('departments.id', $departmentId);
                })
                ->where('workstations.sect_name', $sectionName)
                ->whereYear('defect_inputs.tgl', $currentYear)
                ->whereMonth('defect_inputs.tgl', $currentMonth)
                ->select(
                    'defect_subs.jenis_defect as defect_name',
                    DB::raw('SUM(defect_input_details.jumlah_defect) as total_defect')
                )
                ->groupBy('defect_subs.jenis_defect')
                ->orderByDesc('total_defect')
                ->limit(3)
                ->get()
                ->pluck('defect_name');

            $workstationDetails = [];
            foreach ($topDefects as $defectName) {
                // Ambil detail untuk setiap defect
                $details = DefectInputDetail::join('defect_inputs', 'defect_input_details.defect_input_id', '=', 'defect_inputs.id')
                    ->join('defect_subs', 'defect_input_details.defect_sub_id', '=', 'defect_subs.id')
                    ->join('sub_workstations', 'defect_inputs.line', '=', 'sub_workstations.subsect_name')
                    ->join('workstations', 'sub_workstations.id_workstation', '=', 'workstations.id')
                    ->when($departmentId, function ($q) use ($departmentId) {
                        $q->where('workstations.id_dept', $departmentId);
                    })
                    ->where('workstations.sect_name', $sectionName)
                    ->whereYear('defect_inputs.tgl', $currentYear)
                    ->whereMonth('defect_inputs.tgl', $currentMonth)
                    ->where('defect_subs.jenis_defect', $defectName)
                    ->select(
                        'defect_inputs.marking_number as model',
                        'defect_inputs.line',
                        'workstations.sect_name as workstation',
                        'defect_inputs.lot',
                        DB::raw('SUM(defect_input_details.jumlah_defect) as qty')
                    )
                    ->groupBy('defect_inputs.marking_number', 'defect_inputs.line', 'workstations.sect_name', 'defect_inputs.lot')
                    ->get();

                // Hitung trend data
                $trendQuery = DefectInputDetail::join('defect_inputs', 'defect_input_details.defect_input_id', '=', 'defect_inputs.id')
                    ->join('defect_subs', 'defect_input_details.defect_sub_id', '=', 'defect_subs.id')
                    ->join('sub_workstations', 'defect_inputs.line', '=', 'sub_workstations.subsect_name')
                    ->join('workstations', 'sub_workstations.id_workstation', '=', 'workstations.id')
                    ->when($departmentId, function ($q) use ($departmentId) {
                        $q->where('workstations.id_dept', $departmentId);
                    })
                    ->where('workstations.sect_name', $sectionName)
                    ->where('defect_subs.jenis_defect', $defectName)
                    ->where('defect_inputs.tgl', '>=', Carbon::now()->subMonth()->startOfMonth())
                    ->where('defect_inputs.tgl', '<=', Carbon::now()->endOfMonth())
                    ->select(
                        'defect_inputs.tgl',
                        DB::raw('SUM(defect_input_details.jumlah_defect) as qty')
                    )
                    ->groupBy('defect_inputs.tgl')
                    ->get();

                $previousMonth = Carbon::now()->subMonth();
                $currentMonthObj = Carbon::now();
                $weeks = [];
                foreach ([$previousMonth, $currentMonthObj] as $month) {
                    $monthName = $month->format('M');
                    $numWeeks = ceil($month->daysInMonth / 7);
                    for ($w = 1; $w <= $numWeeks; $w++) {
                        $weeks[] = "{$monthName} W{$w}";
                    }
                }

                $trendValues = array_fill(0, count($weeks), 0);
                foreach ($trendQuery as $item) {
                    $date = Carbon::parse($item->tgl);
                    $monthName = $date->format('M');
                    $weekNum = ceil($date->day / 7);
                    $weekLabel = "{$monthName} W{$weekNum}";
                    $index = array_search($weekLabel, $weeks);
                    if ($index !== false) {
                        $trendValues[$index] += (int) $item->qty;
                    }
                }

                // Logika warna berdasarkan perbandingan minggu sebelumnya
                $trendColors = [];
                for ($i = 0; $i < count($trendValues); $i++) {
                    if ($i == 0) {
                        $trendColors[] = '#808080'; // minggu pertama netral
                    } else {
                        if ($trendValues[$i] > $trendValues[$i - 1]) {
                            $trendColors[] = '#FF0000'; // naik → merah
                        } elseif ($trendValues[$i] < $trendValues[$i - 1]) {
                            $trendColors[] = '#008000'; // turun → hijau
                        } else {
                            $trendColors[] = '#808080'; // sama → abu-abu
                        }
                    }
                }

                $trend = [
                    'labels' => $weeks,
                    'values' => $trendValues,
                    'colors' => $trendColors,
                ];

                $workstationDetails[$defectName] = [
                    'details' => $details,
                    'trend' => $trend,
                ];
            }

            $defectDetails[$sectionName] = $workstationDetails;
        }

        return $defectDetails;
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
