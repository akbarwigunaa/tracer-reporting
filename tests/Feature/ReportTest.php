<?php

namespace Tests\Feature;

use App\Models\Report;
use App\Models\TracerStudy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use RefreshDatabase;

    public function test_reports_index_displays_successfully(): void
    {
        $response = $this->get(route('reports.index'));

        $response->assertOk();
        $response->assertSee('Daftar Laporan');
    }

    public function test_generate_report_for_completed_study(): void
    {
        $ts = $this->createCompletedTracerStudy();

        $response = $this->post(route('reports.generate', $ts));

        $response->assertRedirect(route('reports.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('reports', ['tracer_study_id' => $ts->id]);
    }

    public function test_generate_report_rejects_non_completed(): void
    {
        $ts = TracerStudy::factory()->create(['status' => 'processing']);

        $response = $this->post(route('reports.generate', $ts));

        $response->assertRedirect(route('reports.index'));
        $response->assertSessionHas('error');
    }

    public function test_download_report(): void
    {
        $ts = $this->createCompletedTracerStudy();
        $this->post(route('reports.generate', $ts));

        $report = Report::where('tracer_study_id', $ts->id)->first();
        $response = $this->get(route('reports.download', $report));

        $response->assertOk();
        $response->assertHeader('content-disposition');
    }

    public function test_delete_report(): void
    {
        $ts = $this->createCompletedTracerStudy();
        $this->post(route('reports.generate', $ts));

        $report = Report::where('tracer_study_id', $ts->id)->first();
        $response = $this->delete(route('reports.destroy', $report));

        $response->assertRedirect(route('reports.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('reports', ['id' => $report->id]);
    }

    private function createCompletedTracerStudy(): TracerStudy
    {
        $file = new UploadedFile(
            base_path('tests/Fixtures/tracer-sample.xlsx'),
            'tracer.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true,
        );

        $this->post(route('upload.store'), [
            'nama_institusi' => 'Universitas Test',
            'nama_prodi' => 'Prodi Test',
            'jenjang' => 'S1',
            'tahun_lulusan' => '2023',
            'tahun_tracer' => '2024',
            'total_lulusan' => 200,
            'file' => $file,
        ]);

        return TracerStudy::where('nama_prodi', 'Prodi Test')->first();
    }
}
