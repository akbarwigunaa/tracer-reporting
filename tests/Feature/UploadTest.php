<?php

namespace Tests\Feature;

use App\Models\TracerStudy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class UploadTest extends TestCase
{
    use RefreshDatabase;

    public function test_upload_form_displays_successfully(): void
    {
        $response = $this->get(route('upload.create'));

        $response->assertOk();
        $response->assertSee('Upload Data Tracer');
        $response->assertSee('Nama Institusi');
    }

    public function test_upload_requires_all_fields(): void
    {
        $response = $this->post(route('upload.store'), []);

        $response->assertSessionHasErrors([
            'nama_institusi',
            'nama_prodi',
            'jenjang',
            'tahun_lulusan',
            'tahun_tracer',
            'total_lulusan',
            'file',
        ]);
    }

    public function test_upload_validates_jenjang(): void
    {
        $response = $this->post(route('upload.store'), [
            'nama_institusi' => 'Test',
            'nama_prodi' => 'Test',
            'jenjang' => 'X1',
            'tahun_lulusan' => '2023',
            'tahun_tracer' => '2024',
            'total_lulusan' => 100,
            'file' => UploadedFile::fake()->create('test.xlsx', 100),
        ]);

        $response->assertSessionHasErrors('jenjang');
    }

    public function test_upload_rejects_duplicate(): void
    {
        TracerStudy::factory()->create([
            'nama_prodi' => 'Informatika',
            'tahun_lulusan' => '2023',
            'tahun_tracer' => '2024',
        ]);

        $file = new UploadedFile(
            base_path('tests/Fixtures/tracer-sample.xlsx'),
            'tracer.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true,
        );

        $response = $this->post(route('upload.store'), [
            'nama_institusi' => 'UB',
            'nama_prodi' => 'Informatika',
            'jenjang' => 'S1',
            'tahun_lulusan' => '2023',
            'tahun_tracer' => '2024',
            'total_lulusan' => 200,
            'file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }

    public function test_upload_processes_excel_successfully(): void
    {
        $file = new UploadedFile(
            base_path('tests/Fixtures/tracer-sample.xlsx'),
            'tracer.xlsx',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            null,
            true,
        );

        $response = $this->post(route('upload.store'), [
            'nama_institusi' => 'Universitas Brawijaya',
            'nama_prodi' => 'Teknik Informatika',
            'jenjang' => 'S1',
            'tahun_lulusan' => '2023',
            'tahun_tracer' => '2024',
            'total_lulusan' => 200,
            'file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('tracer_studies', [
            'nama_prodi' => 'Teknik Informatika',
            'status' => 'completed',
        ]);

        $ts = TracerStudy::where('nama_prodi', 'Teknik Informatika')->first();
        $this->assertEquals(13, $ts->analysisResults()->count());
    }
}
