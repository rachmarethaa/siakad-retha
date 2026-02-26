@extends('layouts.app')

@section('page-title', 'Edit Nilai - ' . $nilai->mahasiswa->name)

@section('title', 'Edit Nilai - ' . $nilai->mahasiswa->name . ' - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-pencil-square"></i> Edit Nilai Mahasiswa</h1>
        <p class="text-muted">{{ $jadwal->mata_kuliah }} ({{ $jadwal->kode_mk }})</p>
    </div>
</div>

<!-- Form Card -->
<div class="stat-card">
    
    <!-- Mahasiswa Info -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 30px; padding-bottom: 20px; border-bottom: 1px solid #dee2e6;">
        <div>
            <p style="font-size: 12px; color: #999; font-weight: 600; text-transform: uppercase; margin-bottom: 5px;">Nama Mahasiswa</p>
            <p style="font-size: 16px; font-weight: 700; color: #333;">{{ $nilai->mahasiswa->name }}</p>
        </div>
        <div>
            <p style="font-size: 12px; color: #999; font-weight: 600; text-transform: uppercase; margin-bottom: 5px;">NIM</p>
            <p style="font-size: 16px; font-weight: 700; color: #333;">{{ $nilai->mahasiswa->nim }}</p>
        </div>
        <div>
            <p style="font-size: 12px; color: #999; font-weight: 600; text-transform: uppercase; margin-bottom: 5px;">Email</p>
            <p style="font-size: 14px; color: #666;">{{ $nilai->mahasiswa->user->email ?? '-' }}</p>
        </div>
        <div>
            <p style="font-size: 12px; color: #999; font-weight: 600; text-transform: uppercase; margin-bottom: 5px;">Program Studi</p>
            <p style="font-size: 14px; color: #666;">{{ $nilai->mahasiswa->prodi }}</p>
        </div>
    </div>

    <!-- Form -->
    <form action="{{ route('dosen.nilai.update', [$jadwal->id, $nilai->mahasiswa_id]) }}" method="POST">
        @csrf
        @method('PUT')

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    
                    <!-- Nilai Tugas -->
                    <div>
                        <label for="nilai_tugas" style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 8px;">
                            Nilai Tugas <span style="color: #dc3545;">*</span>
                            <span style="font-size: 11px; color: #999; font-weight: 400;">(bobot 30%)</span>
                        </label>
                        <input type="number" 
                               id="nilai_tugas" 
                               name="nilai_tugas" 
                               value="{{ old('nilai_tugas', $nilai->nilai_tugas) }}"
                               min="0" max="100" step="0.01" 
                               class="form-control"
                               placeholder="0.00"
                               oninput="hitungNilai()"
                               style="padding: 10px; font-size: 14px;">
                        @error('nilai_tugas')
                            <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nilai UTS -->
                    <div>
                        <label for="nilai_uts" style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 8px;">
                            Nilai UTS <span style="color: #dc3545;">*</span>
                            <span style="font-size: 11px; color: #999; font-weight: 400;">(bobot 30%)</span>
                        </label>
                        <input type="number" 
                               id="nilai_uts" 
                               name="nilai_uts" 
                               value="{{ old('nilai_uts', $nilai->nilai_uts) }}"
                               min="0" max="100" step="0.01" 
                               class="form-control"
                               placeholder="0.00"
                               oninput="hitungNilai()"
                               style="padding: 10px; font-size: 14px;">
                        @error('nilai_uts')
                            <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Nilai UAS -->
                    <div>
                        <label for="nilai_uas" style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 8px;">
                            Nilai UAS <span style="color: #dc3545;">*</span>
                            <span style="font-size: 11px; color: #999; font-weight: 400;">(bobot 40%)</span>
                        </label>
                        <input type="number" 
                               id="nilai_uas" 
                               name="nilai_uas" 
                               value="{{ old('nilai_uas', $nilai->nilai_uas) }}"
                               min="0" max="100" step="0.01" 
                               class="form-control"
                               placeholder="0.00"
                               oninput="hitungNilai()"
                               style="padding: 10px; font-size: 14px;">
                        @error('nilai_uas')
                            <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Grade -->
                    <div>
                        <label style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 8px;">
                            Grade <span style="color: #dc3545;">*</span>
                        </label>
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <span id="grade_badge" 
                                  style="padding: 10px 16px; border-radius: 6px; font-size: 20px; font-weight: 700; color: white; background-color: #6c757d; min-width: 50px; text-align: center;">
                                {{ $nilai->grade ?? '-' }}
                            </span>
                            <div style="font-size: 12px; color: #999;">
                                <p style="margin: 0;">A: ≥85 | B: ≥75 | C: ≥65 | D: ≥55 | E: &lt;55</p>
                            </div>
                        </div>
                        <input type="hidden" id="grade" name="grade" value="{{ $nilai->grade }}">
                    </div>
                </div>

                <!-- Nilai Akhir Display -->
                <div style="background-color: #e8f4f8; border: 1px solid #b3d9e8; border-radius: 8px; padding: 15px; margin-bottom: 20px;">
                    <p style="font-size: 12px; color: #666; font-weight: 600; margin-bottom: 8px; text-transform: uppercase;">Nilai Akhir</p>
                    <p style="font-size: 32px; font-weight: 700; color: #4a90e2; margin: 0;">
                        <span id="nilai_akhir_display">{{ number_format($nilai->nilai_akhir ?? 0, 2) }}</span>
                    </p>
                    <p style="font-size: 11px; color: #999; margin-top: 5px; margin-bottom: 0;">Dihitung otomatis: (30% Tugas) + (30% UTS) + (40% UAS)</p>
                    <input type="hidden" id="nilai_akhir" name="nilai_akhir" value="{{ $nilai->nilai_akhir }}">
                </div>

                <!-- Keterangan -->
                <div style="margin-bottom: 20px;">
                    <label for="keterangan" style="display: block; font-size: 13px; font-weight: 600; color: #333; margin-bottom: 8px;">
                        Keterangan (Catatan)
                    </label>
                    <textarea id="keterangan" 
                              name="keterangan" 
                              rows="4"
                              class="form-control"
                              placeholder="Masukkan catatan jika ada"
                              style="padding: 10px; font-size: 13px;">{{ old('keterangan', $nilai->keterangan) }}</textarea>
                    @error('keterangan')
                        <p style="color: #dc3545; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Buttons -->
                <div style="display: flex; gap: 10px; justify-content: flex-end; padding-top: 20px; border-top: 1px solid #dee2e6;">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">
                        <i class="bi bi-check-lg"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('dosen.nilai.show', $jadwal->id) }}" class="btn btn-secondary" style="padding: 10px 20px;">
                        <i class="bi bi-x-lg"></i> Batal
                    </a>
                </div>
            </form>
        </div>
