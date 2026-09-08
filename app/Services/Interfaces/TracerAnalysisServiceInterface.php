<?php

namespace App\Services\Interfaces;

use App\Models\TracerStudy;
use Illuminate\Http\UploadedFile;

interface TracerAnalysisServiceInterface
{
    public function process(array $meta, UploadedFile $file): TracerStudy;
}
