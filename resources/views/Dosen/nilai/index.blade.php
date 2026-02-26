@extends('layouts.app')

@section('page-title', 'Input Nilai Mahasiswa')

@section('title', 'Input Nilai Mahasiswa - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-pencil-square"></i> Input Nilai Mahasiswa</h1>
        <p class="text-muted">Kelola dan input nilai untuk setiap kelas yang Anda ajar</p>
    </div>
</div>

@if($message = Session::get('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle"></i> {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<div class="row g-4">
    @if($jadwals->isEmpty())
        <div class="col-12">
            <div class="stat-card">
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i> Anda belum memiliki jadwal mengajar
                </div>
            </div>
        </div>
    @else
        @foreach($jadwals as $jadwal)
            <div class="col-lg-6 col-md-6 col-sm-12">
                <div class="stat-card">
                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                        <div style="flex: 1;">
                            <h5 style="margin: 0 0 5px 0; font-weight: 700; color: #333;">
                                {{ $jadwal->mata_kuliah }}
                            </h5>
                            <p style="margin: 0; font-size: 13px; color: #777;">
                                <i class="bi bi-tag"></i> {{ $jadwal->kode_mk }}
                            </p>
                        </div>
                        <span class="badge" style="background-color: #4a90e2; color: white; padding: 6px 12px; font-size: 12px;">
                            {{ $jadwal->prodi ?? 'N/A' }}
                        </span>
                    </div>

                    <div style="border-top: 1px solid #e0e0e0; padding-top: 12px; margin-bottom: 12px;">
                        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; font-size: 13px;">
                            <div>
                                <span class="text-muted"><i class="bi bi-clock"></i> Jam:</span>
                                <br>{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}
                            </div>
                            <div>
                                <span class="text-muted"><i class="bi bi-door"></i> Ruang:</span>
                                <br>{{ $jadwal->ruang }}
                            </div>
                            <div>
                                <span class="text-muted"><i class="bi bi-calendar-week"></i> Hari:</span>
                                <br>{{ ucfirst(str_replace(['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu', 'minggu'], 
                                        ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'], strtolower($jadwal->hari))) }}
                            </div>
                            <div>
                                <span class="text-muted"><i class="bi bi-book"></i> Semester:</span>
                                <br>{{ $jadwal->semester }}
                            </div>
                        </div>
                    </div>

                    <div style="background-color: #f8f9fa; padding: 10px 12px; border-radius: 6px; margin-bottom: 15px; font-size: 13px;">
                        <div style="display: flex; justify-content: space-between;">
                            <span>
                                <i class="bi bi-check-circle-fill" style="color: #28a745;"></i>
                                <strong>{{ $jadwal->nilai()->count() }}</strong> Terisi
                            </span>
                        </div>
                    </div>

                    <div style="display: flex; gap: 8px;">
                        <a href="{{ route('dosen.nilai.create', $jadwal->id) }}" class="btn btn-sm btn-primary flex-grow-1">
                            <i class="bi bi-plus-lg"></i> Input Nilai
                        </a>
                        <a href="{{ route('dosen.nilai.show', $jadwal->id) }}" class="btn btn-sm btn-info flex-grow-1">
                            <i class="bi bi-eye"></i> Lihat Detail
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection

@section('extra-css')
<style>
    .badge {
        font-weight: 500;
    }
    
    .btn-sm {
        padding: 8px 12px;
        font-size: 13px;
        border-radius: 6px;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background-color: #4a90e2;
        border-color: #4a90e2;
        color: white;
    }
    
    .btn-primary:hover {
        background-color: #1565c0;
        border-color: #1565c0;
        text-decoration: none;
        color: white;
    }
    
    .btn-info {
        background-color: #17a2b8;
        border-color: #17a2b8;
        color: white;
    }
    
    .btn-info:hover {
        background-color: #138496;
        border-color: #138496;
        text-decoration: none;
        color: white;
    }

    .flex-grow-1 {
        flex: 1;
    }
</style>
@endsection
