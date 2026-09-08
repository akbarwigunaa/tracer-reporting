<?php

namespace App\Services\Interfaces;

interface StatisticsServiceInterface
{
    public function calculate(string $parameterKey, array $config, array $rawData): array;
}
