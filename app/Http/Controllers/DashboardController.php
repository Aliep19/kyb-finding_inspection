<?php

namespace App\Http\Controllers;

use App\Services\ChartService;
use App\Services\RatioChartService;
use App\Services\PaintingRatioChartService;
use App\Models\Department;

class DashboardController extends Controller
{
    protected $chartService;
    protected $ratioChartService;
    protected $paintingRatioChartService;

    public function __construct(ChartService $chartService, RatioChartService $ratioChartService, PaintingRatioChartService $paintingRatioChartService)
    {
        $this->chartService = $chartService;
        $this->ratioChartService = $ratioChartService;
        $this->paintingRatioChartService = $paintingRatioChartService;
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

        return view('index', compact('departments', 'chartData', 'ratioChartData', 'paintingRatioChartData', 'defaultDepartment'));
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
}
