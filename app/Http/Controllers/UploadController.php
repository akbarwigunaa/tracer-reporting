<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UploadController extends Controller
{
    public function create(): View
    {
        return view('upload');
    }

    public function store(Request $request): RedirectResponse
    {
        // Will be implemented in Stage 16–21 (Services)
        return redirect()->route('dashboard');
    }
}
