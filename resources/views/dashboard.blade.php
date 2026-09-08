@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')

@section('content')
{{-- Summary Cards --}}
<div class="row mb-4">
    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-primary">
            <div class="inner">
                <h3>{{ $tracerStudies->count() }}</h3>
                <p>Total Tracer Study</p>
            </div>
            <div class="small-box-icon">
                <i class="bi bi-database"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-success">
            <div class="inner">
                <h3>{{ $tracerStudies->where('status', 'completed')->count() }}</h3>
                <p>Analisis Selesai</p>
            </div>
            <div class="small-box-icon">
                <i class="bi bi-check-circle"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-info">
            <div class="inner">
                <h3>{{ number_format($tracerStudies->sum('total_responden')) }}</h3>
                <p>Total Responden</p>
            </div>
            <div class="small-box-icon">
                <i class="bi bi-people"></i>
            </div>
        </div>
    </div>

    <div class="col-lg-3 col-6">
        <div class="small-box text-bg-warning">
            <div class="inner">
                <h3>{{ $totalReports }}</h3>
                <p>Total Laporan</p>
            </div>
            <div class="small-box-icon">
                <i class="bi bi-file-earmark-word"></i>
            </div>
        </div>
    </div>
</div>

{{-- Tracer Study Table --}}
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h3 class="card-title mb-0">Data Tracer Study</h3>
        <a href="{{ route('upload.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-lg me-1"></i> Upload Baru
        </a>
    </div>
    <div class="card-body p-0">
        @if($tracerStudies->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-inbox display-4 text-muted"></i>
                <p class="mt-3 text-muted">Belum ada data tracer study.</p>
                <a href="{{ route('upload.create') }}" class="btn btn-outline-primary">
                    <i class="bi bi-cloud-arrow-up me-1"></i> Upload Data Pertama
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40px">#</th>
                            <th>Institusi / Prodi</th>
                            <th>Jenjang</th>
                            <th class="text-center">Tahun Lulusan</th>
                            <th class="text-center">Tahun Tracer</th>
                            <th class="text-end">Responden</th>
                            <th class="text-end">Response Rate</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 160px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tracerStudies as $i => $ts)
                            <tr>
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $ts->nama_prodi }}</div>
                                    <small class="text-muted">{{ $ts->nama_institusi }}</small>
                                </td>
                                <td>{{ $ts->jenjang }}</td>
                                <td class="text-center">{{ $ts->tahun_lulusan }}</td>
                                <td class="text-center">{{ $ts->tahun_tracer }}</td>
                                <td class="text-end">
                                    @if($ts->total_responden)
                                        {{ number_format($ts->total_responden) }} / {{ number_format($ts->total_lulusan) }}
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end">
                                    @if($ts->response_rate)
                                        {{ number_format($ts->response_rate, 2) }}%
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    @if($ts->isCompleted())
                                        <span class="badge text-bg-success">Selesai</span>
                                    @elseif($ts->isProcessing())
                                        <span class="badge text-bg-warning">Proses</span>
                                    @elseif($ts->isFailed())
                                        <span class="badge text-bg-danger">Gagal</span>
                                    @else
                                        <span class="badge text-bg-secondary">{{ $ts->status }}</span>
                                    @endif
                                </td>
                                <td class="text-center text-nowrap">
                                    @if($ts->isCompleted())
                                        <a href="{{ route('analysis.show', $ts) }}" class="btn btn-outline-primary btn-sm" title="Lihat Analisis">
                                            <i class="bi bi-bar-chart-line"></i>
                                        </a>
                                        <form action="{{ route('reports.generate', $ts) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn btn-outline-success btn-sm" title="Generate Laporan">
                                                <i class="bi bi-file-earmark-word"></i>
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('tracer-studies.destroy', $ts) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus data tracer study {{ $ts->nama_prodi }} ({{ $ts->tahun_lulusan }})?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm" title="Hapus">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
