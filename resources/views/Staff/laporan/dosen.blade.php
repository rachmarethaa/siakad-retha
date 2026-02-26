@extends('layouts.app')

@section('page-title', 'Laporan Dosen')

@section('title', 'Laporan Presensi Dosen - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <a href="{{ route('staff.laporan.dashboard') }}" class="btn btn-sm btn-secondary mb-2">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1><i class="bi bi-person-badge"></i> Laporan Dosen</h1>
        <p class="text-muted">{{ $dosen->nama_lengkap }}</p>
    </div>
</div>

<!-- Dosen Info -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Nama Dosen:</strong> {{ $dosen->nama_lengkap }}</p>
                <p><strong>Email:</strong> {{ $dosen->email }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Jumlah Kelas:</strong> <strong style="color: #3498db;">{{ count($classStats) }}</strong></p>
                <p><strong>Rata-rata Kehadiran:</strong> 
                    @php
                        $avgPercentage = count($classStats) > 0 ? array_sum(array_column($classStats, 'percentage')) / count($classStats) : 0;
                    @endphp
                    <strong style="color: #2ecc71;">{{ round($avgPercentage, 2) }}%</strong>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Class Statistics -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Statistik Presensi Per Kelas</h5>
    </div>
    <div class="card-body">
        <div style="overflow-x: auto;">
            <table class="table table-hover">
                <thead style="background-color: #f8f9fa;">
                    <tr>
                        <th>Mata Kuliah</th>
                        <th>Prodi</th>
                        <th style="text-align: center;">Kode MK</th>
                        <th style="text-align: center;">Mahasiswa</th>
                        <th style="text-align: center;">Total Rekam</th>
                        <th style="text-align: center;">
                            <span class="badge bg-success">Hadir</span>
                        </th>
                        <th style="text-align: center;">
                            <span class="badge bg-danger">Absen</span>
                        </th>
                        <th style="text-align: center;">
                            <span class="badge bg-warning text-dark">Izin</span>
                        </th>
                        <th style="text-align: center;">
                            <span class="badge bg-info">Sakit</span>
                        </th>
                        <th style="text-align: center;">Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($classStats as $stat)
                        <tr>
                            <td>
                                <strong>{{ $stat['jadwal']->mata_kuliah }}</strong><br>
                                <small class="text-muted">{{ $stat['jadwal']->hari }} - {{ \Carbon\Carbon::parse($stat['jadwal']->jam_mulai)->format('H:i') }}</small>
                            </td>
                            <td style="text-align: center;">{{ $stat['jadwal']->prodi ?? '-' }}</td>
                            <td style="text-align: center;">{{ $stat['jadwal']->kode_mk }}</td>
                            <td style="text-align: center; font-weight: 600;">{{ $stat['students'] }}</td>
                            <td style="text-align: center; font-weight: 600;">{{ $stat['total_records'] }}</td>
                            <td style="text-align: center;">
                                <span class="badge bg-success">{{ $stat['hadir'] }}</span>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge bg-danger">{{ $stat['absen'] }}</span>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge bg-warning text-dark">{{ $stat['izin'] }}</span>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge bg-info">{{ $stat['sakit'] }}</span>
                            </td>
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 20px;">
                                <p class="text-muted">Belum ada data kelas</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

