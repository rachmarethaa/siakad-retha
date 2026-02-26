@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Kelola Dosen</h1>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <p><a href="{{ route('staff.dosen.create') }}" class="btn btn-primary">Tambah Dosen</a></p>

    <table class="table table-striped">
        <thead>
            <tr>
                <th>#</th>
                <th>Nama</th>
                <th>Email</th>
                <th>NIP</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($dosen as $d)
            <tr>
                <td>{{ $d->id }}</td>
                <td>{{ $d->name }}</td>
                <td>{{ $d->email }}</td>
                <td>{{ $d->nip }}</td>
                <td>
                    <a href="{{ route('staff.dosen.edit', $d->id) }}" class="btn btn-sm btn-secondary">Edit</a>
                    <form action="{{ route('staff.dosen.destroy', $d->id) }}" method="POST" style="display:inline-block" class="delete-form">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-sm btn-danger btn-delete">Hapus</button>
                        <div class="delete-confirm d-none" style="display:inline-block; margin-left:8px;">
                            <button type="button" class="btn btn-sm btn-danger btn-confirm-yes">Ya, Hapus</button>
                            <button type="button" class="btn btn-sm btn-secondary btn-confirm-no">Batal</button>
                        </div>
                    </form>
                    <form action="{{ route('staff.dosen.resetPassword', $d->id) }}" method="POST" style="display:inline-block" class="reset-form">
                        @csrf
                        <button type="button" class="btn btn-sm btn-warning btn-reset">Reset Password</button>
                        <div class="reset-confirm d-none" style="display:inline-block; margin-left:8px;">
                            <button type="button" class="btn btn-sm btn-warning btn-reset-yes">Ya, Reset</button>
                            <button type="button" class="btn btn-sm btn-secondary btn-reset-no">Batal</button>
                        </div>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="d-flex justify-content-end mt-3">
        <a href="{{ route('staff.dashboard') }}" class="btn btn-secondary">&larr; Kembali</a>
    </div>

    {{ $dosen->links() }}
</div>

@endsection

@section('extra-js')
<script>
document.addEventListener('DOMContentLoaded', function(){
    document.querySelectorAll('.btn-delete').forEach(function(btn){
        btn.addEventListener('click', function(){
            var form = btn.closest('.delete-form');
            var confirmEl = form.querySelector('.delete-confirm');
            if(confirmEl) confirmEl.classList.remove('d-none');
            btn.style.display = 'none';
        });
    });

    document.querySelectorAll('.btn-confirm-no').forEach(function(btn){
        btn.addEventListener('click', function(){
            var form = btn.closest('.delete-form');
            var confirmEl = form.querySelector('.delete-confirm');
            var deleteBtn = form.querySelector('.btn-delete');
            if(confirmEl) confirmEl.classList.add('d-none');
            if(deleteBtn) deleteBtn.style.display = '';
        });
    });

    document.querySelectorAll('.btn-confirm-yes').forEach(function(btn){
        btn.addEventListener('click', function(){
            var form = btn.closest('form');
            if(form) form.submit();
        });
    });
    // Reset password inline confirmation
    document.querySelectorAll('.btn-reset').forEach(function(btn){
        btn.addEventListener('click', function(){
            var form = btn.closest('.reset-form');
            var confirmEl = form.querySelector('.reset-confirm');
            if(confirmEl) confirmEl.classList.remove('d-none');
            btn.style.display = 'none';
        });
    });

    document.querySelectorAll('.btn-reset-no').forEach(function(btn){
        btn.addEventListener('click', function(){
            var form = btn.closest('.reset-form');
            var confirmEl = form.querySelector('.reset-confirm');
            var resetBtn = form.querySelector('.btn-reset');
            if(confirmEl) confirmEl.classList.add('d-none');
            if(resetBtn) resetBtn.style.display = '';
        });
    });

    document.querySelectorAll('.btn-reset-yes').forEach(function(btn){
        btn.addEventListener('click', function(){
            var form = btn.closest('form');
            if(form) form.submit();
        });
    });
});
</script>
@endsection
