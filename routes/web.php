<?php

use App\Http\Controllers\AnalysisController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\UploadController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn () => redirect()->route('dashboard'));

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/upload', [UploadController::class, 'create'])->name('upload.create');
Route::post('/upload', [UploadController::class, 'store'])->name('upload.store');

Route::get('/analysis', [AnalysisController::class, 'index'])->name('analysis.index');
Route::get('/analysis/{tracerStudy}', [AnalysisController::class, 'show'])->name('analysis.show');

Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
Route::post('/reports/{tracerStudy}/generate', [ReportController::class, 'generate'])->name('reports.generate');
Route::get('/reports/{report}/download', [ReportController::class, 'download'])->name('reports.download');
Route::delete('/reports/{report}', [ReportController::class, 'destroy'])->name('reports.destroy');

Route::delete('/tracer-studies/{tracerStudy}', [DashboardController::class, 'destroy'])->name('tracer-studies.destroy');
