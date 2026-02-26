@extends('layouts.app')

@section('page-title', 'Edit Profil Dosen')

@section('title', 'Edit Profil - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <h1>Edit Profil Dosen</h1>
        <p class="text-muted">Perbarui informasi pribadi Anda</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="stat-card">
            <form method="POST" action="{{ route('dosen.profile.update') }}">
                @csrf
                @method('PUT')

                <!-- Name Field -->
                <div class="mb-3">
                    <label for="name" class="form-label">
                        <strong>Nama Lengkap</strong>
                    </label>
                    <input type="text" 
                        class="form-control @error('name') is-invalid @enderror" 
                        id="name" 
                        name="name" 
                        value="{{ old('name', $dosen->name) }}"
                        required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email Field (Read Only) -->
                <div class="mb-3">
                    <label for="email" class="form-label">
                        <strong>Email</strong>
                    </label>
                    <input type="email" 
                        class="form-control" 
                        id="email" 
                        value="{{ $dosen->email }}"
                        disabled
                        readonly>
                    <small class="text-muted">Email tidak dapat diubah</small>
                </div>

                <!-- NIP Field (Read Only) -->
                <div class="mb-3">
                    <label for="nip" class="form-label">
                        <strong>NIP (Nomor Induk Pegawai)</strong>
                    </label>
                    <input type="text" 
                        class="form-control" 
                        id="nip" 
                        value="{{ $dosen->nip ?? 'Tidak ada' }}"
                        disabled
                        readonly>
                    <small class="text-muted">NIP tidak dapat diubah</small>
                </div>

                <hr class="my-4">
                <h6 class="mb-3"><i class="bi bi-lock"></i> Ganti Password (Opsional)</h6>

                <!-- Password Field -->
                <div class="mb-3">
                    <label for="password" class="form-label">
                        <strong>Password Baru</strong>
                    </label>
                    <input type="password" 
                        class="form-control @error('password') is-invalid @enderror" 
                        id="password" 
                        name="password"
                        placeholder="Kosongkan jika tidak ingin mengubah">
                    <small class="text-muted d-block mt-1">Minimal 8 karakter</small>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password Confirmation Field -->
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">
                        <strong>Konfirmasi Password</strong>
                    </label>
                    <input type="password" 
                        class="form-control" 
                        id="password_confirmation" 
                        name="password_confirmation"
                        placeholder="Konfirmasi password baru">
                </div>

                <!-- Submit Button -->
                <div class="mt-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('dosen.dashboard') }}" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Info Box -->
    <div class="col-lg-6">
        <div class="stat-card" style="background-color: #eff6ff; border-left: 4px solid #4a90e2;">
            <h5 class="mb-3">
                <i class="bi bi-info-circle"></i> Informasi Keamanan
            </h5>
            <div class="alert alert-info mb-0">
                <p><strong>Catatan Penting:</strong></p>
                <ul class="mb-0 ms-2">
                    <li>Anda hanya dapat mengubah <strong>Nama</strong> dan <strong>Password</strong></li>
                    <li>Email dan NIP bersifat permanen dan tidak dapat diubah</li>
                    <li>Untuk mengubah Email atau NIP, hubungi administrator</li>
                    <li>Pastikan password Anda kuat dan aman</li>
                    <li>Jangan bagikan password Anda kepada siapa pun</li>
                </ul>
            </div>
        </div>

        <div class="stat-card mt-3">
            <h5 class="mb-3">
                <i class="bi bi-clock-history"></i> Informasi Akun
            </h5>
            <table class="table table-sm table-borderless">
                <tr>
                    <td style="width: 40%;"><strong>Dibuat pada:</strong></td>
                    <td>{{ $dosen->created_at->format('d M Y H:i') }}</td>
                </tr>
                <tr>
                    <td><strong>Terakhir diperbarui:</strong></td>
                    <td>{{ $dosen->updated_at->format('d M Y H:i') }}</td>
                </tr>
            </table>
        </div>
    </div>
</div>

<style>
    .table-borderless td {
        padding: 8px 0;
        border: none;
        font-size: 14px;
    }

    .form-control:disabled,
    .form-control[readonly] {
        background-color: #f8f9fa;
        color: #6c757d;
    }

    .btn {
        padding: 10px 20px;
        border-radius: 6px;
        font-weight: 500;
    }
</style>
@endsection
