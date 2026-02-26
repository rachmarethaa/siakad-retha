@extends('layouts.app')

@section('page-title', 'Nilai Saya')

@section('title', 'Nilai Saya - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-file-earmark-text"></i> Nilai Saya</h1>
        <p class="text-muted">Lihat semua nilai mata kuliah Anda</p>
    </div>
    <div>
        <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="stat-card">
            @if($nilais->isEmpty())
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i> Anda belum memiliki nilai
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr style="background-color: #f8f9fa;">
                                <th>Mata Kuliah</th>
                                <th>Kode MK</th>
                                <th>Program Studi</th>
                                <th>Dosen</th>
                                <th style="text-align: center;">Semester</th>
                                <th style="text-align: center;">UTS</th>
                                <th style="text-align: center;">UAS</th>
                                <th style="text-align: center;">Akhir</th>
                                <th style="text-align: center;">Grade</th>
                                <th style="text-align: center;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($nilais as $nilai)
                                <tr>
                                    <td style="font-weight: 600; color: #333;">{{ $nilai->jadwalMengajar->mata_kuliah ?? '-' }}</td>
                                    <td>{{ $nilai->jadwalMengajar->kode_mk ?? '-' }}</td>
                                    <td>
                                        <span class="badge" style="background-color: #4a90e2; color: white;">
                                            {{ $nilai->jadwalMengajar->prodi ?? '-' }}
                                        </span>
                                    </td>
                                    <td>{{ $nilai->jadwalMengajar->dosen->name ?? '-' }}</td>
                                    <td style="text-align: center;">
                                        <span class="badge" style="background-color: #17a2b8; color: white;">{{ $nilai->jadwalMengajar->semester ?? '-' }}</span>
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
                                    <td style="text-align: center; font-size: 13px;">
                                        {{ $nilai->keterangan ?? '-' }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-4">
                    {{ $nilais->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
    .table-hover tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .badge {
        font-weight: 500;
        padding: 6px 10px;
        font-size: 12px;
    }
</style>
@endsection