@endsection

@section('extra-css')
<style>
    .form-control {
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 8px 12px;
        font-size: 13px;
        transition: all 0.3s ease;
        width: 100%;
    }
    
    .form-control:focus {
        border-color: #4a90e2;
        box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
        outline: none;
    }
    
    .btn {
        font-size: 13px;
        border-radius: 6px;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .btn-primary {
        background-color: #4a90e2;
        color: white;
    }
    
    .btn-primary:hover {
        background-color: #1565c0;
    }
    
    .btn-secondary {
        background-color: #6c757d;
        color: white;
    }
    
    .btn-secondary:hover {
        background-color: #545b62;
    }
</style>

<script>
function hitungNilai() {
    const tugas = parseFloat(document.getElementById('nilai_tugas').value) || 0;
    const uts = parseFloat(document.getElementById('nilai_uts').value) || 0;
    const uas = parseFloat(document.getElementById('nilai_uas').value) || 0;

    if ((tugas || uts || uas) > 0) {
        // Rumus: 30% tugas + 30% UTS + 40% UAS
        const nilaiAkhir = (0.30 * tugas) + (0.30 * uts) + (0.40 * uas);
        document.getElementById('nilai_akhir').value = nilaiAkhir.toFixed(2);
        document.getElementById('nilai_akhir_display').textContent = nilaiAkhir.toFixed(2);
        
        // Update grade
        updateGrade(nilaiAkhir);
    }
}

function updateGrade(nilai) {
    let grade = '-';
    let bgColor = '#6c757d';

    if (nilai >= 85) {
        grade = 'A';
        bgColor = '#28a745';
    } else if (nilai >= 75) {
        grade = 'B';
        bgColor = '#4a90e2';
    } else if (nilai >= 65) {
        grade = 'C';
        bgColor = '#ffc107';
    } else if (nilai >= 55) {
        grade = 'D';
        bgColor = '#fd7e14';
    } else if (nilai > 0) {
        grade = 'E';
        bgColor = '#dc3545';
    }

    document.getElementById('grade').value = grade;
    const gradeBadge = document.getElementById('grade_badge');
    gradeBadge.textContent = grade;
    gradeBadge.style.backgroundColor = bgColor;
}

// Hitung nilai saat page load
window.addEventListener('load', hitungNilai);
</script>
