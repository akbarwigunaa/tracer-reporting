@extends('layouts.app')

@section('title', 'Panduan Penggunaan')
@section('page-title', 'Panduan Penggunaan')
@section('breadcrumb')
<li class="breadcrumb-item active">Panduan</li>
@endsection

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-10">

        {{-- Intro --}}
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="fw-bold mb-3">Tentang Sistem</h5>
                <p class="mb-2">
                    <strong>Web Tracer Reporting System</strong> adalah aplikasi yang mengotomasi pengolahan data tracer study
                    dari file Excel format DIKTI menjadi dashboard analisis interaktif dan laporan Microsoft Word (.docx) secara otomatis.
                </p>
                <p class="mb-0 text-muted">
                    Dikembangkan untuk Universitas Muhammadiyah Sidoarjo (UMSIDA).
                </p>
            </div>
        </div>

        {{-- Langkah 1: Upload --}}
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0"><i class="bi bi-1-circle me-2"></i>Upload Data Excel</h5>
            </div>
            <div class="card-body">
                <p>Buka menu <strong><i class="bi bi-cloud-arrow-up"></i> Upload Data</strong> di sidebar, lalu:</p>
                <ol>
                    <li class="mb-2">
                        <strong>Isi Informasi Institusi:</strong>
                        <ul class="mt-1">
                            <li>Nama Institusi sudah terisi otomatis (Universitas Muhammadiyah Sidoarjo)</li>
                            <li>Masukkan <strong>Program Studi</strong> (contoh: Teknik Informatika)</li>
                            <li>Pilih <strong>Jenjang</strong> (D3, D4, S1, S2, S3)</li>
                        </ul>
                    </li>
                    <li class="mb-2">
                        <strong>Isi Data Tahun:</strong>
                        <ul class="mt-1">
                            <li><strong>Tahun Lulusan</strong> — tahun kelulusan responden (contoh: 2024)</li>
                            <li><strong>Tahun Tracer</strong> — tahun pelaksanaan survei (contoh: 2025)</li>
                            <li><strong>Total Lulusan</strong> — jumlah seluruh lulusan (untuk menghitung response rate)</li>
                        </ul>
                    </li>
                    <li class="mb-2">
                        <strong>Upload File Excel:</strong>
                        <ul class="mt-1">
                            <li>Drag & drop atau klik area upload</li>
                            <li>Format file: <code>.xlsx</code> (Excel format DIKTI)</li>
                            <li>Maksimal ukuran: 10 MB</li>
                        </ul>
                    </li>
                    <li>Klik <strong>Upload & Proses</strong> — sistem akan membaca dan menganalisis data secara otomatis.</li>
                </ol>

                <div class="alert alert-info mt-3 mb-0">
                    <i class="bi bi-info-circle me-1"></i>
                    <strong>Format Excel DIKTI:</strong> Baris 1–3 berisi judul dokumen, baris 4 berisi kode kolom (f-code),
                    dan baris 5 ke bawah berisi data responden. Sheet harus bernama <code>data tracer</code>.
                </div>
            </div>
        </div>

        {{-- Langkah 2: Analisis --}}
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0"><i class="bi bi-2-circle me-2"></i>Melihat Hasil Analisis</h5>
            </div>
            <div class="card-body">
                <p>Setelah upload berhasil, buka menu <strong><i class="bi bi-bar-chart-line"></i> Hasil Analisis</strong>:</p>
                <ol>
                    <li class="mb-2">Halaman daftar menampilkan semua tracer study yang telah diproses beserta statusnya:
                        <ul class="mt-1">
                            <li><span class="badge text-bg-success">Selesai</span> — data berhasil dianalisis</li>
                            <li><span class="badge text-bg-warning">Proses</span> — sedang diproses</li>
                            <li><span class="badge text-bg-danger">Gagal</span> — terjadi kesalahan saat pemrosesan</li>
                        </ul>
                    </li>
                    <li class="mb-2">Klik tombol <strong><i class="bi bi-eye"></i> Lihat</strong> pada data yang berstatus Selesai untuk melihat detail analisis.</li>
                    <li class="mb-2">Halaman detail menampilkan <strong>14 parameter analisis</strong> dengan:
                        <ul class="mt-1">
                            <li>Narasi deskriptif otomatis</li>
                            <li>Tabel data statistik</li>
                            <li>Grafik interaktif (pie chart, bar chart, grouped bar chart)</li>
                        </ul>
                    </li>
                </ol>

                <h6 class="fw-bold mt-4 mb-3">Parameter yang Dianalisis:</h6>
                <div class="table-responsive">
                    <table class="table table-sm table-striped mb-0">
                        <thead>
                            <tr>
                                <th style="width:40px">#</th>
                                <th>Parameter</th>
                                <th>Tipe Analisis</th>
                                <th>Grup</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr><td>1</td><td>Status Setelah Lulus</td><td><span class="badge text-bg-secondary">Frekuensi</span></td><td>Status Alumni</td></tr>
                            <tr><td>2</td><td>Lama Mendapatkan Pekerjaan</td><td><span class="badge text-bg-secondary">Frekuensi</span></td><td>Bekerja</td></tr>
                            <tr><td>3</td><td>Tingkat Tempat Kerja</td><td><span class="badge text-bg-secondary">Frekuensi</span></td><td>Bekerja</td></tr>
                            <tr><td>4</td><td>Bidang Kerja Lulusan</td><td><span class="badge text-bg-secondary">Frekuensi</span></td><td>Bekerja</td></tr>
                            <tr><td>5</td><td>Kesesuaian Bidang Studi</td><td><span class="badge text-bg-secondary">Frekuensi</span></td><td>Bekerja</td></tr>
                            <tr><td>6</td><td>Tingkat Pendidikan Pekerjaan</td><td><span class="badge text-bg-secondary">Frekuensi</span></td><td>Bekerja</td></tr>
                            <tr><td>7</td><td>Pendapatan Pertama</td><td><span class="badge text-bg-info">Mean/Median</span></td><td>Bekerja</td></tr>
                            <tr><td>8</td><td>Penghasilan Lulusan</td><td><span class="badge text-bg-secondary">Frekuensi</span></td><td>Bekerja</td></tr>
                            <tr><td>9</td><td>Mulai Mencari Pekerjaan</td><td><span class="badge text-bg-info">Rata-rata</span></td><td>Mencari Kerja</td></tr>
                            <tr><td>10</td><td>Melamar Pekerjaan</td><td><span class="badge text-bg-info">Total</span></td><td>Mencari Kerja</td></tr>
                            <tr><td>11</td><td>Diundang Wawancara</td><td><span class="badge text-bg-info">Total</span></td><td>Mencari Kerja</td></tr>
                            <tr><td>12</td><td>Tempat Pekerjaan</td><td><span class="badge text-bg-warning text-dark">Daftar</span></td><td>Bekerja</td></tr>
                            <tr><td>13</td><td>Tempat Pendidikan Lanjut</td><td><span class="badge text-bg-warning text-dark">Daftar</span></td><td>Studi Lanjut</td></tr>
                            <tr><td>14</td><td>Penilaian Kemampuan Diri</td><td><span class="badge text-bg-success">Indeks/Gap</span></td><td>Kompetensi</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Langkah 3: Laporan --}}
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0"><i class="bi bi-3-circle me-2"></i>Generate &amp; Download Laporan</h5>
            </div>
            <div class="card-body">
                <p>Buka menu <strong><i class="bi bi-file-earmark-word"></i> Laporan</strong>:</p>
                <ol>
                    <li class="mb-2">Klik tombol <strong>Generate Laporan</strong> pada tracer study yang sudah selesai dianalisis.</li>
                    <li class="mb-2">Sistem akan membuat file Word (.docx) yang berisi:
                        <ul class="mt-1">
                            <li>Halaman sampul (judul, program studi, institusi, tahun)</li>
                            <li>Tabel ringkasan (institusi, prodi, response rate, dll.)</li>
                            <li>Seksi per parameter dengan narasi dan tabel data</li>
                            <li>Footer otomatis</li>
                        </ul>
                    </li>
                    <li class="mb-2">Klik <strong><i class="bi bi-download"></i> Download</strong> untuk mengunduh file .docx.</li>
                    <li>File dapat langsung dibuka di Microsoft Word, Google Docs, atau LibreOffice Writer.</li>
                </ol>
            </div>
        </div>

        {{-- Langkah 4: Kelola Data --}}
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0"><i class="bi bi-4-circle me-2"></i>Mengelola Data</h5>
            </div>
            <div class="card-body">
                <h6 class="fw-bold mb-2">Menghapus Tracer Study</h6>
                <p>Pada halaman <strong>Hasil Analisis</strong>, klik tombol <i class="bi bi-trash text-danger"></i> pada data yang ingin dihapus.
                   Semua analisis dan laporan terkait juga akan ikut terhapus.</p>

                <h6 class="fw-bold mb-2 mt-3">Menghapus Laporan</h6>
                <p class="mb-0">Pada halaman <strong>Laporan</strong>, klik tombol hapus pada laporan tertentu.
                   Data analisis tetap tersimpan dan laporan bisa di-generate ulang kapan saja.</p>
            </div>
        </div>

        {{-- Persyaratan File --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-file-earmark-check me-2"></i>Persyaratan File Excel</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered mb-0">
                        <tbody>
                            <tr>
                                <td class="fw-bold bg-light" style="width:200px">Format File</td>
                                <td><code>.xlsx</code> (Microsoft Excel)</td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-light">Nama Sheet</td>
                                <td><code>data tracer</code></td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-light">Baris 1–3</td>
                                <td>Judul dokumen (diabaikan oleh sistem)</td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-light">Baris 4</td>
                                <td>Kode kolom / f-code (digunakan untuk mapping otomatis)</td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-light">Baris 5+</td>
                                <td>Data responden</td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-light">Ukuran Maksimal</td>
                                <td>10 MB</td>
                            </tr>
                            <tr>
                                <td class="fw-bold bg-light">Sumber Data</td>
                                <td>File Excel dari sistem DIKTI (Kementerian Pendidikan)</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- FAQ --}}
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0"><i class="bi bi-question-circle me-2"></i>FAQ</h5>
            </div>
            <div class="card-body">
                <div class="accordion" id="faq-accordion">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-1">
                                Mengapa status analisis "Gagal"?
                            </button>
                        </h2>
                        <div id="faq-1" class="accordion-collapse collapse" data-bs-parent="#faq-accordion">
                            <div class="accordion-body">
                                Pastikan file Excel sesuai format DIKTI: sheet bernama <code>data tracer</code>,
                                baris 4 berisi kode f-code, dan data dimulai dari baris 5.
                                Hapus data yang gagal lalu upload ulang dengan file yang benar.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-2">
                                Bisakah menganalisis lebih dari satu program studi?
                            </button>
                        </h2>
                        <div id="faq-2" class="accordion-collapse collapse" data-bs-parent="#faq-accordion">
                            <div class="accordion-body">
                                Ya. Upload file Excel untuk masing-masing program studi secara terpisah.
                                Setiap upload menghasilkan satu entri analisis yang independen.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-3">
                                File Word tidak bisa dibuka?
                            </button>
                        </h2>
                        <div id="faq-3" class="accordion-collapse collapse" data-bs-parent="#faq-accordion">
                            <div class="accordion-body">
                                Pastikan membuka file dengan Microsoft Word versi 2010 ke atas, Google Docs,
                                atau LibreOffice Writer. Jika masih bermasalah, coba generate ulang laporan.
                            </div>
                        </div>
                    </div>
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq-4">
                                Bisakah laporan di-generate ulang?
                            </button>
                        </h2>
                        <div id="faq-4" class="accordion-collapse collapse" data-bs-parent="#faq-accordion">
                            <div class="accordion-body">
                                Ya. Hapus laporan lama, lalu klik Generate Laporan kembali di halaman Laporan.
                                Data analisis tidak akan terpengaruh.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
