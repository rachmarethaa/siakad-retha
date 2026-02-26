@extends('layouts.app')

@section('page-title', 'Jadwal Kuliah')

@section('title', 'Jadwal Kuliah - SIAKAD')

@section('content')
<div class="page-header">
    <h1>Jadwal Kuliah</h1>
    <p class="text-muted">Lihat jadwal kuliah untuk semester ini</p>
</div>

<div class="mb-4 flex gap-2">
    <form method="GET" class="flex gap-2">
        <select name="hari" class="input">
            <option value="">Semua Hari</option>
            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $h)
                <option value="{{ $h }}" @if(request('hari') == $h) selected @endif>{{ $h }}</option>
            @endforeach
        </select>
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari mata kuliah..." class="input" />
        <button class="btn btn-primary">Filter</button>
    </form>
</div>

@if($jadwals->isEmpty())
    <div class="alert alert-info">
        <i class="bi bi-info-circle"></i> Tidak ada jadwal kuliah untuk semester ini
    </div>
@else
    <div style="display: grid; gap: 24px;">
        @foreach($jadwals as $hari => $items)
            <div style="background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05);">
                <h5 style="color: #4a90e2; font-weight: 600; margin-bottom: 16px; display: flex; align-items: center;">
                    <i class="bi bi-calendar-event" style="margin-right: 8px;"></i> {{ $hari }}
                </h5>
                <div style="display: grid; gap: 12px;">
                    @foreach($items as $j)
                        <div style="border-left: 4px solid #4a90e2; padding: 12px 16px; background: #f8f9fa; border-radius: 4px;">
                            <div style="display: grid; gap: 8px;">
                                <div>
                                    <div style="font-weight: 600; font-size: 15px; color: #333;">{{ $j->mata_kuliah }}</div>
                                    <div style="font-size: 12px; color: #777;">Kode: {{ $j->kode_mk ?? '-' }}</div>
                                </div>
                                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 13px; color: #555;">
                                    <div>
                                        <i class="bi bi-person-fill" style="color: #4a90e2;"></i>
                                        <span>{{ $j->dosen?->name ?? 'TBA' }}</span>
                                    </div>
                                    <div>
                                        <i class="bi bi-clock" style="color: #4a90e2;"></i>
                                        <span>{{ \Carbon\Carbon::parse($j->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($j->jam_selesai)->format('H:i') }}</span>
                                    </div>
                                    <div>
                                        <i class="bi bi-door" style="color: #4a90e2;"></i>
                                        <span>Ruang {{ $j->ruang }}</span>
                                    </div>
                                    <div>
                                        <i class="bi bi-tag" style="color: #4a90e2;"></i>
                                        <span>Prodi {{ $j->prodi ?? '-' }}<br>Semester {{ $j->semester }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>
@endif

<style>
    .input, .btn {
        padding: 8px 12px;
        border: 1px solid #ddd;
        border-radius: 4px;
        font-size: 14px;
    }
    .btn {
        background: #4a90e2;
        color: white;
        cursor: pointer;
    }
    .btn:hover {
        background: #3a7bc8;
    }
    .alert {
        padding: 12px 16px;
        border-radius: 4px;
        border: 1px solid #cfe2ff;
        background: #cfe2ff;
        color: #084298;
    }
</style>
@endsection
