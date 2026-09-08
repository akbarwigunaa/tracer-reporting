<?php

namespace App\Http\Controllers;

use App\Models\TracerStudy;
use App\Repositories\Interfaces\TracerStudyRepositoryInterface;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private TracerStudyRepositoryInterface $tracerStudyRepository,
    ) {}

    public function index(): View
    {
        $tracerStudies = $this->tracerStudyRepository->all();

        return view('dashboard', compact('tracerStudies'));
    }

    public function destroy(TracerStudy $tracerStudy): RedirectResponse
    {
        $this->tracerStudyRepository->delete($tracerStudy);

        return redirect()->route('dashboard')
            ->with('success', 'Data tracer study berhasil dihapus.');
    }
}
