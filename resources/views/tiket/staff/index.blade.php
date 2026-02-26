@extends('layouts.app')

@section('content')
<div class="container p-6">
    <h2 class="text-xl font-bold mb-4">Daftar Tiket Mahasiswa</h2>

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
                <td class="border p-2">{{ $t->status }}</td>
                <td class="border p-2"><a href="{{ route('staff.tiket.show', $t->id) }}" class="px-2 py-1 bg-blue-600 text-white rounded">Lihat</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
