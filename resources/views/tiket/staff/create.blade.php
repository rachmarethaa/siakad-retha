@extends('layouts.app')

@section('page-title', 'Buat Tiket untuk Mahasiswa')

@section('content')
<div class="container p-6">
    <h2 class="text-xl font-bold mb-4">Buat Tiket untuk Mahasiswa</h2>

    @if(session('success'))
        <div class="p-3 bg-green-100 text-green-800 rounded mb-3">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('staff.tiket.store') }}">
        @csrf
        <div class="mb-3">
            <label class="form-label"><strong>Mahasiswa</strong></label>
            <select name="mahasiswa_id" class="form-control" required>
                <option value="">Pilih Mahasiswa...</option>
                @foreach($mahasiswas as $m)
                    <option value="{{ $m->id }}">{{ $m->name }} ({{ $m->nim }})</option>
                @endforeach
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label"><strong>Judul</strong></label>
            <input type="text" name="judul" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label"><strong>Keluhan</strong></label>
            <textarea name="keluhan" rows="6" class="form-control" required></textarea>
        </div>

        <div>
            <button class="btn btn-primary">Kirim Tiket</button>
            <a href="{{ route('staff.tiket.index') }}" class="btn btn-secondary">Batal</a>
        </div>
    </form>
</div>
@endsection
