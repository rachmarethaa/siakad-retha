@extends('layouts.app')

@section('content')
<div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Edit Jadwal Mengajar</h1>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle"></i> <strong>Terjadi kesalahan:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('staff.jadwal.update', $jadwal->id) }}" method="POST">
        @csrf
        @method('PUT')
        @include('jadwal_mengajar._form')
        <div class="mt-4">
            <button class="btn btn-primary">Simpan Perubahan</button>
            <a href="{{ route('staff.jadwal.index') }}" class="btn">Batal</a>
        </div>
    </form>
    
</div>
@endsection
