<?php

namespace Database\Factories;

use App\Models\TracerStudy;
use Illuminate\Database\Eloquent\Factories\Factory;

class TracerStudyFactory extends Factory
{
    protected $model = TracerStudy::class;

    public function definition(): array
    {
        return [
            'nama_institusi' => fake()->company(),
            'nama_prodi' => fake()->unique()->words(2, true),
            'jenjang' => fake()->randomElement(['S1', 'S2', 'S3', 'D3', 'D4']),
            'tahun_lulusan' => (string) fake()->numberBetween(2020, 2025),
            'tahun_tracer' => (string) fake()->numberBetween(2021, 2026),
            'total_lulusan' => fake()->numberBetween(50, 500),
            'total_responden' => fake()->numberBetween(10, 200),
            'response_rate' => fake()->randomFloat(2, 10, 100),
            'status' => 'completed',
            'file_path' => null,
        ];
    }

    public function processing(): static
    {
        return $this->state(fn() => ['status' => 'processing']);
    }

    public function failed(): static
    {
        return $this->state(fn() => ['status' => 'failed']);
    }
}
