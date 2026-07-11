@extends('layouts.pelanggan')
@section('title', 'Beranda')
@section('header', 'Halo, ' . auth()->user()->name)

@section('content')

{{-- Info sambungan --}}
<div class="bg-white rounded-2xl shadow-sm p-4 mt-4 mb-4 flex justify-between items-center">
    <div>
        <div class="text-xs text-gray-400">No. Sambungan</div>
        <div class="font-bold text-blue-700 text-lg">{{ $pelanggan->nomor_sambungan }}</div>
        <div class="text-xs text-gray-500 mt-0.5">{{ $pelanggan->alamat }}</div>
    </div>
    <span class="text-xs px-3 py-1 rounded-full
        {{ $pelanggan->isAktif() ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
        {{ $pelanggan->isAktif() ? 'Aktif' : 'Nonaktif' }}
    </span>
</div>

{{-- Tagihan aktif --}}
@if($tagihanAktif)
    <div class="rounded-2xl p-4 mb-4 shadow-sm
        {{ $tagihanAktif->isMenungguVerifikasi() ? 'bg-yellow-50 border border-yellow-200' : 'bg-red-50 border border-red-200' }}">
        <div class="flex justify-between items-start mb-3">
            <div>
                <div class="text-xs text-gray-500">Tagihan {{ $tagihanAktif->periode->labelBulan() }}</div>
                <div class="text-2xl font-bold {{ $tagihanAktif->isMenungguVerifikasi() ? 'text-yellow-700' : 'text-red-700' }}">
                    Rp {{ number_format($tagihanAktif->total_tagihan, 0, ',', '.') }}
                </div>
            </div>
            <span class="text-xs px-2 py-1 rounded-full
                {{ $tagihanAktif->isMenungguVerifikasi()
                    ? 'bg-yellow-200 text-yellow-800'
                    : 'bg-red-200 text-red-800' }}">
                {{ $tagihanAktif->labelStatus() }}
            </span>
        </div>

        @if($tagihanAktif->isBelumDibayar())
            <a href="{{ route('pelanggan.tagihan.qris', $tagihanAktif->id) }}"
               class="block w-full bg-blue-600 hover:bg-blue-700 text-white text-center font-semibold py-2.5 rounded-xl text-sm transition">
                📲 Bayar via QRIS
            </a>
        @else
            <div class="text-xs text-yellow-700 text-center">
                Bukti bayar sedang diverifikasi administrator.
            </div>
        @endif
    </div>

@elseif($totalTunggakan == 0)
    <div class="bg-green-50 border border-green-200 rounded-2xl p-4 mb-4 text-center">
        <div class="text-3xl mb-1">✅</div>
        <div class="text-sm font-semibold text-green-700">Semua tagihan lunas</div>
        <div class="text-xs text-green-500">Tidak ada tunggakan.</div>
    </div>
@endif

{{-- Riwayat tagihan singkat --}}
@if($riwayatTagihan->isNotEmpty())
<div class="bg-white rounded-2xl shadow-sm p-4 mb-4">
    <div class="flex justify-between items-center mb-3">
        <h2 class="font-semibold text-gray-700 text-sm">Tagihan Terbaru</h2>
        <a href="{{ route('pelanggan.tagihan.index') }}" class="text-xs text-blue-600">Lihat semua →</a>
    </div>
    <div class="divide-y divide-gray-100">
        @foreach($riwayatTagihan as $t)
        <a href="{{ route('pelanggan.tagihan.show', $t->id) }}"
           class="flex justify-between items-center py-3">
            <div>
                <div class="text-sm text-gray-700">{{ $t->periode->labelBulan() }}</div>
                <div class="text-xs text-gray-400">{{ $t->total_pemakaian }} m³</div>
            </div>
            <div class="text-right">
                <div class="text-sm font-semibold text-gray-800">
                    Rp {{ number_format($t->total_tagihan, 0, ',', '.') }}
                </div>
                <span class="text-xs
                    @if($t->isLunas()) text-green-600
                    @elseif($t->isMenungguVerifikasi()) text-yellow-600
                    @else text-red-500 @endif">
                    {{ $t->labelStatus() }}
                </span>
            </div>
        </a>
        @endforeach
    </div>
</div>
@endif

{{-- Quick links --}}
<div class="grid grid-cols-2 gap-3">
    <a href="{{ route('pelanggan.riwayat') }}"
       class="bg-white rounded-2xl shadow-sm p-4 text-center hover:shadow-md transition">
        <div class="text-2xl mb-1">📊</div>
        <div class="text-xs font-medium text-gray-700">Riwayat Pemakaian</div>
    </a>
    <a href="{{ route('pelanggan.pengaduan.create') }}"
       class="bg-white rounded-2xl shadow-sm p-4 text-center hover:shadow-md transition">
        <div class="text-2xl mb-1">📢</div>
        <div class="text-xs font-medium text-gray-700">Buat Pengaduan</div>
    </a>
</div>

@endsection