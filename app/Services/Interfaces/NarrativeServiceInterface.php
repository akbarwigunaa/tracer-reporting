<?php

namespace App\Services\Interfaces;

interface NarrativeServiceInterface
{
    public function generate(string $parameterKey, array $statisticsResult, array $meta = []): string;
}
