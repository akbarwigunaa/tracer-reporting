<?php

namespace App\Services\Interfaces;

interface ExcelReaderServiceInterface
{
    /**
     * @return array<string, array<int, mixed>> Keyed by parameter_key, each an array of raw cell values
     */
    public function read(string $filePath): array;
}
