@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Jadwal Mengajar: {{ $dosen->name }}</h1>

    <form method="GET" class="mb-4 flex gap-2">
        <select name="hari" class="input">
            <option value="">Semua Hari</option>
            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $h)
                <option value="{{ $h }}" @if(request('hari')== $h) selected @endif>{{ $h }}</option>
            @endforeach
        </select>
        <button class="btn">Filter</button>
    </form>

    @if($jadwals->isEmpty())
        <p>Tidak ada jadwal.</p>
    @else
        @foreach($jadwals as $hari => $items)
            <div class="mb-6">
                <h2 class="text-xl font-semibold">{{ $hari }}</h2>
                <ul class="mt-2">
                    @foreach($items as $j)
                        <li class="border p-2 my-1 flex justify-between">
                            <div>
                                <strong>{{ $j->mata_kuliah }}</strong> <span class="text-sm">{{ $j->kode_mk }}</span>
                                <div class="text-sm">Prodi: {{ $j->prodi ?? '-' }} — Semester: {{ $j->semester }}</div>
                            </div>
                            <div class="text-sm">{{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}<br>{{ $j->ruang }}</div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endforeach
    @endif

</div>
@endsection
