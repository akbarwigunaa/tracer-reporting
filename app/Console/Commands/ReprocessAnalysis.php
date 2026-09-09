<?php

namespace App\Console\Commands;

use App\Models\AnalysisResult;
use App\Models\TracerStudy;
use App\Services\Interfaces\ChartConfigServiceInterface;
use App\Services\Interfaces\ExcelReaderServiceInterface;
use App\Services\Interfaces\NarrativeServiceInterface;
use App\Services\Interfaces\StatisticsServiceInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class ReprocessAnalysis extends Command
{
    protected $signature = 'tracer:reprocess';
    protected $description = 'Reprocess completed tracer studies to add missing parameters';

    public function handle(
        ExcelReaderServiceInterface $excelReader,
        StatisticsServiceInterface $statistics,
        NarrativeServiceInterface $narrative,
        ChartConfigServiceInterface $chartConfig,
    ): int {
        $studies = TracerStudy::where('status', 'completed')->get();

        if ($studies->isEmpty()) {
            $this->info('No completed tracer studies found.');
            return 0;
        }

        foreach ($studies as $ts) {
            $this->info("Processing: {$ts->nama_prodi} ({$ts->tahun_lulusan})");

            $absPath = Storage::disk('local')->path($ts->file_path);
            if (!file_exists($absPath)) {
                $this->warn("  Excel file not found, skipping.");
                continue;
            }

            $rawData = $excelReader->read($absPath);
            $parameters = config('tracer.parameters');
            $narrativeMeta = [
                'prodi' => $ts->nama_prodi,
                'jenjang' => $ts->jenjang,
                'tahun_lulusan' => $ts->tahun_lulusan,
            ];

            $added = 0;
            $updated = 0;

            foreach ($parameters as $key => $paramConfig) {
                $existing = AnalysisResult::where('tracer_study_id', $ts->id)
                    ->where('parameter_key', $key)
                    ->first();

                $paramData = $rawData[$key] ?? [];
                $statsResult = $statistics->calculate($key, $paramConfig, $paramData);
                $narrativeText = $narrative->generate($key, $statsResult, $narrativeMeta);
                $chartCfg = $chartConfig->generate($key, $paramConfig, $statsResult);

                if ($existing) {
                    $existing->update([
                        'order' => $paramConfig['order'],
                        'result_data' => $statsResult,
                        'chart_config' => $chartCfg,
                        'narrative' => $narrativeText,
                    ]);
                    $updated++;
                } else {
                    AnalysisResult::create([
                        'tracer_study_id' => $ts->id,
                        'parameter_key' => $key,
                        'parameter_name' => $paramConfig['name'],
                        'statistic_type' => $paramConfig['statistic_type'],
                        'result_data' => $statsResult,
                        'chart_config' => $chartCfg,
                        'narrative' => $narrativeText,
                        'order' => $paramConfig['order'],
                    ]);
                    $added++;
                }
            }

            $this->info("  Done: {$added} added, {$updated} updated.");
        }

        return 0;
    }
}
