@extends('layouts.app')

@section('page-title', 'Buat Tiket Keluhan')

@section('content')
<div class="page-header">
    <div>
        <h1>Buat Tiket Keluhan</h1>
        <p class="text-muted">Kirimkan keluhan Anda; staff akan menindaklanjuti.</p>
    </div>
    <div>
        <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-secondary">Kembali</a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="stat-card">
            @if(session('success'))
                <div class="p-3 bg-green-100 text-green-800 rounded mb-3">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="p-3 bg-red-100 text-red-800 rounded mb-3">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="p-3 bg-yellow-100 text-yellow-800 rounded mb-3">
                    <ul style="margin:0; padding-left: 18px;">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('tiket.mahasiswa.store') }}">
                @csrf

                <div class="mb-3">
                    <label class="form-label"><strong>Nama</strong></label>
                    <input type="text" class="form-control" value="{{ auth('mahasiswa')->user()->name }}" disabled readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>NIM</strong></label>
                    <input type="text" class="form-control" value="{{ auth('mahasiswa')->user()->nim ?? ' - ' }}" disabled readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Program Studi</strong></label>
                    <input type="text" class="form-control" value="{{ auth('mahasiswa')->user()->prodi ?? ' - ' }}" disabled readonly>
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Judul</strong></label>
                    <input type="text" name="judul" class="form-control @error('judul') is-invalid @enderror" value="{{ old('judul') }}" required>
                    @error('judul')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mb-3">
                    <label class="form-label"><strong>Keluhan</strong></label>
                    <textarea name="keluhan" rows="6" class="form-control @error('keluhan') is-invalid @enderror" required>{{ old('keluhan') }}</textarea>
                    @error('keluhan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                </div>

                <div class="mt-3">
                    <button class="btn btn-primary">Kirim Keluhan</button>
                </div>
            </form>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="stat-card">
            <h5>Petunjuk</h5>
            <p class="text-muted">Tuliskan keluhan Anda secara jelas dan singkat. Staff akan menindaklanjuti sesuai SLA.</p>
        </div>
    </div>
</div>

@endsection
