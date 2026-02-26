@extends('layouts.app')

@section('page-title', 'Laporan Presensi')

@section('title', 'Laporan Presensi - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-graph-up"></i> Laporan Presensi</h1>
        <p class="text-muted">Dashboard laporan kehadiran mahasiswa</p>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(52, 152, 219, 0.1);">
                <i class="bi bi-book" style="color: #3498db;"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $totalClasses }}</h3>
                <p>Total Kelas</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(46, 204, 113, 0.1);">
                <i class="bi bi-people" style="color: #2ecc71;"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $totalStudents }}</h3>
                <p>Total Mahasiswa</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(155, 89, 182, 0.1);">
                <i class="bi bi-check-circle" style="color: #9b59b6;"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $totalHadir }}</h3>
                <p>Hadir</p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(231, 76, 60, 0.1);">
                <i class="bi bi-x-circle" style="color: #e74c3c;"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $totalAbsen }}</h3>
                <p>Absen</p>
            </div>
        </div>
    </div>
</div>

<!-- Attendance by Class -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Ringkasan Presensi Per Kelas</h5>
    </div>
    <div class="card-body">
        <div style="overflow-x: auto;">
            <table class="table table-hover">
                <thead style="background-color: #f8f9fa;">
                    <tr>
                        <th>Mata Kuliah</th>
                        <th style="text-align: center;">Hari/Jam</th>
                        <th style="text-align: center;">Mahasiswa</th>
                        <th style="text-align: center;">Hadir</th>
                        <th style="text-align: center;">Absen</th>
                        <th style="text-align: center;">Persentase</th>
                        <th style="text-align: center;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classStats as $stat)
                        <tr>
                            <td>
                                <strong>{{ $stat['jadwal']->mata_kuliah }}</strong><br>
                                <small class="text-muted">{{ $stat['jadwal']->kode_mk }}</small>
                            </td>
                            <td style="text-align: center;">
                                {{ ucfirst($stat['jadwal']->hari) }}<br>
                                <small>{{ \Carbon\Carbon::parse($stat['jadwal']->jam_mulai)->format('H:i') }}</small>
                            </td>
                            <td style="text-align: center; font-weight: 600;">{{ $stat['students'] }}</td>
                            <td style="text-align: center;">
                                <span class="badge bg-success">{{ $stat['hadir'] }}</span>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge bg-danger">{{ $stat['absen'] }}</span>
                            </td>
                            <td style="text-align: center;">
                                <div class="progress" style="height: 25px;">
                                    <div class="progress-bar bg-success" role="progressbar" style="width: {{ $stat['percentage'] }}%">
                                        {{ $stat['percentage'] }}%
                                    </div>
                                </div>
                            </td>
                            <td style="text-align: center;">
                                <a href="{{ route('dosen.laporan.kelas', $stat['jadwal']->id) }}" class="btn btn-sm btn-info">
                                    <i class="bi bi-eye"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" style="text-align: center; padding: 20px;">
                                <p class="text-muted">Belum ada data presensi</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Recent Attendance -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Presensi Terbaru</h5>
    </div>
    <div class="card-body">
        <div style="overflow-x: auto;">
            <table class="table table-sm table-hover">
                <thead style="background-color: #f8f9fa;">
                    <tr>
                        <th>Tanggal</th>
                        <th>Mahasiswa</th>
                        <th>Kelas</th>
                        <th>Status</th>
                        <th>Jam Masuk</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentPresensi as $p)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                            <td>{{ $p->mahasiswa->name ?? $p->mahasiswa->nama ?? '-' }}</td>
                            <td>{{ $p->jadwalMengajar->kode_mk ?? $p->jadwalMengajar->mata_kuliah ?? '-' }}</td>
                            <td>
                                @php
                                    $statusClass = match($p->status) {
                                        'hadir' => 'bg-success',
                                        'absen' => 'bg-danger',
                                        'izin' => 'bg-warning text-dark',
                                        'sakit' => 'bg-info',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $statusClass }}">
                                    {{ $p->getStatusLabel() }}
                                </span>
                            </td>
                            <td>{{ $p->jam_masuk ?? '-' }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 20px;">
                                <p class="text-muted">Belum ada presensi terbaru</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('extra-css')
<style>
    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 24px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 24px;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 12px;
    }

    .stat-content h3 {
        margin: 0;
        font-weight: 700;
        color: #333;
        font-size: 28px;
    }

    .stat-content p {
        margin: 5px 0 0 0;
        color: #666;
        font-size: 14px;
    }
</style>
@endsection
