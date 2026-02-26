@extends('layouts.app')

@section('content')
<div class="container p-6">
    <h2 class="text-xl font-bold mb-4">Daftar Tiket Saya</h2>
    @if(session('success'))
        <div class="p-3 bg-green-100 text-green-800 rounded mb-3">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="p-3 bg-red-100 text-red-800 rounded mb-3">{{ session('error') }}</div>
    @endif
    <a href="{{ route('tiket.mahasiswa.create') }}" class="px-3 py-2 bg-green-600 text-white rounded mb-4 inline-block">Buat Tiket</a>

    <table class="w-full border-collapse">
        <thead>
            <tr>
                <th class="border p-2">#</th>
                <th class="border p-2">Judul</th>
                <th class="border p-2">Program Studi</th>
                <th class="border p-2">Pesan</th>
                <th class="border p-2">Status</th>
                <th class="border p-2">Dibuat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($tikets as $t)
            <tr>
                <td class="border p-2"><a href="{{ route('tiket.mahasiswa.show', $t->id) }}">{{ $t->id }}</a></td>
                <td class="border p-2">{{ $t->judul }}</td>
                <td class="border p-2">{{ $t->mahasiswa->prodi ?? '-' }}</td>
                <td class="border p-2">
                    @php
                        $hasResponses = \Illuminate\Support\Facades\Schema::hasTable('tiket_responses');
                        $latestStaff = null;
                        if ($hasResponses && isset($t->responses)) {
                            $latestStaff = $t->responses->where('sender_role','staff')->last();
                        }
                    @endphp
                    @if($latestStaff)
                        {{ \Illuminate\Support\Str::limit($latestStaff->message, 80) }}
                    @else
                        -
                    @endif
                </td>
                <td class="border p-2">
                    @if($t->status == 'ditanggapi')
                        <span class="px-2 py-1 bg-green-100 text-green-800 rounded">Ada Balasan</span>
                    @else
                        <span class="px-2 py-1 bg-gray-100 text-gray-800 rounded">{{ $t->status }}</span>
                    @endif
                </td>
                <td class="border p-2">{{ $t->created_at->format('Y-m-d H:i') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
