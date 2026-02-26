@extends('layouts.app')

@section('page-title', 'Input Nilai - ' . $jadwal->mata_kuliah)

@section('title', 'Input Nilai - ' . $jadwal->mata_kuliah . ' - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <h1><i class="bi bi-pencil-square"></i> Input Nilai Mahasiswa</h1>
        <p class="text-muted">
            <strong>{{ $jadwal->mata_kuliah }}</strong> ({{ $jadwal->kode_mk }}) | 
            {{ $jadwal->hari }}, {{ \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') }} - {{ \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') }} | 
            Ruang: {{ $jadwal->ruang }}
        </p>
    </div>
</div>

@if($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle"></i> <strong>Terjadi kesalahan:</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif

<!-- Form Input Nilai -->
<form action="{{ route('dosen.nilai.store', $jadwal->id) }}" method="POST" class="stat-card">
    @csrf
    
    <!-- Table Input dengan overflow responsif -->
    <div style="overflow-x: auto;">
        <table class="table table-hover" style="margin-bottom: 0;">
            <thead style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <tr>
                    <th style="text-align: center; vertical-align: middle; width: 40px;">No</th>
                    <th style="vertical-align: middle;">Nama Mahasiswa</th>
                    <th style="text-align: center; vertical-align: middle; width: 90px;">
                        Tugas <br><small style="font-weight: 400; font-size: 11px;">(30%)</small>
                    </th>
                    <th style="text-align: center; vertical-align: middle; width: 90px;">
                        UTS <br><small style="font-weight: 400; font-size: 11px;">(30%)</small>
                    </th>
                    <th style="text-align: center; vertical-align: middle; width: 90px;">
                        UAS <br><small style="font-weight: 400; font-size: 11px;">(40%)</small>
                    </th>
                    <th style="text-align: center; vertical-align: middle; width: 100px;">Nilai Akhir</th>
                    <th style="text-align: center; vertical-align: middle; width: 60px;">Grade</th>
                    <th style="vertical-align: middle;">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @forelse($mahasiswas as $index => $mahasiswa)
                    @php
                        $nilai = $nilais->get($mahasiswa->id);
                    @endphp
                    <tr>
                        <td style="text-align: center; vertical-align: middle; font-size: 13px; color: #777;">{{ $index + 1 }}</td>
                        <td style="vertical-align: middle;">
                            <div style="font-weight: 600; color: #333;">{{ $mahasiswa->name }}</div>
                            <div style="font-size: 12px; color: #999;">NIM: {{ $mahasiswa->nim ?? '-' }}</div>
                        </td>
                        
                        <!-- Nilai Tugas -->
                        <td style="vertical-align: middle;">
                            <input type="number" 
                                   name="nilai[{{ $mahasiswa->id }}][nilai_tugas]" 
                                   value="{{ $nilai->nilai_tugas ?? '' }}"
                                   step="0.01" min="0" max="100"
                                   class="form-control form-control-sm text-center nilai-input"
                                   placeholder="0"
                                   style="font-size: 13px;">
                        </td>

                        <!-- Nilai UTS -->
                        <td style="vertical-align: middle;">
                            <input type="number" 
                                   name="nilai[{{ $mahasiswa->id }}][nilai_uts]" 
                                   value="{{ $nilai->nilai_uts ?? '' }}"
                                   step="0.01" min="0" max="100"
                                   class="form-control form-control-sm text-center nilai-input"
                                   placeholder="0"
                                   style="font-size: 13px;">
                        </td>

                        <!-- Nilai UAS -->
                        <td style="vertical-align: middle;">
                            <input type="number" 
                                   name="nilai[{{ $mahasiswa->id }}][nilai_uas]" 
                                   value="{{ $nilai->nilai_uas ?? '' }}"
                                   step="0.01" min="0" max="100"
                                   class="form-control form-control-sm text-center nilai-input"
                                   placeholder="0"
                                   style="font-size: 13px;">
                        </td>

                        <!-- Nilai Akhir (Auto) -->
                        <td style="vertical-align: middle;">
                            <input type="number" 
                                   name="nilai[{{ $mahasiswa->id }}][nilai_akhir]"
                                   value="{{ $nilai->nilai_akhir ?? '' }}"
                                   readonly
                                   class="form-control form-control-sm text-center nilai-akhir"
                                   placeholder="-"
                                   style="font-size: 13px; font-weight: 700; background-color: #f8f9fa;"
                                   step="0.01">
                        </td>

                        <!-- Grade (Auto) -->
                        <td style="text-align: center; vertical-align: middle;">
                            <span class="badge grade-badge"
                                  data-grade="{{ $nilai->grade ?? '' }}"
                                  style="font-size: 12px; padding: 6px 10px; display: inline-block;">
                                {{ $nilai->grade ?? '-' }}
                            </span>
                        </td>

                        <!-- Keterangan -->
                        <td style="vertical-align: middle;">
                            <input type="text" 
                                   name="nilai[{{ $mahasiswa->id }}][keterangan]"
                                   value="{{ $nilai->keterangan ?? '' }}"
                                   class="form-control form-control-sm"
                                   placeholder="Catatan (opsional)"
                                   maxlength="100"
                                   style="font-size: 13px;">
                        </td>
                    </tr>

                    <!-- Hidden input untuk mahasiswa_id -->
                    <input type="hidden" name="nilai[{{ $mahasiswa->id }}][mahasiswa_id]" value="{{ $mahasiswa->id }}">
                @empty
                    <tr>
                        <td colspan="8" style="text-align: center; padding: 20px; color: #999;">
                            <i class="bi bi-inbox"></i> Tidak ada mahasiswa
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Buttons -->
    <div style="border-top: 1px solid #dee2e6; padding-top: 20px; margin-top: 20px; display: flex; gap: 10px; justify-content: flex-end;">
        <a href="{{ route('dosen.nilai.index') }}" class="btn btn-secondary" style="padding: 8px 16px;">
            <i class="bi bi-x-lg"></i> Batal
        </a>
        <button type="submit" class="btn btn-primary" style="padding: 8px 20px;">
            <i class="bi bi-check-lg"></i> Simpan Nilai
        </button>
    </div>
</form>

<!-- Navigation Links -->
<div style="margin-top: 20px;">
    <a href="{{ route('dosen.nilai.index') }}" class="btn btn-link" style="color: #4a90e2; text-decoration: none;">
        <i class="bi bi-arrow-left"></i> Kembali ke Daftar Nilai
    </a>
</div>
@endsection

@section('extra-css')
<style>
    .form-control-sm {
        border-radius: 4px;
        border: 1px solid #dee2e6;
    }
    
    .form-control-sm:focus {
        border-color: #4a90e2;
        box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
    }
    
    .badge {
        font-weight: 600;
        padding: 6px 10px;
        display: inline-block;
    }
    
    .table {
        font-size: 13px;
    }
    
    .table tbody tr:hover {
        background-color: #f8f9fa;
    }
    
    .btn {
        font-size: 13px;
        padding: 8px 16px;
        border-radius: 6px;
    }
    
    .btn-primary {
        background-color: #4a90e2;
        border-color: #4a90e2;
        color: white;
    }
    
    .btn-primary:hover {
        background-color: #1565c0;
        border-color: #1565c0;
        color: white;
    }
    
    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
    }
    
    .btn-secondary:hover {
        background-color: #545b62;
        border-color: #545b62;
        color: white;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize grade colors on page load
    initializeGradeColors();
    
    // Add event listeners to nilai inputs
    document.querySelectorAll('.nilai-input').forEach(input => {
        input.addEventListener('change', function() {
            hitungNilai(this);
        });
        input.addEventListener('input', function() {
            hitungNilai(this);
        });
    });
});

