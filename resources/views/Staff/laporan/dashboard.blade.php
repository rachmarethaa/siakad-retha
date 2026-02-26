@extends('layouts.app')

@section('page-title', 'Laporan Presensi')

@section('title', 'Laporan Presensi - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-bar-chart"></i> Laporan Presensi Keseluruhan</h1>
        <p class="text-muted">Dashboard laporan kehadiran seluruh mahasiswa</p>
    </div>
</div>

<!-- Overall Statistics -->
<div class="row mb-4">
    <div class="col-md-2">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(52, 152, 219, 0.1);">
                <i class="bi bi-book" style="color: #3498db;"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $totalClasses }}</h3>
                <p>Kelas</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(46, 204, 113, 0.1);">
                <i class="bi bi-people" style="color: #2ecc71;"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $totalStudents }}</h3>
                <p>Mahasiswa</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
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
    <div class="col-md-2">
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
    <div class="col-md-2">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(241, 196, 15, 0.1);">
                <i class="bi bi-exclamation-circle" style="color: #f1c40f;"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $totalIzin }}</h3>
                <p>Izin</p>
            </div>
        </div>
    </div>
    <div class="col-md-2">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(52, 73, 94, 0.1);">
                <i class="bi bi-hospital" style="color: #34495e;"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $totalSakit }}</h3>
                <p>Sakit</p>
            </div>
        </div>
    </div>
</div>

<!-- Tabs for Reports -->
<ul class="nav nav-tabs mb-3" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab-dosen" data-bs-toggle="tab" data-bs-target="#tab-dosen-content" type="button" role="tab">
            <i class="bi bi-person-badge"></i> Per Dosen
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-student" data-bs-toggle="tab" data-bs-target="#tab-student-content" type="button" role="tab">
            <i class="bi bi-mortarboard"></i> Per Mahasiswa
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab-top" data-bs-toggle="tab" data-bs-target="#tab-top-content" type="button" role="tab">
            <i class="bi bi-graph-up"></i> Terbaik
        </button>
    </li>
</ul>

<div class="tab-content">
    <!-- Dosen Performance Tab -->
    <div class="tab-pane fade show active" id="tab-dosen-content" role="tabpanel">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Performa Dosen</h5>
            </div>
            <div class="card-body">
                <div style="overflow-x: auto;">
                    <table class="table table-hover">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th>#</th>
                                <th>Nama Dosen</th>
                                <th style="text-align: center;">Jumlah Kelas</th>
                                <th style="text-align: center;">Persentase Kehadiran</th>
                                <th style="text-align: center;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($dosenStats as $index => $stat)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $stat['dosen'] }}</td>
                                    <td style="text-align: center; font-weight: 600;">{{ $stat['classes'] }}</td>
                                    <td style="text-align: center;">
                                        <div class="progress" style="height: 25px;">
                                            @if($stat['percentage'] >= 75)
                                                <div class="progress-bar bg-success" role="progressbar" style="width: {{ $stat['percentage'] }}%">
                                                    {{ $stat['percentage'] }}%
                                                </div>
                                            @elseif($stat['percentage'] >= 50)
                                                <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $stat['percentage'] }}%">
                                                    {{ $stat['percentage'] }}%
                                                </div>
                                            @else
                                                <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $stat['percentage'] }}%">
                                                    {{ $stat['percentage'] }}%
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="{{ route('staff.laporan.dosen', $stat['dosen_id'] ?? 0) }}" class="btn btn-sm btn-info">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" style="text-align: center; padding: 20px;">
                                        <p class="text-muted">Belum ada data dosen</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Student Report Tab -->
    <div class="tab-pane fade" id="tab-student-content" role="tabpanel">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Laporan Per Mahasiswa</h5>
                    <a href="{{ route('staff.laporan.mahasiswa') }}" class="btn btn-sm btn-light">
                        <i class="bi bi-arrow-right"></i> Lihat Semua
                    </a>
                </div>
            </div>
            <div class="card-body">
                <p class="text-muted">Klik tombol "Lihat Semua" untuk melihat laporan detail per mahasiswa</p>
            </div>
        </div>
    </div>

    <!-- Top Attendance Tab -->
    <div class="tab-pane fade" id="tab-top-content" role="tabpanel">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Presensi Terbaik</h5>
            </div>
            <div class="card-body">
                <div style="overflow-x: auto;">
                    <table class="table table-sm table-hover">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th>#</th>
                                <th>Tanggal</th>
                                <th>Mahasiswa</th>
                                <th>Prodi</th>
                                <th>Kelas (Dosen)</th>
                                <th>Status</th>
                                <th>Jam Masuk</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topAttendance as $index => $p)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ \Carbon\Carbon::parse($p->tanggal)->format('d/m/Y') }}</td>
                                    <td>{{ $p->mahasiswa->name ?? $p->mahasiswa->nama ?? '-' }}</td>
                                    <td>{{ $p->mahasiswa->prodi ?? '-' }}</td>
                                    <td>
                                        {{ $p->jadwalMengajar->kode_mk ?? '-' }}
                                        <br>
                                        <small class="text-muted">{{ $p->jadwalMengajar->dosen->nama_lengkap ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">{{ ucfirst($p->status) }}</span>
                                    </td>
                                    <td>{{ $p->jam_masuk ?? '-' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" style="text-align: center; padding: 20px;">
                                        <p class="text-muted">Belum ada data presensi</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
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
        width: 50px;
        height: 50px;
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
