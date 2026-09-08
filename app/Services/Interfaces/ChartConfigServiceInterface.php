<?php

namespace App\Services\Interfaces;

interface ChartConfigServiceInterface
{
    public function generate(string $parameterKey, array $config, array $statisticsResult): ?array;
}
