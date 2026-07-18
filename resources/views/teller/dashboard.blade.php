@extends('layouts.teller')
@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<h1 class="text-xl font-bold text-gray-800 mb-1">Selamat datang, {{ auth()->user()->name }}</h1>
<p class="text-sm text-gray-400 mb-5">{{ now()->translatedFormat('l, d F Y') }}</p>

{{-- Statistik --}}
<div class="grid grid-cols-3 gap-3 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-4 text-center">
        <div class="text-2xl font-bold text-teal-600">{{ $totalTransaksiHariIni }}</div>
        <div class="text-xs text-gray-500 mt-1">Transaksi Hari Ini</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 text-center col-span-2">
        <div class="text-2xl font-bold text-green-600">
            Rp {{ number_format($totalPendapatanHariIni, 0, ',', '.') }}
        </div>
        <div class="text-xs text-gray-500 mt-1">Pendapatan Hari Ini</div>
    </div>
</div>

<div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-5 flex justify-between items-center">
    <div>
        <div class="text-sm font-semibold text-yellow-800">Tagihan Belum Dibayar</div>
        <div class="text-xs text-yellow-600">Dari seluruh pelanggan</div>
    </div>
    <div class="text-2xl font-bold text-yellow-600">{{ $totalBelumDibayar }}</div>
</div>

{{-- Shortcut Kasir --}}
<a href="{{ route('teller.kasir.index') }}"
   class="block w-full text-white text-center font-semibold py-3 mb-4 rounded-2xl text-sm transition active:scale-[0.98]"
    style="background: linear-gradient(135deg, #0B3D3D, #0B3D3D);">
    <i class="fas fa-cash-register"></i>
    Buka Kasir
</a>

{{-- Transaksi Terbaru --}}
@if($transaksiTerbaru->isNotEmpty())
<div class="flex justify-between items-center mb-2 mt-6">
    <h2 class="font-display font-bold text-deep text-sm">Transaksi Terbaru</h2>
    <a href="{{ route('teller.kasir.riwayat') }}" class="text-xs text-aquadark font-semibold">Lihat semua →</a>
</div>
<div class="bg-white rounded-2xl shadow-card p-3 mb-3">
    <div class="divide-y divide-gray-100">
        @foreach($transaksiTerbaru as $trx)
        <a href="{{ route('teller.kasir.nota', $trx->id) }}"
           class="flex justify-between items-center py-3 hover:bg-gray-50 active:bg-surface -mx-1 px-3 rounded-lg transition gap-3">
            
            <div class="flex items-center gap-2.5">
                <span class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-deep/5 text-deep">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                </span>
                <div>
                    <div class="text-sm text-ink font-medium">{{ $trx->tagihan->pelanggan->nama }}</div>
                    <div class="text-xs text-muted">{{ $trx->nomor_nota }} · {{ $trx->tanggal_bayar->format('H:i') }}</div>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <div class="text-sm font-bold text-ink">
                        Rp {{ number_format($trx->jumlah_bayar, 0, ',', '.') }}
                    </div>
                </div>
                
                <i class="fa-solid fa-chevron-right text-sm text-gray-400 flex-shrink-0"></i>
            </div>

        </a>
        @endforeach
    </div>
</div>
@endif
<button id="btn-install-app" class="hidden items-center justify-center gap-2 w-full bg-deep text-white text-center font-semibold py-3.5 mt-4 rounded-2xl text-sm transition active:scale-[0.98]">
  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
  </svg>
  Install Aplikasi
</button>
<script>
    let deferredPrompt;
    const installBtn = document.getElementById('btn-install-app');

    function isAppInstalled() {
        return window.matchMedia('(display-mode: standalone)').matches
            || window.navigator.standalone === true; 
    }

    if (isAppInstalled()) {
        installBtn?.classList.add('hidden');
    } else {
        window.addEventListener('beforeinstallprompt', (e) => {
            e.preventDefault();
            deferredPrompt = e;
            installBtn.classList.remove('hidden');
            installBtn.classList.add('flex');
        });
    }

    installBtn?.addEventListener('click', async () => {
        if (!deferredPrompt) return;
        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        deferredPrompt = null;
        installBtn.classList.add('hidden');
    });

    window.addEventListener('appinstalled', () => {
        installBtn.classList.add('hidden');
        deferredPrompt = null;
    });
</script>
@endsection