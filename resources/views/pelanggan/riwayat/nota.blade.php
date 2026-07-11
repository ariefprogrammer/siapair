@extends('layouts.pelanggan')
@section('title', 'Nota Pembayaran')

@section('content')
<div class="flex justify-between items-center mb-4">
    <a href="{{ route('pelanggan.riwayat') }}" class="text-sm color-siap mt-8 mb-6 block">
        <i class="fa-solid fa-angle-left text-sm mr-1"></i>Riwayat
    </a>
</div>

{{-- Nota --}}
<div id="nota" class="bg-white rounded-xl shadow-sm p-6 font-mono text-sm">

    {{-- Header --}}
    <div class="text-center mb-4">
        <div class="text-lg font-bold">💧 SIAP AIR</div>
        <div class="text-xs text-gray-500">Sistem Informasi Air Perpipaan</div>
        <div class="border-t border-dashed border-gray-300 mt-3 pt-3 text-xs text-gray-400">
            NOTA PEMBAYARAN
        </div>
    </div>

    {{-- Info nota --}}
    <div class="space-y-1 text-xs mb-4">
        <div class="flex justify-between">
            <span class="text-gray-500">No. Nota</span>
            <span class="font-semibold">{{ $pembayaran->nomor_nota }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Tanggal</span>
            <span>{{ $pembayaran->tanggal_bayar->format('d/m/Y H:i') }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Diproses oleh</span>
            <span>
                @if($pembayaran->metode === 'qris')
                    {{ $pembayaran->adminVerifikasi?->name ?? '-' }}
                @else
                    {{ $pembayaran->teller?->name ?? '-' }}
                @endif
            </span>
        </div>
    </div>

    <div class="border-t border-dashed border-gray-300 my-3"></div>

    {{-- Data pelanggan --}}
    <div class="space-y-1 text-xs mb-4">
        <div class="flex justify-between">
            <span class="text-gray-500">Pelanggan</span>
            <span class="font-semibold text-right max-w-[180px]">
                {{ $pembayaran->tagihan->pelanggan->nama }}
            </span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">No. Sambungan</span>
            <span>{{ $pembayaran->tagihan->pelanggan->nomor_sambungan }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Periode</span>
            <span>{{ $pembayaran->tagihan->periode->labelBulan() }}</span>
        </div>
    </div>

    <div class="border-t border-dashed border-gray-300 my-3"></div>

    <div class="space-y-1 text-xs mb-3">
        <div class="flex justify-between">
            <span class="text-gray-500">Meter Lalu</span>
            <span>{{ number_format($pembayaran->tagihan->catatanMeter->angka_meter_lalu, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Meter Sekarang</span>
            <span>{{ number_format($pembayaran->tagihan->catatanMeter->angka_meter_sekarang, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Pemakaian</span>
            <span>{{ number_format($pembayaran->tagihan->total_pemakaian, 2) }} m³</span>
        </div>
    
        @foreach($pembayaran->tagihan->breakdown_tarif as $b)
        <div class="flex justify-between text-gray-400 pl-2">
            <span>
                Tier {{ $b['tier'] }}
                {{ number_format($b['pemakaian'], 2) }}m³
                × {{ number_format($b['harga_per_m3'], 0) }}
            </span>
            <span>Rp {{ number_format($b['subtotal'], 0, ',', '.') }}</span>
        </div>
        @endforeach
    </div>

    <div class="border-t border-dashed border-gray-300 my-3"></div>

    {{-- Rincian tagihan --}}
    <div class="space-y-1 text-xs mb-3">

        <div class="flex justify-between">
            <span class="text-gray-500">Biaya Air</span>
            <span>Rp {{ number_format($pembayaran->tagihan->biaya_air, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Biaya Admin</span>
            <span>Rp {{ number_format($pembayaran->tagihan->biaya_admin, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Biaya Beban</span>
            <span>Rp {{ number_format($pembayaran->tagihan->biaya_beban, 0, ',', '.') }}</span>
        </div>
    </div>

    <div class="border-t border-dashed border-gray-300 my-3"></div>

    {{-- Total --}}
    <div class="flex justify-between font-bold text-base mb-1">
        <span>TOTAL BAYAR</span>
        <span>Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}</span>
    </div>
    <div class="text-xs text-gray-400 flex justify-between">
        <span>Metode</span>
        <span>{{ $pembayaran->metode }}</span>
    </div>

    <div class="border-t border-dashed border-gray-300 my-4"></div>

    <div class="text-center text-xs text-gray-400">
        <div>Terima kasih atas pembayaran Anda.</div>
        <div class="mt-1">Simpan nota ini sebagai bukti pembayaran.</div>
        <div class="mt-3 text-gray-300">— SIAP AIR —</div>
    </div>
</div>

{{-- Aksi --}}
<div class="mt-4">
    <button onclick="window.print()"
        class="w-full text-center bg-surface border border-gray-300 hover:bg-gray-50 text-gray-700 font-semibold py-3 rounded-xl transition text-sm flex items-center justify-center">
        <i class="fa-solid fa-print mr-2"></i>
        Cetak
    </button>
</div>

<style>
@media print {
    body * { visibility: hidden; }
    #nota, #nota * { visibility: visible; }
    #nota { position: fixed; top: 0; left: 0; width: 100%; }
    nav, .fixed { display: none !important; }
}
</style>
@endsection