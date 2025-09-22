<?php

namespace App\Http\Controllers;

use App\Services\ChartService;
use App\Services\RatioChartService;
use App\Services\PaintingRatioChartService;
use App\Services\ParetoFindingsService; // Added new service
use App\Models\Department;

class DashboardController extends Controller
{
    protected $chartService;
    protected $ratioChartService;
    protected $paintingRatioChartService;
    protected $paretoFindingsService; // Added new property

    public function __construct(ChartService $chartService, RatioChartService $ratioChartService, PaintingRatioChartService $paintingRatioChartService, ParetoFindingsService $paretoFindingsService)
    {
        $this->chartService = $chartService;
        $this->ratioChartService = $ratioChartService;
        $this->paintingRatioChartService = $paintingRatioChartService;
        $this->paretoFindingsService = $paretoFindingsService; // Added new service injection
    }

    public function index()
    {
        // Ambil daftar department dengan id 4 dan 5 saja
        $departments = Department::whereIn('id', [4, 5])->get();
        // Ambil department default (misalnya id 5)
        $defaultDepartment = Department::find(5);
        // Kalau default department ketemu, ambil id-nya, kalau tidak null
        $departmentId = $defaultDepartment ? $defaultDepartment->id : null;

        // Ambil chart data untuk department pertama (atau null jika tidak ada department)
        $chartData = $this->chartService->getChartData($departmentId);
        $ratioChartData = $this->ratioChartService->getRatioChartData($departmentId);
        $paintingRatioChartData = $this->paintingRatioChartService->getPaintingRatioChartData($departmentId); // Tambahkan data untuk chart painting
        $paretoFindingsChartData = $this->paretoFindingsService->getParetoFindingsByLine($departmentId); // Added new chart data

        return view('index', compact('departments', 'chartData', 'ratioChartData', 'paintingRatioChartData', 'paretoFindingsChartData', 'defaultDepartment'));
    }

    public function getChartData($departmentId = null)
    {
        return response()->json($this->chartService->getChartData($departmentId));
    }

    public function getRatioChartData($departmentId = null)
    {
        return response()->json($this->ratioChartService->getRatioChartData($departmentId));
    }

    public function getPaintingRatioChartData($departmentId = null)
    {
        return response()->json($this->paintingRatioChartService->getPaintingRatioChartData($departmentId));
    }

    public function getParetoFindingsChartData($departmentId = null) // Added new method
    {
        return response()->json($this->paretoFindingsService->getParetoFindingsByLine($departmentId));
    }
}
