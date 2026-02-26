@extends('layouts.app')

@section('page-title', 'Detail Presensi Kelas')

@section('title', 'Detail Presensi - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <a href="{{ route('dosen.presensi.index') }}" class="btn btn-sm btn-secondary mb-2">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1><i class="bi bi-check2-square"></i> Detail Presensi</h1>
        <p class="text-muted">{{ $jadwal->mata_kuliah }} - {{ $jadwal->kode_mk }}</p>
    </div>
    <div>
        <a href="{{ route('dosen.presensi.create', $jadwal->id) }}" class="btn btn-primary">
            <i class="bi bi-plus"></i> Input Presensi Baru
        </a>
        <a href="{{ route('dosen.presensi.export', $jadwal->id) }}" class="btn btn-info">
            <i class="bi bi-download"></i> Export
        </a>
    </div>
</div>
        <p class="text-muted">{{ $jadwal->mata_kuliah }} ({{ $jadwal->kode_mk }})</p>
    </div>
    <div>
        <a href="{{ route('dosen.presensi.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <a href="{{ route('dosen.presensi.export', $jadwal->id) }}" class="btn btn-success">
            <i class="bi bi-download"></i> Export CSV
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Statistics Card -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(40, 167, 69, 0.1);">
                <i class="bi bi-check-circle" style="color: #28a745;"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['hadir'] ?? 0 }}</h3>
                <p>Hadir</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(220, 53, 69, 0.1);">
                <i class="bi bi-x-circle" style="color: #dc3545;"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['absen'] ?? 0 }}</h3>
                <p>Absen</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(255, 193, 7, 0.1);">
                <i class="bi bi-exclamation-circle" style="color: #ffc107;"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['izin'] ?? 0 }}</h3>
                <p>Izin</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(23, 162, 184, 0.1);">
                <i class="bi bi-hospital" style="color: #17a2b8;"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $stats['sakit'] ?? 0 }}</h3>
                <p>Sakit</p>
            </div>
        </div>
    </div>
</div>

<!-- Presensi History -->
<div class="stat-card">
    <div style="margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center;">
        <h5 style="margin: 0;"><i class="bi bi-calendar-check"></i> Riwayat Presensi Berdasarkan Tanggal</h5>
        <a href="{{ route('dosen.presensi.create', $jadwal->id) }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle"></i> Input Presensi Baru
        </a>
    </div>

    @if($presensiByDate->isEmpty())
        <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle"></i> Belum ada data presensi
        </div>
    @else
        <div style="overflow-x: auto;">
            <table class="table table-hover mb-0">
                <thead style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <tr>
                        <th>Tanggal</th>
                        <th style="text-align: center;">Hadir</th>
                        <th style="text-align: center;">Absen</th>
                        <th style="text-align: center;">Izin</th>
                        <th style="text-align: center;">Sakit</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($presensiByDate as $tanggal => $presensiList)
                        <tr>
                            <td style="font-weight: 600;">
                                {{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}
                                <small class="text-muted d-block">{{ \Carbon\Carbon::parse($tanggal)->translatedFormat('l') }}</small>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge" style="background-color: #28a745;">
                                    {{ $presensiList->where('status', 'hadir')->count() }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge" style="background-color: #dc3545;">
                                    {{ $presensiList->where('status', 'absen')->count() }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge" style="background-color: #ffc107; color: #000;">
                                    {{ $presensiList->where('status', 'izin')->count() }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge" style="background-color: #17a2b8;">
                                    {{ $presensiList->where('status', 'sakit')->count() }}
                                </span>
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('dosen.presensi.edit', [$jadwal->id, $tanggal]) }}" class="btn btn-sm btn-warning">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

<!-- Mahasiswa Attendance Summary -->
<div class="stat-card" style="margin-top: 24px;">
    <h5 style="margin-bottom: 20px;"><i class="bi bi-people"></i> Ringkasan Kehadiran Per Mahasiswa</h5>

    @if($krs->isEmpty())
        <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle"></i> Belum ada mahasiswa terdaftar di kelas ini
        </div>
    @else
        <div style="overflow-x: auto;">
            <table class="table table-hover mb-0">
                <thead style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <tr>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Prodi</th>
                        <th style="text-align: center;">Total</th>
                        <th style="text-align: center;">Hadir</th>
                        <th style="text-align: center;">Absen</th>
                        <th style="text-align: center;">Izin</th>
                        <th style="text-align: center;">Sakit</th>
                        <th style="text-align: center;">%</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($krs as $k)
                        @php
                            $presensi = \App\Models\Presensi::where('mahasiswa_id', $k->mahasiswa_id)
                                ->where('jadwal_mengajar_id', $jadwal->id)
                                ->get();
                            $total = $presensi->count();
                            $hadir = $presensi->where('status', 'hadir')->count();
                            $absen = $presensi->where('status', 'absen')->count();
                            $izin = $presensi->where('status', 'izin')->count();
                            $sakit = $presensi->where('status', 'sakit')->count();
                            $persentase = $total > 0 ? round(($hadir / $total) * 100, 2) : 0;
                        @endphp
                        <tr>
                            <td style="font-weight: 600;">{{ $k->mahasiswa->nim ?? '-' }}</td>
                            <td>{{ $k->mahasiswa->name ?? $k->mahasiswa->nama ?? '-' }}</td>
                            <td>{{ $k->mahasiswa->prodi ?? '-' }}</td>
                            <td style="text-align: center;">{{ $total }}</td>
                            <td style="text-align: center;"><span class="badge" style="background-color: #28a745;">{{ $hadir }}</span></td>
                            <td style="text-align: center;"><span class="badge" style="background-color: #dc3545;">{{ $absen }}</span></td>
                            <td style="text-align: center;"><span class="badge" style="background-color: #ffc107; color: #000;">{{ $izin }}</span></td>
                            <td style="text-align: center;"><span class="badge" style="background-color: #17a2b8;">{{ $sakit }}</span></td>
                            <td style="text-align: center;">
                                <strong>{{ $persentase }}%</strong>
                                @if($persentase < 75)
                                    <span class="badge" style="background-color: #dc3545; margin-left: 8px;">⚠️</span>
                                @endif
                            </td>
                        <tr>
                                <td colspan="9" style="text-align: center; padding: 20px;">
                                    <p class="text-muted">Belum ada mahasiswa terdaftar di kelas ini</p>
                                </td>
                            </tr>
    @endif
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
        font-size: 28px;
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
        background-color: rgba(0, 0, 0, 0.02);
    }
</style>
@endsection
