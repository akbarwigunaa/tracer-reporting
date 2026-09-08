<?php

namespace App\Services;

use App\Services\Interfaces\StatisticsServiceInterface;
use RuntimeException;

class StatisticsService implements StatisticsServiceInterface
{
    public function calculate(string $parameterKey, array $config, array $rawData): array
    {
        return match ($config['statistic_type']) {
            'frequency' => $this->calculateFrequency($config, $rawData),
            'mean_median' => $this->calculateMeanMedian($rawData),
            'mean' => $this->calculateMean($rawData),
            'sum' => $this->calculateSum($rawData),
            'display' => $this->calculateDisplay($rawData),
            'index' => $this->calculateIndex($rawData),
            default => throw new RuntimeException("Tipe statistik \"{$config['statistic_type']}\" tidak dikenali."),
        };
    }

    private function calculateFrequency(array $config, array $rawData): array
    {
        $categories = $config['categories'] ?? [];
        $hasRanges = isset($config['category_ranges']);

        $counts = [];
        foreach ($categories as $key => $label) {
            $counts[$key] = 0;
        }

        foreach ($rawData as $value) {
            if ($hasRanges) {
                $numericValue = (float) $value;
                foreach ($config['category_ranges'] as $catKey => [$min, $max]) {
                    if ($numericValue >= $min && ($max === null || $numericValue <= $max)) {
                        $counts[$catKey]++;
                        break;
                    }
                }
            } else {
                $intValue = (int) $value;
                if (isset($counts[$intValue])) {
                    $counts[$intValue]++;
                }
            }
        }

        $total = array_sum($counts);
        $frequencies = [];

        foreach ($categories as $key => $label) {
            $count = $counts[$key];
            $frequencies[] = [
                'key' => $key,
                'label' => $label,
                'count' => $count,
                'percentage' => $total > 0 ? round(($count / $total) * 100, 1) : 0,
            ];
        }

        usort($frequencies, fn($a, $b) => $b['count'] <=> $a['count']);

        $top = $frequencies[0] ?? null;
        $second = $frequencies[1] ?? null;

        return [
            'total' => $total,
            'frequencies' => $frequencies,
            'top_category' => $top['label'] ?? '-',
            'top_count' => $top['count'] ?? 0,
            'top_pct' => $top['percentage'] ?? 0,
            'second_category' => $second['label'] ?? '-',
            'second_count' => $second['count'] ?? 0,
            'second_pct' => $second['percentage'] ?? 0,
        ];
    }

    private function calculateMeanMedian(array $rawData): array
    {
        $numeric = $this->filterNumeric($rawData);

        if (empty($numeric)) {
            return ['mean' => 0, 'median' => 0, 'min' => 0, 'max' => 0, 'count' => 0];
        }

        sort($numeric);
        $count = count($numeric);
        $mid = intdiv($count, 2);
        $median = $count % 2 === 0
            ? ($numeric[$mid - 1] + $numeric[$mid]) / 2
            : $numeric[$mid];

        return [
            'mean' => round(array_sum($numeric) / $count, 0),
            'median' => round($median, 0),
            'min' => min($numeric),
            'max' => max($numeric),
            'count' => $count,
        ];
    }

    private function calculateMean(array $rawData): array
    {
        $numeric = $this->filterNumeric($rawData);

        if (empty($numeric)) {
            return ['mean' => 0, 'count' => 0];
        }

        return [
            'mean' => round(array_sum($numeric) / count($numeric), 1),
            'count' => count($numeric),
        ];
    }

    private function calculateSum(array $rawData): array
    {
        $numeric = $this->filterNumeric($rawData);
        $count = count($numeric);

        return [
            'total_sum' => array_sum($numeric),
            'mean' => $count > 0 ? round(array_sum($numeric) / $count, 1) : 0,
            'count' => $count,
        ];
    }

    private function calculateDisplay(array $rawData): array
    {
        $values = array_values(array_filter(
            array_map(fn($v) => trim((string) $v), $rawData),
            fn($v) => $v !== '',
        ));

        return [
            'values' => $values,
            'count' => count($values),
        ];
    }

    private function calculateIndex(array $rawData): array
    {
        $competencies = [];
        $topIndexA = 0;
        $topCompetency = '';
        $maxGap = 0;
        $gapCompetency = '';

        foreach ($rawData as $compKey => $comp) {
            $numericA = $this->filterNumeric($comp['values_a']);
            $numericB = $this->filterNumeric($comp['values_b']);

            $indexA = !empty($numericA) ? round(array_sum($numericA) / count($numericA), 2) : 0;
            $indexB = !empty($numericB) ? round(array_sum($numericB) / count($numericB), 2) : 0;
            $gap = round($indexB - $indexA, 2);

            $competencies[$compKey] = [
                'name' => $comp['name'],
                'index_a' => $indexA,
                'index_b' => $indexB,
                'gap' => $gap,
                'count_a' => count($numericA),
                'count_b' => count($numericB),
            ];

            if ($indexA > $topIndexA) {
                $topIndexA = $indexA;
                $topCompetency = $comp['name'];
            }

            if (abs($gap) > abs($maxGap)) {
                $maxGap = $gap;
                $gapCompetency = $comp['name'];
            }
        }

        return [
            'competencies' => $competencies,
            'top_competency' => $topCompetency,
            'top_index_a' => $topIndexA,
            'gap_competency' => $gapCompetency,
            'gap_value' => $maxGap,
        ];
    }

    private function filterNumeric(array $data): array
    {
        return array_values(array_filter(
            array_map(fn($v) => is_numeric($v) ? (float) $v : null, $data),
            fn($v) => $v !== null,
        ));
    }
}
