@extends('layouts.app')

@section('content')
<div class="container p-6">
    <h2 class="text-xl font-bold mb-4">Daftar Tiket (Dosen)</h2>
    @if(session('success'))
        <div class="p-3 bg-green-100 text-green-800 rounded mb-3">{{ session('success') }}</div>
    @endif
    <table class="w-full border-collapse">
        <thead>
            <tr>
                <th class="border p-2">#</th>
                <th class="border p-2">Judul</th>
                <th class="border p-2">Mahasiswa</th>
                <th class="border p-2">Program Studi</th>
                <th class="border p-2">Status</th>
                <th class="border p-2">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tikets as $t)
            <tr>
                <td class="border p-2">{{ $t->id }}</td>
                <td class="border p-2">{{ $t->judul }}</td>
                <td class="border p-2">{{ optional($t->mahasiswa)->name }}</td>
                <td class="border p-2">{{ optional($t->mahasiswa)->prodi ?? '-' }}</td>
                <td class="border p-2">
                    @if($t->status == 'diteruskan')
                        <span class="px-2 py-1 bg-yellow-100 text-yellow-800 rounded">Dikirim oleh Staff</span>
                    @elseif($t->status == 'diproses')
                        <span class="px-2 py-1 bg-blue-100 text-blue-800 rounded">Diproses</span>
                    @elseif($t->status == 'ditanggapi')
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded">Sudah Ditanggapi</span>
                    @else
                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded">{{ $t->status }}</span>
                    @endif
                </td>
                <td class="border p-2">
                    <a href="{{ route('dosen.tiket.show', $t->id) }}" class="px-3 py-1 bg-indigo-600 text-white rounded">Buka & Balas</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
