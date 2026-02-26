@extends('layouts.app')

@section('page-title', 'Edit Profil Mahasiswa')

@section('title', 'Edit Profil - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <h1>Edit Profil Mahasiswa</h1>
        <p class="text-muted">Perbarui informasi pribadi Anda</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="stat-card">
            <form method="POST" action="{{ route('mahasiswa.profile.update') }}">
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
                        value="{{ old('name', $mahasiswa->name) }}"
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
                        value="{{ $mahasiswa->email }}"
                        disabled
                        readonly>
                    <small class="text-muted">Email tidak dapat diubah</small>
                </div>

                <!-- NIM Field (Read Only) -->
                <div class="mb-3">
                    <label for="nim" class="form-label">
                        <strong>NIM (Nomor Induk Mahasiswa)</strong>
                    </label>
                    <input type="text" 
                        class="form-control" 
                        id="nim" 
                        value="{{ $mahasiswa->nim ?? 'Tidak ada' }}"
                        disabled
                        readonly>
                    <small class="text-muted">NIM tidak dapat diubah</small>
                </div>

                <!-- Prodi Field (Read Only) -->
                <div class="mb-3">
                    <label for="prodi" class="form-label">
                        <strong>Program Studi (Prodi)</strong>
                    </label>
                    <input type="text" 
                        class="form-control" 
                        id="prodi" 
                        value="{{ $mahasiswa->prodi ?? '-' }}"
                        disabled
                        readonly>
                    <small class="text-muted">Program studi tidak dapat diubah. Hubungi administrator untuk perubahan.</small>
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
                    <a href="{{ route('mahasiswa.dashboard') }}" class="btn btn-secondary">
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
                    <li>Email dan NIM bersifat permanen dan tidak dapat diubah</li>
                    <li>Untuk mengubah Email atau NIM, hubungi administrator</li>
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
                    <td>{{ $mahasiswa->created_at->format('d M Y H:i') }}</td>
                </tr>
                <tr>
                    <td><strong>Terakhir diperbarui:</strong></td>
                    <td>{{ $mahasiswa->updated_at->format('d M Y H:i') }}</td>
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
