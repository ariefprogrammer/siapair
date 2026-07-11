@extends('layouts.pelanggan')
@section('title', 'Detail Tagihan')
@section('header', 'Detail Tagihan')

@section('content')
<div>
    <a href="{{ route('pelanggan.tagihan.index') }}" 
        class="text-sm color-siap mt-8 mb-6 block">
        <i class="fa-solid fa-angle-left text-sm mr-1"></i>
        Kembali</a>
</div>

{{-- Status badge --}}
<div class="text-center mb-4">
    <span class="px-4 py-1.5 rounded-full text-sm font-semibold
        @if($tagihan->isLunas()) bg-green-100 text-green-700
        @elseif($tagihan->isMenungguVerifikasi()) bg-yellow-100 text-yellow-700
        @else bg-red-100 text-red-700 @endif">
        {{ $tagihan->labelStatus() }}
    </span>
</div>

{{-- Info tagihan --}}
<div class="bg-white rounded-2xl shadow-sm p-4 mb-4 space-y-3 text-sm">
    <div class="flex justify-between">
        <span class="text-gray-500">Periode</span>
        <span class="font-medium">{{ $tagihan->periode->labelBulan() }}</span>
    </div>
    <div class="flex justify-between">
        <span class="text-gray-500">Pemakaian</span>
        <span>{{ number_format($tagihan->total_pemakaian, 2) }} m³</span>
    </div>

    {{-- Breakdown tarif --}}
    @foreach($tagihan->breakdown_tarif as $b)
    <div class="flex justify-between text-xs text-gray-400 pl-3">
        <span>
            Tier {{ $b['tier'] }}:
            {{ number_format($b['pemakaian'], 2) }} m³
            × Rp {{ number_format($b['harga_per_m3'], 0, ',', '.') }}
        </span>
        <span>Rp {{ number_format($b['subtotal'], 0, ',', '.') }}</span>
    </div>
    @endforeach

    <div class="flex justify-between">
        <span class="text-gray-500">Biaya Air</span>
        <span>Rp {{ number_format($tagihan->biaya_air, 0, ',', '.') }}</span>
    </div>
    <div class="flex justify-between">
        <span class="text-gray-500">Biaya Admin</span>
        <span>Rp {{ number_format($tagihan->biaya_admin, 0, ',', '.') }}</span>
    </div>
    <hr>
    <div class="flex justify-between font-bold text-base">
        <span>Total Tagihan</span>
        <span class="text-blue-700">Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</span>
    </div>
    @if($tagihan->tanggal_jatuh_tempo)
    <div class="flex justify-between text-xs text-gray-400">
        <span>Jatuh Tempo</span>
        <span>{{ $tagihan->tanggal_jatuh_tempo->format('d M Y') }}</span>
    </div>
    @endif
</div>

{{-- Info pembayaran jika sudah ada --}}
@if($tagihan->pembayaran)
    <div class="bg-white rounded-2xl shadow-sm p-4 mb-4 text-sm space-y-2">
        <h2 class="font-semibold text-gray-700 mb-2">Info Pembayaran</h2>
        <div class="flex justify-between">
            <span class="text-gray-500">Metode</span>
            <span class="uppercase font-medium">{{ $tagihan->pembayaran->metode }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Tanggal Bayar</span>
            <span>{{ $tagihan->pembayaran->tanggal_bayar->format('d M Y, H:i') }}</span>
        </div>
        @if($tagihan->pembayaran->isQris())
            <div class="flex justify-between">
                <span class="text-gray-500">Status Verifikasi</span>
                <span class="font-medium
                    @if($tagihan->pembayaran->isDisetujui()) text-green-600
                    @elseif($tagihan->pembayaran->isDitolak()) text-red-600
                    @else text-yellow-600 @endif">
                    {{ $tagihan->pembayaran->labelStatusVerifikasi() }}
                </span>
            </div>
            @if($tagihan->pembayaran->catatan_verifikasi)
            <div class="bg-red-50 rounded-lg p-3 text-xs text-red-700">
                <strong>Catatan Admin:</strong> {{ $tagihan->pembayaran->catatan_verifikasi }}
            </div>
            @endif
            @if($tagihan->pembayaran->bukti_bayar_path)
            <div>
                <p class="text-gray-500 text-xs mb-1">Bukti Bayar</p>
                <img src="{{ asset('storage/' . $tagihan->pembayaran->bukti_bayar_path) }}"
                    alt="Bukti bayar" class="rounded-lg w-full max-h-48 object-cover">
            </div>
            @endif
        @endif
    </div>
    @if($tagihan->pembayaran && $tagihan->pembayaran->isDisetujui())
        <a href="{{ route('pelanggan.riwayat.nota', $tagihan->pembayaran->tagihan_id) }}"
            class="flex items-center justify-center gap-2 w-full bg-deep text-white text-center font-semibold py-3.5 mt-4 rounded-2xl text-sm transition active:scale-[0.98]">
            <i class="fa-solid fa-print mr-2"></i>
            Lihat Nota
        </a>
    @endif
@endif

{{-- Tombol aksi --}}
@if($tagihan->isBelumDibayar())
    <a href="{{ route('pelanggan.tagihan.qris', $tagihan->id) }}"
        class="block w-full text-white text-center font-semibold py-3 rounded-2xl text-sm transition active:scale-[0.98]"
        style="background: linear-gradient(135deg, #14B8B0, #0E8F89);">
        <i class="fas fa-qrcode"></i> Bayar via QRIS
    </a>
@elseif($tagihan->isMenungguVerifikasi())
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-sm text-yellow-700 text-center">
        ⏳ Bukti bayar sedang diverifikasi. Harap tunggu konfirmasi dari administrator.
    </div>
@endif
@endsection