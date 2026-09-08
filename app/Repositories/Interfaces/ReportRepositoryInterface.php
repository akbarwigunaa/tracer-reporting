<?php

namespace App\Repositories\Interfaces;

use App\Models\Report;
use Illuminate\Database\Eloquent\Collection;

interface ReportRepositoryInterface
{
    public function findByTracerStudy(int $tracerStudyId): Collection;

    public function find(int $id): ?Report;

    public function create(array $data): Report;

    public function delete(Report $report): bool;
}
