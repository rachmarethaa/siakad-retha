<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\NilaiMahasiswa;

class JadwalMengajar extends Model
{
    use HasFactory;

    protected $table = 'jadwal_mengajar';

    protected $fillable = [
        'dosen_id',
        'kode_mk',
        'mata_kuliah',
        'prodi',
        'ruang',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'semester',
    ];

    public function dosen()
    {
        return $this->belongsTo(Dosen::class);
    }

    public function nilai()
    {
        return $this->hasMany(NilaiMahasiswa::class, 'jadwal_mengajar_id');
    }

    public function presensi()
    {
        return $this->hasMany(Presensi::class, 'jadwal_mengajar_id');
    }

    public function krs()
    {
        return $this->hasMany(KRS::class, 'jadwal_mengajar_id');
    }

    /**
     * Get formatted schedule time
     */
    public function getFormattedSchedule()
    {
        return ucfirst($this->hari) . ' ' . $this->jam_mulai . ' - ' . $this->jam_selesai;
    }

    /**
     * Get total students enrolled
     */
    public function getTotalStudents()
    {
        return $this->krs()
            ->where('status', 'aktif')
            ->count();
    }

    /**
     * Get attendance percentage for a specific student
     */
    public function getStudentAttendancePercentage($mahasiswaId)
    {
        $presensi = $this->presensi()
            ->where('mahasiswa_id', $mahasiswaId)
            ->get();

        if ($presensi->count() === 0) {
            return 0;
        }

        $hadir = $presensi->where('status', 'hadir')->count();
        return round(($hadir / $presensi->count()) * 100, 2);
    }
}
