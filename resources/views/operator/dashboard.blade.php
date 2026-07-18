@extends('layouts.operator')
@section('title', 'Dashboard')
@section('header', 'Dashboard')

@section('content')
<h1 class="text-xl font-bold text-gray-800 mb-1">Selamat datang, {{ auth()->user()->name }}</h1>

{{-- Periode aktif --}}
@if($periodeAktif)
    <div class="bg-green-50 border border-green-300 text-green-800 rounded-lg px-4 py-2 text-sm mb-4">
        <i class="fas fa-calendar-check mr-1"></i>
        Periode aktif: <strong>{{ $periodeAktif->labelBulan() }}</strong>
    </div>
@else
    <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 rounded-lg px-4 py-2 text-sm mb-4">
        ⚠️ Tidak ada periode pencatatan yang sedang buka.
    </div>
@endif

{{-- Statistik --}}
<div class="grid grid-cols-3 gap-3 mb-6">
    <div class="bg-white rounded-xl shadow-sm p-4 text-center">
        <div class="text-2xl font-bold text-blue-600">{{ $totalPelanggan }}</div>
        <div class="text-xs text-gray-500 mt-1">Total Pelanggan</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 text-center">
        <div class="text-2xl font-bold text-green-600">{{ $sudahDicatat }}</div>
        <div class="text-xs text-gray-500 mt-1">Sudah Dicatat</div>
    </div>
    <div class="bg-white rounded-xl shadow-sm p-4 text-center">
        <div class="text-2xl font-bold text-red-500">{{ $belumDicatat }}</div>
        <div class="text-xs text-gray-500 mt-1">Belum Dicatat</div>
    </div>
</div>

{{-- Progress bar --}}
@if($totalPelanggan > 0)
@php $persen = round(($sudahDicatat / $totalPelanggan) * 100); @endphp
<div class="bg-white rounded-xl shadow-sm p-4 mb-6">
    <div class="flex justify-between text-sm text-gray-600 mb-2">
        <span>Progress Pencatatan</span>
        <span class="font-semibold">{{ $persen }}%</span>
    </div>
    <div class="w-full bg-gray-200 rounded-full h-3">
        <div class="bg-blue-500 h-3 rounded-full transition-all" style="width: {{ $persen }}%"></div>
    </div>
</div>
@endif

{{-- Shortcut --}}
@if($periodeAktif)
<a href="{{ route('operator.catatan-meter.index') }}"
   class="flex items-center justify-center gap-2 w-full bg-deep text-white text-center font-semibold py-3.5 mt-4 rounded-2xl text-sm transition active:scale-[0.98]">
   <i class="fas fa-sign-out-alt fa-file-invoice"></i> 
   Input Catatan Meter
</a>
@endif

{{-- Catatan terbaru --}}
@if($catatanTerbaru->isNotEmpty())
<div class="flex justify-between items-center mb-2 mt-4">
    <h2 class="font-display font-bold text-deep text-sm">Catatan Terbaru</h2>
    <a href="{{ route('operator.catatan-meter.index') }}" class="text-xs text-aquadark font-semibold">Lihat semua →</a>
</div>
<div class="bg-white rounded-2xl shadow-card p-3 mb-3">
    <div class="divide-y divide-gray-100">
        @foreach($catatanTerbaru as $catatan)
        <a href="{{ route('operator.catatan-meter.show', $catatan->id) }}"
           class="flex justify-between items-center py-3 hover:bg-gray-50 active:bg-surface -mx-1 px-3 rounded-lg transition gap-3">
            
            <div class="flex items-center gap-2.5">
                <span class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-deep/5 text-deep">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                </span>
                <div>
                    <div class="text-sm text-ink font-medium">{{ $catatan->pelanggan->nama }}</div>
                    <div class="text-xs text-muted">{{ $catatan->pelanggan->nomor_sambungan }} · {{ $catatan->dicatat_at->format('d M Y') }}</div>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <div class="text-sm font-bold text-ink">
                        {{ number_format($catatan->pemakaian, 1) }} m³
                    </div>
                </div>
                
                <i class="fa-solid fa-chevron-right text-sm text-gray-400 flex-shrink-0"></i>
            </div>

        </a>
        @endforeach
    </div>
</div>
@endif
<button id="btn-install-app" class="flex items-center justify-center gap-2 w-full bg-deep text-white text-center font-semibold py-3.5 mt-4 rounded-2xl text-sm transition active:scale-[0.98]">
  <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v2a2 2 0 002 2h12a2 2 0 002-2v-2M7 10l5 5 5-5M12 15V3" />
  </svg>
  Install Aplikasi
</button>

<script>
    let deferredPrompt;
    const installBtn = document.getElementById('btn-install-app');

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault(); 
        deferredPrompt = e;
        installBtn.classList.remove('hidden');
        installBtn.classList.add('flex');
    });

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