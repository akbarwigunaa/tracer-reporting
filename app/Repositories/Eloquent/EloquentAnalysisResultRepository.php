<?php

namespace App\Repositories\Eloquent;

use App\Models\AnalysisResult;
use App\Repositories\Interfaces\AnalysisResultRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentAnalysisResultRepository implements AnalysisResultRepositoryInterface
{
    public function findByTracerStudy(int $tracerStudyId): Collection
    {
        return AnalysisResult::where('tracer_study_id', $tracerStudyId)
            ->orderBy('order')
            ->get();
    }

    public function findByTracerStudyAndKey(int $tracerStudyId, string $parameterKey): ?AnalysisResult
    {
        return AnalysisResult::where('tracer_study_id', $tracerStudyId)
            ->where('parameter_key', $parameterKey)
            ->first();
    }

    public function create(array $data): AnalysisResult
    {
        return AnalysisResult::create($data);
    }

    public function deleteByTracerStudy(int $tracerStudyId): int
    {
        return AnalysisResult::where('tracer_study_id', $tracerStudyId)->delete();
    }
}
