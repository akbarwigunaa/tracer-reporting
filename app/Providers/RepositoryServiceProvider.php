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
use App\Services\ChartConfigService;
use App\Services\Interfaces\ChartConfigServiceInterface;
use App\Services\Interfaces\NarrativeServiceInterface;
use App\Services\Interfaces\StatisticsServiceInterface;
use App\Services\Interfaces\ReportGeneratorServiceInterface;
use App\Services\Interfaces\TracerAnalysisServiceInterface;
use App\Services\NarrativeService;
use App\Services\ReportGeneratorService;
use App\Services\StatisticsService;
use App\Services\TracerAnalysisService;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(TracerStudyRepositoryInterface::class, EloquentTracerStudyRepository::class);
        $this->app->bind(AnalysisResultRepositoryInterface::class, EloquentAnalysisResultRepository::class);
        $this->app->bind(ReportRepositoryInterface::class, EloquentReportRepository::class);

        $this->app->bind(ExcelReaderServiceInterface::class, ExcelReaderService::class);
        $this->app->bind(StatisticsServiceInterface::class, StatisticsService::class);
        $this->app->bind(NarrativeServiceInterface::class, NarrativeService::class);
        $this->app->bind(ChartConfigServiceInterface::class, ChartConfigService::class);
        $this->app->bind(TracerAnalysisServiceInterface::class, TracerAnalysisService::class);
        $this->app->bind(ReportGeneratorServiceInterface::class, ReportGeneratorService::class);
    }
}
