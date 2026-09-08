<?php

namespace Database\Factories;

use App\Models\Report;
use App\Models\TracerStudy;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReportFactory extends Factory
{
    protected $model = Report::class;

    public function definition(): array
    {
        return [
            'tracer_study_id' => TracerStudy::factory(),
            'filename' => 'Laporan_Test_' . fake()->numerify('####') . '.docx',
            'file_path' => 'reports/test_report.docx',
            'file_size' => fake()->numberBetween(10000, 500000),
        ];
    }
}
