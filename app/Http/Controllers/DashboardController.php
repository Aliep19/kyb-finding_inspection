<?php

namespace App\Http\Controllers;

use App\Services\ChartService;
use App\Models\Department;

class DashboardController extends Controller
{
    protected $chartService;

    public function __construct(ChartService $chartService)
    {
        $this->chartService = $chartService;
    }

    public function index()
    {
        // Ambil semua departments untuk dropdown
        $departments = Department::all();

        // Ambil department pertama sebagai default
        $defaultDepartment = Department::orderBy('id')->first();
        $departmentId = $defaultDepartment ? $defaultDepartment->id : null;

        // Ambil chart data untuk department pertama (atau null jika tidak ada department)
        $chartData = $this->chartService->getChartData($departmentId);

        return view('index', compact('departments', 'chartData', 'defaultDepartment'));
    }

    // Endpoint untuk AJAX chart data
    public function getChartData($departmentId = null)
    {
        return response()->json($this->chartService->getChartData($departmentId));
    }
}
