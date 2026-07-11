@extends('layouts.teller')
@section('title', 'Riwayat Transaksi')
@section('header', 'Riwayat Transaksi')

@section('content')
<h1 class="text-xl font-bold text-gray-800 mb-4">Riwayat Transaksi</h1>

{{-- Filter tanggal --}}
<form method="GET" class="flex gap-2 mb-5">
    <input type="date" name="tanggal" value="{{ $tanggal }}"
        class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400">
    <button type="submit"
        class="bg-teal-600 text-white px-4 py-2 rounded-lg text-sm">Tampilkan</button>
</form>

{{-- Ringkasan --}}
<div class="bg-teal-50 border border-teal-200 rounded-xl p-4 mb-4 flex justify-between items-center">
    <div class="text-sm text-teal-700">Total Pendapatan</div>
    <div class="font-bold text-teal-700 text-lg">
        Rp {{ number_format($totalPendapatan, 0, ',', '.') }}
    </div>
</div>

@if($transaksi->isEmpty())
    <div class="text-center text-gray-400 text-sm py-8">
        Tidak ada transaksi pada tanggal ini.
    </div>
@else
    <div class="space-y-3">
        @foreach($transaksi as $trx)
        <div class="bg-white rounded-xl shadow-sm p-4 flex justify-between items-center">
            <div>
                <div class="font-medium text-sm text-gray-800">
                    {{ $trx->tagihan->pelanggan->nama }}
                </div>
                <div class="text-xs text-gray-400">
                    {{ $trx->nomor_nota }}
                </div>
                <div class="text-xs text-gray-400">
                    {{ $trx->tagihan->periode->labelBulan() }}
                    · {{ $trx->tanggal_bayar->format('H:i') }}
                </div>
            </div>
            <div class="text-right">
                <div class="font-bold text-teal-600 text-sm">
                    Rp {{ number_format($trx->jumlah_bayar, 0, ',', '.') }}
                </div>
                <a href="{{ route('teller.kasir.nota', $trx->id) }}"
                   class="text-xs text-teal-500">Lihat nota →</a>
            </div>
        </div>
        @endforeach
    </div>

    <div class="mt-4">
        {{ $transaksi->withQueryString()->links() }}
    </div>
@endif
@endsection