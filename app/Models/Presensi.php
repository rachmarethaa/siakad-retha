<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Presensi extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'presensi';

    protected $fillable = [
        'krs_id',
        'mahasiswa_id',
        'jadwal_mengajar_id',
        'dosen_id',
        'tanggal',
        'status', // hadir, absen, izin, sakit
        'jam_masuk',
        'jam_keluar',
        'keterangan',
        'sudah_dikonfirmasi',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'sudah_dikonfirmasi' => 'boolean',
    ];

    /**
     * Relasi ke KRS
     */
    public function krs()
    {
        return $this->belongsTo(KRS::class, 'krs_id');
    }

    /**
     * Relasi ke Mahasiswa
     */
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    /**
     * Relasi ke JadwalMengajar
     */
    public function jadwalMengajar()
    {
        return $this->belongsTo(JadwalMengajar::class, 'jadwal_mengajar_id');
    }

    /**
     * Relasi ke Dosen
     */
    public function dosen()
    {
        return $this->belongsTo(Dosen::class, 'dosen_id');
    }

    /**
     * Get status badge color
     */
    public function getStatusBadgeColor()
    {
        return match($this->status) {
            'hadir' => '#28a745',
            'absen' => '#dc3545',
            'izin' => '#ffc107',
            'sakit' => '#17a2b8',
            default => '#6c757d'
        };
    }

    /**
     * Get status label (Indonesian)
     */
    public function getStatusLabel()
    {
        return match($this->status) {
            'hadir' => 'Hadir',
            'absen' => 'Absen',
            'izin' => 'Izin',
            'sakit' => 'Sakit',
            default => '-'
        };
    }

    /**
     * Scope untuk filter presensi by status
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope untuk filter presensi by date range
     */
    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->whereBetween('tanggal', [$startDate, $endDate]);
    }

    /**
     * Scope untuk filter presensi by mahasiswa
     */
    public function scopeByMahasiswa($query, $mahasiswaId)
    {
        return $query->where('mahasiswa_id', $mahasiswaId);
    }

    /**
     * Scope untuk filter presensi by jadwal
     */
    public function scopeByJadwal($query, $jadwalId)
    {
        return $query->where('jadwal_mengajar_id', $jadwalId);
    }

    /**
     * Scope untuk filter presensi by dosen
     */
    public function scopeByDosen($query, $dosenId)
    {
        return $query->where('dosen_id', $dosenId);
    }

    /**
     * Check if presensi is terlambat based on jam masuk
     */
    public function isTerlambat()
    {
        // Terlambat jika masuk lebih dari 15 menit setelah jadwal mulai
        if (!$this->jam_masuk || !$this->jadwalMengajar) {
            return false;
        }

        $jamMulai = \Carbon\Carbon::parse($this->jadwalMengajar->jam_mulai);
        $jamMasuk = \Carbon\Carbon::parse($this->jam_masuk);

        return $jamMasuk->diffInMinutes($jamMulai) > 15;
    }

    /**
     * Get durasi kehadiran dalam menit
     */
    public function getDurasiKehadiran()
    {
        if (!$this->jam_masuk || !$this->jam_keluar) {
            return null;
        }

        $jamMasuk = \Carbon\Carbon::parse($this->jam_masuk);
        $jamKeluar = \Carbon\Carbon::parse($this->jam_keluar);

        return $jamKeluar->diffInMinutes($jamMasuk);
    }
}
