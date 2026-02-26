@extends('layouts.app')

@section('page-title', 'Dashboard Mahasiswa')

@section('title', 'Dashboard Mahasiswa - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <h1>Selamat Datang, {{ auth('mahasiswa')->user()->name }}!</h1>
        <p class="text-muted">Kelola akademik, jadwal, dan nilai dengan mudah</p>
        <p class="text-muted">Program Studi: {{ auth('mahasiswa')->user()->prodi ?? '-' }}</p>
    </div>
</div>

<!-- Statistics Row -->
<div class="row g-4 mb-4">
    <!-- Current Semester Card -->
    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
        <div class="stat-card">
            <div class="stat-card-icon" style="color: #4a90e2;">
                <i class="bi bi-calendar2-event-fill"></i>
            </div>
            <div class="stat-card-title">Semester Aktif</div>
            <div class="stat-card-value">{{ $semesterAktif }}</div>
            <small class="text-muted">Tahun Akademik</small>
        </div>
    </div>

    <!-- Classes Card -->
    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
        <div class="stat-card">
            <div class="stat-card-icon" style="color: #17a2b8;">
                <i class="bi bi-book-fill"></i>
            </div>
            <div class="stat-card-title">Kelas Diambil</div>
            <div class="stat-card-value">{{ $krsCount ?? 0 }}</div>
            <small class="text-muted">Mata Kuliah (KRS)</small>
        </div>
    </div>

    <!-- GPA Card -->
    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
        <div class="stat-card">
            <div class="stat-card-icon" style="color: #28a745;">
                <i class="bi bi-star-fill"></i>
            </div>
            <div class="stat-card-title">Nilai IP</div>
            <div class="stat-card-value">{{ number_format($gpa ?? 0, 2) }}</div>
            <small class="text-muted">Indeks Prestasi</small>
        </div>
    </div>

    <!-- Attendance Card -->
    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
        <div class="stat-card">
            <div class="stat-card-icon" style="color: #ffc107;">
                <i class="bi bi-percent"></i>
            </div>
            <div class="stat-card-title">Kehadiran</div>
            <div class="stat-card-value">0%</div>
            <small class="text-muted">Persentase Hadir</small>
        </div>
    </div>
</div>

<!-- Main Content Row -->
<div class="row g-4">
    <!-- Schedule Card -->
    <div class="col-lg-8">
        <div class="stat-card">
            <h5 style="margin-bottom: 20px;">
                <i class="bi bi-calendar3"></i> Jadwal Kuliah Minggu Ini
            </h5>
            @if($jadwals->isEmpty())
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i> Tidak ada jadwal untuk minggu ini
                </div>
            @else
                <div style="max-height: 400px; overflow-y: auto;">
                    @foreach($jadwals as $hari => $items)
                        <div class="mb-3">
                            <h6 style="color: #17a2b8; font-weight: 600; margin-bottom: 8px;">{{ $hari }}</h6>
                            @foreach($items as $jadwal)
                                <div style="border-left: 3px solid #17a2b8; padding: 10px 12px; margin-bottom: 8px; background: #f8f9fa; border-radius: 4px;">
                                    <div style="font-weight: 600; color: #333;">{{ $jadwal->mata_kuliah }}</div>
                                    <div style="font-size: 12px; color: #666; margin-top: 4px;">
                                        <i class="bi bi-person-fill"></i> {{ $jadwal->dosen?->name ?? 'TBA' }}
                                        <br>
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
                <a href="{{ route('mahasiswa.krs.index') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-clipboard-check"></i> Kelola KRS
                </a>
                <a href="{{ route('mahasiswa.jadwal.index') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-calendar3"></i> Jadwal Kuliah
                </a>
                <a href="{{ route('tiket.mahasiswa.create') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-ticket-perforated"></i> Buat Tiket Keluhan
                </a>
                <a href="{{ route('tiket.mahasiswa.index') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-list-check"></i> Daftar Tiket Saya
                </a>
                <a href="{{ route('mahasiswa.nilai.index') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-file-earmark-text"></i> Lihat Nilai
                </a>
                <a href="{{ route('mahasiswa.transkip') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-file-pdf"></i> Transkip Akademik
                </a>
            </div>
        </div>
    </div>

            @if($recentTikets->count())
            <div class="row mt-4">
                <div class="col-12">
                    <div class="stat-card">
                        <h5><i class="bi bi-clock-history"></i> Tiket Terbaru</h5>
                        <ul class="list-unstyled mt-2">
                            @foreach($recentTikets as $t)
                                <li class="mb-2">
                                    <strong>#{{ $t->id }}</strong> - {{ $t->judul }} <span class="text-muted">({{ $t->status }})</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            @endif
</div>

<style>
    .list-group-item {
        border: none;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
        text-align: left;
        cursor: pointer;
        transition: all 0.3s ease;
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
