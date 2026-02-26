<div class="grid grid-cols-1 gap-4">
    <div>
        <label class="block">Mata Kuliah</label>
        <input type="text" name="mata_kuliah" value="{{ old('mata_kuliah', $jadwal->mata_kuliah ?? '') }}" class="input" required />
    </div>
    <div>
        <label class="block">Kode MK</label>
        <input type="text" name="kode_mk" value="{{ old('kode_mk', $jadwal->kode_mk ?? '') }}" class="input" />
    </div>
    <div>
        <label class="block">Dosen</label>
        <select name="dosen_id" class="input">
            <option value="">-- Pilih Dosen --</option>
            @foreach($dosen as $d)
                <option value="{{ $d->id }}" @if(old('dosen_id', $jadwal->dosen_id ?? '') == $d->id) selected @endif>{{ $d->name }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block">Hari</label>
        <select name="hari" class="input">
            @foreach(['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu'] as $h)
                <option value="{{ $h }}" @if(old('hari', $jadwal->hari ?? '') == $h) selected @endif>{{ $h }}</option>
            @endforeach
        </select>
    </div>
    <div class="flex gap-2">
        <div>
            <label class="block">Jam Mulai</label>
            <input type="time" name="jam_mulai" value="{{ old('jam_mulai', isset($jadwal) && $jadwal->jam_mulai ? \Carbon\Carbon::parse($jadwal->jam_mulai)->format('H:i') : '') }}" class="input" required />
        </div>
        <div>
            <label class="block">Jam Selesai</label>
            <input type="time" name="jam_selesai" value="{{ old('jam_selesai', isset($jadwal) && $jadwal->jam_selesai ? \Carbon\Carbon::parse($jadwal->jam_selesai)->format('H:i') : '') }}" class="input" required />
        </div>
    </div>
    <div>
        <label class="block">Ruang</label>
        <input type="text" name="ruang" value="{{ old('ruang', $jadwal->ruang ?? '') }}" class="input" />
    </div>
    <div>
        <label class="block">Prodi</label>
        <select name="prodi" class="input">
            <option value="">-- Pilih Prodi --</option>
            @foreach(config('prodi') as $code => $label)
                <option value="{{ $code }}" @if(old('prodi', $jadwal->prodi ?? '') == $code) selected @endif>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div>
        <label class="block">Semester</label>
        <input type="text" name="semester" value="{{ old('semester', $jadwal->semester ?? '') }}" class="input" />
    </div>
</div>
