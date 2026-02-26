@extends('layouts.app')

@section('page-title', 'Edit Presensi')

@section('title', 'Edit Presensi - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-pencil"></i> Edit Presensi</h1>
        <p class="text-muted">{{ $jadwal->mata_kuliah }} - {{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}</p>
    </div>
    <a href="{{ route('dosen.presensi.show', $jadwal->id) }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle"></i> <strong>Terjadi Kesalahan:</strong>
        <ul style="margin-bottom: 0; margin-top: 8px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="stat-card">
    <form action="{{ route('dosen.presensi.update', [$jadwal->id, $tanggal]) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-section">
            <h5 style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #dee2e6;">
                <i class="bi bi-info-circle"></i> Informasi Kelas
            </h5>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mata Kuliah</label>
                    <input type="text" class="form-control" value="{{ $jadwal->mata_kuliah }}" disabled>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Kode MK</label>
                    <input type="text" class="form-control" value="{{ $jadwal->kode_mk }}" disabled>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="text" class="form-control" value="{{ \Carbon\Carbon::parse($tanggal)->format('d-m-Y') }}" disabled>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Hari</label>
                    <input type="text" class="form-control" value="{{ ucfirst($jadwal->hari) }}" disabled>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="form-label">Jam</label>
                    <input type="text" class="form-control" 
                           value="{{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}" 
                           disabled>
                </div>
            </div>
        </div>

        <div class="form-section" style="margin-top: 30px;">
            <h5 style="margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #dee2e6;">
                <i class="bi bi-people"></i> Data Presensi Mahasiswa
            </h5>

            @if($krs->isEmpty())
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i> Tidak ada mahasiswa terdaftar di kelas ini
                </div>
            @else
                <div style="overflow-x: auto;">
                    <table class="table">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th style="width: 30px;"></th>
                                <th>NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th style="width: 150px;">Status</th>
                                <th style="width: 100px;">Jam Masuk</th>
                                <th style="width: 100px;">Jam Keluar</th>
                                <th style="width: 150px;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($krs as $index => $k)
                                @php
                                    $p = $presensiData[$k->mahasiswa_id] ?? null;
                                @endphp
                                <tr>
                                    <td style="vertical-align: middle; text-align: center;">{{ $index + 1 }}</td>
                                    <td style="vertical-align: middle; font-weight: 600;">{{ $k->mahasiswa->nim ?? '-' }}</td>
                                    <td style="vertical-align: middle;">{{ $k->mahasiswa->nama ?? '-' }}</td>
                                    <td>
                                        <select name="presensi[{{ $loop->index }}][status]" class="form-select form-select-sm" required>
                                            <option value="">-- Pilih Status --</option>
                                            <option value="hadir" @selected($p && $p->status === 'hadir')>Hadir</option>
                                            <option value="absen" @selected($p && $p->status === 'absen')>Absen</option>
                                            <option value="izin" @selected($p && $p->status === 'izin')>Izin</option>
                                            <option value="sakit" @selected($p && $p->status === 'sakit')>Sakit</option>
                                        </select>
                                        <input type="hidden" name="presensi[{{ $loop->index }}][mahasiswa_id]" value="{{ $k->mahasiswa_id }}">
                                    </td>
                                    <td>
                                        <input type="time" name="presensi[{{ $loop->index }}][jam_masuk]" 
                                               class="form-control form-control-sm" 
                                               value="{{ $p && $p->jam_masuk ? $p->jam_masuk : '' }}"
                                               placeholder="HH:mm">
                                    </td>
                                    <td>
                                        <input type="time" name="presensi[{{ $loop->index }}][jam_keluar]" 
                                               class="form-control form-control-sm" 
                                               value="{{ $p && $p->jam_keluar ? $p->jam_keluar : '' }}"
                                               placeholder="HH:mm">
                                    </td>
                                    <td>
                                        <input type="text" name="presensi[{{ $loop->index }}][keterangan]" 
                                               class="form-control form-control-sm" 
                                               value="{{ $p && $p->keterangan ? $p->keterangan : '' }}"
                                               placeholder="Catatan (opsional)"
                                               maxlength="255">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <div style="margin-top: 30px; display: flex; gap: 10px;">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-check-circle"></i> Simpan Perubahan
            </button>
            <a href="{{ route('dosen.presensi.show', $jadwal->id) }}" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> Batal
            </a>
        </div>
    </form>
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
    }

    .form-section {
        margin-bottom: 24px;
    }

    .form-control-sm, .form-select-sm {
        font-size: 13px;
        padding: 6px 8px;
    }

    .table {
        margin-bottom: 0;
    }

    .table tbody tr {
        vertical-align: middle;
    }

    .table tbody tr:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .btn {
        padding: 8px 16px;
        font-size: 14px;
    }
</style>
@endsection
