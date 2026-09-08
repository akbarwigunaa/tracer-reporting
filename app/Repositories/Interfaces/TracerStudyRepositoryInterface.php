<?php

namespace App\Repositories\Interfaces;

use App\Models\TracerStudy;
use Illuminate\Database\Eloquent\Collection;

interface TracerStudyRepositoryInterface
{
    public function all(): Collection;

    public function find(int $id): ?TracerStudy;

    public function findOrFail(int $id): TracerStudy;

    public function create(array $data): TracerStudy;

    public function update(TracerStudy $tracerStudy, array $data): TracerStudy;

    public function delete(TracerStudy $tracerStudy): bool;

    public function findByProdiAndTahun(string $prodi, string $tahunLulusan, string $tahunTracer): ?TracerStudy;
}
