<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\Inspeksi\DefectcategoryController;
use App\Http\Controllers\Inspeksi\DefectSubController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Inspeksi\DefectInputController;
use App\Http\Controllers\Inspeksi\DefectInputDetailController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MonitoringController;
use Illuminate\Support\Facades\Route;


// Route::get('/', function () {
//     return view('welcome');
// });
// });

//login
Route::get('/login', [LoginController::class, 'showLogin'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.process');
Route::get('/otp', [LoginController::class, 'showOtp'])->name('show.otp');
Route::post('/otp', [LoginController::class, 'verifyOtp'])->name('verify.otp');
Route::post('/request-new-otp', [LoginController::class, 'requestNewOtp'])->name('request.new.otp');
Route::get('/dashboard', [LoginController::class, 'dashboard'])->name('dashboard');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout'); // POST untuk versi aman
Route::get('/', function () {
    return redirect()->route('login');
});


// Dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/chart-data/{departmentId?}', [DashboardController::class, 'getChartData']);
Route::get('/ratio-chart-data/{departmentId?}', [DashboardController::class, 'getRatioChartData']);
Route::get('/painting-ratio-chart-data/{departmentId?}', [DashboardController::class, 'getPaintingRatioChartData']);
Route::get('/pareto-findings-chart-data/{departmentId?}', [DashboardController::class, 'getParetoFindingsChartData']);
//Main Features
Route::get('/', [MainController::class, 'index'])->name('index');

//defect
Route::resource('defect', DefectcategoryController::class);

//defect sub
Route::get('/defect-subs', [DefectSubController::class, 'index'])->name('defect-subs.index');
Route::get('/defect-subs/create', [DefectSubController::class, 'create'])->name('defect-subs.create');
Route::post('/defect-subs', [DefectSubController::class, 'store'])->name('defect-subs.store');
Route::get('/defect-categories/{id}/subs', [DefectSubController::class, 'subsByCategory'])->name('defect-subs.byCategory');
Route::get('/defect-subs/{id}/edit', [DefectSubController::class, 'edit'])->name('defect-subs.edit');
Route::put('/defect-subs/{id}', [DefectSubController::class, 'update'])->name('defect-subs.update');
Route::delete('/defect-subs/{id}', [DefectSubController::class, 'destroy'])->name('defect-subs.destroy');

// routes/web.php
Route::prefix('defect-inputs')->name('defect-inputs.')->group(function () {
    Route::get('/summary', [DefectInputController::class, 'summary'])->name('summary');
    Route::get('/', [DefectInputController::class, 'index'])->name('index');
    Route::get('/create', [DefectInputController::class, 'create'])->name('create');
    Route::post('/', [DefectInputController::class, 'store'])->name('store');
    Route::get('/{defectInput}/edit', [DefectInputController::class, 'edit'])
        ->name('edit')
        ->middleware('role:foreman,staff');
    Route::put('/{defectInput}', [DefectInputController::class, 'update'])
        ->name('update')
        ->middleware('role:foreman,staff');
    Route::delete('/{defectInput}', [DefectInputController::class, 'destroy'])->name('destroy');
    Route::get('/{defectInput}', [DefectInputController::class, 'show'])->name('show');
    Route::post('/{defectInput}/details/{detail}/upload-pica', [DefectInputController::class, 'uploadPica'])->name('upload-pica');
    Route::delete('/{defectInput}/details/{detail}/delete-pica', [DefectInputController::class, 'deletePica'])->name('delete-pica');
});

Route::prefix('defect-input-details')->name('defect-input-details.')->group(function () {
    Route::get('/{defectInput}', [DefectInputDetailController::class, 'index'])->name('index');
    Route::get('/{defectInput}/{detail}/edit', [DefectInputDetailController::class, 'edit'])->name('edit');
    Route::put('/{defectInput}/{detail}', [DefectInputDetailController::class, 'update'])->name('update');
});

//target
Route::resource('targets', TargetController::class);

// web.php

// Monitoring
Route::get('/monitoring/{departmentId?}', [MonitoringController::class, 'index'])->name('monitoring');
Route::get('/asakai/{departmentId?}', [MonitoringController::class, 'index'])->name('asakai');
Route::get('/monitoring/chart-data/{departmentId?}', [MonitoringController::class, 'getChartData'])->name('monitoring.chart-data');
Route::get('/monitoring/ratio-chart-data/{departmentId?}', [MonitoringController::class, 'getRatioChartData'])->name('monitoring.ratio-chart-data');
Route::get('/monitoring/painting-ratio-chart-data/{departmentId?}', [MonitoringController::class, 'getPaintingRatioChartData'])->name('monitoring.painting-ratio-chart-data');
Route::get('/monitoring/pareto-findings-chart-data/{departmentId?}', [MonitoringController::class, 'getParetoFindingsChartData'])->name('monitoring.pareto-findings-chart-data');
// Tambahkan rute baru untuk Pareto Defect Findings
Route::get('/monitoring/pareto-defect-findings-data/{departmentId?}', [MonitoringController::class, 'getParetoDefectFindingsData'])->name('monitoring.pareto-defect-findings-data');
