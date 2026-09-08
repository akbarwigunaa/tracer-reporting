<?php

namespace Tests\Unit;

use App\Services\NarrativeService;
use Tests\TestCase;

class NarrativeServiceTest extends TestCase
{
    private NarrativeService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new NarrativeService();
    }

    public function test_generates_frequency_narrative(): void
    {
        $stats = [
            'total' => 10,
            'top_category' => 'Bekerja',
            'top_count' => 6,
            'top_pct' => 60,
            'second_category' => 'Lanjut Studi',
            'second_count' => 3,
            'second_pct' => 30,
        ];

        $meta = [
            'prodi' => 'Informatika',
            'jenjang' => 'S1',
            'tahun_lulusan' => '2023',
        ];

        $result = $this->service->generate('status_setelah_lulus', $stats, $meta);

        $this->assertNotEmpty($result);
        $this->assertStringContainsString('Bekerja', $result);
        $this->assertStringContainsString('60', $result);
    }

    public function test_returns_empty_for_unknown_key(): void
    {
        $result = $this->service->generate('nonexistent_key', [], []);

        $this->assertEquals('', $result);
    }

    public function test_long_placeholders_replaced_before_short(): void
    {
        $stats = [
            'total' => 10,
            'total_sum' => 5000000,
            'mean' => 500000,
            'count' => 10,
            'top_category' => '-',
            'top_count' => 0,
            'top_pct' => 0,
            'second_count' => 0,
        ];

        $result = $this->service->generate('pendapatan', $stats, ['prodi' => 'Test']);

        $this->assertStringNotContainsString(':total_sum', $result);
        $this->assertStringNotContainsString(':total', $result);
    }
}
