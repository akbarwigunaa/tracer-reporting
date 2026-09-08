<?php

namespace Tests\Feature;

use App\Models\TracerStudy;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_successfully(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Dashboard');
        $response->assertSee('Total Tracer Study');
    }

    public function test_dashboard_shows_empty_state(): void
    {
        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Belum ada data tracer study');
    }

    public function test_dashboard_shows_tracer_study_data(): void
    {
        $ts = TracerStudy::factory()->create([
            'nama_prodi' => 'Teknik Informatika',
            'status' => 'completed',
        ]);

        $response = $this->get(route('dashboard'));

        $response->assertOk();
        $response->assertSee('Teknik Informatika');
        $response->assertSee('Selesai');
    }

    public function test_root_redirects_to_dashboard(): void
    {
        $response = $this->get('/');

        $response->assertRedirect(route('dashboard'));
    }

    public function test_destroy_deletes_tracer_study(): void
    {
        $ts = TracerStudy::factory()->create();

        $response = $this->delete(route('tracer-studies.destroy', $ts));

        $response->assertRedirect(route('dashboard'));
        $this->assertDatabaseMissing('tracer_studies', ['id' => $ts->id]);
    }
}
