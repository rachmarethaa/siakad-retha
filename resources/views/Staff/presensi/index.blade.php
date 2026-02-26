@extends('layouts.app')

@section('page-title', 'Manajemen Presensi - Staff')

@section('title', 'Manajemen Presensi - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-check2-square"></i> Manajemen Presensi (Staff)</h1>
        <p class="text-muted">Pilih jadwal untuk input presensi</p>
    </div>
</div>

<div class="stat-card">
    @if($jadwals->isEmpty())
        <div class="alert alert-info">Belum ada jadwal mengajar</div>
    @else
        <div style="overflow-x:auto;">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Kode MK</th>
                        <th>Mata Kuliah</th>
                        <th>Dosen</th>
                        <th>Hari</th>
                        <th>Jam</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($jadwals as $j)
                        <tr>
                            <td>{{ $j->id }}</td>
                            <td>{{ $j->kode_mk ?? '-' }}</td>
                            <td>{{ $j->mata_kuliah ?? '-' }}</td>
                            <td>{{ $j->dosen->nama_lengkap ?? $j->dosen->name ?? 'TBA' }}</td>
                            <td>{{ ucfirst($j->hari) }}</td>
                            <td>{{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}</td>
                            <td>
                                <a href="{{ route('staff.presensi.create', $j->id) }}" class="btn btn-sm btn-primary">Input Presensi</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>

@endsection
