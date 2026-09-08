<?php

namespace App\Repositories\Eloquent;

use App\Models\TracerStudy;
use App\Repositories\Interfaces\TracerStudyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class EloquentTracerStudyRepository implements TracerStudyRepositoryInterface
{
    public function all(): Collection
    {
        return TracerStudy::latest()->get();
    }

    public function find(int $id): ?TracerStudy
    {
        return TracerStudy::find($id);
    }

    public function findOrFail(int $id): TracerStudy
    {
        return TracerStudy::findOrFail($id);
    }

    public function create(array $data): TracerStudy
    {
        return TracerStudy::create($data);
    }

    public function update(TracerStudy $tracerStudy, array $data): TracerStudy
    {
        $tracerStudy->update($data);

        return $tracerStudy;
    }

    public function delete(TracerStudy $tracerStudy): bool
    {
        return $tracerStudy->delete();
    }

    public function findByProdiAndTahun(string $prodi, string $tahunLulusan, string $tahunTracer): ?TracerStudy
    {
        return TracerStudy::where('nama_prodi', $prodi)
            ->where('tahun_lulusan', $tahunLulusan)
            ->where('tahun_tracer', $tahunTracer)
            ->first();
    }
}
