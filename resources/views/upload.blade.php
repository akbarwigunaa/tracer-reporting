@extends('layouts.app')

@section('title', 'Upload Data')
@section('page-title', 'Upload Data Tracer')
@section('breadcrumb')
<li class="breadcrumb-item active">Upload</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data" id="upload-form">
            @csrf

            {{-- Info Institusi --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">Informasi Institusi</h3>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-12">
                            <label for="nama_institusi" class="form-label">Nama Institusi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_institusi') is-invalid @enderror"
                                   id="nama_institusi" name="nama_institusi"
                                   value="{{ old('nama_institusi') }}"
                                   placeholder="Contoh: Universitas Brawijaya">
                            @error('nama_institusi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="nama_prodi" class="form-label">Program Studi <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('nama_prodi') is-invalid @enderror"
                                   id="nama_prodi" name="nama_prodi"
                                   value="{{ old('nama_prodi') }}"
                                   placeholder="Contoh: Teknik Informatika">
                            @error('nama_prodi')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="jenjang" class="form-label">Jenjang <span class="text-danger">*</span></label>
                            <select class="form-select @error('jenjang') is-invalid @enderror" id="jenjang" name="jenjang">
                                <option value="">Pilih...</option>
                                @foreach(['D3', 'D4', 'S1', 'S2', 'S3'] as $j)
                                    <option value="{{ $j }}" {{ old('jenjang') === $j ? 'selected' : '' }}>{{ $j }}</option>
                                @endforeach
                            </select>
                            @error('jenjang')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label for="tahun_lulusan" class="form-label">Tahun Lulusan <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('tahun_lulusan') is-invalid @enderror"
                                   id="tahun_lulusan" name="tahun_lulusan"
                                   value="{{ old('tahun_lulusan') }}"
                                   placeholder="2023" maxlength="4">
                            @error('tahun_lulusan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="tahun_tracer" class="form-label">Tahun Tracer <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('tahun_tracer') is-invalid @enderror"
                                   id="tahun_tracer" name="tahun_tracer"
                                   value="{{ old('tahun_tracer') }}"
                                   placeholder="2024" maxlength="4">
                            @error('tahun_tracer')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4">
                            <label for="total_lulusan" class="form-label">Total Lulusan <span class="text-danger">*</span></label>
                            <input type="number" class="form-control @error('total_lulusan') is-invalid @enderror"
                                   id="total_lulusan" name="total_lulusan"
                                   value="{{ old('total_lulusan') }}"
                                   placeholder="200" min="1">
                            @error('total_lulusan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>

            {{-- File Upload --}}
            <div class="card mb-4">
                <div class="card-header">
                    <h3 class="card-title">File Excel Tracer Study</h3>
                </div>
                <div class="card-body">
                    <div class="dropzone-area @error('file') border-danger @enderror" id="dropzone-area">
                        <input type="file" name="file" id="file-input" accept=".xlsx" class="d-none">
                        <div class="dropzone-content text-center py-5" id="dropzone-content">
                            <i class="bi bi-cloud-arrow-up display-4 text-muted"></i>
                            <p class="mt-2 mb-1 fw-semibold">Drag & drop file Excel di sini</p>
                            <p class="text-muted small">atau <a href="#" id="browse-link" class="text-decoration-none">klik untuk browse</a></p>
                            <p class="text-muted small mb-0">Format: .xlsx &bull; Maksimal: 10 MB</p>
                        </div>
                        <div class="dropzone-preview d-none text-center py-4" id="dropzone-preview">
                            <i class="bi bi-file-earmark-spreadsheet display-5 text-success"></i>
                            <p class="mt-2 mb-1 fw-semibold" id="file-name"></p>
                            <p class="text-muted small mb-2" id="file-size"></p>
                            <button type="button" class="btn btn-sm btn-outline-danger" id="file-remove">
                                <i class="bi bi-x-lg me-1"></i> Hapus
                            </button>
                        </div>
                    </div>
                    @error('file')
                        <div class="text-danger small mt-2">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Submit --}}
            <div class="d-flex justify-content-between">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i> Kembali
                </a>
                <button type="submit" class="btn btn-primary" id="submit-btn">
                    <i class="bi bi-cloud-arrow-up me-1"></i> Upload & Proses
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('styles')
<style>
    .dropzone-area {
        border: 2px dashed #dee2e6;
        border-radius: 0.5rem;
        cursor: pointer;
        transition: border-color 0.2s, background-color 0.2s;
    }
    .dropzone-area:hover,
    .dropzone-area.dragover {
        border-color: #0d6efd;
        background-color: rgba(13, 110, 253, 0.04);
    }
    .dropzone-area.border-danger {
        border-color: #dc3545 !important;
    }
    .dropzone-area.has-file {
        border-style: solid;
        border-color: #198754;
        background-color: rgba(25, 135, 84, 0.04);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const area = document.getElementById('dropzone-area');
    const input = document.getElementById('file-input');
    const content = document.getElementById('dropzone-content');
    const preview = document.getElementById('dropzone-preview');
    const fileName = document.getElementById('file-name');
    const fileSize = document.getElementById('file-size');
    const removeBtn = document.getElementById('file-remove');
    const browseLink = document.getElementById('browse-link');

    function formatSize(bytes) {
        if (bytes < 1024) return bytes + ' B';
        if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
        return (bytes / 1048576).toFixed(1) + ' MB';
    }

    function showFile(file) {
        fileName.textContent = file.name;
        fileSize.textContent = formatSize(file.size);
        content.classList.add('d-none');
        preview.classList.remove('d-none');
        area.classList.add('has-file');
        area.classList.remove('border-danger');
    }

    function clearFile() {
        input.value = '';
        content.classList.remove('d-none');
        preview.classList.add('d-none');
        area.classList.remove('has-file');
    }

    area.addEventListener('click', function(e) {
        if (e.target !== removeBtn && !removeBtn.contains(e.target)) {
            input.click();
        }
    });

    browseLink.addEventListener('click', function(e) {
        e.preventDefault();
        input.click();
    });

    input.addEventListener('change', function() {
        if (this.files.length) showFile(this.files[0]);
    });

    removeBtn.addEventListener('click', function(e) {
        e.stopPropagation();
        clearFile();
    });

    area.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });

    area.addEventListener('dragleave', function() {
        this.classList.remove('dragover');
    });

    area.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        const files = e.dataTransfer.files;
        if (files.length && files[0].name.endsWith('.xlsx')) {
            input.files = files;
            showFile(files[0]);
        }
    });
});
</script>
@endpush
