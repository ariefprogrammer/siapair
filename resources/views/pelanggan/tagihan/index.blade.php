@extends('layouts.pelanggan')
@section('title', 'Tagihan')
@section('header', 'Tagihan Saya')

@section('content')

{{-- Tagihan Aktif --}}
@if($tagihanAktif ?? false)
<div class="mt-6 mb-4">
    <h2 class="font-display font-bold text-deep text-sm px-1 mb-2">Tagihan Aktif</h2>
    <div class="rounded-3xl p-5 shadow-card border bg-white">
        <div class="flex justify-between items-start mb-4">
            <div>
                <div class="text-[11px] text-muted font-medium">Periode</div>
                <div class="font-display text-lg font-bold text-deep">{{ $tagihanAktif->periode->labelBulan() }}</div>
            </div>
            <span class="text-[11px] font-semibold px-2.5 py-1 rounded-full
                {{ $tagihanAktif->isMenungguVerifikasi() ? 'bg-amber-100 text-amber-700' : 'bg-red-100 text-red-700' }}">
                {{ $tagihanAktif->labelStatus() }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-3 mb-4">
            <div class="bg-surface rounded-2xl p-3">
                <div class="text-[11px] text-muted">Total Tagihan</div>
                <div class="font-display font-extrabold text-deep text-lg mt-0.5">
                    Rp {{ number_format($tagihanAktif->total_tagihan, 0, ',', '.') }}
                </div>
            </div>
            <div class="bg-surface rounded-2xl p-3">
                <div class="text-[11px] text-muted">Pemakaian Air</div>
                <div class="font-display font-extrabold text-deep text-lg mt-0.5">
                    {{ number_format($tagihanAktif->total_pemakaian, 2) }} m³
                </div>
            </div>
        </div>

        @if($tagihanAktif->isBelumDibayar())
            <a href="{{ route('pelanggan.tagihan.qris', $tagihanAktif->id) }}"
               class="block w-full text-white text-center font-semibold py-3 rounded-2xl text-sm transition active:scale-[0.98]"
               style="background: linear-gradient(135deg, #14B8B0, #0E8F89);">
                <i class="fas fa-qrcode"></i> Bayar via QRIS
            </a>
        @else
            <div class="text-xs text-amber-700 text-center font-medium">
                Bukti bayar sedang diverifikasi administrator.
            </div>
        @endif
    </div>

</div>
@endif

<div class="mt-4">
    <h2 class="font-display font-bold text-deep text-sm px-1 mb-2 mt-4">Metode Pembayaran</h2>
    <div class="rounded-3xl p-5 shadow-card border bg-white">
        
        <div class="flex justify-center items-center gap-3">
            
            <label class="flex-1 max-w-[140px] cursor-pointer group">
                <input type="radio" name="metode_pembayaran" value="qris" class="sr-only peer" checked>
                <div class="flex flex-col items-center justify-center p-4 rounded-2xl border-2 border-gray-100 bg-gray-50/50 text-center transition-all duration-200 
                            peer-checked:border-deep peer-checked:bg-deep/5 peer-checked:text-deep hover:bg-gray-50">
                    <div class="w-5 h-5 rounded-xl bg-white flex items-center justify-center shadow-sm mb-2 text-lg text-gray-500 peer-checked:group-[]:text-deep transition-colors">
                        <i class="fa-solid fa-qrcode text-xl"></i>
                    </div>
                    <span class="text-xs font-semibold text-ink">QRIS</span>
                </div>
            </label>

            <label class="flex-1 max-w-[140px] cursor-pointer group">
                <input type="radio" name="metode_pembayaran" value="tunai" class="sr-only peer">
                <div class="flex flex-col items-center justify-center p-4 rounded-2xl border-2 border-gray-100 bg-gray-50/50 text-center transition-all duration-200 
                            peer-checked:border-deep peer-checked:bg-deep/5 peer-checked:text-deep hover:bg-gray-50">
                    <div class="w-5 h-5 rounded-xl bg-white flex items-center justify-center shadow-sm mb-2 text-lg text-gray-500 peer-checked:group-[]:text-deep transition-colors">
                        <i class="fa-solid fa-money-bill-wave text-lg"></i>
                    </div>
                    <span class="text-xs font-semibold text-ink">Tunai</span>
                </div>
            </label>

        </div>

    </div>
</div>

{{-- Riwayat Tagihan --}}
<div class="flex justify-between items-center mb-2 mt-4">
    <h2 class="font-display font-bold text-deep text-sm">Riwayat Tagihan</h2>
</div>
<div class="bg-white rounded-2xl shadow-card p-3 mb-3">
    <div class="divide-y divide-gray-100">
        @foreach($tagihan as $t)
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

<div class="mt-4">{{ $tagihan->links() }}</div>
@endsection