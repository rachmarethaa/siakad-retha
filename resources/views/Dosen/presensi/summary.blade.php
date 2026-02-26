@extends('layouts.app')

@section('page-title', 'Ringkasan Presensi')

@section('title', 'Ringkasan Presensi - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <a href="{{ route('dosen.presensi.show', $jadwal->id) }}" class="btn btn-sm btn-secondary mb-2">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1><i class="bi bi-graph-up"></i> Ringkasan Presensi Per Mahasiswa</h1>
        <p class="text-muted">{{ $jadwal->mata_kuliah }} ({{ $jadwal->kode_mk }})</p>
    </div>
</div>

<div class="stat-card">
    <div style="overflow-x: auto;">
        <table class="table table-hover mb-0">
            <thead style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6; position: sticky; top: 0;">
                <tr>
                    <th style="width: 50px;">No</th>
                    <th style="width: 120px;">NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Prodi</th>
                    <th style="text-align: center; width: 80px;">Total</th>
                    <th style="text-align: center; width: 80px;">Hadir</th>
                    <th style="text-align: center; width: 80px;">Absen</th>
                    <th style="text-align: center; width: 80px;">Izin</th>
                    <th style="text-align: center; width: 80px;">Sakit</th>
                    <th style="text-align: center; width: 100px;">Persentase</th>
                    <th style="text-align: center; width: 100px;">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($summary as $index => $item)
                    @php
                        $statusClass = $item['persentase'] >= 80 ? 'success' : ($item['persentase'] >= 60 ? 'warning' : 'danger');
                        $statusLabel = $item['persentase'] >= 80 ? 'Baik' : ($item['persentase'] >= 60 ? 'Perlu Perhatian' : 'Kritis');
                    @endphp
                    <tr @if($item['persentase'] < 80) style="background-color: rgba(255, 193, 7, 0.05);" @endif>
                        <td style="font-weight: 600;">{{ $index + 1 }}</td>
                        <td style="font-weight: 600;">{{ $item['mahasiswa']->nim ?? '-' }}</td>
                        <td>{{ $item['mahasiswa']->name ?? $item['mahasiswa']->nama ?? '-' }}</td>
                        <td>{{ $item['mahasiswa']->prodi ?? '-' }}</td>
                        <td style="text-align: center; font-weight: 600;">{{ $item['total'] }}</td>
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
                            <strong style="font-size: 16px;">{{ $item['persentase'] }}%</strong>
                            <div class="progress" style="height: 6px; margin-top: 4px;">
                                @php
                                    $bgColor = $statusClass === 'success' ? '#28a745' : ($statusClass === 'warning' ? '#ffc107' : '#dc3545');
                                @endphp
                                <div class="progress-bar" style="width: {{ $item['persentase'] }}%; background-color: {{ $bgColor }};"></div>
                            </div>
                        </td>
                        <td style="text-align: center;">
                            <span class="badge bg-{{ $statusClass }}">{{ $statusLabel }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="11" style="text-align: center; padding: 20px;">
                            <i class="bi bi-inbox"></i> Belum ada data mahasiswa
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Legend -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="stat-card">
            <h6 style="margin-bottom: 16px;"><i class="bi bi-info-circle"></i> Keterangan Status</h6>
            <div style="display: flex; gap: 24px; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="badge bg-success">Baik</span>
                    <small>Kehadiran ≥ 80%</small>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="badge bg-warning">Perlu Perhatian</span>
                    <small>Kehadiran 60-79%</small>
                </div>
                <div style="display: flex; align-items: center; gap: 8px;">
                    <span class="badge bg-danger">Kritis</span>
                    <small>Kehadiran < 60%</small>
                </div>
            </div>
        </div>
    </div>
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
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 8px;
    }

    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 24px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin-bottom: 24px;
    }

    .table {
        margin-bottom: 0;
    }

    .table tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02) !important;
    }

    .progress {
        background-color: #e9ecef;
        border-radius: 2px;
    }

    .badge {
        padding: 4px 8px;
        font-size: 11px;
        font-weight: 600;
    }
</style>
@endsection
