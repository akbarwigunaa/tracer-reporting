<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AnalysisResult extends Model
{
    protected $fillable = [
        'tracer_study_id',
        'parameter_key',
        'parameter_name',
        'statistic_type',
        'result_data',
        'chart_config',
        'narrative',
        'order',
    ];

    protected function casts(): array
    {
        return [
            'result_data' => 'array',
            'chart_config' => 'array',
            'order' => 'integer',
        ];
    }

    public function tracerStudy(): BelongsTo
    {
        return $this->belongsTo(TracerStudy::class);
    }
}
