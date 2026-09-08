<?php

namespace App\Services\Interfaces;

use App\Models\Report;
use App\Models\TracerStudy;

interface ReportGeneratorServiceInterface
{
    public function generate(TracerStudy $tracerStudy): Report;
}
