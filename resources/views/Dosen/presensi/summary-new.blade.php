@extends('layouts.app')

@section('page-title', 'Ringkasan Presensi')

@section('title', 'Ringkasan Presensi - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <a href="{{ route('dosen.presensi.show', $jadwal->id) }}" class="btn btn-sm btn-secondary mb-2">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1><i class="bi bi-graph-up"></i> Ringkasan Presensi Mahasiswa</h1>
        <p class="text-muted">{{ $jadwal->mata_kuliah }} ({{ $jadwal->kode_mk }})</p>
    </div>
</div>

<div class="card">
    <div class="card-body" style="overflow-x: auto;">
        <table class="table table-hover mb-0">
            <thead class="table-light">
                <tr>
                    <th>No</th>
                    <th>NIM</th>
                    <th>Nama Mahasiswa</th>
                    <th style="text-align: center;">Total</th>
                    <th style="text-align: center;">Hadir</th>
                    <th style="text-align: center;">Absen</th>
                    <th style="text-align: center;">Izin</th>
                    <th style="text-align: center;">Sakit</th>
                    <th style="text-align: center;">Persentase</th>
                    <th style="text-align: center;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($summary as $index => $item)
                    @php
                        $statusClass = $item['persentase'] >= 80 ? 'success' : ($item['persentase'] >= 60 ? 'warning' : 'danger');
                        $statusLabel = $item['persentase'] >= 80 ? 'Baik' : ($item['persentase'] >= 60 ? 'Perhatian' : 'Kritis');
                        $bgColor = $statusClass === 'success' ? '#28a745' : ($statusClass === 'warning' ? '#ffc107' : '#dc3545');
                    @endphp
                    <tr>
                        <td>{{ $index + 1 }}</td>
                        <td><strong>{{ $item['mahasiswa']->nim ?? '-' }}</strong></td>
                        <td>{{ $item['mahasiswa']->nama ?? '-' }}</td>
                        <td style="text-align: center;"><strong>{{ $item['total'] }}</strong></td>
                        <td style="text-align: center;">
                            <span class="badge" style="background-color: #28a745;">{{ $item['hadir'] }}</span>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge" style="background-color: #dc3545;">{{ $item['absen'] }}</span>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge" style="background-color: #ffc107; color: #000;">{{ $item['izin'] }}</span>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge" style="background-color: #17a2b8;">{{ $item['sakit'] }}</span>
                        </td>
                        <td style="text-align: center;">
                            <strong>{{ $item['persentase'] }}%</strong>
                            <div class="progress" style="height: 5px; margin-top: 2px;">
                                <div class="progress-bar" style="width: {{ $item['persentase'] }}px; background-color: {{ $bgColor }};"></div>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge bg-{{ $statusClass }}">{{ $statusLabel }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" style="text-align: center; padding: 20px;">
                            <i class="bi bi-inbox"></i> Belum ada data mahasiswa
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
