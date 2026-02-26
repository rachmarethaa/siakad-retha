@extends('layouts.app')

@section('page-title', 'Laporan Kelas')

@section('title', 'Laporan Presensi Kelas - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <a href="{{ route('dosen.laporan.dashboard') }}" class="btn btn-sm btn-secondary mb-2">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1><i class="bi bi-graph-up"></i> Laporan Kelas</h1>
        <p class="text-muted">{{ $jadwal->mata_kuliah }} ({{ $jadwal->kode_mk }})</p>
    </div>
</div>

<!-- Export Options -->
<div class="mb-3">
    <a href="{{ route('dosen.laporan.excel', $jadwal->id) }}" class="btn btn-success">
        <i class="bi bi-file-earmark-excel"></i> Export Excel
    </a>
    <a href="{{ route('dosen.laporan.pdf', $jadwal->id) }}" class="btn btn-danger">
        <i class="bi bi-file-earmark-pdf"></i> Export PDF
    </a>
</div>

<!-- Class Info Card -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Mata Kuliah:</strong> {{ $jadwal->mata_kuliah }}</p>
                <p><strong>Kode MK:</strong> {{ $jadwal->kode_mk }}</p>
                <p><strong>Dosen:</strong> {{ $dosen->nama_lengkap ?? $dosen->name }}</p>
            </div>
            <div class="col-md-6">
                <p><strong>Hari:</strong> {{ ucfirst($jadwal->hari) }}</p>
                <p><strong>Jam:</strong> {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</p>
                <p><strong>Ruang:</strong> {{ $jadwal->ruang ?? '-' }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Student Attendance Report -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Laporan Kehadiran Mahasiswa</h5>
    </div>
    <div class="card-body">
        <div style="overflow-x: auto;">
            <table class="table table-hover">
                <thead style="background-color: #f8f9fa;">
                    <tr>
                        <th style="width: 30px;">#</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Prodi</th>
                        <th style="text-align: center;">Total</th>
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
                    @forelse($studentStats as $index => $stat)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $stat['nim'] }}</td>
                            <td>{{ $stat['mahasiswa']->name ?? $stat['mahasiswa']->nama ?? '-' }}</td>
                            <td>{{ $stat['mahasiswa']->prodi ?? '-' }}</td>
                            <td style="text-align: center; font-weight: 600;">{{ $stat['total'] }}</td>
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
                                <p class="text-muted">Belum ada data mahasiswa</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection
