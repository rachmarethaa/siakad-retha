@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h2 class="text-2xl font-semibold mb-4">Tiket #{{ $tiket->id }} - {{ $tiket->judul }}</h2>

    <div class="bg-white shadow rounded p-4 mb-4">
        <p class="text-gray-700">{{ $tiket->keluhan }}</p>
        <p class="text-sm text-gray-500 mt-2">Dibuat: {{ $tiket->created_at->format('Y-m-d H:i') }}</p>
        @if($tiket->isExpired())
            <p class="text-sm text-red-600 mt-2">Tiket ini sudah kadaluarsa.</p>
        @elseif($tiket->expired_at)
            <p class="text-sm text-yellow-600 mt-2">Berakhir pada {{ $tiket->expired_at->format('Y-m-d H:i') }}</p>
        @endif
    </div>

    <div class="bg-white shadow rounded p-4">
        <h3 class="text-lg font-medium mb-3">Riwayat Balasan</h3>
        @php
            $hasResponses = \Illuminate\Support\Facades\Schema::hasTable('tiket_responses');
            // Mahasiswa should only see responses that were forwarded/sent by staff
            $visibleResponses = collect();
            if ($hasResponses) {
                $visibleResponses = $tiket->responses->where('sender_role', 'staff');
            }
        @endphp
        @if($hasResponses && $visibleResponses->count())
            @foreach($visibleResponses as $resp)
                <div class="mb-3 p-3 border rounded">
                    <div class="text-sm text-gray-600">Dari: {{ ucfirst($resp->sender_role) }} - ID #{{ $resp->sender_id }}</div>
                    <div class="mt-2">{{ $resp->message }}</div>
                    <div class="text-xs text-gray-400 mt-1">{{ $resp->created_at->format('Y-m-d H:i') }}</div>
                </div>
            @endforeach
        @else
            <p class="text-gray-500">Belum ada balasan.</p>
        @endif
    </div>

    {{-- Debug: show raw responses when APP_DEBUG=true to help troubleshooting --}}
    @if(config('app.debug'))
        <div class="mt-4 p-3 bg-gray-50 border rounded">
            <h4 class="font-semibold">DEBUG: All responses (raw)</h4>
            <pre class="text-xs overflow-auto" style="max-height:200px">{{ json_encode($tiket->responses->map(function($r){ return ['id'=>$r->id,'role'=>$r->sender_role,'sender'=>$r->sender_id,'message'=>$r->message,'created'=>$r->created_at]; }), JSON_PRETTY_PRINT) }}</pre>
        </div>
    @endif

    <div class="mt-4">
        <a href="{{ route('tiket.mahasiswa.index') }}" class="inline-block px-4 py-2 bg-gray-200 text-gray-800 rounded">Kembali</a>
    </div>
</div>
@endsection
