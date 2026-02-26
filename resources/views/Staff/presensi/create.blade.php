@extends('layouts.app')

@section('page-title', 'Input Presensi - ' . $jadwal->mata_kuliah)

@section('title', 'Input Presensi - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-check2-square"></i> Input Presensi (Staff)</h1>
        <p class="text-muted">{{ $jadwal->mata_kuliah }} - {{ $jadwal->hari }}</p>
    </div>
    <div>
        <a href="{{ route('staff.presensi.index') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<div class="stat-card">
    <form action="{{ route('staff.presensi.store', $jadwal->id) }}" method="POST">
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

        <div class="mb-3">
            <label for="tanggal" class="form-label">Tanggal Perkuliahan</label>
            <input type="date" id="tanggal" name="tanggal" class="form-control" value="{{ $tanggal }}" required>
        </div>

        <div style="overflow-x: auto; margin-bottom: 20px;">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>NIM</th>
                        <th>Nama</th>
                        <th>Status</th>
                        <th>Jam Masuk</th>
                        <th>Jam Keluar</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($krs as $index => $k)
                        @php
                            $presensiForStudent = $existingPresensi->has($k->mahasiswa->id) ? $existingPresensi[$k->mahasiswa->id] : null;
                        @endphp
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $k->mahasiswa->nim ?? '-' }}</td>
                            <td>{{ $k->mahasiswa->name ?? $k->mahasiswa->nama ?? '-' }}</td>
                            <td>
                                <input type="hidden" name="presensi[{{ $index }}][mahasiswa_id]" value="{{ $k->mahasiswa->id }}">
                                <select name="presensi[{{ $index }}][status]" class="form-select form-select-sm" required>
                                    @php $cur = $presensiForStudent ? $presensiForStudent->status : 'absen'; @endphp
                                    <option value="hadir" @if($cur === 'hadir') selected @endif>Hadir</option>
                                    <option value="absen" @if($cur === 'absen') selected @endif>Absen</option>
                                    <option value="izin" @if($cur === 'izin') selected @endif>Izin</option>
                                    <option value="sakit" @if($cur === 'sakit') selected @endif>Sakit</option>
                                </select>
                            </td>
                            <td>
                                <input type="time" name="presensi[{{ $index }}][jam_masuk]" class="form-control form-control-sm" value="{{ $presensiForStudent && $presensiForStudent->jam_masuk ? $presensiForStudent->jam_masuk : '' }}">
                            </td>
                            <td>
                                <input type="time" name="presensi[{{ $index }}][jam_keluar]" class="form-control form-control-sm" value="{{ $presensiForStudent && $presensiForStudent->jam_keluar ? $presensiForStudent->jam_keluar : '' }}">
                            </td>
                            <td>
                                <input type="text" name="presensi[{{ $index }}][keterangan]" class="form-control form-control-sm" value="{{ $presensiForStudent && $presensiForStudent->keterangan ? $presensiForStudent->keterangan : '' }}">
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div style="display:flex; gap:10px; justify-content:flex-end;">
            <a href="{{ route('staff.presensi.index') }}" class="btn btn-secondary">Batal</a>
            <button type="submit" class="btn btn-primary">Simpan Presensi</button>
        </div>
    </form>
</div>

@endsection
