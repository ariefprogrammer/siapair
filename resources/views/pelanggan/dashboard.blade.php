@extends('layouts.pelanggan')
@section('title', 'Beranda')
@section('header', 'Halo, ' . explode(' ', $pelanggan->nama)[0])

@section('content')

{{-- Info sambungan --}}
<div class="bg-white rounded-2xl shadow-card p-4 mt-3 mb-3 flex justify-between items-center">
    <div>
        <div class="text-[11px] text-muted font-medium">ID Pelanggan</div>
        <div class="font-display font-bold text-deep text-lg">{{ $pelanggan->nomor_sambungan }}</div>
        <div class="text-xs text-muted mt-0.5">{{ $pelanggan->alamat }}</div>
    </div>
    <span class="text-[11px] font-semibold px-3 py-1 rounded-full
        {{ $pelanggan->isAktif() ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
        {{ $pelanggan->isAktif() ? '● Aktif' : '● Nonaktif' }}
    </span>
</div>

{{-- Tagihan aktif --}}
@if($tagihanAktif)
    <div class="rounded-2xl p-4 mb-3 shadow-card
        {{ $tagihanAktif->isMenungguVerifikasi() ? 'bg-amber-50 border border-amber-200' : 'bg-red-50 border border-red-200' }}">
        <div class="flex justify-between items-start mb-3">
            <div>
                <div class="text-[11px] text-muted font-medium">Tagihan {{ $tagihanAktif->periode->labelBulan() }}</div>
                <div class="font-display text-2xl font-extrabold {{ $tagihanAktif->isMenungguVerifikasi() ? 'text-amber-700' : 'text-red-700' }}">
                    Rp {{ number_format($tagihanAktif->total_tagihan, 0, ',', '.') }}
                </div>
            </div>
            <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full
                {{ $tagihanAktif->isMenungguVerifikasi()
                    ? 'bg-amber-200 text-amber-800'
                    : 'bg-red-200 text-red-800' }}">
                {{ $tagihanAktif->labelStatus() }}
            </span>
        </div>

        @if($tagihanAktif->isBelumDibayar())
            <a href="{{ route('pelanggan.tagihan.qris', $tagihanAktif->id) }}"
               class="block w-full text-white text-center font-semibold py-3 rounded-2xl text-sm transition active:scale-[0.98]"
               style="background: linear-gradient(135deg, #0B3D3D, #0B3D3D);">
                <i class="fas fa-qrcode mr-2"></i> Bayar via QRIS
            </a>
        @else
            <div class="text-xs text-amber-700 text-center font-medium">
                Bukti bayar sedang diverifikasi administrator.
            </div>
        @endif
    </div>
@endif

{{-- Grafik riwayat pemakaian 12 bulan --}}
@php
    // Dummy data untuk demo tampilan — ganti dengan query riwayat pemakaian asli dari controller.
    // Contoh: $pemakaianTahunan = $pelanggan->tagihans()->latest()->take(12)->get()->reverse();
    $bulanLabel = ['Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun'];
    $pemakaianDummy = [14, 16, 12, 19, 21, 15, 17, 20, 23, 18, 16, 22];
    $rataRata = round(array_sum($pemakaianDummy) / count($pemakaianDummy), 1);
@endphp
<div class="bg-white rounded-2xl shadow-card p-4 mb-3">
    <div class="flex justify-between items-start mb-1">
        <div>
            <h2 class="font-display font-bold text-deep text-sm">Pemakaian Setahun Terakhir</h2>
            <p class="text-[11px] text-muted">Rata-rata {{ $rataRata }} m³ / bulan</p>
        </div>
        <span class="text-[11px] font-semibold text-aquadark bg-aqua/10 px-2.5 py-1 rounded-full">m³</span>
    </div>
    <div class="mt-2" style="height: 180px;">
        <canvas id="chartPemakaian"></canvas>
    </div>
</div>

{{-- Riwayat tagihan singkat --}}
@if($riwayatTagihan->isNotEmpty())
<div class="flex justify-between items-center mb-2 mt-4">
    <h2 class="font-display font-bold text-deep text-sm">Tagihan Terbaru</h2>
    <a href="{{ route('pelanggan.tagihan.index') }}" class="text-xs text-aquadark font-semibold">Lihat semua →</a>
</div>
<div class="bg-white rounded-2xl shadow-card p-3 mb-3">
    <div class="divide-y divide-gray-100">
        @foreach($riwayatTagihan as $t)
        <a href="{{ route('pelanggan.tagihan.show', $t->id) }}"
           class="flex justify-between items-center py-3 active:bg-surface -mx-1 px-3 rounded-lg transition gap-3">
            
            <div class="flex items-center gap-2.5">
                <span class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-deep/5 text-deep">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                </span>
                <div>
                    <div class="text-sm text-ink font-medium">{{ $t->periode->labelBulan() }}</div>
                    <div class="text-xs text-muted">{{ $t->total_pemakaian }} m³</div>
                </div>
            </div>
            
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <div class="text-sm font-bold text-ink">
                        Rp {{ number_format($t->total_tagihan, 0, ',', '.') }}
                    </div>
                    <span class="text-[11px] font-semibold
                        @if($t->isLunas()) text-green-600
                        @elseif($t->isMenungguVerifikasi()) text-amber-600
                        @else text-red-500 @endif">
                        {{ $t->labelStatus() }}
                    </span>
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.4/dist/chart.umd.min.js"></script>
<script>
    const ctx = document.getElementById('chartPemakaian');
    const gradient = ctx.getContext('2d').createLinearGradient(0, 0, 0, 180);
    gradient.addColorStop(0, 'rgba(20,184,176,0.35)');
    gradient.addColorStop(1, 'rgba(20,184,176,0.02)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($bulanLabel),
            datasets: [{
                label: 'Pemakaian (m³)',
                data: @json($pemakaianDummy),
                borderColor: '#0E8F89',
                backgroundColor: gradient,
                borderWidth: 2.5,
                pointRadius: 3,
                pointBackgroundColor: '#0E8F89',
                pointBorderColor: '#fff',
                pointBorderWidth: 1.5,
                tension: 0.35,
                fill: true,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: {
                    grid: { display: false },
                    ticks: { font: { size: 10 }, color: '#6B8482' }
                },
                y: {
                    beginAtZero: true,
                    grid: { color: '#F0F5F5' },
                    ticks: { font: { size: 10 }, color: '#6B8482', stepSize: 5 }
                }
            }
        }
    });

    let deferredPrompt;
    const installBtn = document.getElementById('btn-install-app');

    window.addEventListener('beforeinstallprompt', (e) => {
        e.preventDefault(); // cegah mini-infobar otomatis browser
        deferredPrompt = e;
        installBtn.classList.remove('hidden');
        installBtn.classList.add('flex');
    });

    installBtn?.addEventListener('click', async () => {
        if (!deferredPrompt) return;
        deferredPrompt.prompt();
        const { outcome } = await deferredPrompt.userChoice;
        // outcome: 'accepted' atau 'dismissed', bisa dikirim ke analytics kalau perlu
        deferredPrompt = null;
        installBtn.classList.add('hidden');
    });

    // Kalau user sudah install (dari sumber lain), sembunyikan tombol
    window.addEventListener('appinstalled', () => {
        installBtn.classList.add('hidden');
        deferredPrompt = null;
    });
</script>

@endsection