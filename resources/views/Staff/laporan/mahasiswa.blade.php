@extends('layouts.app')

@section('page-title', 'Laporan Mahasiswa')

@section('title', 'Laporan Presensi Mahasiswa - SIAKAD')

@section('content')
<div class="page-header">
    <div>
        <a href="{{ route('staff.laporan.dashboard') }}" class="btn btn-sm btn-secondary mb-2">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
        <h1><i class="bi bi-mortarboard"></i> Laporan Presensi Mahasiswa</h1>
        <p class="text-muted">Laporan kehadiran seluruh mahasiswa</p>
    </div>
</div>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Cari Mahasiswa</label>
                <input type="text" id="searchInput" class="form-control" placeholder="Nama atau NIM...">
            </div>
            <div class="col-md-6">
                <label class="form-label">Urutkan</label>
                <select id="sortInput" class="form-control">
                    <option value="percentage-asc">Persentase Terendah</option>
                    <option value="percentage-desc">Persentase Tertinggi</option>
                    <option value="name">Nama A-Z</option>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Student Report Table -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">Daftar Kehadiran Mahasiswa</h5>
    </div>
    <div class="card-body">
        <div style="overflow-x: auto;">
            <table class="table table-hover" id="reportTable">
                <thead style="background-color: #f8f9fa;">
                    <tr>
                        <th style="width: 30px;">#</th>
                        <th>NIM</th>
                        <th>Nama Mahasiswa</th>
                        <th>Prodi</th>
                        <th style="text-align: center;">Total Rekam</th>
                        <th style="text-align: center;">
                            <span class="badge bg-success">Hadir</span>
                        </th>
                        <th style="text-align: center;">
                            <span class="badge bg-danger">Absen</span>
                        </th>
                        <th style="text-align: center;">
                            <span class="badge bg-warning text-dark">Izin</span>
                        </th>
                        <th style="text-align: center;">
                            <span class="badge bg-info">Sakit</span>
                        </th>
                        <th style="text-align: center;">Persentase</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($studentStats as $index => $stat)
                        <tr class="student-row" data-percentage="{{ $stat['percentage'] }}" data-name="{{ $stat['mahasiswa'] }}" data-prodi="{{ $stat['prodi'] ?? '-' }}">
                            <td>{{ $index + 1 }}</td>
                            <td>{{ $stat['nim'] }}</td>
                            <td>{{ $stat['mahasiswa'] }}</td>
                            <td>{{ $stat['prodi'] ?? '-' }}</td>
                            <td style="text-align: center; font-weight: 600;">{{ $stat['total'] }}</td>
                            <td style="text-align: center;">
                                <span class="badge bg-success">{{ $stat['hadir'] }}</span>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge bg-danger">{{ $stat['absen'] }}</span>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge bg-warning text-dark">{{ $stat['izin'] }}</span>
                            </td>
                            <td style="text-align: center;">
                                <span class="badge bg-info">{{ $stat['sakit'] }}</span>
                            </td>
                            <td style="text-align: center;">
                                <div class="progress" style="height: 25px;">
                                    @if($stat['percentage'] >= 75)
                                        <div class="progress-bar bg-success" role="progressbar" style="width: {{ $stat['percentage'] }}%">
                                            {{ $stat['percentage'] }}%
                                        </div>
                                    @elseif($stat['percentage'] >= 50)
                                        <div class="progress-bar bg-warning" role="progressbar" style="width: {{ $stat['percentage'] }}%">
                                            {{ $stat['percentage'] }}%
                                        </div>
                                    @else
                                        <div class="progress-bar bg-danger" role="progressbar" style="width: {{ $stat['percentage'] }}%">
                                            {{ $stat['percentage'] }}%
                                        </div>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 20px;">
                                <p class="text-muted">Belum ada data mahasiswa</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@endsection

@section('extra-css')
<style>
    .student-row {
        transition: background-color 0.2s;
    }

    .student-row:hover {
        background-color: rgba(0, 0, 0, 0.02);
    }

    .hidden {
        display: none;
    }
</style>
@endsection

@section('extra-js')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchInput');
    const sortInput = document.getElementById('sortInput');
    const table = document.getElementById('reportTable');
    const rows = Array.from(table.querySelectorAll('tbody tr'));

    function filterAndSort() {
        const searchTerm = searchInput.value.toLowerCase();
        
        let filteredRows = rows.filter(row => {
            const text = row.textContent.toLowerCase();
            return text.includes(searchTerm);
        });

        const sortValue = sortInput.value;
        filteredRows.sort((a, b) => {
            if (sortValue === 'percentage-asc') {
                return parseFloat(a.dataset.percentage) - parseFloat(b.dataset.percentage);
            } else if (sortValue === 'percentage-desc') {
                return parseFloat(b.dataset.percentage) - parseFloat(a.dataset.percentage);
            } else if (sortValue === 'name') {
                return a.dataset.name.localeCompare(b.dataset.name);
            }
        });

        // Update table with filtered and sorted rows
        const tbody = table.querySelector('tbody');
        tbody.innerHTML = '';
        
        if (filteredRows.length === 0) {
            tbody.innerHTML = '<tr><td colspan="9" style="text-align: center; padding: 20px;"><p class="text-muted">Tidak ada hasil</p></td></tr>';
        } else {
            filteredRows.forEach((row, index) => {
                const newRow = row.cloneNode(true);
                const cells = newRow.querySelectorAll('td');
                cells[0].textContent = index + 1;
                tbody.appendChild(newRow);
            });
        }
    }

    searchInput.addEventListener('keyup', filterAndSort);
    sortInput.addEventListener('change', filterAndSort);
});
</script>
@endsection
