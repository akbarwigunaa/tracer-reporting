@extends('layouts.app')

@section('title', 'Analisis — ' . $tracerStudy->nama_prodi)
@section('page-title', 'Detail Analisis')
@section('breadcrumb')
<li class="breadcrumb-item"><a href="{{ route('analysis.index') }}">Analisis</a></li>
<li class="breadcrumb-item active">{{ $tracerStudy->nama_prodi }}</li>
@endsection

@section('content')
{{-- Header Info --}}
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h5 class="fw-bold mb-1">{{ $tracerStudy->nama_prodi }}</h5>
                <p class="text-muted mb-2">{{ $tracerStudy->nama_institusi }}</p>
                <div class="d-flex gap-3 flex-wrap">
                    <span class="badge text-bg-secondary">{{ $tracerStudy->jenjang }}</span>
                    <span class="text-muted"><i class="bi bi-mortarboard me-1"></i>Lulusan {{ $tracerStudy->tahun_lulusan }}</span>
                    <span class="text-muted"><i class="bi bi-calendar-check me-1"></i>Tracer {{ $tracerStudy->tahun_tracer }}</span>
                </div>
            </div>
            <div class="col-md-6">
                <div class="row text-center mt-3 mt-md-0">
                    <div class="col-4">
                        <div class="fs-4 fw-bold text-primary">{{ number_format($tracerStudy->total_responden) }}</div>
                        <small class="text-muted">Responden</small>
                    </div>
                    <div class="col-4">
                        <div class="fs-4 fw-bold text-primary">{{ number_format($tracerStudy->total_lulusan) }}</div>
                        <small class="text-muted">Total Lulusan</small>
                    </div>
                    <div class="col-4">
                        <div class="fs-4 fw-bold text-success">{{ number_format($tracerStudy->response_rate, 2) }}%</div>
                        <small class="text-muted">Response Rate</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Action Bar --}}
<div class="d-flex justify-content-end gap-2 mb-4">
    @if($latestReport)
        <a href="{{ route('reports.download', $latestReport) }}" class="btn btn-success btn-sm">
            <i class="bi bi-download me-1"></i> Download Laporan (.docx)
        </a>
    @endif
    <form action="{{ route('reports.generate', $tracerStudy) }}" method="POST" class="d-inline">
        @csrf
        <button type="submit" class="btn btn-primary btn-sm">
            <i class="bi bi-file-earmark-word me-1"></i> {{ $latestReport ? 'Generate Ulang' : 'Generate Laporan' }}
        </button>
    </form>
</div>

{{-- Quick Navigation --}}
<div class="card mb-4">
    <div class="card-body py-2">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="fw-semibold text-muted small me-1">Navigasi:</span>
            @foreach($results as $result)
                <a href="#param-{{ $result->parameter_key }}" class="badge text-bg-light text-decoration-none border">
                    {{ $result->order }}. {{ Str::limit($result->parameter_name, 25) }}
                </a>
            @endforeach
        </div>
    </div>
</div>

