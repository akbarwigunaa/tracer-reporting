<?php

namespace App\Repositories\Eloquent;

use App\Models\Report;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentReportRepository implements ReportRepositoryInterface
{
    public function findByTracerStudy(int $tracerStudyId): Collection
    {
        return Report::where('tracer_study_id', $tracerStudyId)
            ->latest()
            ->get();
    }

    public function find(int $id): ?Report
    {
        return Report::find($id);
    }

    public function create(array $data): Report
    {
        return Report::create($data);
    }

    public function delete(Report $report): bool
    {
        return $report->delete();
    }
}
