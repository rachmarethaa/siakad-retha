<?php

namespace App\Services;

use App\Models\Presensi;
use App\Models\KRS;
use App\Models\JadwalMengajar;
use Carbon\Carbon;

class PresensiService
{
    /**
     * Calculate attendance statistics for a specific class
     */
    public function getClassStatistics(int $jadwalId): array
    {
        $presensi = Presensi::where('jadwal_mengajar_id', $jadwalId)->get();

        $stats = [
            'total_records' => $presensi->count(),
            'hadir' => $presensi->where('status', 'hadir')->count(),
            'absen' => $presensi->where('status', 'absen')->count(),
            'izin' => $presensi->where('status', 'izin')->count(),
            'sakit' => $presensi->where('status', 'sakit')->count(),
            'total_dates' => Presensi::where('jadwal_mengajar_id', $jadwalId)
                ->distinct('tanggal')
                ->count('tanggal'),
        ];

        if ($stats['total_records'] > 0) {
            $stats['persentase_hadir'] = round(($stats['hadir'] / $stats['total_records']) * 100, 2);
            $stats['persentase_absen'] = round(($stats['absen'] / $stats['total_records']) * 100, 2);
            $stats['persentase_izin'] = round(($stats['izin'] / $stats['total_records']) * 100, 2);
            $stats['persentase_sakit'] = round(($stats['sakit'] / $stats['total_records']) * 100, 2);
        } else {
            $stats['persentase_hadir'] = 0;
            $stats['persentase_absen'] = 0;
            $stats['persentase_izin'] = 0;
            $stats['persentase_sakit'] = 0;
        }

        return $stats;
    }

    /**
     * Calculate attendance statistics for a student in a specific class
     */
    public function getStudentClassStatistics(int $mahasiswaId, int $jadwalId): array
    {
        $presensi = Presensi::where('mahasiswa_id', $mahasiswaId)
            ->where('jadwal_mengajar_id', $jadwalId)
            ->get();

        $total = $presensi->count();
        $hadir = $presensi->where('status', 'hadir')->count();
        $absen = $presensi->where('status', 'absen')->count();
        $izin = $presensi->where('status', 'izin')->count();
        $sakit = $presensi->where('status', 'sakit')->count();
        $terlambat = $presensi->filter(fn($p) => $p->isTerlambat())->count();

        return [
            'total' => $total,
            'hadir' => $hadir,
            'absen' => $absen,
            'izin' => $izin,
            'sakit' => $sakit,
            'terlambat' => $terlambat,
            'persentase' => $total > 0 ? round(($hadir / $total) * 100, 2) : 0,
            'status' => $this->getAttendanceStatus($total > 0 ? round(($hadir / $total) * 100, 2) : 0),
        ];
    }

    /**
     * Calculate overall attendance statistics for a student
     */
    public function getStudentOverallStatistics(int $mahasiswaId): array
    {
        $presensi = Presensi::where('mahasiswa_id', $mahasiswaId)->get();

        $total = $presensi->count();
        $hadir = $presensi->where('status', 'hadir')->count();

        return [
            'total' => $total,
            'hadir' => $hadir,
            'absen' => $presensi->where('status', 'absen')->count(),
            'izin' => $presensi->where('status', 'izin')->count(),
            'sakit' => $presensi->where('status', 'sakit')->count(),
            'persentase' => $total > 0 ? round(($hadir / $total) * 100, 2) : 0,
        ];
    }

    /**
     * Get attendance status based on percentage
     */
    public function getAttendanceStatus(float $persentase): array
    {
        if ($persentase >= 80) {
            return ['label' => 'Baik', 'color' => '#28a745', 'badge' => 'success'];
        } elseif ($persentase >= 60) {
            return ['label' => 'Perlu Perhatian', 'color' => '#ffc107', 'badge' => 'warning'];
        } else {
            return ['label' => 'Kritis', 'color' => '#dc3545', 'badge' => 'danger'];
        }
    }

    /**
     * Get students attendance summary for a class
     */
    public function getClassAttendanceSummary(int $jadwalId): array
    {
        $krs = KRS::where('jadwal_mengajar_id', $jadwalId)
            ->where('status', 'aktif')
            ->with('mahasiswa')
            ->get();

        $summary = [];
        foreach ($krs as $k) {
            $stats = $this->getStudentClassStatistics($k->mahasiswa_id, $jadwalId);
            $summary[] = array_merge(['mahasiswa' => $k->mahasiswa], $stats);
        }

        return $summary;
    }

    /**
     * Check attendance eligibility (for minimum attendance requirements)
     */
    public function isAttendanceEligible(int $mahasiswaId, int $jadwalId, float $minimumPercentage = 80): bool
    {
        $stats = $this->getStudentClassStatistics($mahasiswaId, $jadwalId);
        return $stats['persentase'] >= $minimumPercentage;
    }

    /**
     * Get attendance report by date range
     */
    public function getAttendanceReportByDateRange(int $jadwalId, Carbon $startDate, Carbon $endDate): array
    {
        return Presensi::where('jadwal_mengajar_id', $jadwalId)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->with('mahasiswa')
            ->orderBy('tanggal', 'desc')
            ->get()
            ->groupBy('tanggal')
            ->toArray();
    }

    /**
     * Create attendance records for a class and date
     */
    public function createAttendanceRecords(int $jadwalId, string $tanggal, array $attendanceData): int
    {
        $jadwal = JadwalMengajar::findOrFail($jadwalId);
        $dosen = $jadwal->dosen;
        $created = 0;

        foreach ($attendanceData as $data) {
            $krs = KRS::where('mahasiswa_id', $data['mahasiswa_id'])
                ->where('jadwal_mengajar_id', $jadwalId)
                ->first();

            if ($krs) {
                Presensi::create([
                    'krs_id' => $krs->id,
                    'mahasiswa_id' => $data['mahasiswa_id'],
                    'jadwal_mengajar_id' => $jadwalId,
                    'dosen_id' => $dosen->id,
                    'tanggal' => $tanggal,
                    'status' => $data['status'] ?? 'absen',
                    'jam_masuk' => $data['jam_masuk'] ?? null,
                    'jam_keluar' => $data['jam_keluar'] ?? null,
                    'keterangan' => $data['keterangan'] ?? null,
                    'sudah_dikonfirmasi' => true,
                ]);
                $created++;
            }
        }

        return $created;
    }

    /**
     * Update attendance record
     */
    public function updateAttendanceRecord(int $presensiId, array $data): bool
    {
        $presensi = Presensi::findOrFail($presensiId);
        return $presensi->update([
            'status' => $data['status'] ?? $presensi->status,
            'jam_masuk' => $data['jam_masuk'] ?? $presensi->jam_masuk,
            'jam_keluar' => $data['jam_keluar'] ?? $presensi->jam_keluar,
            'keterangan' => $data['keterangan'] ?? $presensi->keterangan,
        ]);
    }
}
