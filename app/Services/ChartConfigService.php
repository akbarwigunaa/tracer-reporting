<?php

namespace App\Services;

use App\Services\Interfaces\ChartConfigServiceInterface;

class ChartConfigService implements ChartConfigServiceInterface
{
    private const COLORS = [
        '#4e79a7', '#f28e2b', '#e15759', '#76b7b2',
        '#59a14f', '#edc948', '#b07aa1', '#ff9da7',
        '#9c755f', '#bab0ac',
    ];

    public function generate(string $parameterKey, array $config, array $stats): ?array
    {
        $chartType = $config['chart_type'] ?? null;

        if ($chartType === null) {
            return null;
        }

        return match ($chartType) {
            'pie' => $this->buildPie($config, $stats),
            'bar' => $this->buildBar($config, $stats),
            'grouped_bar' => $this->buildGroupedBar($config, $stats),
            default => null,
        };
    }

    private function buildPie(array $config, array $stats): array
    {
        $frequencies = $stats['frequencies'] ?? [];
        $labels = array_column($frequencies, 'label');
        $data = array_column($frequencies, 'count');
        $colors = array_slice(self::COLORS, 0, count($labels));

        return [
            'type' => 'pie',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                ]],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => $config['name'],
                        'font' => ['size' => 14, 'weight' => 'bold'],
                    ],
                    'legend' => [
                        'position' => 'bottom',
                        'labels' => ['padding' => 15],
                    ],
                    'tooltip' => [
                        'callbacks' => [
                            'label' => '__CALLBACK_PIE_TOOLTIP__',
                        ],
                    ],
                ],
            ],
        ];
    }

    private function buildBar(array $config, array $stats): array
    {
        $frequencies = $stats['frequencies'] ?? [];
        $labels = array_column($frequencies, 'label');
        $data = array_column($frequencies, 'count');
        $colors = array_slice(self::COLORS, 0, count($labels));

        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'Jumlah Responden',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderWidth' => 1,
                    'borderColor' => array_map(fn($c) => $c . 'cc', $colors),
                    'borderRadius' => 4,
                ]],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => $config['name'],
                        'font' => ['size' => 14, 'weight' => 'bold'],
                    ],
                    'legend' => ['display' => false],
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'ticks' => ['stepSize' => 1, 'precision' => 0],
                        'title' => ['display' => true, 'text' => 'Jumlah'],
                    ],
                    'x' => [
                        'title' => ['display' => true, 'text' => $config['name']],
                    ],
                ],
            ],
        ];
    }

    private function buildGroupedBar(array $config, array $stats): array
    {
        $competencies = $stats['competencies'] ?? [];
        $labels = array_column($competencies, 'name');
        $dataA = array_column($competencies, 'index_a');
        $dataB = array_column($competencies, 'index_b');

        return [
            'type' => 'bar',
            'data' => [
                'labels' => $labels,
                'datasets' => [
                    [
                        'label' => 'Dikuasai Saat Lulus',
                        'data' => array_values($dataA),
                        'backgroundColor' => '#4e79a7',
                        'borderWidth' => 1,
                        'borderColor' => '#4e79a7cc',
                        'borderRadius' => 4,
                    ],
                    [
                        'label' => 'Dibutuhkan dalam Pekerjaan',
                        'data' => array_values($dataB),
                        'backgroundColor' => '#f28e2b',
                        'borderWidth' => 1,
                        'borderColor' => '#f28e2bcc',
                        'borderRadius' => 4,
                    ],
                ],
            ],
            'options' => [
                'responsive' => true,
                'maintainAspectRatio' => false,
                'plugins' => [
                    'title' => [
                        'display' => true,
                        'text' => $config['name'],
                        'font' => ['size' => 14, 'weight' => 'bold'],
                    ],
                    'legend' => [
                        'position' => 'bottom',
                        'labels' => ['padding' => 15],
                    ],
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'max' => 5,
                        'ticks' => ['stepSize' => 1],
                        'title' => ['display' => true, 'text' => 'Indeks (1-5)'],
                    ],
                    'x' => [
                        'title' => ['display' => true, 'text' => 'Kompetensi'],
                    ],
                ],
            ],
        ];
    }
}
