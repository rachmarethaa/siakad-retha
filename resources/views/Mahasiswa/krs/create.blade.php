@extends('layouts.app')

@section('page-title', 'Tambah Mata Kuliah - KRS')

@section('title', 'Tambah Mata Kuliah - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-plus-circle"></i> Tambah Mata Kuliah ke KRS</h1>
        <p class="text-muted">Pilih mata kuliah yang ingin Anda ambil</p>
    </div>
    <div>
        <a href="{{ route('mahasiswa.krs.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row">
    <div class="col-lg-12">
        <div class="stat-card">
            @if($availableJadwals->count() > 0)
                <div style="overflow-x: auto;">
                    <table class="table table-hover" style="margin-bottom: 0;">
                        <thead style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <tr>
                                <th style="text-align: center; width: 40px;">No</th>
                                <th>Mata Kuliah</th>
                                <th style="text-align: center; width: 100px;">Kode MK</th>
                                <th style="text-align: center; width: 100px;">Prodi</th>
                                <th>Dosen</th>
                                <th style="text-align: center; width: 80px;">Hari</th>
                                <th style="text-align: center; width: 120px;">Jam</th>
                                <th style="text-align: center; width: 80px;">Ruang</th>
                                <th style="text-align: center; width: 60px;">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($availableJadwals as $index => $jadwal)
                                <tr>
                                    <td style="text-align: center; color: #999;">{{ $index + 1 }}</td>
                                    <td style="font-weight: 600; color: #333;">{{ $jadwal->mata_kuliah }}</td>
                                    <td style="text-align: center;">{{ $jadwal->kode_mk ?? '-' }}</td>
                                    <td style="text-align: center;">
                                        <span class="badge badge-info">{{ $jadwal->prodi ?? 'Umum' }}</span>
                                    </td>
                                    <td>{{ $jadwal->dosen->name ?? 'TBA' }}</td>
                                    <td style="text-align: center;">{{ $jadwal->hari }}</td>
                                    <td style="text-align: center; font-size: 13px;">
                                        {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                                    </td>
                                    <td style="text-align: center;">{{ $jadwal->ruang ?? '-' }}</td>
                                    <td style="text-align: center;">
                                        <form action="{{ route('mahasiswa.krs.store') }}" method="POST" style="display: inline;">
                                            @csrf
                                            <input type="hidden" name="jadwal_mengajar_id" value="{{ $jadwal->id }}">
                                            <button type="submit" class="btn btn-sm btn-success" title="Ambil">
                                                <i class="bi bi-check-circle"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div style="text-align: center; padding: 40px; color: #999;">
                    <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.5;"></i>
                    <p style="margin-top: 15px;">Tidak ada mata kuliah yang tersedia untuk diambil</p>
                    <p>Semua mata kuliah sesuai prodi Anda sudah Anda ambil</p>
                    <p><a href="{{ route('mahasiswa.krs.index') }}" class="btn btn-sm btn-primary">Kembali</a></p>
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

@section('extra-css')
<style>
    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        border-left: 4px solid #dee2e6;
    }

    .table {
        font-size: 13px;
    }

    .badge {
        display: inline-block;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 11px;
        font-weight: 600;
    }

    .badge-info {
        background-color: #e7f3ff;
        color: #0066cc;
    }
</style>
@endsection
