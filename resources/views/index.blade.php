<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'SIAKAD') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Flowbite -->
    <link href="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/flowbite@4.0.1/dist/flowbite.min.js"></script>
</head>

<body class="bg-gray-50 dark:bg-gray-900" style="min-height:100vh;display:flex;align-items:center;justify-content:center;padding:0;">

    <div class="w-full max-w-md bg-white rounded-lg shadow dark:bg-gray-800 dark:border dark:border-gray-700 overflow-hidden" style="margin:0;">

        <!-- Logo + Title -->
        <div class="flex flex-col items-center p-6 pt-8 text-center">
            <div class="flex items-center justify-center w-24 h-24 mb-3 rounded-full overflow-hidden bg-white dark:bg-gray-900">
                <img src="{{ asset('Politeknik Bhakti Semesta.png') }}" alt="Logo Politeknik Bhakti Semesta" class="max-w-full max-h-full object-contain">
            </div>

            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                Politeknik Bhakti Semesta
            </h1>

            <p class="text-sm text-gray-500 dark:text-gray-300">
                Sistem Login Akademik
            </p>
        </div>

        <!-- Login Form -->
        <div class="p-6 space-y-6">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white text-center">
                Sign in to your account
            </h2>

            <!-- Error Messages -->
            @if ($errors->any())
                <div class="p-4 text-sm text-red-800 rounded-lg bg-red-50 dark:bg-gray-800 dark:text-red-400 border border-red-200">
                    <strong>⚠️ Login Gagal!</strong>
                    @foreach ($errors->all() as $error)
                        <div>• {{ $error }}</div>
                    @endforeach
                </div>
            @endif

            <form class="space-y-4" action="{{ route('login.authenticate') }}" method="POST">
                @csrf

                <!-- Role Selection -->
                <div>
                    <label for="role" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">
                        Login Sebagai <span class="text-red-500">*</span>
                    </label>
                    <select name="role" id="role" required value="{{ old('role') }}"
                        class="bg-gray-50 border @error('role') border-red-500 @else border-gray-300 @enderror text-gray-900 rounded-lg focus:ring-blue-600
                        focus:border-blue-600 block w-full p-2.5 dark:bg-gray-700 dark:border-gray-600
                        dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500
                        dark:focus:border-blue-500">
                        <option value="">-- Pilih Role --</option>
                        <option value="staff" @selected(old('role') === 'staff')> Staff/Admin</option>
                        <option value="dosen" @selected(old('role') === 'dosen')> Dosen</option>
                        <option value="mahasiswa" @selected(old('role') === 'mahasiswa')> Mahasiswa</option>
                    </select>
                    @error('role')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
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
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Button -->
                <button type="submit"
                    class="w-full text-white bg-blue-600 hover:bg-blue-700 focus:ring-4 focus:outline-none
                    focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center
                    dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                    Sign In
                </button>
            </form>

            <p class="text-sm font-light text-gray-500 dark:text-gray-400 text-center">
                Belum punya akun?
                <a href="/register" class="font-medium text-blue-600 hover:underline dark:text-blue-500">
                    Daftar Sekarang
                </a>
            </p>
        </div>
    </div>

</body>

</html>