<?php

namespace App\Http\Controllers;

use App\Models\TracerStudy;
use App\Repositories\Interfaces\AnalysisResultRepositoryInterface;
use App\Repositories\Interfaces\TracerStudyRepositoryInterface;
use Illuminate\View\View;

class AnalysisController extends Controller
{
    public function __construct(
        private TracerStudyRepositoryInterface $tracerStudyRepository,
        private AnalysisResultRepositoryInterface $analysisResultRepository,
    ) {}

    public function index(): View
    {
        $tracerStudies = $this->tracerStudyRepository->all();

        return view('analysis.index', compact('tracerStudies'));
    }

    public function show(TracerStudy $tracerStudy): View
    {
        $results = $this->analysisResultRepository->findByTracerStudy($tracerStudy->id);

        return view('analysis.show', compact('tracerStudy', 'results'));
    }
}
