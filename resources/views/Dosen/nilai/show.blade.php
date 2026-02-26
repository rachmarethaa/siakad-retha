@extends('layouts.app')

@section('page-title', 'Detail Nilai - ' . $jadwal->mata_kuliah)

@section('title', 'Detail Nilai - ' . $jadwal->mata_kuliah . ' - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-eye"></i> Detail Nilai Mahasiswa</h1>
        <p class="text-muted">
            <strong>{{ $jadwal->mata_kuliah }}</strong> ({{ $jadwal->kode_mk }})
        </p>
    </div>
    <div>
        <a href="{{ route('dosen.nilai.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<!-- Statistik Card -->
<div class="row g-3 mb-4">
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="stat-card" style="border-left: 4px solid #4a90e2;">
            <div class="stat-card-icon" style="color: #4a90e2;">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-card-title">Total Mahasiswa</div>
            <div class="stat-card-value">{{ $sudah_dinilai }}</div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="stat-card" style="border-left: 4px solid #6c757d;">
            <div class="stat-card-icon" style="color: #6c757d;">
                <i class="bi bi-exclamation-circle-fill"></i>
            </div>
            <div class="stat-card-title">Belum Dinilai</div>
            <div class="stat-card-value">{{ $belum_dinilai }}</div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="stat-card" style="border-left: 4px solid #28a745;">
            <div class="stat-card-icon" style="color: #28a745;">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <div class="stat-card-title">Rata-rata</div>
            <div class="stat-card-value">{{ number_format($rata_rata_nilai ?? 0, 2) }}</div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="stat-card" style="border-left: 4px solid #ffc107;">
            <div class="stat-card-icon" style="color: #ffc107;">
                <i class="bi bi-arrow-up-circle-fill"></i>
            </div>
            <div class="stat-card-title">Tertinggi</div>
            <div class="stat-card-value">{{ number_format($tertinggi ?? 0, 2) }}</div>
        </div>
    </div>
    <div class="col-lg-2 col-md-4 col-sm-6">
        <div class="stat-card" style="border-left: 4px solid #dc3545;">
            <div class="stat-card-icon" style="color: #dc3545;">
                <i class="bi bi-arrow-down-circle-fill"></i>
            </div>
            <div class="stat-card-title">Terendah</div>
            <div class="stat-card-value">{{ number_format($terendah ?? 0, 2) }}</div>
        </div>
    </div>
</div>

<!-- Tabel Nilai -->
<div class="stat-card">
    <div style="overflow-x: auto;">
        <table class="table table-hover" style="margin-bottom: 0;">
            <thead style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <tr>
                    <th style="text-align: center; width: 40px;">No</th>
                    <th>Nama Mahasiswa</th>
                    <th style="text-align: center; width: 80px;">Tugas</th>
                    <th style="text-align: center; width: 80px;">UTS</th>
                    <th style="text-align: center; width: 80px;">UAS</th>
                    <th style="text-align: center; width: 100px;">Nilai Akhir</th>
                    <th style="text-align: center; width: 60px;">Grade</th>
                    <th>Keterangan</th>
                    <th style="text-align: center; width: 60px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($nilais as $index => $nilai)
                    <tr>
                        <td style="text-align: center; color: #999;">{{ $index + 1 }}</td>
                        <td>
                            <div style="font-weight: 600; color: #333;">{{ $nilai->mahasiswa->name }}</div>
                            <div style="font-size: 12px; color: #999;">NIM: {{ $nilai->mahasiswa->nim ?? '-' }}</div>
                        </td>
                        <td style="text-align: center;">
                            {{ $nilai->nilai_tugas !== null ? number_format($nilai->nilai_tugas, 2) : '-' }}
                        </td>
                        <td style="text-align: center;">
                            {{ $nilai->nilai_uts !== null ? number_format($nilai->nilai_uts, 2) : '-' }}
                        </td>
                        <td style="text-align: center;">
                            {{ $nilai->nilai_uas !== null ? number_format($nilai->nilai_uas, 2) : '-' }}
                        </td>
                        <td style="text-align: center; font-weight: 700;">
                            {{ $nilai->nilai_akhir !== null ? number_format($nilai->nilai_akhir, 2) : '-' }}
                        </td>
                        <td style="text-align: center;">
                            @php
                                $gradeClass = match($nilai->grade ?? null) {
                                    'A' => 'badge-success',
                                    'B' => 'badge-primary',
                                    'C' => 'badge-warning',
                                    'D' => 'badge-warning',
                                    'E' => 'badge-danger',
                                    default => 'badge-secondary'
                                };
                            @endphp
                            <span class="badge {{ $gradeClass }}">
                                {{ $nilai->grade ?? '-' }}
                            </span>
                        </td>
                        <td style="font-size: 13px; color: #666;">
                            {{ $nilai->keterangan ?? '-' }}
                        </td>
                        <td style="text-align: center;">
                            <a href="{{ route('dosen.nilai.edit', [$jadwal->id, $nilai->mahasiswa_id]) }}" 
                               class="btn btn-sm btn-warning" 
                               style="padding: 4px 8px; font-size: 12px; background-color: #fd7e14; border-color: #fd7e14; color: white; text-decoration: none;">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" style="text-align: center; padding: 20px; color: #999;">
                            <i class="bi bi-inbox"></i> Tidak ada data nilai
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('extra-css')
<style>
    .stat-card {
        border-left: 4px solid #dee2e6;
    }
    
    .table {
        font-size: 13px;
    }
    
    .badge {
        display: inline-block;
    }
</style>
@endsection

