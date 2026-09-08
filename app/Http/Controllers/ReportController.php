<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\TracerStudy;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use App\Services\Interfaces\ReportGeneratorServiceInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use RuntimeException;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function __construct(
        private ReportRepositoryInterface $reportRepository,
        private ReportGeneratorServiceInterface $reportGenerator,
    ) {}

    public function index(): View
    {
        $reports = Report::with('tracerStudy')->latest()->get();

        return view('reports.index', compact('reports'));
    }

    public function generate(TracerStudy $tracerStudy): RedirectResponse
    {
        if (!$tracerStudy->isCompleted()) {
            return redirect()->route('reports.index')
                ->with('error', 'Analisis belum selesai, tidak dapat membuat laporan.');
        }

        try {
            $report = $this->reportGenerator->generate($tracerStudy);

            return redirect()->route('reports.index')
                ->with('success', 'Laporan berhasil dibuat: ' . $report->filename);
        } catch (RuntimeException $e) {
            return redirect()->route('reports.index')
                ->with('error', 'Gagal membuat laporan: ' . $e->getMessage());
        }
    }

    public function download(Report $report): BinaryFileResponse
    {
        $absolutePath = Storage::disk('local')->path($report->file_path);

        if (!file_exists($absolutePath)) {
            abort(404, 'File laporan tidak ditemukan.');
        }

        return response()->download($absolutePath, $report->filename);
    }

    public function destroy(Report $report): RedirectResponse
    {
        $absolutePath = Storage::disk('local')->path($report->file_path);

        if (file_exists($absolutePath)) {
            unlink($absolutePath);
        }

        $this->reportRepository->delete($report);

        return redirect()->route('reports.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }
}
