@extends('layouts.pelanggan')
@section('title', 'Riwayat Pemakaian')
@section('header', 'Riwayat Pemakaian')

@section('content')
<div class="mt-8 space-y-3">
    @forelse($riwayat as $catatan)
    @php $bisaLihatNota = $catatan->tagihan && $catatan->tagihan->isLunas(); @endphp
    @if($bisaLihatNota)
    <a href="{{ route('pelanggan.riwayat.nota', $catatan->id) }}" class="block active:scale-[0.99] transition">
    @endif
        <div class="bg-white rounded-2xl shadow-card p-4 border border-gray-50">
            <!-- Bagian Atas: Detail Bulan & Total Pemakaian -->
            <div class="flex justify-between items-start mb-3">
                <div class="flex items-center gap-2.5">
                    <span class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full bg-deep/5 text-deep">
                        <i class="fa-solid fa-chart-simple text-sm"></i>
                    </span>
                    <div>
                        <div class="text-sm text-ink font-semibold">{{ $catatan->periode->labelBulan() }}</div>
                        <div class="text-xs text-muted">
                            Dicatat {{ $catatan->dicatat_at->format('d M Y') }}
                        </div>
                    </div>
                </div>
                <div class="text-right">
                    <div class="text-base font-bold text-ink">
                        {{ number_format($catatan->pemakaian, 0, ',', '.') }} m³
                    </div>
                    @if($catatan->status_kondisi !== 'normal')
                        <span class="text-[10px] font-semibold bg-amber-50 text-amber-700 px-2 py-0.5 rounded-full uppercase tracking-wider">
                            {{ str_replace('_', ' ', $catatan->status_kondisi) }}
                        </span>
                    @endif
                </div>
            </div>

            <!-- Bagian Tengah: Detail Stand Meter -->
            <div class="flex items-center justify-between text-xs text-muted bg-gray-50/50 rounded-xl px-2 py-1">
                <div class="flex flex-col">
                    <span class="text-[10px] text-muted uppercase">Meter Awal</span>
                    <span class="font-medium text-ink">{{ number_format($catatan->angka_meter_lalu, 0, ',', '.') }}</span>
                </div>
                <i class="fa-solid fa-chevron-right text-[10px] text-gray-300"></i>
                <div class="flex flex-col text-right">
                    <span class="text-[10px] text-muted uppercase">Meter Akhir</span>
                    <span class="font-medium text-ink">{{ number_format($catatan->angka_meter_sekarang, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Bagian Bawah: Ringkasan Tagihan (Jika Ada) -->
            @if($catatan->tagihan)
            <div class="flex justify-between items-center pt-3 border-t border-gray-100">
                <span class="text-xs font-medium text-muted flex items-center gap-1.5">
                    <i class="fa-solid fa-file-invoice text-gray-400"></i>
                    Tagihan
                </span>
                <div class="text-right flex items-center gap-2">
                    <span class="text-xs font-bold text-ink">
                        Rp {{ number_format($catatan->tagihan->total_tagihan, 0, ',', '.') }}
                    </span>
                    <span class="text-[11px] font-semibold
                        @if($catatan->tagihan->isLunas()) text-green-600
                        @elseif($catatan->tagihan->isMenungguVerifikasi()) text-amber-600
                        @else text-red-500 @endif">
                        ({{ $catatan->tagihan->labelStatus() }})
                    </span>
                </div>
            </div>
            @endif
        </div>
    @if($bisaLihatNota)
    </a>
    @endif
    @empty
        <div class="text-center text-muted text-sm py-12">
            <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-chart-simple text-gray-300 text-lg"></i>
            </div>
            Belum ada riwayat pemakaian.
        </div>
    @endforelse
</div>

<div class="mt-4">{{ $riwayat->links() }}</div>
@endsection