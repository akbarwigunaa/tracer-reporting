<?php

namespace Database\Factories;

use App\Models\AnalysisResult;
use App\Models\TracerStudy;
use Illuminate\Database\Eloquent\Factories\Factory;

class AnalysisResultFactory extends Factory
{
    protected $model = AnalysisResult::class;

    public function definition(): array
    {
        return [
            'tracer_study_id' => TracerStudy::factory(),
            'parameter_key' => fake()->unique()->slug(2),
            'parameter_name' => fake()->words(3, true),
            'statistic_type' => fake()->randomElement(['frequency', 'mean_median', 'mean', 'sum', 'display', 'index']),
            'result_data' => [
                'total' => 10,
                'frequencies' => [],
                'top_category' => 'Test',
                'top_count' => 5,
                'top_pct' => 50,
            ],
            'chart_config' => null,
            'narrative' => fake()->sentence(),
            'order' => fake()->numberBetween(1, 13),
        ];
    }
}
