<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SIAKAD') }} - Register</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Flowbite -->
    <link href="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
</head>

<body class="bg-gray-50 dark:bg-gray-900 flex items-center justify-center min-h-screen p-4">

    <div class="w-full max-w-md bg-white rounded-lg shadow dark:bg-gray-800 dark:border dark:border-gray-700">

        <!-- Logo + Title -->
        <div class="flex flex-col items-center mt-6">
            <img src="{{ asset('Politeknik Bhakti Semesta.png') }}" alt="Logo Politeknik Bhakti Semesta" class="w-20 h-20 mb-3">

            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
                Politeknik Bhakti Semesta
            </h1>

            <p class="text-sm text-gray-500 dark:text-gray-300">
                Sistem Registrasi Akademik
            </p>
        </div>

        <!-- Register Form -->
        <div class="p-6 space-y-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white text-center">
                Buat Akun Baru
            </h2>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 border border-red-200">
                    <strong>⚠️ Registrasi Gagal!</strong>
                    @foreach ($errors->all() as $error)
                        <div>• {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form class="space-y-4" action="{{ route('register') }}" method="POST">
                @csrf

                <!-- Role Selection -->
                <div>
                    <label for="role" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Daftar Sebagai <span class="text-red-500">*</span>
                    </label>
                    <select name="role" id="role" required value="{{ old('role') }}"
                        class="bg-gray-50 border @error('role') border-red-500 @else border-gray-300 @enderror text-gray-900 rounded-lg focus:ring-blue-600
                        focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600
                        dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500
                        dark:focus:border-blue-500" onchange="updateNimNipField()">
                        <option value="">-- Pilih Role --</option>
                        <option value="staff" @selected(old('role') === 'staff')>Staff/Admin</option>
                        <option value="dosen" @selected(old('role') === 'dosen')>Dosen</option>
                        <option value="mahasiswa" @selected(old('role') === 'mahasiswa')>Mahasiswa</option>
                    </select>
                    @error('role')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Name -->
                <div>
                    <label for="name" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" name="name" id="name" required value="{{ old('name') }}"
                        class="bg-gray-50 border @error('name') border-red-500 @else border-gray-300 @enderror text-gray-900 rounded-lg focus:ring-blue-600
                        focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600
                        dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500
                        dark:focus:border-blue-500"
                        placeholder="Nama Lengkap">
                    @error('name')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Email -->
                <div>
                    <label for="email" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input type="email" name="email" id="email" required value="{{ old('email') }}"
                        class="bg-gray-50 border @error('email') border-red-500 @else border-gray-300 @enderror text-gray-900 rounded-lg focus:ring-blue-600
                        focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600
                        dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500
                        dark:focus:border-blue-500"
                        placeholder="email@bhaktisemesta.ac.id">
                    @error('email')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- NIP/NIM Field (Dynamic) -->
                <div id="nimNipContainer" style="display: none;">
                    <label for="nip_nim" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        <span id="nimNipLabel">NIP/NIM</span> (Opsional)
                    </label>
                    <input type="text" name="nip" id="nip" value="{{ old('nip') }}"
                        class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600
                        focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600
                        dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500
                        dark:focus:border-blue-500"
                        placeholder="NIP/NIM">
                    <input type="text" name="nim" id="nim" value="{{ old('nim') }}"
                        class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600
                        focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600
                        dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500
                        dark:focus:border-blue-500"
                        placeholder="NIM"
                        style="display: none;">
                    @error('nip')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                    @error('nim')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Prodi (for Mahasiswa) -->
                <div id="prodiContainer" style="display: none; margin-top: 8px;">
                    <label for="prodi" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Program Studi (Prodi) <span class="text-red-500">*</span>
                    </label>
                    <select name="prodi" id="prodi"
                        class="bg-gray-50 border @error('prodi') border-red-500 @else border-gray-300 @enderror text-gray-900 rounded-lg focus:ring-blue-600
                        focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600
                        dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500
                        dark:focus:border-blue-500">
                        <option value="">-- Pilih Program Studi --</option>
                        <option value="Rekayasa Keamanan Siber (RKS)" @selected(old('prodi') === 'Rekayasa Keamanan Siber (RKS)')>Rekayasa Keamanan Siber (RKS)</option>
                        <option value="Bisnis Digital (BD)" @selected(old('prodi') === 'Bisnis Digital (BD)')>Bisnis Digital (BD)</option>
                        <option value="Teknologi Rekayasa Multimedia (TRM)" @selected(old('prodi') === 'Teknologi Rekayasa Multimedia (TRM)')>Teknologi Rekayasa Multimedia (TRM)</option>
                    </select>
                    @error('prodi')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div>
                    <label for="password" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password" id="password" required
                        class="bg-gray-50 border @error('password') border-red-500 @else border-gray-300 @enderror text-gray-900 rounded-lg focus:ring-blue-600
                        focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600
                        dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500
                        dark:focus:border-blue-500"
                        placeholder="••••••••">
                    <p class="text-gray-500 text-xs mt-1">
                        Minimal 8 karakter dengan kombinasi huruf, angka, dan simbol
                    </p>
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div>
                    <label for="password_confirmation" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Konfirmasi Password <span class="text-red-500">*</span>
                    </label>
                    <input type="password" name="password_confirmation" id="password_confirmation" required
                        class="bg-gray-50 border border-gray-300 text-gray-900 rounded-lg focus:ring-blue-600
                        focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600
                        dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500
                        dark:focus:border-blue-500"
                        placeholder="••••••••">
                    @error('password_confirmation')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit"
                    class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none
                    focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2 text-center
                    dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Daftar Sekarang
                </button>
            </form>

            <p class="text-sm font-light text-gray-500 dark:text-gray-400 text-center">
                Sudah punya akun?
                <a href="/" class="font-medium text-blue-600 hover:underline dark:text-blue-500">
                    Login Sekarang
                </a>
            </p>
        </div>
    </div>

    <script>
        function updateNimNipField() {
            const role = document.getElementById('role').value;
            const container = document.getElementById('nimNipContainer');
            const nipInput = document.getElementById('nip');
            const nimInput = document.getElementById('nim');
            const label = document.getElementById('nimNipLabel');
            const prodiContainer = document.getElementById('prodiContainer');

            if (role === 'dosen') {
                container.style.display = 'block';
                nipInput.style.display = 'block';
                nimInput.style.display = 'none';
                label.textContent = 'NIP (Nomor Induk Pegawai)';
                if (prodiContainer) prodiContainer.style.display = 'none';
            } else if (role === 'mahasiswa') {
                container.style.display = 'block';
                nipInput.style.display = 'none';
                nimInput.style.display = 'block';
                label.textContent = 'NIM (Nomor Induk Mahasiswa)';
                if (prodiContainer) prodiContainer.style.display = 'block';
            } else {
                container.style.display = 'none';
                nipInput.style.display = 'none';
                nimInput.style.display = 'none';
                if (prodiContainer) prodiContainer.style.display = 'none';
            }
        }

        // Initialize on page load
        updateNimNipField();
    </script>

</body>

</html>
