@extends('layouts.operator')
@section('title', 'Detail Catatan Meter')

@section('content')
<div>
    <a href="{{ route('operator.catatan-meter.index') }}" 
        class="text-sm color-siap mt-8 mb-6 block">
        <i class="fa-solid fa-angle-left text-sm mr-1"></i>
    Kembali</a>
</div>

<h1 class="text-xl font-bold text-gray-800 mb-4">Detail Catatan Meter</h1>

<div class="bg-white rounded-xl shadow-sm p-4 mb-4 space-y-3">
    <div class="flex justify-between text-sm">
        <span class="text-gray-500">Pelanggan</span>
        <span class="font-medium">{{ $catatanMeter->pelanggan->nama }}</span>
    </div>
    <div class="flex justify-between text-sm">
        <span class="text-gray-500">No. Sambungan</span>
        <span>{{ $catatanMeter->pelanggan->nomor_sambungan }}</span>
    </div>
    <div class="flex justify-between text-sm">
        <span class="text-gray-500">Periode</span>
        <span>{{ $catatanMeter->periode->labelBulan() }}</span>
    </div>
    <hr>
    <div class="flex justify-between text-sm">
        <span class="text-gray-500">Meter Lalu</span>
        <span>{{ number_format($catatanMeter->angka_meter_lalu, 2) }} m³</span>
    </div>
    <div class="flex justify-between text-sm">
        <span class="text-gray-500">Meter Sekarang</span>
        <span>{{ number_format($catatanMeter->angka_meter_sekarang, 2) }} m³</span>
    </div>
    <div class="flex justify-between text-sm font-semibold">
        <span class="text-gray-600">Pemakaian</span>
        <span class="text-blue-600">{{ number_format($catatanMeter->pemakaian, 2) }} m³</span>
    </div>
    <hr>
    <div class="flex justify-between text-sm">
        <span class="text-gray-500">Kondisi</span>
        <span class="capitalize @if($catatanMeter->status_kondisi !== 'normal') text-yellow-600 font-medium @endif">
            {{ str_replace('_', ' ', $catatanMeter->status_kondisi) }}
        </span>
    </div>
    @if($catatanMeter->catatan)
    <div class="flex justify-between text-sm">
        <span class="text-gray-500">Catatan</span>
        <span class="text-right max-w-xs">{{ $catatanMeter->catatan }}</span>
    </div>
    @endif
    <div class="flex justify-between text-sm">
        <span class="text-gray-500">Dicatat</span>
        <span>{{ $catatanMeter->dicatat_at->format('d M Y, H:i') }}</span>
    </div>
</div>

{{-- Foto --}}
@if($catatanMeter->foto_path)
<div class="bg-white rounded-xl shadow-sm p-4 mb-4">
    <p class="text-sm text-gray-500 mb-2">Foto Meteran</p>
    <img src="{{ Storage::url($catatanMeter->foto_path) }}"
         alt="Foto meteran" class="rounded-lg w-full object-cover max-h-64">
</div>
@endif

{{-- Tagihan --}}
@if($catatanMeter->tagihan)
@php $tagihan = $catatanMeter->tagihan; @endphp
<div class="bg-green-50 border border-green-200 rounded-xl p-4">
    <h2 class="font-semibold text-green-800 mb-3 text-sm">✅ Tagihan Tergenerate</h2>
    <div class="space-y-2 text-sm">
        <div class="flex justify-between">
            <span class="text-gray-500">Pemakaian</span>
            <span>{{ number_format($tagihan->total_pemakaian, 2) }} m³</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Biaya Air</span>
            <span>Rp {{ number_format($tagihan->biaya_air, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Biaya Admin</span>
            <span>Rp {{ number_format($tagihan->biaya_admin, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Biaya Beban</span>
            <span>Rp {{ number_format($tagihan->biaya_beban, 0, ',', '.') }}</span>
        </div>
        <hr class="border-green-200">
        <div class="flex justify-between font-bold">
            <span>Total Tagihan</span>
            <span class="text-green-700">Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between text-xs text-gray-400">
            <span>Status</span>
            <span class="capitalize font-medium
                @if($tagihan->status === 'lunas') text-green-600
                @elseif($tagihan->status === 'menunggu_verifikasi') text-yellow-600
                @else text-red-500 @endif">
                {{ $tagihan->labelStatus() }}
            </span>
        </div>
    </div>
</div>
@else
<div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-sm text-yellow-700">
    ⚠️ Tagihan belum digenerate — kondisi meteran tidak normal.
</div>
@endif
@endsection