<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadTracerRequest;
use App\Repositories\Interfaces\TracerStudyRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UploadController extends Controller
{
    public function __construct(
        private TracerStudyRepositoryInterface $tracerStudyRepository,
    ) {}

    public function create(): View
    {
        return view('upload');
    }

    public function store(UploadTracerRequest $request): RedirectResponse
    {
        $existing = $this->tracerStudyRepository->findByProdiAndTahun(
            $request->nama_prodi,
            $request->tahun_lulusan,
            $request->tahun_tracer,
        );

        if ($existing) {
            return back()->withInput()->with('error',
                'Data tracer untuk prodi, tahun lulusan, dan tahun tracer ini sudah ada.'
            );
        }

        // TracerAnalysisService will be injected here in Stage 21
        // For now, just validate and redirect
        return redirect()->route('dashboard')
            ->with('success', 'Upload berhasil. Analisis akan diproses.');
    }
}
