<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadTracerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nama_institusi' => ['required', 'string', 'max:255'],
            'nama_prodi' => ['required', 'string', 'max:255'],
            'jenjang' => ['required', 'string', 'in:D3,D4,S1,S2,S3'],
            'tahun_lulusan' => ['required', 'string', 'digits:4'],
            'tahun_tracer' => ['required', 'string', 'digits:4'],
            'total_lulusan' => ['required', 'integer', 'min:1'],
            'file' => ['required', 'file', 'mimes:xlsx', 'max:10240'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama_institusi.required' => 'Nama institusi wajib diisi.',
            'nama_prodi.required' => 'Nama program studi wajib diisi.',
            'jenjang.required' => 'Jenjang pendidikan wajib dipilih.',
            'jenjang.in' => 'Jenjang harus salah satu dari: D3, D4, S1, S2, S3.',
            'tahun_lulusan.required' => 'Tahun lulusan wajib diisi.',
            'tahun_lulusan.digits' => 'Tahun lulusan harus 4 digit.',
            'tahun_tracer.required' => 'Tahun tracer wajib diisi.',
            'tahun_tracer.digits' => 'Tahun tracer harus 4 digit.',
            'total_lulusan.required' => 'Total lulusan wajib diisi.',
            'total_lulusan.min' => 'Total lulusan minimal 1.',
            'file.required' => 'File Excel wajib diupload.',
            'file.mimes' => 'File harus berformat .xlsx.',
            'file.max' => 'Ukuran file maksimal 10 MB.',
        ];
    }
}
