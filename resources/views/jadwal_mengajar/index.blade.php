@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Manajemen Jadwal Mengajar</h1>

    <div class="mb-4 flex gap-2">
        <a href="{{ route('staff.jadwal.create') }}" class="btn btn-primary">Tambah Jadwal</a>
        <form method="GET" class="flex gap-2">
            <select name="dosen_id" class="input">
                <option value="">Semua Dosen</option>
                @foreach($dosen as $d)
                    <option value="{{ $d->id }}" @if(request('dosen_id') == $d->id) selected @endif>{{ $d->name }}</option>
                @endforeach
            </select>
            <select name="prodi" class="input">
                <option value="">Semua Prodi</option>
                @foreach($prodis as $code => $label)
                    <option value="{{ $code }}" @if(request('prodi') == $code) selected @endif>{{ $label }}</option>
                @endforeach
            </select>
            <select name="hari" class="input">
                <option value="">Semua Hari</option>
                @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $h)
                    <option value="{{ $h }}" @if(request('hari')== $h) selected @endif>{{ $h }}</option>
                @endforeach
            </select>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari..." class="input" />
            <button class="btn">Filter</button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <table class="table-auto w-full">
        <thead>
            <tr>
                <th>ID</th>
                <th>Mata Kuliah</th>
                <th>Dosen</th>
                <th>Prodi</th>
                <th>Hari</th>
                <th>Jam</th>
                <th>Ruang</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($jadwals as $j)
                <tr>
                    <td>{{ $j->id }}</td>
                    <td>{{ $j->mata_kuliah }} <small class="text-sm">{{ $j->kode_mk }}</small></td>
                    <td>{{ $j->dosen?->name }}</td>
                    <td>{{ config('prodi.'.$j->prodi) ?? $j->prodi ?? '-' }}</td>
                    <td>{{ $j->hari }}</td>
                    <td>{{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}</td>
                    <td>{{ $j->ruang }}</td>
                    <td>
                        <a href="{{ route('staff.jadwal.edit', $j->id) }}" class="btn">Edit</a>
                        <form action="{{ route('staff.jadwal.destroy', $j->id) }}" method="POST" style="display:inline-block">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger" onclick="return confirm('Hapus jadwal?')">Hapus</button>
                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="mt-4">
        {{ $jadwals->links() }}
    </div>
</div>
@endsection
