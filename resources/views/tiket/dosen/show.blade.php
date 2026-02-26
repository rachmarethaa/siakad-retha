@extends('layouts.app')

@section('content')
<div class="container p-6">
    <h2 class="text-xl font-bold mb-4">Tiket #{{ $tiket->id }}</h2>

    @if(session('success'))
        <div class="p-3 bg-green-100 text-green-800 rounded mb-3">{{ session('success') }}</div>
    @endif

    <div class="mb-4">
        <strong>NIM:</strong> {{ optional($tiket->mahasiswa)->nim }}<br>
        <strong>Nama:</strong> {{ optional($tiket->mahasiswa)->name }}
    </div>

    <div class="mb-4">
        <h3 class="font-semibold">Keluhan</h3>
        <p class="p-3 bg-gray-100">{{ $tiket->keluhan }}</p>
    </div>

    @if($tiket->responses && $tiket->responses->count())
        <div class="mb-4">
            <h3 class="font-semibold">Riwayat Balasan</h3>
            <div class="space-y-2 mt-2">
                @foreach($tiket->responses as $r)
                    <div class="p-3 border rounded">
                        <div class="text-sm text-gray-600">Dari: <strong>{{ ucfirst($r->sender_role) }}</strong> @if($r->sender_id) (ID: {{ $r->sender_id }}) @endif — <small>{{ $r->created_at->format('Y-m-d H:i') }}</small></div>
                        <div class="mt-2">{{ $r->message }}</div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="mb-4">
        <h3 class="font-semibold">Jawab Keluhan</h3>
        <form method="POST" action="{{ route('dosen.tiket.respond', $tiket->id) }}">
            @csrf
            <div class="mb-2">
                <textarea name="message" rows="5" required class="w-full p-2 border rounded" placeholder="Tulis jawaban Anda di sini..."></textarea>
            </div>
            <div class="mb-3">
                <label class="block text-sm">Teruskan melalui Staff (opsional)</label>
                <select name="staff_id" class="w-full p-2 border rounded">
                    <option value="">-- Pilih staff (jika ingin staff mengirim ke mahasiswa) --</option>
                    @foreach($staffs as $s)
                        <option value="{{ $s->id }}" @if($tiket->assigned_staff_id == $s->id) selected @endif>{{ $s->name }} ({{ $s->id }})</option>
                    @endforeach
                </select>
            </div>
            <div>
                <button type="submit" class="btn btn-primary">Simpan Jawaban</button>
            </div>
        </form>
    </div>
</div>
@endsection
