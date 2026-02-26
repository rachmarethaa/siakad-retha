@extends('layouts.app')

@section('page-title', 'Detail KRS - ' . $krs->jadwalMengajar->mata_kuliah)

@section('title', 'Detail KRS - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-eye"></i> Detail Mata Kuliah</h1>
        <p class="text-muted">{{ $krs->jadwalMengajar->mata_kuliah }}</p>
    </div>
    <div>
        <a href="{{ route('mahasiswa.krs.index') }}" class="btn btn-sm btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="stat-card">
            <h5 style="margin-bottom: 20px;"><i class="bi bi-book"></i> Informasi Mata Kuliah</h5>
            <table class="table table-borderless" style="margin-bottom: 0;">
                <tr>
                    <td style="font-weight: 600; width: 200px;">Mata Kuliah</td>
                    <td>{{ $krs->jadwalMengajar->mata_kuliah }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600;">Kode MK</td>
                    <td>{{ $krs->jadwalMengajar->kode_mk ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600;">Dosen</td>
                    <td>{{ $krs->jadwalMengajar->dosen->name ?? 'TBA' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600;">Program Studi</td>
                    <td>{{ $krs->jadwalMengajar->prodi ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600;">Hari</td>
                    <td>{{ $krs->jadwalMengajar->hari }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600;">Jam</td>
                    <td>{{ \Carbon\Carbon::parse($krs->jadwalMengajar->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($krs->jadwalMengajar->jam_selesai)->format('H:i') }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600;">Ruang</td>
                    <td>{{ $krs->jadwalMengajar->ruang ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600;">Semester</td>
                    <td>{{ $krs->jadwalMengajar->semester ?? '-' }}</td>
                </tr>
                <tr>
                    <td style="font-weight: 600;">Status KRS</td>
                    <td>
                        <span class="badge" style="background-color: #d4edda; color: #155724;">{{ $krs->status }}</span>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: 600;">Diambil Pada</td>
                    <td>{{ $krs->diambil_at ? $krs->diambil_at->format('d/m/Y H:i') : '-' }}</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="stat-card" style="border-left: 4px solid #ffc107;">
            <h5 style="margin-bottom: 15px;"><i class="bi bi-info-circle"></i> Aksi</h5>
            <form action="{{ route('mahasiswa.krs.destroy', $krs->id) }}" method="POST">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger w-100" onclick="return confirm('Batalkan KRS ini?')">
                    <i class="bi bi-trash"></i> Batalkan KRS
                </button>
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

    .table {
        font-size: 13px;
    }

    .badge {
        display: inline-block;
        padding: 6px 12px;
        border-radius: 4px;
        font-weight: 600;
    }
</style>
@endsection
