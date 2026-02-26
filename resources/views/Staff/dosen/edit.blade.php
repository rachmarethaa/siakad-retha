@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Edit Dosen</h1>

    @if($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('staff.dosen.update', $d->id) }}" method="POST">
        @csrf
        @method('PUT')
        <div class="mb-3">
            <label class="form-label">Nama</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $d->name) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" value="{{ old('email', $d->email) }}" required>
        </div>
        <div class="mb-3">
            <label class="form-label">NIP</label>
            <input type="text" name="nip" class="form-control" value="{{ old('nip', $d->nip) }}">
        </div>
        <div class="mb-3">
            <label class="form-label">Password (isi untuk mengganti)</label>
            <input type="password" name="password" class="form-control">
        </div>
        <button class="btn btn-primary">Simpan</button>
        <a href="{{ route('staff.dosen.index') }}" class="btn btn-secondary">Batal</a>
    </form>
</div>

<div style="position: fixed; right: 20px; bottom: 20px; z-index: 1000;">
    <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary">&larr; Kembali</a>
</div>

@endsection
