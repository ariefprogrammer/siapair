<x-filament-panels::page.simple>

    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@600;700;800&family=Inter:wght@400;500;600&family=JetBrains+Mono:wght@500&display=swap" rel="stylesheet">

    <style>
        .siapair-login * { font-family: 'Inter', sans-serif; }
        .siapair-login .font-display { font-family: 'Plus Jakarta Sans', sans-serif; }
        .siapair-login .font-mono { font-family: 'JetBrains Mono', monospace; }

        @keyframes siapair-drift {
            0%   { transform: translateX(0); }
            100% { transform: translateX(-120px); }
        }
        .siapair-current path { animation: siapair-drift 14s linear infinite; }
        .siapair-current path:nth-child(2) { animation-duration: 20s; animation-direction: reverse; }
        .siapair-current path:nth-child(3) { animation-duration: 26s; }

        @media (prefers-reduced-motion: reduce) {
            .siapair-current path { animation: none; }
        }
    </style>

    <div class="siapair-login grid lg:grid-cols-2 overflow-y-auto" style="position:fixed; inset:0; background-color:#F3F8F8;">

        {{-- Panel kiri — identitas & motif aliran air --}}
        <div class="hidden lg:flex relative flex-col justify-between overflow-hidden px-12 py-12"
             style="background: linear-gradient(160deg, #0B3D3D 0%, #0F5757 100%);">

            {{-- tekstur titik halus, sangat pelan, sekadar tekstur latar --}}
            <div class="absolute inset-0 opacity-[0.06]"
                 style="background-image: radial-gradient(#14B8B0 1px, transparent 1px); background-size: 22px 22px;"></div>

            {{-- Header panel kiri --}}
            <div class="relative z-10">
                <div class="flex items-center gap-2.5 mb-16">
                    @if ($logo = filament()->getBrandLogo())
                        <img src="{{ $logo }}" alt="Logo" class="w-9 h-9 rounded-xl object-cover">
                    @else
                        <span class="text-xl">💧</span>
                    @endif
                    <span class="font-mono text-[11px] tracking-[0.2em] uppercase" style="color:#14B8B0;">
                        Sistem Informasi Air Perpipaan
                    </span>
                </div>

                <h1 class="font-display font-bold text-white leading-[1.1] mb-5" style="font-size: 2.5rem; letter-spacing:-0.02em;">
                    Air mengalir,<br>data pun mengikuti.
                </h1>
                <p class="max-w-sm text-sm leading-relaxed" style="color:#9FBFBD;">
                    Satu panel untuk mengelola pencatatan meter, tagihan, dan pengaduan warga di seluruh wilayah layanan.
                </p>
            </div>

            {{-- Daftar modul nyata aplikasi --}}
            <div class="relative z-10 space-y-4">
                <div class="flex items-center gap-3">
                    <span class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center" style="background:rgba(20,184,176,0.12);">
                        <i class="fa-solid fa-gauge-high text-sm" style="color:#14B8B0;"></i>
                    </span>
                    <span class="text-sm font-medium text-white">Pencatatan Meter</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center" style="background:rgba(20,184,176,0.12);">
                        <i class="fa-solid fa-file-invoice text-sm" style="color:#14B8B0;"></i>
                    </span>
                    <span class="text-sm font-medium text-white">Tagihan &amp; Pembayaran</span>
                </div>
                <div class="flex items-center gap-3">
                    <span class="flex-shrink-0 w-9 h-9 rounded-lg flex items-center justify-center" style="background:rgba(20,184,176,0.12);">
                        <i class="fa-solid fa-comment-dots text-sm" style="color:#14B8B0;"></i>
                    </span>
                    <span class="text-sm font-medium text-white">Pengaduan Warga</span>
                </div>
            </div>

            {{-- Motif arus air — signature element, bleeding di tepi bawah --}}
            <svg class="siapair-current absolute -bottom-6 -left-10 w-[calc(100%+80px)] opacity-70" viewBox="0 0 600 160" preserveAspectRatio="none">
                <path d="M-50,90 C 80,40 160,140 300,90 S 520,40 650,90" fill="none" stroke="#14B8B0" stroke-width="2" stroke-linecap="round" opacity="0.5"/>
                <path d="M-50,115 C 80,70 160,160 300,115 S 520,70 650,115" fill="none" stroke="#F0A623" stroke-width="1.5" stroke-linecap="round" opacity="0.35"/>
                <path d="M-50,135 C 80,100 160,175 300,135 S 520,100 650,135" fill="none" stroke="#14B8B0" stroke-width="1.5" stroke-linecap="round" opacity="0.25"/>
            </svg>

            <p class="relative z-10 font-mono text-[11px] mt-10" style="color:#5A8C88;">
                &copy; {{ date('Y') }} {{ filament()->getBrandName() ?? 'SIAP AIR' }}
            </p>
        </div>

        {{-- Panel kanan — form login --}}
        <div class="flex flex-col items-center justify-center px-6 py-12">
            <div class="w-full max-w-sm">

                {{-- Logo mobile saja --}}
                <div class="flex lg:hidden items-center gap-2.5 justify-center mb-8">
                    @if ($logo = filament()->getBrandLogo())
                        <img src="{{ $logo }}" alt="Logo" class="w-10 h-10 rounded-xl object-cover">
                    @else
                        <span class="text-2xl">💧</span>
                    @endif
                    <span class="font-display font-bold text-lg" style="color:#0B2B2B;">
                        {{ filament()->getBrandName() ?? 'SIAP AIR' }}
                    </span>
                </div>

                <div class="mb-7">
                    <h2 class="font-display font-bold text-2xl" style="color:#0B2B2B;">Masuk</h2>
                    <p class="text-sm mt-1.5" style="color:#6B8482;">
                        Masukkan kredensial Anda untuk mengakses panel admin.
                    </p>
                </div>

                <x-filament-panels::form wire:submit="authenticate">
                    {{ $this->form }}

                    <x-filament-panels::form.actions
                        :actions="$this->getCachedFormActions()"
                        :full-width="$this->hasFullWidthFormActions()"
                    />
                </x-filament-panels::form>

                <p class="text-center font-mono text-[11px] mt-10 lg:hidden" style="color:#6B8482;">
                    &copy; {{ date('Y') }} {{ filament()->getBrandName() ?? 'SIAP AIR' }}
                </p>
            </div>
        </div>

    </div>

</x-filament-panels::page.simple>