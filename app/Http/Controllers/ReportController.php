<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\TracerStudy;
use App\Repositories\Interfaces\ReportRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ReportController extends Controller
{
    public function __construct(
        private ReportRepositoryInterface $reportRepository,
    ) {}

    public function index(): View
    {
        $reports = Report::with('tracerStudy')->latest()->get();

        return view('reports.index', compact('reports'));
    }

    public function generate(TracerStudy $tracerStudy): RedirectResponse
    {
        // Will be implemented in Stage 24 (ReportGeneratorService)
        return redirect()->route('reports.index');
    }

    public function download(Report $report): BinaryFileResponse
    {
        return response()->download(
            storage_path('app/' . $report->file_path),
            $report->filename
        );
    }

    public function destroy(Report $report): RedirectResponse
    {
        $filePath = storage_path('app/' . $report->file_path);

        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $this->reportRepository->delete($report);

        return redirect()->route('reports.index')
            ->with('success', 'Laporan berhasil dihapus.');
    }
}