{{-- Analysis Results --}}
@foreach($results as $result)
    <div class="card mb-4" id="param-{{ $result->parameter_key }}">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h3 class="card-title mb-0">
                <span class="badge text-bg-primary me-2">{{ $result->order }}</span>
                {{ $result->parameter_name }}
            </h3>
            <span class="badge text-bg-light border">{{ $result->statistic_type }}</span>
        </div>
        <div class="card-body">
            {{-- Narrative --}}
            <div class="mb-4 p-3 bg-body-secondary rounded">
                <i class="bi bi-chat-quote me-1 text-muted"></i>
                {{ $result->narrative }}
            </div>

            <div class="row">
                {{-- Statistics Table --}}
                <div class="{{ $result->chart_config ? 'col-lg-5' : 'col-12' }}">
                    @switch($result->statistic_type)
                        @case('frequency')
                            <div class="table-responsive">
                                <table class="table table-sm table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Kategori</th>
                                            <th class="text-end">Jumlah</th>
                                            <th class="text-end">%</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($result->result_data['frequencies'] as $freq)
                                            <tr @if($freq['label'] === $result->result_data['top_category']) class="table-primary" @endif>
                                                <td>{{ $freq['label'] }}</td>
                                                <td class="text-end">{{ $freq['count'] }}</td>
                                                <td class="text-end">{{ number_format($freq['percentage'], 1) }}%</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="fw-semibold">
                                            <td>Total</td>
                                            <td class="text-end">{{ $result->result_data['total'] }}</td>
                                            <td class="text-end">100%</td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            @break

                        @case('mean_median')
                            <table class="table table-sm mb-0">
                                <tbody>
                                    <tr><th class="w-50">Rata-rata (Mean)</th><td class="text-end">Rp {{ number_format($result->result_data['mean'], 0, ',', '.') }}</td></tr>
                                    <tr><th>Median</th><td class="text-end">Rp {{ number_format($result->result_data['median'], 0, ',', '.') }}</td></tr>
                                    <tr><th>Minimum</th><td class="text-end">Rp {{ number_format($result->result_data['min'], 0, ',', '.') }}</td></tr>
                                    <tr><th>Maksimum</th><td class="text-end">Rp {{ number_format($result->result_data['max'], 0, ',', '.') }}</td></tr>
                                    <tr><th>Jumlah Data</th><td class="text-end">{{ $result->result_data['count'] }}</td></tr>
                                </tbody>
                            </table>
                            @break

                        @case('mean')
                            <table class="table table-sm mb-0">
                                <tbody>
                                    <tr><th class="w-50">Rata-rata</th><td class="text-end">{{ $result->result_data['mean'] }} bulan</td></tr>
                                    <tr><th>Jumlah Data</th><td class="text-end">{{ $result->result_data['count'] }}</td></tr>
                                </tbody>
                            </table>
                            @break

                        @case('sum')
                            <table class="table table-sm mb-0">
                                <tbody>
                                    <tr><th class="w-50">Total</th><td class="text-end">{{ number_format($result->result_data['total_sum']) }}</td></tr>
                                    <tr><th>Rata-rata</th><td class="text-end">{{ number_format($result->result_data['mean'], 1) }}</td></tr>
                                    <tr><th>Jumlah Data</th><td class="text-end">{{ $result->result_data['count'] }}</td></tr>
                                </tbody>
                            </table>
                            @break

                        @case('display')
                            @if(!empty($result->result_data['values']))
                                <ul class="list-group list-group-flush">
                                    @foreach($result->result_data['values'] as $val)
                                        <li class="list-group-item px-0">{{ $val }}</li>
                                    @endforeach
                                </ul>
                                <p class="text-muted small mt-2 mb-0">Total: {{ $result->result_data['count'] }} data</p>
                            @else
                                <p class="text-muted">Tidak ada data.</p>
                            @endif
                            @break

                        @case('index')
                            <div class="table-responsive">
                                <table class="table table-sm table-striped mb-0">
                                    <thead>
                                        <tr>
                                            <th>Kompetensi</th>
                                            <th class="text-end">Dikuasai</th>
                                            <th class="text-end">Dibutuhkan</th>
                                            <th class="text-end">Gap</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($result->result_data['competencies'] as $comp)
                                            <tr>
                                                <td>{{ $comp['name'] }}</td>
                                                <td class="text-end">{{ number_format($comp['index_a'], 2) }}</td>
                                                <td class="text-end">{{ number_format($comp['index_b'], 2) }}</td>
                                                <td class="text-end {{ $comp['gap'] < 0 ? 'text-danger' : 'text-success' }}">
                                                    {{ number_format($comp['gap'], 2) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            @break
                    @endswitch
                </div>

                {{-- Chart --}}
                @if($result->chart_config)
                    <div class="col-lg-7 mt-3 mt-lg-0">
                        <div style="position: relative; height: 320px;">
                            <canvas id="chart-{{ $result->parameter_key }}"></canvas>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endforeach

{{-- Actions --}}
<div class="d-flex justify-content-between mb-4">
    <a href="{{ route('analysis.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar
    </a>
    <div class="d-flex gap-2">
        @if($latestReport)
            <a href="{{ route('reports.download', $latestReport) }}" class="btn btn-success">
                <i class="bi bi-download me-1"></i> Download Laporan
            </a>
        @else
            <form action="{{ route('reports.generate', $tracerStudy) }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-file-earmark-word me-1"></i> Generate Laporan
                </button>
            </form>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.7/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chartConfigs = @json(
        $results->filter(fn($r) => $r->chart_config !== null)
            ->mapWithKeys(fn($r) => [$r->parameter_key => $r->chart_config])
    );

    Object.keys(chartConfigs).forEach(function(key) {
        const canvas = document.getElementById('chart-' + key);
        if (!canvas) return;

        const config = chartConfigs[key];
        new Chart(canvas, {
            type: config.type,
            data: config.data,
            options: config.options
        });
    });
});
</script>
@endpush
