@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Detail Jadwal</h1>

    <div class="border p-4">
        <h2 class="text-xl">{{ $jadwal->mata_kuliah }} <small>{{ $jadwal->kode_mk }}</small></h2>
        <p>Dosen: {{ $jadwal->dosen?->name }}</p>
        <p>Hari: {{ $jadwal->hari }}</p>
        <p>Jam: {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }}</p>
        <p>Ruang: {{ $jadwal->ruang }}</p>
        <p>Prodi: {{ $jadwal->prodi }}</p>
        <p>Semester: {{ $jadwal->semester }}</p>
    </div>

    <div class="mt-4">
        <a href="{{ route('staff.jadwal.index') }}" class="btn">Kembali</a>
    </div>
</div>
@endsection
