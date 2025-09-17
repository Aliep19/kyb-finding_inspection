<?php

use App\Http\Controllers\MainController;
use App\Http\Controllers\Inspeksi\DefectcategoryController;
use App\Http\Controllers\Inspeksi\DefectSubController;
use App\Http\Controllers\TargetController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Inspeksi\DefectInputController;
use App\Http\Controllers\Inspeksi\DefectInputDetailController;
use App\Http\Controllers\DashboardController;
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

//dashboard
Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/chart-data/{departmentId?}', [DashboardController::class, 'getChartData']);

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

// ROUTE: Defect Input
Route::prefix('defect-inputs')->name('defect-inputs.')->group(function () {
    Route::get('/', [DefectInputController::class, 'index'])->name('index');
    Route::get('/create', [DefectInputController::class, 'create'])->name('create');
    Route::post('/', [DefectInputController::class, 'store'])->name('store');
    Route::get('/{defectInput}/edit', [DefectInputController::class, 'edit'])->name('edit');
    Route::put('/{defectInput}', [DefectInputController::class, 'update'])->name('update');
    Route::delete('/{defectInput}', [DefectInputController::class, 'destroy'])->name('destroy');
    Route::get('/{defectInput}', [DefectInputController::class, 'show'])->name('show');
});

Route::prefix('defect-input-details')->name('defect-input-details.')->group(function () {
    Route::get('/{defectInput}/create', [DefectInputDetailController::class, 'create'])->name('create');
    Route::post('/{defectInput}', [DefectInputDetailController::class, 'store'])->name('store');
    Route::get('/{defectInput}/{detail}/edit', [DefectInputDetailController::class, 'edit'])->name('edit');
    Route::put('/{defectInput}/{detail}', [DefectInputDetailController::class, 'update'])->name('update');
    Route::delete('/{defectInput}/{detail}', [DefectInputDetailController::class, 'destroy'])->name('destroy');
    Route::get('/{defectInput}', [DefectInputDetailController::class, 'index'])->name('index');
});

//target
Route::resource('targets', TargetController::class);

