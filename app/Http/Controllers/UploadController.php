<?php

namespace App\Http\Controllers;

use App\Http\Requests\UploadTracerRequest;
use App\Repositories\Interfaces\TracerStudyRepositoryInterface;
use App\Services\Interfaces\TracerAnalysisServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use RuntimeException;

class UploadController extends Controller
{
    public function __construct(
        private TracerStudyRepositoryInterface $tracerStudyRepository,
        private TracerAnalysisServiceInterface $tracerAnalysisService,
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

        try {
            $tracerStudy = $this->tracerAnalysisService->process(
                $request->safe()->except('file'),
                $request->file('file'),
            );

            return redirect()->route('analysis.show', $tracerStudy)
                ->with('success', 'Data tracer berhasil diupload dan dianalisis.');
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error',
                'Gagal memproses file: ' . $e->getMessage()
            );
        }
    }
}
