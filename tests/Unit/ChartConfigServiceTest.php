<?php

namespace Tests\Unit;

use App\Services\ChartConfigService;
use PHPUnit\Framework\TestCase;

class ChartConfigServiceTest extends TestCase
{
    private ChartConfigService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ChartConfigService();
    }

    public function test_returns_null_when_no_chart_type(): void
    {
        $result = $this->service->generate('test', ['name' => 'Test'], []);

        $this->assertNull($result);
    }

    public function test_builds_pie_chart(): void
    {
        $config = ['chart_type' => 'pie', 'name' => 'Status'];
        $stats = [
            'frequencies' => [
                ['label' => 'Bekerja', 'count' => 6],
                ['label' => 'Lanjut Studi', 'count' => 4],
            ],
        ];

        $result = $this->service->generate('test', $config, $stats);

        $this->assertEquals('pie', $result['type']);
        $this->assertCount(2, $result['data']['labels']);
        $this->assertCount(2, $result['data']['datasets'][0]['data']);
    }

    public function test_builds_bar_chart(): void
    {
        $config = ['chart_type' => 'bar', 'name' => 'Masa Tunggu'];
        $stats = [
            'frequencies' => [
                ['label' => '< 6 bulan', 'count' => 5],
                ['label' => '6-12 bulan', 'count' => 3],
            ],
        ];

        $result = $this->service->generate('test', $config, $stats);

        $this->assertEquals('bar', $result['type']);
        $this->assertEquals('Jumlah Responden', $result['data']['datasets'][0]['label']);
    }

    public function test_builds_grouped_bar_chart(): void
    {
        $config = ['chart_type' => 'grouped_bar', 'name' => 'Kompetensi'];
        $stats = [
            'competencies' => [
                ['name' => 'Etika', 'index_a' => 4.2, 'index_b' => 4.8],
            ],
        ];

        $result = $this->service->generate('test', $config, $stats);

        $this->assertEquals('bar', $result['type']);
        $this->assertCount(2, $result['data']['datasets']);
        $this->assertEquals('Dikuasai Saat Lulus', $result['data']['datasets'][0]['label']);
    }

    public function test_returns_null_for_unknown_chart_type(): void
    {
        $result = $this->service->generate('test', ['chart_type' => 'radar', 'name' => 'X'], []);

        $this->assertNull($result);
    }
}
