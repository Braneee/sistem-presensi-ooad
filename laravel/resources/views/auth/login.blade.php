<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin — Sistem Presensi</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen flex items-center justify-center"
      style="background: linear-gradient(135deg, #1F4E79 0%, #2E75B6 100%)">

    <div class="w-full max-w-md px-4">

        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="inline-flex w-16 h-16 bg-white/20 rounded-2xl items-center justify-center mb-4">
                <svg class="w-9 h-9 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-white">Sistem Presensi</h1>
            <p class="text-blue-200 text-sm mt-1">Face Recognition Attendance System</p>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <h2 class="text-lg font-semibold text-gray-800 mb-6">Masuk sebagai Admin</h2>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Email
                    </label>
                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           autocomplete="email"
                           required
                           class="w-full px-4 py-2.5 border rounded-xl text-sm outline-none transition-all
                                  {{ $errors->has('email') ? 'border-red-400 bg-red-50' : 'border-gray-300 focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20' }}"
                           placeholder="admin@presensi.id">
                    @error('email')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password -->
                <div class="mb-6">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1.5">
                        Password
                    </label>
                    <input type="password"
                           id="password"
                           name="password"
                           required
                           class="w-full px-4 py-2.5 border border-gray-300 rounded-xl text-sm outline-none transition-all focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20"
                           placeholder="••••••••">
                    @error('password')
                        <p class="mt-1.5 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="flex items-center mb-6">
                    <input type="checkbox" id="remember" name="remember"
                           class="w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                    <label for="remember" class="ml-2 text-sm text-gray-600">Ingat saya</label>
                </div>

                <!-- Submit -->
                <button type="submit"
                        class="w-full py-3 rounded-xl text-white font-semibold text-sm transition-all hover:opacity-90 active:scale-[0.98]"
                        style="background: linear-gradient(135deg, #1F4E79, #2E75B6)">
                    Masuk
                </button>
            </form>

            <!-- Demo credentials hint -->
            <div class="mt-6 p-3 bg-blue-50 rounded-xl border border-blue-100">
                <p class="text-xs text-blue-700 font-medium mb-1">Demo Credentials:</p>
                <p class="text-xs text-blue-600">Email: <code class="font-mono">admin@presensi.id</code></p>
                <p class="text-xs text-blue-600">Password: <code class="font-mono">password123</code></p>
            </div>
        </div>

        <!-- Back to attendance -->
        <p class="text-center mt-6">
            <a href="{{ route('attendance.index') }}" class="text-blue-100 hover:text-white text-sm transition-colors">
                ← Kembali ke Halaman Presensi
            </a>
        </p>
    </div>

</body>
</html>
