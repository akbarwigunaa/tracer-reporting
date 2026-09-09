@extends('layouts.app')

@section('title', 'Hasil Analisis')
@section('page-title', 'Hasil Analisis')
@section('breadcrumb')
<li class="breadcrumb-item active">Analisis</li>
@endsection

@section('content')
<div class="card">
    <div class="card-header">
        <h3 class="card-title mb-0">Daftar Tracer Study</h3>
    </div>
    <div class="card-body p-0">
        @if($tracerStudies->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-bar-chart-line display-4 text-muted"></i>
                <p class="mt-3 text-muted">Belum ada data analisis.</p>
                <a href="{{ route('upload.create') }}" class="btn btn-outline-primary">
                    <i class="bi bi-cloud-arrow-up me-1"></i> Upload Data
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
                            <th class="text-end">Response Rate</th>
                            <th class="text-center">Status</th>
                            <th class="text-center" style="width: 100px">Aksi</th>
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
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        @if($ts->isCompleted())
                                            <a href="{{ route('analysis.show', $ts) }}" class="btn btn-primary btn-sm">
                                                <i class="bi bi-eye me-1"></i> Lihat
                                            </a>
                                        @endif
                                        <form action="{{ route('tracer-studies.destroy', $ts) }}" method="POST"
                                              onsubmit="return confirm('Hapus data {{ $ts->nama_prodi }} ({{ $ts->tahun_lulusan }})? Semua analisis dan laporan terkait juga akan dihapus.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </div>
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
