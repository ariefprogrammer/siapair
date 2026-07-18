<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="theme-color" content="#0B3D3D">
    <title>@yield('title', 'Teller') — {{ $configGeneral->app_name ?? 'SIAP AIR' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="manifest" href="/manifest.json">
    <link rel="apple-touch-icon" href="/icon512_maskable.png">
    <script>
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js');
            });
        }
        
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        display: ['"Plus Jakarta Sans"', 'sans-serif'],
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        deep: '#0B3D3D',
                        deep2: '#0F5757',
                        aqua: '#14B8B0',
                        aquadark: '#0E8F89',
                        surface: '#F3F8F8',
                        ink: '#0B2B2B',
                        muted: '#6B8482',
                        amberish: '#F0A623',
                    },
                    boxShadow: {
                        card: '0 2px 18px rgba(11,61,61,0.07)',
                        pop: '0 8px 24px rgba(20,184,176,0.28)',
                    },
                }
            }
        }
    </script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        h1,h2,h3,.font-display { font-family: 'Plus Jakarta Sans', sans-serif; }
        /* respect safe areas on notched devices */
        .safe-top { padding-top: env(safe-area-inset-top); }
        .safe-bottom { padding-bottom: env(safe-area-inset-bottom); }
        ::-webkit-scrollbar { display: none; }

        .color-siapair { color: #0B3D3D; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen">

    @if(($header ?? 0) == 1)
        {{-- Header with wave silhouette --}}
        <header class="safe-top relative" style="background: linear-gradient(135deg, #0B3D3D 0%, #0F5757 100%);">
            <nav class="text-white px-5 pt-4 pb-8 flex justify-between items-start">
                <div>
                    <p class="text-aqua text-[11px] font-semibold tracking-widest uppercase flex items-center gap-1">
                        @if($configGeneral && $configGeneral->app_logo)
                            <img src="{{ asset('storage/' . $configGeneral->app_logo) }}" alt="Logo" class="w-4 h-4 rounded-full object-cover">
                        @else
                            <span>💧</span>
                        @endif
                        {{ $configGeneral->app_name ?? 'SIAP AIR' }} - Teller
                    </p>
                    <h1 class="font-display text-xl font-bold mt-1 leading-tight">@yield('header', 'Dashboard')</h1>
                </div>                
            </nav>

            {{-- wave divider — signature water motif --}}
            <svg class="absolute -bottom-px left-0 w-full" viewBox="0 0 400 24" preserveAspectRatio="none" style="height: 22px;">
                <path d="M0,12 C60,24 100,0 160,10 C220,20 260,2 320,12 C350,17 380,8 400,12 L400,24 L0,24 Z"
                    fill="#F3F8F8"></path>
            </svg>
        </header>
    @endif

    {{-- Bottom nav (mobile-first) --}}    
    <div class="safe-bottom fixed bottom-3 left-3 right-3 z-50">
        <div class="max-w-lg mx-auto bg-deep/95 backdrop-blur text-white rounded-2xl shadow-pop px-2 py-2 flex">
            @php
                $navItems = [
                    ['route' => 'teller.dashboard', 'is' => 'teller.dashboard', 'icon' => 'fa-solid fa-house', 'label' => 'Beranda'],
                    ['route' => 'teller.kasir.index', 'is' => 'teller.kasir.index', 'icon' => 'fa-solid fa-file-invoice', 'label' => 'Kasir'],                    
                    ['route' => 'teller.kasir.riwayat', 'is' => 'teller.kasir.riwayat', 'icon' => 'fa-solid fa-history', 'label' => 'Riwayat'],                    
                    ['route' => 'teller.profile.index', 'is' => 'teller.profile.index', 'icon' => 'fa-solid fa-user', 'label' => 'Profil'],                    
                ];
            @endphp
            @foreach($navItems as $item)
                @php $active = request()->routeIs($item['is']); @endphp
                <a href="{{ route($item['route']) }}"
                   class="flex-1 flex flex-col items-center justify-center gap-1 py-1.5 rounded-xl text-[10px] font-medium transition
                   {{ $active ? 'bg-aqua text-deep' : 'text-white/60' }}">
                    <i class="{{ $item['icon'] }} text-sm leading-none"></i>
                    <span>{{ $item['label'] }}</span>
                </a>
            @endforeach
        </div>
    </div>

    <main class="max-w-lg mx-auto px-4 pt-4 pb-24">

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-800 px-4 py-3 rounded mb-4 text-sm">
                {{ session('success') }}
            </div>
        @endif
        @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-800 px-4 py-3 rounded mb-4 text-sm">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

</body>
</html>