<?php

namespace App\Providers;

use App\Repositories\Eloquent\EloquentAnalysisResultRepository;
use App\Repositories\Eloquent\EloquentReportRepository;
use App\Repositories\Eloquent\EloquentTracerStudyRepository;
use App\Repositories\Interfaces\AnalysisResultRepositoryInterface;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use App\Repositories\Interfaces\TracerStudyRepositoryInterface;
use App\Services\ExcelReaderService;
use App\Services\Interfaces\ExcelReaderServiceInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TracerStudyRepositoryInterface::class, EloquentTracerStudyRepository::class);
        $this->app->bind(AnalysisResultRepositoryInterface::class, EloquentAnalysisResultRepository::class);
        $this->app->bind(ReportRepositoryInterface::class, EloquentReportRepository::class);

        $this->app->bind(ExcelReaderServiceInterface::class, ExcelReaderService::class);
    }
}
