@extends('layouts.app')

@section('page-title', 'Dashboard Dosen')

@section('title', 'Dashboard Dosen - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <h1>Selamat Datang, {{ auth('dosen')->user()->name }}!</h1>
        <p class="text-muted">Kelola kelas, nilai, dan pembelajaran dengan mudah</p>
    </div>
</div>

<!-- Statistics Row -->
<div class="row g-4 mb-4">
    <!-- Classes Card -->
    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
        <div class="stat-card">
            <div class="stat-card-icon" style="color: #4a90e2;">
                <i class="bi bi-book-fill"></i>
            </div>
            <div class="stat-card-title">Kelas Mengajar</div>
            <div class="stat-card-value">{{ $totalJadwal ?? 0 }}</div>
            <small class="text-muted">Kelas Aktif</small>
        </div>
    </div>

    <!-- Students Card -->
    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
        <div class="stat-card">
            <div class="stat-card-icon" style="color: #17a2b8;">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-card-title">Total Mahasiswa</div>
            <div class="stat-card-value">{{ $totalMahasiswa ?? 0 }}</div>
            <small class="text-muted">Siswa di Kelas</small>
        </div>
    </div>

    <!-- Grades Card -->
    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
        <div class="stat-card">
            <div class="stat-card-icon" style="color: #28a745;">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <div class="stat-card-title">Nilai Terisi</div>
            <div class="stat-card-value">{{ $nilaiCount ?? 0 }}</div>
            <small class="text-muted">Data Nilai</small>
        </div>
    </div>

    <!-- Attendance Card -->
    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
        <div class="stat-card">
            <div class="stat-card-icon" style="color: #ffc107;">
                <i class="bi bi-calendar-check-fill"></i>
            </div>
            <div class="stat-card-title">Hadir Minggu Ini</div>
            <div class="stat-card-value">0</div>
            <small class="text-muted">Pertemuan Dilakukan</small>
        </div>
    </div>
</div>

<!-- Main Content Row -->
<div class="row g-4">
    <!-- Schedule Card -->
    <div class="col-lg-8">
        <div class="stat-card">
            <h5 style="margin-bottom: 20px;">
                <i class="bi bi-calendar3"></i> Jadwal Mengajar Minggu Ini
            </h5>
            @if($jadwals->isEmpty())
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i> Tidak ada jadwal untuk minggu ini
                </div>
            @else
                <div style="max-height: 400px; overflow-y: auto;">
                    @foreach($jadwals as $hari => $items)
                        <div class="mb-3">
                            <h6 style="color: #4a90e2; font-weight: 600; margin-bottom: 8px;">{{ $hari }}</h6>
                            @foreach($items as $jadwal)
                                <div style="border-left: 3px solid #4a90e2; padding: 10px 12px; margin-bottom: 8px; background: #f8f9fa; border-radius: 4px;">
                                    <div style="font-weight: 600; color: #333;">{{ $jadwal->mata_kuliah }}</div>
                                    <div style="font-size: 12px; color: #666; margin-top: 4px;">
                                        <i class="bi bi-clock"></i> {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                        <br>
                                        <i class="bi bi-door"></i> {{ $jadwal->ruang }} | <i class="bi bi-tag"></i> {{ $jadwal->prodi ?? '-' }}
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="col-lg-4">
        <div class="stat-card">
            <h5 style="margin-bottom: 15px;">
                <i class="bi bi-lightning"></i> Quick Actions
            </h5>
            <div class="list-group list-group-flush">
                <a href="{{ route('dosen.nilai.index') }}" class="list-group-item list-group-item-action" type="button" style="text-decoration: none; color: #333;">
                    <i class="bi bi-pencil-square"></i> Input Nilai
                </a>
                <button class="list-group-item list-group-item-action" type="button">
                    <i class="bi bi-clipboard-check"></i> Presensi Kelas
                </button>
                <a href="{{ route('dosen.tiket.index') }}" class="list-group-item list-group-item-action" type="button" style="text-decoration: none; color: #333;">
                    <i class="bi bi-chat-dots"></i> Tiket Keluhan
                </a>
                <button class="list-group-item list-group-item-action" type="button">
                    <i class="bi bi-file-earmark"></i> Lihat Laporan
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    .list-group-item {
        border: none;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
        text-align: left;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .list-group-item:hover {
        background-color: #f8f9fa;
        padding-left: 10px;
    }

    .list-group-item:last-child {
        border-bottom: none;
    }
</style>
@endsection
