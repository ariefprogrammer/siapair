<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#1d4ed8">
    <title>@yield('title', 'Pelanggan') — SIAP AIR</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="manifest" href="/manifest.json">
</head>
<body class="bg-gray-50 min-h-screen">

    {{-- Header --}}
    <div class="bg-blue-700 text-white px-4 pt-10 pb-6">
        <div class="flex justify-between items-start max-w-lg mx-auto">
            <div>
                <p class="text-blue-200 text-xs">💧 SIAP AIR</p>
                <h1 class="text-xl font-bold mt-0.5">@yield('header', 'Dashboard')</h1>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="text-blue-200 hover:text-white text-xs mt-1">
                    Logout
                </button>
            </form>
        </div>
    </div>

    {{-- Bottom Nav --}}
    <div class="fixed bottom-0 left-0 right-0 bg-white border-t border-gray-200 flex z-50 shadow-lg">
        <a href="{{ route('pelanggan.dashboard') }}"
           class="flex-1 flex flex-col items-center py-2 text-xs gap-0.5
           {{ request()->routeIs('pelanggan.dashboard') ? 'text-blue-600' : 'text-gray-400' }}">
            <span class="text-lg">🏠</span><span>Beranda</span>
        </a>
        <a href="{{ route('pelanggan.tagihan.index') }}"
           class="flex-1 flex flex-col items-center py-2 text-xs gap-0.5
           {{ request()->routeIs('pelanggan.tagihan.*') ? 'text-blue-600' : 'text-gray-400' }}">
            <span class="text-lg">📄</span><span>Tagihan</span>
        </a>
        <a href="{{ route('pelanggan.riwayat') }}"
           class="flex-1 flex flex-col items-center py-2 text-xs gap-0.5
           {{ request()->routeIs('pelanggan.riwayat') ? 'text-blue-600' : 'text-gray-400' }}">
            <span class="text-lg">📊</span><span>Riwayat</span>
        </a>
        <a href="{{ route('pelanggan.pengaduan.index') }}"
           class="flex-1 flex flex-col items-center py-2 text-xs gap-0.5
           {{ request()->routeIs('pelanggan.pengaduan.*') ? 'text-blue-600' : 'text-gray-400' }}">
            <span class="text-lg">📢</span><span>Pengaduan</span>
        </a>
    </div>

    {{-- Main content --}}
    <main class="max-w-lg mx-auto px-4 pb-24 -mt-3">

        @if(session('success'))
            <div class="bg-green-100 border border-green-300 text-green-800 text-sm rounded-xl px-4 py-3 mb-4 mt-4">
                ✅ {{ session('success') }}
            </div>
        @endif
        @if(session('info'))
            <div class="bg-blue-100 border border-blue-300 text-blue-800 text-sm rounded-xl px-4 py-3 mb-4 mt-4">
                ℹ️ {{ session('info') }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-100 border border-red-300 text-red-800 text-sm rounded-xl px-4 py-3 mb-4 mt-4">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif

        @yield('content')
    </main>

</body>
</html>