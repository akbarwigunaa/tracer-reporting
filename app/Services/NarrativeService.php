<?php

namespace App\Services;

use App\Services\Interfaces\NarrativeServiceInterface;

class NarrativeService implements NarrativeServiceInterface
{
    public function generate(string $parameterKey, array $stats, array $meta = []): string
    {
        $template = config("narratives.{$parameterKey}");

        if ($template === null) {
            return '';
        }

        $replacements = array_merge(
            $this->buildMetaReplacements($meta),
            $this->buildStatsReplacements($stats),
            $this->buildSecondText($stats),
        );

        // Sort by key length descending so :total_sum replaces before :total
        uksort($replacements, fn($a, $b) => strlen($b) <=> strlen($a));

        return str_replace(
            array_map(fn($k) => ":{$k}", array_keys($replacements)),
            array_values($replacements),
            $template,
        );
    }

    private function buildMetaReplacements(array $meta): array
    {
        return [
            'prodi' => $meta['prodi'] ?? '-',
            'jenjang' => $meta['jenjang'] ?? '-',
            'tahun_lulusan' => $meta['tahun_lulusan'] ?? '-',
        ];
    }

    private function buildStatsReplacements(array $stats): array
    {
        $r = [];

        $r['total'] = $stats['total'] ?? ($stats['count'] ?? 0);
        $r['top_category'] = $stats['top_category'] ?? '-';
        $r['top_count'] = $stats['top_count'] ?? 0;
        $r['top_pct'] = $stats['top_pct'] ?? 0;
        $r['second_category'] = $stats['second_category'] ?? '-';
        $r['second_count'] = $stats['second_count'] ?? 0;
        $r['second_pct'] = $stats['second_pct'] ?? 0;

        if (isset($stats['mean'])) {
            $r['mean'] = $this->shouldFormatCurrency($stats['mean'])
                ? number_format($stats['mean'], 0, ',', '.')
                : $stats['mean'];
        }
        if (isset($stats['median'])) {
            $r['median'] = number_format($stats['median'], 0, ',', '.');
        }
        if (isset($stats['min'])) {
            $r['min'] = number_format($stats['min'], 0, ',', '.');
        }
        if (isset($stats['max'])) {
            $r['max'] = number_format($stats['max'], 0, ',', '.');
        }
        if (isset($stats['total_sum'])) {
            $r['total_sum'] = number_format($stats['total_sum'], 0, ',', '.');
        }

        $r['top_competency'] = $stats['top_competency'] ?? '-';
        $r['top_index_a'] = $stats['top_index_a'] ?? 0;
        $r['gap_competency'] = $stats['gap_competency'] ?? '-';
        $r['gap_value'] = $stats['gap_value'] ?? 0;

        return array_map(fn($v) => (string) $v, $r);
    }

    private function buildSecondText(array $stats): array
    {
        $secondCount = $stats['second_count'] ?? 0;

        if ($secondCount <= 0) {
            return ['second_text' => ''];
        }

        $template = config('narratives.second_text_template', '');

        $text = str_replace(
            [':second_category', ':second_count', ':second_pct'],
            [
                $stats['second_category'] ?? '-',
                (string) $secondCount,
                (string) ($stats['second_pct'] ?? 0),
            ],
            $template,
        );

        return ['second_text' => $text];
    }

    private function shouldFormatCurrency(float $value): bool
    {
        return $value > 10000;
    }
}
