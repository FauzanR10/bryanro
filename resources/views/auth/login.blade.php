<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — BryanRO</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gradient-to-br from-blue-700 to-blue-900 flex items-center justify-center p-4">

    {{--
        HALAMAN LOGIN ADMIN
        ===================
        Halaman ini adalah pintu masuk sistem BryanRO.
        Hanya admin yang punya username & password yang bisa masuk.

        Desain: kartu putih di tengah layar, latar belakang biru gradient.
    --}}

    <div class="w-full max-w-sm">

        {{-- Logo & judul --}}
        <div class="text-center mb-8">
            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg">
                <span class="text-blue-700 font-bold text-2xl">BR</span>
            </div>
            <h1 class="text-white text-2xl font-bold">BryanRO</h1>
            <p class="text-blue-200 text-sm mt-1">Sistem Informasi Depot Air Galon</p>
        </div>

        {{-- Kartu form login --}}
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-gray-800 text-lg font-bold mb-1">Login Admin</h2>
            <p class="text-gray-500 text-xs mb-6">Masukkan username dan password Anda untuk mengakses sistem.</p>

            {{-- Pesan error dari redirect (misal: username/password salah) --}}
            @if(session('error'))
                <div class="mb-4 px-3 py-2.5 bg-red-50 border border-red-200 rounded-lg text-xs text-red-600">
                    ⚠️ {{ session('error') }}
                </div>
            @endif

            {{--
                FORM LOGIN
                - action="{{ route('login.proses') }}" → kirim ke POST /login
                - @csrf → wajib untuk semua form POST di Laravel (keamanan)
                - method="POST" → data tidak tampil di URL
            --}}
            <form action="{{ route('login.proses') }}" method="POST" class="space-y-4">
                @csrf

                {{-- Field Username --}}
                <div>
                    <label for="username" class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Username
                    </label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        {{-- old('username') → isi ulang form jika validasi gagal --}}
                        value="{{ old('username') }}"
                        placeholder="Masukkan username admin"
                        autocomplete="username"
                        class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition
                               {{ $errors->has('username') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                    >
                    {{-- Tampilkan error validasi jika ada --}}
                    @error('username')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Field Password --}}
                <div>
                    <label for="password" class="block text-xs font-semibold text-gray-700 mb-1.5">
                        Password
                    </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        placeholder="Masukkan password"
                        autocomplete="current-password"
                        class="w-full px-3.5 py-2.5 border rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 transition
                               {{ $errors->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}"
                    >
                    @error('password')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Tombol Login --}}
                <button
                    type="submit"
                    class="w-full bg-blue-600 hover:bg-blue-700 active:bg-blue-800 text-white font-semibold py-2.5 rounded-lg text-sm transition-colors mt-2">
                    Masuk ke Sistem
                </button>

            </form>
        </div>

        {{-- Footer --}}
        <p class="text-center text-blue-300 text-xs mt-6">
            &copy; {{ date('Y') }} BryanRO — Sistem Informasi Depot Air Galon
        </p>

    </div>

</body>
</html>
