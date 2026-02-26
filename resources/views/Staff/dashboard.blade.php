@extends('layouts.app')

@section('page-title', 'Dashboard Staff')

@section('title', 'Dashboard Staff - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <h1>Selamat Datang, {{ auth('staff')->user()->name }}!</h1>
        <p class="text-muted">Kelola sistem akademik dengan mudah dan efisien</p>
    </div>
</div>

<!-- Statistics Row -->
<div class="row g-4 mb-4">
    <!-- Total Staff Card -->
    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
        <div class="stat-card">
            <div class="stat-card-icon" style="color: #4a90e2;">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-card-title">Total Staff</div>
            <div class="stat-card-value">{{ $totalStaff ?? 0 }}</div>
            <small class="text-muted">Pengguna Staff Terdaftar</small>
        </div>
    </div>

    <!-- Total Dosen Card -->
    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
        <div class="stat-card">
            <div class="stat-card-icon" style="color: #17a2b8;">
                <i class="bi bi-person-badge-fill"></i>
            </div>
            <div class="stat-card-title">Total Dosen</div>
            <div class="stat-card-value">{{ $totalDosen ?? 0 }}</div>
            <small class="text-muted">Dosen Terdaftar</small>
        </div>
    </div>

    <!-- Total Mahasiswa Card -->
    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
        <div class="stat-card">
            <div class="stat-card-icon" style="color: #28a745;">
                <i class="bi bi-mortarboard-fill"></i>
            </div>
            <div class="stat-card-title">Total Mahasiswa</div>
            <div class="stat-card-value">{{ $totalMahasiswa ?? 0 }}</div>
            <small class="text-muted">Mahasiswa Terdaftar</small>
        </div>
    </div>

    <!-- Total Jadwal Card -->
    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-12">
        <div class="stat-card">
            <div class="stat-card-icon" style="color: #ffc107;">
                <i class="bi bi-calendar3-fill"></i>
            </div>
            <div class="stat-card-title">Total Jadwal</div>
            <div class="stat-card-value">{{ $totalJadwal ?? 0 }}</div>
            <small class="text-muted">Jadwal Mengajar</small>
        </div>
    </div>
</div>

<!-- Main Content Row -->
<div class="row">
    <!-- Welcome Card -->
    <div class="col-lg-8">
        <div class="stat-card" style="background: linear-gradient(135deg, #0d47a1 0%, #1565c0 100%); color: white;">
            <h5 style="color: white; margin-bottom: 15px;">
                <i class="bi bi-info-circle"></i> Informasi Sistem
            </h5>
            <p style="margin: 0 0 10px 0;">
                <strong>Nama Aplikasi:</strong> SIAKAD (Sistem Informasi Akademik)
            </p>
            <p style="margin: 0 0 10px 0;">
                <strong>Institusi:</strong> Politeknik Bhakti Semesta
            </p>
            <p style="margin: 0 0 10px 0;">
                <strong>Versi:</strong> 1.0 Beta
            </p>
            <p style="margin: 0;">
                <strong>Last Updated:</strong> {{ now()->format('d M Y H:i') }}
            </p>
        </div>
    </div>

    <!-- Quick Actions Card -->
    <div class="col-lg-4">
        <div class="stat-card">
            <h5 style="margin-bottom: 15px;">
                <i class="bi bi-lightning"></i> Quick Actions
            </h5>
            <div class="list-group list-group-flush">
                <a href="{{ route('staff.dosen.index') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-people-fill"></i> Kelola Dosen
                </a>
                <a href="{{ route('staff.dosen.create') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-plus-circle"></i> Tambah Dosen
                </a>
                <a href="{{ route('staff.mahasiswa.index') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-mortarboard-fill"></i> Kelola Mahasiswa
                </a>
                <a href="{{ route('staff.mahasiswa.create') }}" class="list-group-item list-group-item-action">
                    <i class="bi bi-plus-circle"></i> Tambah Mahasiswa
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Ticket Summary (Staff) -->
<div class="row mt-4">
    <div class="col-12">
        <div class="stat-card">
            <h5 style="margin-bottom: 10px;"><i class="bi bi-ticket-fill"></i> Tiket Keluhan Mahasiswa</h5>
            <div style="display:flex;align-items:center;gap:20px;">
                <div>
                    <div style="font-size:28px;font-weight:700">{{ $totalTiket ?? 0 }}</div>
                    <div class="text-muted">Total Tiket</div>
                </div>
                <div>
                    <div style="font-size:28px;font-weight:700;color:#ffc107">{{ $totalTiketDibuka ?? 0 }}</div>
                    <div class="text-muted">Tiket Dibuka</div>
                </div>
                <div style="margin-left:auto">
                    <a href="{{ route('staff.tiket.index') }}" class="btn btn-primary">Lihat Tiket</a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Info Section -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="stat-card">
            <h5 style="margin-bottom: 15px;">
                <i class="bi bi-person-circle"></i> Profil Anda
            </h5>
            <table class="table table-borderless">
                <tr>
                    <td style="width: 30%;"><strong>Nama</strong></td>
                    <td>{{ auth('staff')->user()->name }}</td>
                </tr>
                <tr>
                    <td><strong>Email</strong></td>
                    <td>{{ auth('staff')->user()->email }}</td>
                </tr>
                <tr>
                    <td><strong>Role</strong></td>
                    <td>
                        <span class="badge bg-primary">{{ ucfirst(auth('staff')->user()->role) }}</span>
                    </td>
                </tr>
                <tr>
                    <td><strong>Member Since</strong></td>
                    <td>{{ auth('staff')->user()->created_at->format('d M Y') }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="col-md-6">
        <div class="stat-card">
            <h5 style="margin-bottom: 15px;">
                <i class="bi bi-bell"></i> Informasi Terbaru
            </h5>
            <div class="alert alert-info mb-0">
                <i class="bi bi-info-circle"></i> 
                <small>Selamat datang di Dashboard SIAKAD. Fitur penuh akan segera tersedia.</small>
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
    }

    .list-group-item:hover {
        background-color: #f8f9fa;
        padding-left: 10px;
    }

    .list-group-item:last-child {
        border-bottom: none;
    }

    .table-borderless td {
        padding: 8px 0;
        border: none;
    }
</style>
@endsection
