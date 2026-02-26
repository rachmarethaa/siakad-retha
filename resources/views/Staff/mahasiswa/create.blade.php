@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Tambah Mahasiswa</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('staff.mahasiswa.store') }}" method="POST">
        @csrf
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">NIM</label>
            <input type="text" name="nim" class="form-control" value="{{ old('nim') }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Program Studi (Prodi)</label>
            <select name="prodi" class="form-control">
                <option value="">-- Pilih Program Studi --</option>
                @foreach(config('prodi') as $code => $label)
                    <option value="{{ $code }}" @selected(old('prodi') === $code)>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div class="mb-3">
            <label class="form-label">Password (kosongkan untuk default)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('staff.mahasiswa.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>

<div style="position: fixed; right: 20px; bottom: 20px; z-index: 1000;">
    <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary">&larr; Kembali</a>
</div>

@endsection
