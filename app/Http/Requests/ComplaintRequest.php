<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ComplaintRequest extends FormRequest
{
   public function authorize()
    {
        return auth()->check();
    }

    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'nik' => 'required|string|size:16',
            'phone' => 'required|string|max:15',
            'address' => 'required|string|max:500',
            'job' => 'nullable|string|max:100',
            'birth_date' => 'nullable|date|before:today',
            'documents' => 'required|array',
            'documents.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:5120', // 5MB
        ];
    }

    public function messages()
    {
        return [
            'name.required' => 'Nama lengkap wajib diisi.',
            'nik.required' => 'NIK wajib diisi.',
            'nik.size' => 'NIK harus 16 digit.',
            'phone.required' => 'Nomor HP wajib diisi.',
            'address.required' => 'Alamat wajib diisi.',
            'documents.required' => 'Dokumen pendukung wajib diupload.',
            'documents.*.required' => 'Semua dokumen wajib diupload.',
            'documents.*.file' => 'File harus berupa dokumen.',
            'documents.*.mimes' => 'File harus berformat PDF, JPG, JPEG, atau PNG.',
            'documents.*.max' => 'Ukuran file maksimal 5MB.',
        ];
    }
}
