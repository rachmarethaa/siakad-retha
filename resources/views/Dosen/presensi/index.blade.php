@extends('layouts.app')

@section('page-title', 'Manajemen Presensi')

@section('title', 'Manajemen Presensi - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-check2-square"></i> Manajemen Presensi</h1>
        <p class="text-muted">Kelola presensi mahasiswa untuk setiap kelas yang Anda ajar</p>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row mb-4">
    <div class="col-md-4">
        <div class="stat-card">
            <div class="stat-icon" style="background-color: rgba(52, 152, 219, 0.1);">
                <i class="bi bi-book" style="color: #3498db;"></i>
            </div>
            <div class="stat-content">
                <h3>{{ $jadwals->count() }}</h3>
                <p>Total Kelas</p>
            </div>
        </div>
    </div>
</div>

<div class="stat-card">
    @if($jadwals->isEmpty())
        <div class="alert alert-info mb-0">
            <i class="bi bi-info-circle"></i> Anda tidak memiliki jadwal mengajar saat ini
        </div>
    @else
        <div style="overflow-x: auto;">
            <table class="table table-hover mb-0">
                <thead style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <tr>
                        <th>Mata Kuliah</th>
                        <th style="text-align: center; width: 100px;">Kode MK</th>
                        <th style="text-align: center; width: 100px;">Hari</th>
                        <th style="text-align: center; width: 100px;">Jam</th>
                        <th style="text-align: center; width: 80px;">Ruang</th>
                        <th style="text-align: center; width: 80px;">Mahasiswa</th>
                        <th style="text-align: center; width: 100px;">Pertemuan</th>
                        <th style="text-align: center; width: 180px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwals as $jadwal)
                        <tr>
                            <td>
                                <div style="font-weight: 600; color: #333;">{{ $jadwal->mata_kuliah }}</div>
                                <small class="text-muted">{{ $jadwal->prodi }}</small>
                            </td>
                            <td style="text-align: center; font-weight: 600;">{{ $jadwal->kode_mk ?? '-' }}</td>
                            <td style="text-align: center;">
                                <span class="badge" style="background-color: #6c757d;">{{ ucfirst($jadwal->hari) }}</span>
                            </td>
                            <td style="text-align: center; font-size: 13px;">
                                {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }}<br>
                                <small>s.d {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</small>
                            </td>
                            <td style="text-align: center;">{{ $jadwal->ruang ?? '-' }}</td>
                            <td style="text-align: center; font-weight: 600;">{{ $jadwal->total_students ?? 0 }}</td>
                            <td style="text-align: center;">{{ $jadwal->presensi_dates ?? 0 }}</td>
                            <td style="text-align: center;">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('dosen.presensi.create', $jadwal->id) }}" class="btn btn-sm btn-primary" title="Input Presensi Hari Ini">
                                        <i class="bi bi-plus-circle"></i> Input
                                    </a>
                                    <a href="{{ route('dosen.presensi.show', $jadwal->id) }}" class="btn btn-sm btn-info" title="Lihat Riwayat">
                                        <i class="bi bi-eye"></i> Riwayat
                                    </a>
                                    <a href="{{ route('dosen.presensi.summary', $jadwal->id) }}" class="btn btn-sm btn-warning" title="Ringkasan Per Mahasiswa">
                                        <i class="bi bi-graph-up"></i> Ringkasan
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
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
        width: 60px;
        height: 60px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 12px;
    }

    .btn-group .btn {
        padding: 6px 10px;
        font-size: 12px;
    }

    .table {
        margin-bottom: 0;
    }

    .table tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }
</style>
@endsection
