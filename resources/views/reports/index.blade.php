@extends('layouts.app')

@section('title', 'Laporan')
@section('page-title', 'Daftar Laporan')
@section('breadcrumb')
<li class="breadcrumb-item active">Laporan</li>
@endsection

@section('content')
{{-- Generate Report --}}
<div class="card mb-4">
    <div class="card-header">
        <h3 class="card-title mb-0">Buat Laporan Baru</h3>
    </div>
    <div class="card-body">
        @php
            $completedStudies = App\Models\TracerStudy::where('status', 'completed')->latest()->get();
        @endphp

        @if($completedStudies->isEmpty())
            <p class="text-muted mb-0">Belum ada tracer study yang selesai dianalisis.</p>
        @else
            <div class="row align-items-end">
                <div class="col-md-8">
                    <label class="form-label">Pilih Tracer Study</label>
                    <select class="form-select" id="tracer-study-select">
                        @foreach($completedStudies as $ts)
                            <option value="{{ $ts->id }}">
                                {{ $ts->nama_prodi }} ({{ $ts->jenjang }}) — Lulusan {{ $ts->tahun_lulusan }} / Tracer {{ $ts->tahun_tracer }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mt-2 mt-md-0">
                    <form id="generate-form" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="bi bi-file-earmark-word me-1"></i> Generate Laporan
                        </button>
                    </form>
                </div>
            </div>
        @endif
    </div>
</div>

{{-- Reports List --}}
<div class="card">
    <div class="card-header">
        <h3 class="card-title mb-0">Laporan yang Sudah Dibuat</h3>
    </div>
    <div class="card-body p-0">
        @if($reports->isEmpty())
            <div class="text-center py-5">
                <i class="bi bi-file-earmark-word display-4 text-muted"></i>
                <p class="mt-3 text-muted">Belum ada laporan yang dibuat.</p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40px">#</th>
                            <th>Nama File</th>
                            <th>Tracer Study</th>
                            <th class="text-end">Ukuran</th>
                            <th class="text-center">Dibuat</th>
                            <th class="text-center" style="width: 150px">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $i => $report)
                            <tr>
                                <td class="text-muted">{{ $i + 1 }}</td>
                                <td>
                                    <i class="bi bi-file-earmark-word text-primary me-1"></i>
                                    {{ $report->filename }}
                                </td>
                                <td>
                                    @if($report->tracerStudy)
                                        {{ $report->tracerStudy->nama_prodi }}
                                        <small class="text-muted">({{ $report->tracerStudy->tahun_lulusan }})</small>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td class="text-end">{{ $report->formattedFileSize() }}</td>
                                <td class="text-center">{{ $report->created_at->format('d M Y H:i') }}</td>
                                <td class="text-center text-nowrap">
                                    <a href="{{ route('reports.download', $report) }}" class="btn btn-success btn-sm me-1" title="Download">
                                        <i class="bi bi-download"></i>
                                    </a>
                                    <form action="{{ route('reports.destroy', $report) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Hapus laporan {{ $report->filename }}?')">
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

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const select = document.getElementById('tracer-study-select');
    const form = document.getElementById('generate-form');

    if (select && form) {
        function updateAction() {
            form.action = '/reports/' + select.value + '/generate';
        }
        select.addEventListener('change', updateAction);
        updateAction();
    }
});
</script>
@endpush
