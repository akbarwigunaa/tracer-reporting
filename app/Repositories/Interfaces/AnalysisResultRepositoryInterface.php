<?php

namespace App\Repositories\Interfaces;

use App\Models\AnalysisResult;
use Illuminate\Database\Eloquent\Collection;

interface AnalysisResultRepositoryInterface
{
    public function findByTracerStudy(int $tracerStudyId): Collection;

    public function findByTracerStudyAndKey(int $tracerStudyId, string $parameterKey): ?AnalysisResult;

    public function create(array $data): AnalysisResult;

    public function deleteByTracerStudy(int $tracerStudyId): int;
}
