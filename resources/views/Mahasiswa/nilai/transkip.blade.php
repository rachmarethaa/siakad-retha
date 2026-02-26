@extends('layouts.app')

@section('page-title', 'Transkip Akademik')

@section('title', 'Transkip Akademik - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-file-pdf"></i> Transkip Akademik</h1>
        <p class="text-muted">Ringkasan akademik dan GPA Anda</p>
    </div>
    <div>
        <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<div class="row">
    <!-- Header Card -->
    <div class="col-lg-12 mb-4">
        <div class="stat-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white;">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px;">
                <div>
                    <div style="font-size: 13px; opacity: 0.9;">Nama Mahasiswa</div>
                    <div style="font-size: 18px; font-weight: 700;">{{ $mahasiswa->name }}</div>
                </div>
                <div>
                    <div style="font-size: 13px; opacity: 0.9;">NIM</div>
                    <div style="font-size: 18px; font-weight: 700;">{{ $mahasiswa->nim ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size: 13px; opacity: 0.9;">Program Studi</div>
                    <div style="font-size: 18px; font-weight: 700;">{{ $mahasiswa->prodi ?? '-' }}</div>
                </div>
                <div>
                    <div style="font-size: 13px; opacity: 0.9;">GPA</div>
                    <div style="font-size: 24px; font-weight: 700;">{{ number_format($gpa, 2) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="col-12">
        <div class="stat-card">
            <h5 style="margin-bottom: 20px; border-bottom: 2px solid #4a90e2; padding-bottom: 15px;">
                <i class="bi bi-list"></i> Daftar Nilai Mata Kuliah
            </h5>

            @if($nilais->isEmpty())
                <div class="alert alert-info mb-0">
                    <i class="bi bi-info-circle"></i> Belum ada nilai yang tercatat
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr style="background-color: #f8f9fa;">
                                <th style="width: 25%;">Mata Kuliah</th>
                                <th style="width: 12%;">Prodi</th>
                                <th style="width: 12%;">Dosen</th>
                                <th style="text-align: center; width: 10%;">Semester</th>
                                <th style="text-align: center; width: 10%;">Nilai Akhir</th>
                                <th style="text-align: center; width: 8%;">Grade</th>
                                <th style="text-align: center; width: 13%;">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($nilais as $nilai)
                                <tr>
                                    <td style="font-weight: 600; color: #333;">
                                        {{ $nilai->jadwalMengajar->mata_kuliah ?? '-' }}
                                        <br>
                                        <small class="text-muted">{{ $nilai->jadwalMengajar->kode_mk ?? '-' }}</small>
                                    </td>
                                    <td>
                                        <span class="badge" style="background-color: #4a90e2; color: white; padding: 6px 10px;">
                                            {{ $nilai->jadwalMengajar->prodi ?? '-' }}
                                        </span>
                                    </td>
                                    <td>{{ $nilai->jadwalMengajar->dosen->name ?? '-' }}</td>
                                    <td style="text-align: center;">
                                        <span class="badge" style="background-color: #17a2b8; color: white; padding: 6px 10px;">{{ $nilai->jadwalMengajar->semester ?? '-' }}</span>
                                    </td>
                                    <td style="text-align: center; font-weight: 700; font-size: 16px;">
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
                                        <span class="badge {{ $gradeClass }}" style="font-size: 13px; padding: 6px 10px;">
                                            {{ $nilai->grade ?? '-' }}
                                        </span>
                                    </td>
                                    <td style="text-align: center; font-size: 13px; color: #666;">
                                        @if($nilai->lulus)
                                            <span class="badge badge-success">Lulus</span>
                                        @else
                                            <span class="badge badge-danger">Tidak Lulus</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Summary -->
                <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #e0e0e0;">
                    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 6px;">
                            <span class="text-muted">Total Mata Kuliah</span>
                            <div style="font-size: 24px; font-weight: 700; color: #333;">{{ $nilais->count() }}</div>
                        </div>
                        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 6px;">
                            <span class="text-muted">Mata Kuliah Lulus</span>
                            <div style="font-size: 24px; font-weight: 700; color: #28a745;">
                                {{ $nilais->where('lulus', true)->count() }}
                            </div>
                        </div>
                        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 6px;">
                            <span class="text-muted">Rata-rata GPA</span>
                            <div style="font-size: 24px; font-weight: 700; color: #4a90e2;">{{ number_format($gpa, 2) }}</div>
                        </div>
                    </div>
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
    }
</style>
@endsection
