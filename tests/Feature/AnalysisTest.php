<?php

namespace Tests\Feature;

use App\Models\AnalysisResult;
use App\Models\TracerStudy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AnalysisTest extends TestCase
{
    use RefreshDatabase;

    public function test_analysis_index_displays_successfully(): void
    {
        $response = $this->get(route('analysis.index'));

        $response->assertOk();
        $response->assertSee('Hasil Analisis');
    }

    public function test_analysis_index_shows_completed_studies(): void
    {
        $ts = TracerStudy::factory()->create([
            'nama_prodi' => 'Informatika',
            'status' => 'completed',
        ]);

        $response = $this->get(route('analysis.index'));

        $response->assertOk();
        $response->assertSee('Informatika');
        $response->assertSee('Lihat');
    }

    public function test_analysis_show_displays_results(): void
    {
        $ts = TracerStudy::factory()->create(['status' => 'completed']);

        AnalysisResult::factory()->create([
            'tracer_study_id' => $ts->id,
            'parameter_name' => 'Status Setelah Lulus',
            'parameter_key' => 'status_setelah_lulus',
            'statistic_type' => 'frequency',
            'result_data' => [
                'total' => 10,
                'frequencies' => [
                    ['key' => 1, 'label' => 'Bekerja', 'count' => 6, 'percentage' => 60],
                ],
                'top_category' => 'Bekerja',
                'top_count' => 6,
                'top_pct' => 60,
            ],
            'narrative' => 'Mayoritas berstatus Bekerja.',
            'order' => 1,
        ]);

        $response = $this->get(route('analysis.show', $ts));

        $response->assertOk();
        $response->assertSee('Status Setelah Lulus');
        $response->assertSee('Mayoritas berstatus Bekerja');
        $response->assertSee('Bekerja');
    }

    public function test_analysis_show_renders_chart_script(): void
    {
        $ts = TracerStudy::factory()->create(['status' => 'completed']);

        AnalysisResult::factory()->create([
            'tracer_study_id' => $ts->id,
            'parameter_key' => 'status_setelah_lulus',
            'parameter_name' => 'Status Setelah Lulus',
            'statistic_type' => 'frequency',
            'result_data' => ['total' => 10, 'frequencies' => [], 'top_category' => 'A', 'top_count' => 5, 'top_pct' => 50],
            'chart_config' => ['type' => 'pie', 'data' => [], 'options' => []],
            'narrative' => 'Test.',
            'order' => 1,
        ]);

        $response = $this->get(route('analysis.show', $ts));

        $response->assertOk();
        $response->assertSee('chart.umd.min.js');
        $response->assertSee('chart-status_setelah_lulus');
    }
}
