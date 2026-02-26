@extends('layouts.app')

@section('page-title', 'Input Presensi - ' . $jadwal->mata_kuliah)

@section('title', 'Input Presensi - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-check2-square"></i> Input Presensi</h1>
        <p class="text-muted">{{ $jadwal->mata_kuliah }} - {{ $jadwal->hari }}</p>
    </div>
    <div>
        <a href="{{ route('dosen.presensi.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="stat-card">
            <h5 style="margin-bottom: 20px;">
                <i class="bi bi-info-circle"></i> Informasi Kelas
            </h5>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-bottom: 25px;">
                <div>
                    <small class="text-muted">Mata Kuliah</small>
                    <div style="font-weight: 600; font-size: 15px;">{{ $jadwal->mata_kuliah }}</div>
                </div>
                <div>
                    <small class="text-muted">Kode MK</small>
                    <div style="font-weight: 600; font-size: 15px;">{{ $jadwal->kode_mk ?? '-' }}</div>
                </div>
                <div>
                    <small class="text-muted">Hari & Jam</small>
                    <div style="font-weight: 600; font-size: 15px;">{{ $jadwal->hari }} {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</div>
                </div>
                <div>
                    <small class="text-muted">Ruang</small>
                    <div style="font-weight: 600; font-size: 15px;">{{ $jadwal->ruang ?? '-' }}</div>
                </div>
                <div>
                    <small class="text-muted">Total Mahasiswa</small>
                    <div style="font-weight: 600; font-size: 15px;">{{ $krs->count() }}</div>
                </div>
            </div>

            <form action="{{ route('dosen.presensi.store', $jadwal->id) }}" method="POST">
                @csrf

                @if($errors->any())
                    <div class="alert alert-danger">
                        <strong>Terjadi kesalahan validasi:</strong>
                        <ul class="mb-0">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <!-- Tanggal -->
                <div class="mb-3">
                    <label for="tanggal" class="form-label">
                        <i class="bi bi-calendar"></i> Tanggal Perkuliahan
                    </label>
                    <input type="date" id="tanggal" name="tanggal" class="form-control" value="{{ $tanggal }}" required>
                    @error('tanggal')
                        <small class="text-danger">{{ $message }}</small>
                    @enderror
                </div>

                <!-- Table Presensi -->
                <div style="overflow-x: auto; margin-bottom: 20px;">
                    <table class="table table-bordered" style="margin-bottom: 0;">
                        <thead style="background-color: #f8f9fa;">
                            <tr>
                                <th style="width: 40px; text-align: center;">No</th>
                                <th>NIM</th>
                                <th>Nama Mahasiswa</th>
                                <th>Prodi</th>
                                <th style="text-align: center; width: 150px;">Status</th>
                                <th style="text-align: center; width: 100px;">Jam Masuk</th>
                                <th style="text-align: center; width: 100px;">Jam Keluar</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($krs as $index => $k)
                                <tr>
                                    <td style="text-align: center; color: #999;">{{ $index + 1 }}</td>
                                    <td style="font-weight: 600;">{{ $k->mahasiswa->nim ?? '-' }}</td>
                                    <td style="font-weight: 600; color: #333;">{{ $k->mahasiswa->name ?? $k->mahasiswa->nama ?? '-' }}</td>
                                    <td style="font-weight: 600;">{{ $k->mahasiswa->prodi ?? '-' }}</td>
                                    <td style="text-align: center;">
                                        <input type="hidden" name="presensi[{{ $index }}][mahasiswa_id]" value="{{ $k->mahasiswa->id }}">
                                        <select name="presensi[{{ $index }}][status]" class="form-select form-select-sm" style="width: 100%;" required>
                                                @php
                                                    $presensiForStudent = $existingPresensi->has($k->mahasiswa->id) ? $existingPresensi[$k->mahasiswa->id] : null;
                                                    $currentStatus = $presensiForStudent ? $presensiForStudent->status : 'absen';
                                                @endphp
                                                <option value="hadir" @if($currentStatus === 'hadir') selected @endif style="color: #28a745;">✓ Hadir</option>
                                                <option value="absen" @if($currentStatus === 'absen') selected @endif style="color: #dc3545;">✗ Absen</option>
                                                <option value="izin" @if($currentStatus === 'izin') selected @endif style="color: #ffc107;">⊘ Izin</option>
                                                <option value="sakit" @if($currentStatus === 'sakit') selected @endif style="color: #17a2b8;">⚠ Sakit</option>
                                        </select>
                                    </td>
                                    <td style="text-align: center;">
                                        <input type="time" name="presensi[{{ $index }}][jam_masuk]" class="form-control form-control-sm" 
                                               value="{{ $presensiForStudent && $presensiForStudent->jam_masuk ? $presensiForStudent->jam_masuk : '' }}"
                                               placeholder="HH:mm">
                                    </td>
                                    <td style="text-align: center;">
                                        <input type="time" name="presensi[{{ $index }}][jam_keluar]" class="form-control form-control-sm" 
                                               value="{{ $presensiForStudent && $presensiForStudent->jam_keluar ? $presensiForStudent->jam_keluar : '' }}"
                                               placeholder="HH:mm">
                                    </td>
                                    <td>
                                        <input type="text" name="presensi[{{ $index }}][keterangan]" class="form-control form-control-sm" placeholder="Keterangan" value="{{ $presensiForStudent && $presensiForStudent->keterangan ? $presensiForStudent->keterangan : '' }}">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="display: flex; gap: 10px; justify-content: flex-end;">
                    <a href="{{ route('dosen.presensi.index') }}" class="btn btn-secondary">Batal</a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Simpan Presensi
                    </button>
                </div>
            </form>
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

    .form-control, .form-select {
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 8px 12px;
        font-size: 14px;
    }

    .form-control:focus, .form-select:focus {
        border-color: #4a90e2;
        outline: none;
        box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.1);
    }

    .table {
        font-size: 13px;
    }

    .btn {
        padding: 8px 16px;
        border-radius: 4px;
        border: none;
        cursor: pointer;
        font-weight: 500;
    }

    .btn-primary {
        background-color: #4a90e2;
        color: white;
    }

    .btn-primary:hover {
        background-color: #3a7bc8;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }
</style>
@endsection
