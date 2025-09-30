<?php

namespace App\Http\Controllers;

use App\Services\ChartService;
use App\Services\RatioChartService;
use App\Services\PaintingRatioChartService;
use App\Services\ParetoFindingsService;
use App\Services\ParetoProblemService;
use App\Services\ParetoAssemblingService;
use App\Models\Department;
use Illuminate\Http\Request;

class MonitoringController extends Controller
{
    protected $chartService;
    protected $ratioChartService;
    protected $paintingRatioChartService;
    protected $paretoFindingsService;
    protected $paretoProblemService;
    protected $paretoAssemblingService;

    public function __construct(
        ChartService $chartService,
        RatioChartService $ratioChartService,
        PaintingRatioChartService $paintingRatioChartService,
        ParetoFindingsService $paretoFindingsService,
        ParetoProblemService $paretoProblemService,
        ParetoAssemblingService $paretoAssemblingService
    ) {
        $this->chartService = $chartService;
        $this->ratioChartService = $ratioChartService;
        $this->paintingRatioChartService = $paintingRatioChartService;
        $this->paretoFindingsService = $paretoFindingsService;
        $this->paretoProblemService = $paretoProblemService;
        $this->paretoAssemblingService = $paretoAssemblingService;
    }

    public function index($departmentId = null)
    {
        $departments = Department::whereIn('id', [5])->get();
        $defaultDepartment = Department::find(5);
        $departmentId = $departmentId ?? ($defaultDepartment ? $defaultDepartment->id : null);

        $chartData = $this->chartService->getChartData($departmentId);
        $ratioChartData = $this->ratioChartService->getRatioChartData($departmentId);
        $paintingRatioChartData = $this->paintingRatioChartService->getPaintingRatioChartData($departmentId);
        $paretoFindingsChartData = $this->paretoFindingsService->getParetoFindingsByLine($departmentId);
        $paretoProblemChartData = $this->paretoProblemService->getParetoProblems($departmentId);
        $paretoAssemblingChartData = $this->paretoAssemblingService->getTop3ParetoAssembling($departmentId);
        $paretoAssemblingDetails = $this->paretoAssemblingService->getTop3DefectDetails($departmentId);

        return view('monitoring.index', compact(
            'chartData',
            'ratioChartData',
            'paintingRatioChartData',
            'paretoFindingsChartData',
            'paretoProblemChartData',
            'paretoAssemblingChartData',
            'paretoAssemblingDetails',
            'departments',
            'defaultDepartment',
            'departmentId'
        ));
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

    public function getParetoFindingsChartData($departmentId = null)
    {
        return response()->json($this->paretoFindingsService->getParetoFindingsByLine($departmentId));
    }

    public function getParetoProblemChartData($departmentId = null)
    {
        return response()->json($this->paretoProblemService->getParetoProblems($departmentId));
    }

    public function getParetoAssemblingChartData($departmentId = null)
    {
        return response()->json($this->paretoAssemblingService->getTop3ParetoAssembling($departmentId));
    }

    public function getParetoAssemblingDetails($departmentId = null)
    {
        return response()->json($this->paretoAssemblingService->getTop3DefectDetails($departmentId));
    }
}
