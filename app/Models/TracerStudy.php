<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TracerStudy extends Model
{
    protected $fillable = [
        'nama_institusi',
        'nama_prodi',
        'jenjang',
        'tahun_lulusan',
        'tahun_tracer',
        'total_lulusan',
        'total_responden',
        'response_rate',
        'status',
        'file_path',
    ];

    protected function casts(): array
    {
        return [
            'total_lulusan' => 'integer',
            'total_responden' => 'integer',
            'response_rate' => 'decimal:2',
        ];
    }

    public function analysisResults(): HasMany
    {
        return $this->hasMany(AnalysisResult::class)->orderBy('order');
    }

    public function reports(): HasMany
    {
        return $this->hasMany(Report::class);
    }

    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    public function isProcessing(): bool
    {
        return $this->status === 'processing';
    }
}
