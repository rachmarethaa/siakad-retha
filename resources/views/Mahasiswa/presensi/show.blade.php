@extends('layouts.app')

@section('page-title', 'Detail Presensi')

@section('title', 'Detail Presensi - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-file-earmark-text"></i> Detail Presensi</h1>
        <p class="text-muted">{{ $krs->jadwalMengajar->mata_kuliah }} - {{ $krs->jadwalMengajar->kode_mk }}</p>
    </div>
    <a href="{{ route('mahasiswa.presensi.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<!-- Statistics -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(52, 152, 219, 0.1);">
                <i class="bi bi-calendar-check" style="color: #3498db;"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $total }}</h3>
                <p>Total Hadir</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(40, 167, 69, 0.1);">
                <i class="bi bi-check-circle" style="color: #28a745;"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $hadir }}</h3>
                <p>Hadir</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(220, 53, 69, 0.1);">
                <i class="bi bi-x-circle" style="color: #dc3545;"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $absen }}</h3>
                <p>Absen</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(255, 193, 7, 0.1);">
                <i class="bi bi-exclamation-circle" style="color: #ffc107;"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $izin }}</h3>
                <p>Izin</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(23, 162, 184, 0.1);">
                <i class="bi bi-hospital" style="color: #17a2b8;"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $sakit }}</h3>
                <p>Sakit</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(108, 117, 125, 0.1);">
                <i class="bi bi-percent" style="color: #6c757d;"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $persentase }}%</h3>
                <p>Kehadiran</p>
            </div>
        </div>
    </div>
</div>

<!-- Info Kelas -->
<div class="stat-card" style="margin-bottom: 24px;">
    <h5 style="margin-bottom: 16px;"><i class="bi bi-info-circle"></i> Informasi Kelas</h5>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label class="form-label-small">Mata Kuliah</label>
                <div style="font-weight: 600; color: #333;">{{ $krs->jadwalMengajar->mata_kuliah }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="form-label-small">Kode MK</label>
                <div style="font-weight: 600; color: #333;">{{ $krs->jadwalMengajar->kode_mk }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="form-label-small">Dosen Pengampu</label>
                <div style="font-weight: 600; color: #333;">{{ $krs->jadwalMengajar->dosen->name ?? $krs->jadwalMengajar->dosen->nama_lengkap ?? 'TBA' }}</div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="form-label-small">Jadwal</label>
                <div style="font-weight: 600; color: #333;">
                    {{ ucfirst($krs->jadwalMengajar->hari) }} 
                    {{ \Carbon\Carbon::parse($krs->jadwalMengajar->jam_mulai)->format('H:i') }}-{{ \Carbon\Carbon::parse($krs->jadwalMengajar->jam_selesai)->format('H:i') }}
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Presensi History -->
<div class="stat-card">
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <h5 style="margin: 0;"><i class="bi bi-list-ul"></i> Riwayat Presensi</h5>
        <a href="{{ route('mahasiswa.presensi.export') }}" class="btn btn-sm btn-success">
            <i class="bi bi-download"></i> Export
        </a>
    </div>

    @if($presensi->count() === 0)
        <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle"></i> Belum ada data presensi untuk kelas ini
        </div>
    @else
        <div style="overflow-x: auto;">
            <table class="table table-hover mb-0">
                <thead style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th style="width: 100px;">Tanggal</th>
                        <th style="width: 80px;">Hari</th>
                        <th style="text-align: center;">Status</th>
                        <th style="text-align: center; width: 100px;">Jam Masuk</th>
                        <th style="text-align: center; width: 100px;">Jam Keluar</th>
                        <th style="text-align: center; width: 100px;">Durasi</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($presensi as $index => $p)
                        @php
                            $durasi = $p->getDurasiKehadiran();
                            $statusColor = match($p->status) {
                                'hadir' => '#28a745',
                                'absen' => '#dc3545',
                                'izin' => '#ffc107',
                                'sakit' => '#17a2b8',
                                default => '#6c757d'
                            };
                        @endphp
                        <tr>
                            <td style="font-weight: 600;">{{ $index + 1 }}</td>
                            <td style="font-weight: 600;">{{ $p->tanggal->format('d-m-Y') }}</td>
                            <td>{{ $p->tanggal->translatedFormat('l') }}</td>
                            <td style="text-align: center;">
                                <span class="badge" style="background-color: {{ $p->getStatusBadgeColor() }};">
                                    {{ $p->getStatusLabel() }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                @if($p->jam_masuk)
                                    <span style="font-weight: 600;">{{ $p->jam_masuk }}</span>
                                    @if($p->isTerlambat())
                                        <span class="badge bg-warning" style="margin-left: 4px;">Terlambat</span>
                                    @endif
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td style="text-align: center;">
                                {{ $p->jam_keluar ?? '-' }}
                            </td>
                            <td style="text-align: center;">
                                @if($durasi)
                                    <span style="font-weight: 600;">{{ $durasi }} menit</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <small>{{ $p->keterangan ?? '-' }}</small>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div style="margin-top: 16px; display: flex; justify-content: center;">
            {{ $presensi->links() }}
        </div>
    @endif
</div>

<!-- Performance Indicator -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="stat-card">
            <h6 style="margin-bottom: 16px;"><i class="bi bi-speedometer2"></i> Indikator Performa Kehadiran</h6>
            <div class="progress" style="height: 30px;">
                @php
                    $bgColor = $persentase >= 80 ? '#28a745' : ($persentase >= 60 ? '#ffc107' : '#dc3545');
                @endphp
                <div class="progress-bar" style="width: {{ $persentase }}%; background-color: {{ $bgColor }}; display: flex; align-items: center; justify-content: center; color: white; font-weight: 600;">
                    {{ $persentase }}%
                </div>
            </div>
            <div style="margin-top: 12px; font-size: 13px;">
                @if($persentase >= 80)
                    <span style="color: #28a745;"><i class="bi bi-check-circle"></i> Kehadiran Anda sangat baik!</span>
                @elseif($persentase >= 60)
                    <span style="color: #ffc107;"><i class="bi bi-exclamation-circle"></i> Kehadiran Anda perlu perhatian, cobalah untuk hadir lebih konsisten</span>
                @else
                    <span style="color: #dc3545;"><i class="bi bi-x-circle"></i> Kehadiran Anda kritis, segera hubungi dosen untuk diskusi</span>
                @endif
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
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 8px;
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

    .form-label-small {
        font-size: 12px;
        color: #666;
        margin-bottom: 4px;
        display: block;
    }

    .form-group {
        margin-bottom: 16px;
    }

    .table {
        margin-bottom: 0;
    }

    .table tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .badge {
        padding: 4px 8px;
        font-size: 11px;
        font-weight: 600;
    }

    .text-muted {
        color: #999;
    }

    .progress {
        background-color: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
    }

    .progress-bar {
        border-radius: 4px;
    }

    .btn-sm {
        padding: 4px 8px;
        font-size: 12px;
    }
</style>
@endsection
