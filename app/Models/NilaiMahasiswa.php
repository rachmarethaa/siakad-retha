<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NilaiMahasiswa extends Model
{
    use HasFactory;

    protected $table = 'nilai_mahasiswa';

    protected $fillable = [
        'jadwal_mengajar_id',
        'mahasiswa_id',
        'nilai_akhir',
        'nilai_tugas',
        'nilai_uts',
        'nilai_uas',
        'grade',
        'keterangan',
        'input_at',
    ];

    protected $casts = [
        'nilai_akhir' => 'decimal:2',
        'nilai_tugas' => 'decimal:2',
        'nilai_uts' => 'decimal:2',
        'nilai_uas' => 'decimal:2',
        'input_at' => 'datetime',
    ];

    /**
     * Relasi ke JadwalMengajar
     */
    public function jadwal()
    {
        return $this->belongsTo(JadwalMengajar::class, 'jadwal_mengajar_id');
    }

    /**
     * Relasi ke JadwalMengajar (alias untuk convenience)
     */
    public function jadwalMengajar()
    {
        return $this->belongsTo(JadwalMengajar::class, 'jadwal_mengajar_id');
    }

    /**
     * Relasi ke Mahasiswa
     */
    public function mahasiswa()
    {
        return $this->belongsTo(Mahasiswa::class, 'mahasiswa_id');
    }

    /**
     * Hitung nilai akhir berdasarkan komponen
     * Formula: 30% tugas + 30% UTS + 40% UAS
     */
    public static function hitungNilaiAkhir($nilai_tugas, $nilai_uts, $nilai_uas)
    {
        // Jika salah satu komponen bernilai null, tidak dapat menghitung
        if (is_null($nilai_tugas) || is_null($nilai_uts) || is_null($nilai_uas)) {
            return null;
        }

        return (0.30 * $nilai_tugas) + (0.30 * $nilai_uts) + (0.40 * $nilai_uas);
    }

    /**
     * Konversi nilai ke grade
     */
    public static function nilaiToGrade($nilai)
    {
        if (is_null($nilai)) return null;

        if ($nilai >= 85) return 'A';
        if ($nilai >= 75) return 'B';
        if ($nilai >= 65) return 'C';
        if ($nilai >= 55) return 'D';
        return 'E';
    }

    /**
     * Get grade untuk model ini
     */
    public function getGradeAttribute($value)
    {
        // Jika grade belum diset dan nilai_akhir tersedia, hitung dari nilai akhir
        if (is_null($value) && !is_null($this->nilai_akhir)) {
            return self::nilaiToGrade($this->nilai_akhir);
        }

        return $value;
    }

    /**
     * Accessor untuk status kelulusan
     */
    public function getLulusAttribute()
    {
        return $this->nilai_akhir !== null && $this->nilai_akhir >= 55;
    }
}