function hitungNilai(input) {
    // Cari row yang berisi input ini
    const row = input.closest('tr');
    const tugas = parseFloat(row.querySelector('input[name$="[nilai_tugas]"]').value) || 0;
    const uts = parseFloat(row.querySelector('input[name$="[nilai_uts]"]').value) || 0;
    const uas = parseFloat(row.querySelector('input[name$="[nilai_uas]"]').value) || 0;
    
    // Hitung nilai akhir: 30% tugas + 30% UTS + 40% UAS
    const nilaiAkhir = (0.30 * tugas) + (0.30 * uts) + (0.40 * uas);
    
    // Update field nilai akhir
    const fieldNilaiAkhir = row.querySelector('.nilai-akhir');
    if (tugas || uts || uas) {
        fieldNilaiAkhir.value = nilaiAkhir.toFixed(2);
    } else {
        fieldNilaiAkhir.value = '';
    }
    
    // Update grade
    updateGrade(row, nilaiAkhir);
}

function updateGrade(row, nilai) {
    const badge = row.querySelector('.grade-badge');
    let grade = '-';
    
    if (nilai >= 85) grade = 'A';
    else if (nilai >= 75) grade = 'B';
    else if (nilai >= 65) grade = 'C';
    else if (nilai >= 55) grade = 'D';
    else if (nilai > 0) grade = 'E';
    
    badge.textContent = grade;
    badge.setAttribute('data-grade', grade);
    
    // Reset classes
    badge.className = 'badge grade-badge';
    
    // Set warna berdasarkan grade
    if (grade === 'A') badge.style.backgroundColor = '#28a745';
    else if (grade === 'B') badge.style.backgroundColor = '#4a90e2';
    else if (grade === 'C') badge.style.backgroundColor = '#ffc107';
    else if (grade === 'D') badge.style.backgroundColor = '#fd7e14';
    else if (grade === 'E') badge.style.backgroundColor = '#dc3545';
    else badge.style.backgroundColor = '#6c757d';
    
    badge.style.color = 'white';
}

function initializeGradeColors() {
    document.querySelectorAll('.grade-badge').forEach(badge => {
        const grade = badge.getAttribute('data-grade');
        if (grade === 'A') badge.style.backgroundColor = '#28a745';
        else if (grade === 'B') badge.style.backgroundColor = '#4a90e2';
        else if (grade === 'C') badge.style.backgroundColor = '#ffc107';
        else if (grade === 'D') badge.style.backgroundColor = '#fd7e14';
        else if (grade === 'E') badge.style.backgroundColor = '#dc3545';
        else badge.style.backgroundColor = '#6c757d';
        
        badge.style.color = 'white';
    });
}
</script>
@endsection
