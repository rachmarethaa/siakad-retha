@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Jadwal Mengajar</h1>

    @foreach($jadwals as $hari => $items)
        <div class="mb-6">
            <h2 class="text-xl font-semibold">{{ $hari }}</h2>
            <ul class="mt-2">
                @foreach($items as $j)
                    <li class="border p-2 my-1">
                        <div class="flex justify-between">
                            <div>
                                <strong>{{ $j->mata_kuliah }}</strong> <span class="text-sm">{{ $j->kode_mk }}</span>
                                <div class="text-sm">{{ $j->dosen?->name }} — {{ $j->prodi }} ({{ $j->semester }})</div>
                            </div>
                            <div class="text-sm">{{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}<br>{{ $j->ruang }}</div>
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    @endforeach

</div>
@endsection
