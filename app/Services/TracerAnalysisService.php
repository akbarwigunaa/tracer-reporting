<?php

namespace App\Services;

use App\Models\TracerStudy;
use App\Repositories\Interfaces\AnalysisResultRepositoryInterface;
use App\Repositories\Interfaces\TracerStudyRepositoryInterface;
use App\Services\Interfaces\ChartConfigServiceInterface;
use App\Services\Interfaces\ExcelReaderServiceInterface;
use App\Services\Interfaces\NarrativeServiceInterface;
use App\Services\Interfaces\StatisticsServiceInterface;
use App\Services\Interfaces\TracerAnalysisServiceInterface;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class TracerAnalysisService implements TracerAnalysisServiceInterface
{
    public function __construct(
        private TracerStudyRepositoryInterface $tracerStudyRepository,
        private AnalysisResultRepositoryInterface $analysisResultRepository,
        private ExcelReaderServiceInterface $excelReader,
        private StatisticsServiceInterface $statistics,
        private NarrativeServiceInterface $narrative,
        private ChartConfigServiceInterface $chartConfig,
    ) {}

    public function process(array $meta, UploadedFile $file): TracerStudy
    {
        $filePath = $file->store('tracer-files', 'local');

        $tracerStudy = $this->tracerStudyRepository->create([
            'nama_institusi' => $meta['nama_institusi'],
            'nama_prodi' => $meta['nama_prodi'],
            'jenjang' => $meta['jenjang'],
            'tahun_lulusan' => $meta['tahun_lulusan'],
            'tahun_tracer' => $meta['tahun_tracer'],
            'total_lulusan' => $meta['total_lulusan'],
            'file_path' => $filePath,
            'status' => 'processing',
        ]);

        try {
            $rawData = $this->excelReader->read(Storage::disk('local')->path($filePath));

            $totalResponden = $this->countResponden($rawData);
            $responseRate = $meta['total_lulusan'] > 0
                ? round(($totalResponden / $meta['total_lulusan']) * 100, 2)
                : 0;

            $this->tracerStudyRepository->update($tracerStudy, [
                'total_responden' => $totalResponden,
                'response_rate' => $responseRate,
            ]);

            $narrativeMeta = [
                'prodi' => $meta['nama_prodi'],
                'jenjang' => $meta['jenjang'],
                'tahun_lulusan' => $meta['tahun_lulusan'],
            ];

            $parameters = config('tracer.parameters');

            foreach ($parameters as $key => $paramConfig) {
                $paramData = $rawData[$key] ?? [];

                $statsResult = $this->statistics->calculate($key, $paramConfig, $paramData);
                $narrativeText = $this->narrative->generate($key, $statsResult, $narrativeMeta);
                $chartCfg = $this->chartConfig->generate($key, $paramConfig, $statsResult);

                $this->analysisResultRepository->create([
                    'tracer_study_id' => $tracerStudy->id,
                    'parameter_key' => $key,
                    'parameter_name' => $paramConfig['name'],
                    'statistic_type' => $paramConfig['statistic_type'],
                    'result_data' => $statsResult,
                    'chart_config' => $chartCfg,
                    'narrative' => $narrativeText,
                    'order' => $paramConfig['order'],
                ]);
            }

            $this->tracerStudyRepository->update($tracerStudy, ['status' => 'completed']);

        } catch (RuntimeException $e) {
            $this->tracerStudyRepository->update($tracerStudy, ['status' => 'failed']);
            throw $e;
        }

        return $tracerStudy->refresh();
    }

    private function countResponden(array $rawData): int
    {
        $firstParam = reset($rawData);

        if (is_array($firstParam) && !isset($firstParam['name'])) {
            return count($firstParam);
        }

        return 0;
    }
}
