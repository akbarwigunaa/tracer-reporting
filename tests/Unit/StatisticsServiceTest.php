<?php

namespace Tests\Unit;

use App\Services\StatisticsService;
use PHPUnit\Framework\TestCase;
use RuntimeException;

class StatisticsServiceTest extends TestCase
{
    private StatisticsService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new StatisticsService();
    }

    public function test_frequency_counts_correctly(): void
    {
        $config = [
            'statistic_type' => 'frequency',
            'categories' => [1 => 'Bekerja', 2 => 'Lanjut Studi', 3 => 'Wirausaha'],
        ];
        $rawData = [1, 1, 1, 2, 2, 3];

        $result = $this->service->calculate('test', $config, $rawData);

        $this->assertEquals(6, $result['total']);
        $this->assertEquals('Bekerja', $result['top_category']);
        $this->assertEquals(3, $result['top_count']);
        $this->assertEquals(50.0, $result['top_pct']);
    }

    public function test_mean_median_calculates_correctly(): void
    {
        $config = ['statistic_type' => 'mean_median'];
        $rawData = [10, 20, 30, 40, 50];

        $result = $this->service->calculate('test', $config, $rawData);

        $this->assertEquals(30, $result['mean']);
        $this->assertEquals(30, $result['median']);
        $this->assertEquals(10, $result['min']);
        $this->assertEquals(50, $result['max']);
        $this->assertEquals(5, $result['count']);
    }

    public function test_mean_median_even_count(): void
    {
        $config = ['statistic_type' => 'mean_median'];
        $rawData = [10, 20, 30, 40];

        $result = $this->service->calculate('test', $config, $rawData);

        $this->assertEquals(25, $result['median']);
    }

    public function test_mean_calculates_correctly(): void
    {
        $config = ['statistic_type' => 'mean'];
        $rawData = [3.5, 4.0, 4.5];

        $result = $this->service->calculate('test', $config, $rawData);

        $this->assertEquals(4.0, $result['mean']);
        $this->assertEquals(3, $result['count']);
    }

    public function test_sum_calculates_correctly(): void
    {
        $config = ['statistic_type' => 'sum'];
        $rawData = [100, 200, 300];

        $result = $this->service->calculate('test', $config, $rawData);

        $this->assertEquals(600, $result['total_sum']);
        $this->assertEquals(200.0, $result['mean']);
        $this->assertEquals(3, $result['count']);
    }

    public function test_display_collects_values(): void
    {
        $config = ['statistic_type' => 'display'];
        $rawData = ['Dosen', '', 'Programmer', 'Dosen'];

        $result = $this->service->calculate('test', $config, $rawData);

        $this->assertEquals(3, $result['count']);
        $this->assertContains('Dosen', $result['values']);
        $this->assertContains('Programmer', $result['values']);
    }

    public function test_index_calculates_gap(): void
    {
        $config = ['statistic_type' => 'index'];
        $rawData = [
            'comp_a' => [
                'name' => 'Etika',
                'values_a' => [4, 4, 5],
                'values_b' => [5, 5, 5],
            ],
            'comp_b' => [
                'name' => 'Keahlian TI',
                'values_a' => [3, 3, 3],
                'values_b' => [5, 5, 5],
            ],
        ];

        $result = $this->service->calculate('test', $config, $rawData);

        $this->assertEquals('Etika', $result['top_competency']);
        $this->assertCount(2, $result['competencies']);
        $this->assertGreaterThan(0, $result['competencies']['comp_b']['gap']);
    }

    public function test_unknown_type_throws(): void
    {
        $this->expectException(RuntimeException::class);

        $this->service->calculate('test', ['statistic_type' => 'unknown'], []);
    }

    public function test_empty_data_returns_defaults(): void
    {
        $config = ['statistic_type' => 'mean_median'];
        $result = $this->service->calculate('test', $config, []);

        $this->assertEquals(0, $result['mean']);
        $this->assertEquals(0, $result['median']);
        $this->assertEquals(0, $result['count']);
    }
}
