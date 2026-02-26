@extends('layouts.app')

@section('page-title', 'KRS - Kartu Rencana Studi')

@section('title', 'KRS - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-clipboard-check"></i> Kartu Rencana Studi (KRS)</h1>
        <p class="text-muted">Kelola mata kuliah yang ingin Anda ambil</p>
    </div>
    <div>
        <a href="{{ route('mahasiswa.krs.create') }}" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Mata Kuliah
        </a>
    </div>
</div>

@if (session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="bi bi-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- KRS Stats -->
<div class="row g-3 mb-4">
    <div class="col-lg-3 col-md-6">
        <div class="stat-card">
            <div class="stat-card-icon" style="color: #4a90e2;">
                <i class="bi bi-book-fill"></i>
            </div>
            <div class="stat-card-title">Total Mata Kuliah</div>
            <div class="stat-card-value">{{ $krs->count() }}</div>
        </div>
    </div>
</div>

<!-- KRS Table -->
<div class="stat-card">
    @if($krs->count() > 0)
        <div style="overflow-x: auto;">
            <table class="table table-hover" style="margin-bottom: 0;">
                <thead style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                    <tr>
                        <th style="text-align: center; width: 40px;">No</th>
                        <th>Mata Kuliah</th>
                        <th style="text-align: center; width: 100px;">Kode MK</th>
                        <th>Dosen</th>
                        <th style="text-align: center; width: 70px;">Semester</th>
                        <th style="text-align: center; width: 80px;">Hari</th>
                        <th style="text-align: center; width: 120px;">Jam</th>
                        <th style="text-align: center; width: 80px;">Ruang</th>
                        <th style="text-align: center; width: 60px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($krs as $index => $k)
                        <tr>
                            <td style="text-align: center; color: #999;">{{ $index + 1 }}</td>
                            <td style="font-weight: 600; color: #333;">{{ $k->jadwalMengajar->mata_kuliah }}</td>
                            <td style="text-align: center;">{{ $k->jadwalMengajar->kode_mk ?? '-' }}</td>
                            <td>{{ $k->jadwalMengajar->dosen->name ?? 'TBA' }}</td>
                            <td style="text-align: center;">
                                <span class="badge" style="background-color: #4a90e2; color: white;">{{ $k->jadwalMengajar->semester ?? '-' }}</span>
                            </td>
                            <td style="text-align: center;">{{ $k->jadwalMengajar->hari }}</td>
                            <td style="text-align: center; font-size: 13px;">
                                {{ \Carbon\Carbon::parse($k->jadwalMengajar->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($k->jadwalMengajar->jam_selesai)->format('H:i') }}
                            </td>
                            <td style="text-align: center;">{{ $k->jadwalMengajar->ruang ?? '-' }}</td>
                            <td style="text-align: center;">
                                <form action="{{ route('mahasiswa.krs.destroy', $k->id) }}" method="POST" style="display: inline;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Batalkan KRS ini?')" title="Batalkan">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div style="padding: 15px; border-top: 1px solid #dee2e6;">
            {{ $krs->links() }}
        </div>
    @else
        <div style="text-align: center; padding: 40px; color: #999;">
            <i class="bi bi-inbox" style="font-size: 48px; opacity: 0.5;"></i>
            <p style="margin-top: 15px;">Anda belum mengambil mata kuliah apapun</p>
            <p><a href="{{ route('mahasiswa.krs.create') }}" class="btn btn-sm btn-primary">Tambah Mata Kuliah</a></p>
        </div>
    @endif
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

    .table {
        font-size: 13px;
    }
</style>
@endsection
