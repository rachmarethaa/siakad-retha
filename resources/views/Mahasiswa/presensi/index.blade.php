@extends('layouts.app')

@section('page-title', 'Presensi Saya')

@section('title', 'Presensi Saya - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-check-circle"></i> Data Presensi Saya</h1>
        <p class="text-muted">Kelola dan pantau kehadiran Anda di semua kelas</p>
    </div>
    <a href="{{ route('mahasiswa.presensi.export') }}" class="btn btn-success">
        <i class="bi bi-download"></i> Export Laporan
    </a>
</div>

@php
    $allPresensi = \App\Models\Presensi::where('mahasiswa_id', auth()->guard('mahasiswa')->user()->id)->get();
    $totalHadir = $allPresensi->where('status', 'hadir')->count();
    $totalPresensi = $allPresensi->count();
    $overallPercentage = $totalPresensi > 0 ? round(($totalHadir / $totalPresensi) * 100, 2) : 0;
@endphp

<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(52, 152, 219, 0.1);">
                <i class="bi bi-book" style="color: #3498db;"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $krs->count() }}</h3>
                <p>Total Kelas</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(40, 167, 69, 0.1);">
                <i class="bi bi-check-circle" style="color: #28a745;"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $totalHadir }}</h3>
                <p>Total Hadir</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(220, 53, 69, 0.1);">
                <i class="bi bi-x-circle" style="color: #dc3545;"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $totalPresensi }}</h3>
                <p>Total Pertemuan</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(255, 193, 7, 0.1);">
                <i class="bi bi-percent" style="color: #ffc107;"></i>
            </div>
            <div class="stat-content">
                <h3 id="overallPercentage">0%</h3>
                <p>Rata-rata Kehadiran</p>
            </div>
        </div>
    </div>
</div>

<div class="stat-card">
    <h5 style="margin-bottom: 20px;"><i class="bi bi-list-ul"></i> Presensi Per Kelas</h5>

    @if($krs->isEmpty())
        <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle"></i> Anda belum terdaftar di kelas mana pun
        </div>
    @else
        <div style="overflow-x: auto;">
            <table class="table table-hover mb-0">
                <thead style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <tr>
                        <th>Mata Kuliah</th>
                        <th style="text-align: center; width: 100px;">Kode MK</th>
                        <th style="text-align: center; width: 100px;">Dosen</th>
                        <th style="text-align: center; width: 80px;">Total</th>
                        <th style="text-align: center; width: 80px;">Hadir</th>
                        <th style="text-align: center; width: 80px;">Absen</th>
                        <th style="text-align: center; width: 80px;">Izin</th>
                        <th style="text-align: center; width: 80px;">Sakit</th>
                        <th style="text-align: center; width: 120px;">Persentase</th>
                        <th style="text-align: center; width: 100px;">Status</th>
                        <th style="text-align: center; width: 80px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($krs as $k)
                        @php
                            $stats = $presensiStats[$k->jadwal_mengajar_id] ?? [];
                            $total = $stats['total'] ?? 0;
                            $hadir = $stats['hadir'] ?? 0;
                            $absen = $stats['absen'] ?? 0;
                            $izin = $stats['izin'] ?? 0;
                            $sakit = $stats['sakit'] ?? 0;
                            $persentase = $stats['persentase'] ?? 0;
                            $statusWaspada = $stats['status_waspada'] ?? false;
                            
                            $statusClass = $persentase >= 80 ? 'success' : ($persentase >= 60 ? 'warning' : 'danger');
                            $statusLabel = $persentase >= 80 ? 'Baik' : ($persentase >= 60 ? '⚠️ Perhatian' : '❌ Kritis');
                        @endphp
                        <tr @if($statusWaspada) style="background-color: rgba(255, 193, 7, 0.05);" @endif>
                            <td>
                                <div style="font-weight: 600; color: #333;">{{ $k->jadwalMengajar->mata_kuliah }}</div>
                                <small class="text-muted">{{ $k->jadwalMengajar->dosen->name ?? $k->jadwalMengajar->dosen->nama_lengkap ?? 'TBA' }}</small>
                            </td>
                            <td style="text-align: center; font-weight: 600;">{{ $k->jadwalMengajar->kode_mk ?? '-' }}</td>
                            <td style="text-align: center; font-size: 13px;">{{ $k->jadwalMengajar->dosen->name ?? $k->jadwalMengajar->dosen->nama_lengkap ?? 'TBA' }}</td>
                            <td style="text-align: center; font-weight: 600;">{{ $total }}</td>
                            <td style="text-align: center;">
                                <span class="badge" style="background-color: #28a745;">{{ $hadir }}</span>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge" style="background-color: #dc3545;">{{ $absen }}</span>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge" style="background-color: #ffc107; color: #000;">{{ $izin }}</span>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge" style="background-color: #17a2b8;">{{ $sakit }}</span>
                            </td>
                            <td style="text-align: center;">
                                <strong>{{ $persentase }}%</strong>
                                <div class="progress" style="height: 6px; margin-top: 4px;">
                                    @php
                                        $bgColor = $statusClass === 'success' ? '#28a745' : ($statusClass === 'warning' ? '#ffc107' : '#dc3545');
                                    @endphp
                                    <div class="progress-bar" style="width: {{ $persentase }}%; background-color: {{ $bgColor }};"></div>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge bg-{{ $statusClass }}">{{ $statusLabel }}</span>
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('mahasiswa.presensi.show', $k->jadwal_mengajar_id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Legend -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="stat-card">
            <h6 style="margin-bottom: 16px;"><i class="bi bi-info-circle"></i> Keterangan Status</h6>
            <div style="display: flex; gap: 24px; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="badge bg-success">Baik</span>
                    <small>Kehadiran ≥ 80%</small>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="badge bg-warning">Perlu Perhatian</span>
                    <small>Kehadiran 60-79%</small>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="badge bg-danger">Kritis</span>
                    <small>Kehadiran < 60%</small>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@section('extra-css')
<style>
    .page-header {
        margin-bottom: 30px;
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }

    .page-header h1 {
        font-size: 28px;
        font-weight: 700;
        margin-bottom: 8px;
        color: #333;
    }

    .page-header .text-muted {
        font-size: 14px;
        color: #666;
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 24px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 24px;
    }

    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        margin-bottom: 8px;
    }

    .stat-content h3 {
        margin: 0;
        font-size: 24px;
        font-weight: 700;
    }

    .stat-content p {
        margin: 4px 0 0 0;
        font-size: 12px;
        color: #666;
    }

    .table {
        margin-bottom: 0;
    }

    .table tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02) !important;
    }

    .badge {
        padding: 4px 8px;
        font-size: 11px;
        font-weight: 600;
    }

    .progress {
        background-color: #e9ecef;
        border-radius: 2px;
    }

    .btn-sm {
        padding: 4px 8px;
        font-size: 12px;
    }
</style>
@endsection
