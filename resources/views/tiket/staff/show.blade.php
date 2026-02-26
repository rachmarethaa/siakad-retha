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
    @if(isset($dosens) && $dosens->count())
        <div class="mb-4">
            <h3 class="font-semibold">Teruskan ke Dosen</h3>
            <form method="POST" action="{{ route('staff.tiket.forward', $tiket->id) }}">
                @csrf
                <div style="display:flex; gap:10px; align-items:center;">
                    <select name="dosen_id" required style="padding:8px; border-radius:6px;">
                        <option value="">Pilih Dosen...</option>
                        @foreach($dosens as $d)
                            <option value="{{ $d->id }}" @if($tiket->assigned_dosen_id == $d->id) selected @endif>{{ $d->name }} ({{ $d->nip ?? '-' }})</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary">Teruskan</button>
                </div>
            </form>
            @if($tiket->dosen)
                <p style="margin-top:10px;">Sudah diteruskan ke: <strong>{{ $tiket->dosen->name }}</strong></p>
            @endif
        </div>
    @endif
    
    @php
        $responses = $tiket->responses ?? collect();
        $dosenResponses = $responses->where('sender_role','dosen');
    @endphp

    @if($dosenResponses->count())
        <div class="mb-4">
            <h3 class="font-semibold">Balasan dari Dosen (Perlu diteruskan)</h3>
            <div class="space-y-2 mt-2">
                @foreach($dosenResponses as $r)
                    @php
                        // check if this dosen message has already been forwarded by a staff
                        $alreadyForwarded = $responses->first(function($x) use ($r) {
                            return $x->sender_role === 'staff' && trim($x->message) === trim($r->message);
                        });
                    @endphp
                    <div class="p-3 border rounded">
                        <div class="text-sm text-gray-600">Dari: <strong>Dosen</strong> @if($r->sender_id) (ID: {{ $r->sender_id }}) @endif — <small>{{ $r->created_at->format('Y-m-d H:i') }}</small></div>
                        <div class="mt-2">{{ $r->message }}</div>
                        <div class="mt-3">
                            @if($alreadyForwarded)
                                <span class="px-2 py-1 bg-green-100 text-green-800 rounded">Sudah diteruskan</span>
                            @else
                                <form method="POST" action="{{ route('staff.tiket.send_to_mahasiswa', $tiket->id) }}" style="display:inline-block">
                                    @csrf
                                    <input type="hidden" name="message" value="{{ $r->message }}">
                                    <button type="submit" class="px-3 py-1 bg-blue-600 text-white rounded">Kirim ke Mahasiswa</button>
                                </form>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <div class="mb-4">
        <h3 class="font-semibold">Aksi Staff</h3>
        <p class="text-sm text-gray-600">Balasan dosen disimpan dan menunggu konfirmasi staff untuk dikirim ke mahasiswa. Gunakan tombol "Kirim ke Mahasiswa" pada setiap balasan dosen.</p>
    </div>

    <div class="mb-4">
        <h3 class="font-semibold">Hapus Tiket</h3>
        <p class="text-sm text-gray-600">Hapus tiket ini beserta semua balasan terkait (dari dosen maupun staff).</p>
        <form method="POST" action="{{ route('staff.tiket.destroy', $tiket->id) }}">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded">Hapus Tiket</button>
        </form>
    </div>
</div>
@endsection
