<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePresensiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'tanggal' => 'required|date',
            'presensi' => 'required|array',
            'presensi.*.mahasiswa_id' => 'required|integer|exists:mahasiswa,id',
            'presensi.*.status' => 'required|in:hadir,absen,izin,sakit',
            'presensi.*.jam_masuk' => 'nullable|date_format:H:i',
            'presensi.*.jam_keluar' => 'nullable|date_format:H:i',
            'presensi.*.keterangan' => 'nullable|string|max:255',
        ];
    }

    /**
     * Get custom error messages for validation rules.
     */
    public function messages(): array
    {
        return [
            'tanggal.required' => 'Tanggal harus diisi',
            'tanggal.date' => 'Format tanggal tidak valid',
            'presensi.required' => 'Data presensi harus diisi',
            'presensi.*.mahasiswa_id.required' => 'ID mahasiswa harus ada',
            'presensi.*.mahasiswa_id.exists' => 'Mahasiswa tidak ditemukan',
            'presensi.*.status.required' => 'Status kehadiran harus dipilih',
            'presensi.*.status.in' => 'Status kehadiran tidak valid',
            'presensi.*.jam_masuk.date_format' => 'Format jam masuk tidak valid (HH:mm)',
            'presensi.*.jam_keluar.date_format' => 'Format jam keluar tidak valid (HH:mm)',
            'presensi.*.keterangan.max' => 'Keterangan maksimal 255 karakter',
        ];
    }
}
