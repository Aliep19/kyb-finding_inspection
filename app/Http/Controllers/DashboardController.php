<?php

namespace App\Http\Controllers;

use App\Services\ChartService;
use App\Services\RatioChartService;
use App\Services\PaintingRatioChartService;
use App\Services\ParetoFindingsService;
use App\Models\Department;

class DashboardController extends Controller
{
    protected $chartService;
    protected $ratioChartService;
    protected $paintingRatioChartService;
    protected $paretoFindingsService;

    public function __construct(
        ChartService $chartService,
        RatioChartService $ratioChartService,
        PaintingRatioChartService $paintingRatioChartService,
        ParetoFindingsService $paretoFindingsService
    ) {
        $this->chartService = $chartService;
        $this->ratioChartService = $ratioChartService;
        $this->paintingRatioChartService = $paintingRatioChartService;
        $this->paretoFindingsService = $paretoFindingsService;
    }

    public function index()
    {
        // Ambil department default (misalnya id = 5)
        $departments = Department::whereIn('id', [4, 5])->get();
        $defaultDepartment = Department::find(5);
        $departmentId = $defaultDepartment ? $defaultDepartment->id : null;

        // Ambil chart data dari service
        $chartData = $this->chartService->getChartData($departmentId);
        $ratioChartData = $this->ratioChartService->getRatioChartData($departmentId);
        $paintingRatioChartData = $this->paintingRatioChartService->getPaintingRatioChartData($departmentId);
        $paretoFindingsChartData = $this->paretoFindingsService->getParetoFindingsByLine($departmentId);

        // 🔥 comparison persentase bulan ini vs bulan lalu
        $comparison = $chartData['comparison'] ?? null;

        return view('index', compact(
            'departments',
            'chartData',
            'ratioChartData',
            'paintingRatioChartData',
            'paretoFindingsChartData',
            'defaultDepartment',
            'comparison'
        ));
    }

    // API endpoint untuk Chart JS
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

    public function getParetoFindingsChartData($departmentId = null)
    {
        return response()->json($this->paretoFindingsService->getParetoFindingsByLine($departmentId));
    }
}
