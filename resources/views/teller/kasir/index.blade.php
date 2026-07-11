@extends('layouts.teller')
@section('title', 'Kasir')
@section('header', 'Kasir')

@section('content')
<h1 class="text-xl font-bold text-gray-800 mb-4">Kasir</h1>

{{-- Form pencarian --}}
<form method="GET" action="{{ route('teller.kasir.index') }}" class="flex gap-2 mb-6">
    <input type="text" name="q" value="{{ $keyword }}"
        placeholder="Cari nama / nomor sambungan..."
        class="flex-1 border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-teal-400"
        autofocus>
    <button type="submit"
        class="bg-teal-600 hover:bg-teal-700 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition">
        Cari
    </button>
</form>

{{-- Hasil pencarian --}}
@if($keyword && ! $pelanggan)
    <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-sm text-yellow-700">
        Pelanggan dengan kata kunci "<strong>{{ $keyword }}</strong>" tidak ditemukan.
    </div>

@elseif($pelanggan)
    {{-- Data pelanggan --}}
    <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
        <div class="flex items-start justify-between">
            <div>
                <div class="font-bold text-gray-800 text-base">{{ $pelanggan->nama }}</div>
                <div class="text-xs text-gray-400 mt-0.5">{{ $pelanggan->nomor_sambungan }}</div>
                <div class="text-xs text-gray-400">{{ $pelanggan->alamat }}</div>
                <div class="text-xs text-gray-400">RT {{ $pelanggan->rt }} / RW {{ $pelanggan->rw }} · {{ $pelanggan->wilayah }}</div>
            </div>
            <span class="text-xs px-2 py-1 rounded-full
                {{ $pelanggan->isAktif() ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                {{ $pelanggan->isAktif() ? 'Aktif' : 'Nonaktif' }}
            </span>
        </div>
    </div>

    {{-- Tagihan --}}
    @if($tagihan)
        <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
            <h2 class="font-semibold text-gray-700 mb-3 text-sm">Tagihan Bulan Ini</h2>

            <div class="space-y-2 text-sm mb-4">
                <div class="flex justify-between">
                    <span class="text-gray-500">Periode</span>
                    <span>{{ $tagihan->periode->labelBulan() }}</span>
                </div>
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
                <hr>
                <div class="flex justify-between font-bold text-base">
                    <span>Total Tagihan</span>
                    <span class="text-teal-700">
                        Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}
                    </span>
                </div>
                <div class="flex justify-between text-xs text-gray-400">
                    <span>Jatuh Tempo</span>
                    <span>{{ $tagihan->tanggal_jatuh_tempo?->format('d M Y') ?? '-' }}</span>
                </div>
            </div>

            {{-- Rincian breakdown --}}
            @if($tagihan->breakdown_tarif)
            <details class="text-xs text-gray-500 mb-4">
                <summary class="cursor-pointer text-teal-600 font-medium">Lihat rincian tarif</summary>
                <div class="mt-2 bg-gray-50 rounded-lg p-3 space-y-1">
                    @foreach($tagihan->breakdown_tarif as $b)
                    <div class="flex justify-between">
                        <span>
                            Tier {{ $b['tier'] }}
                            ({{ number_format($b['pemakaian'], 2) }} m³
                            × Rp {{ number_format($b['harga_per_m3'], 0, ',', '.') }})
                        </span>
                        <span class="font-medium">Rp {{ number_format($b['subtotal'], 0, ',', '.') }}</span>
                    </div>
                    @endforeach
                </div>
            </details>
            @endif

            {{-- Tombol bayar --}}
            <form method="POST" action="{{ route('teller.kasir.bayar') }}" class="space-y-2">
                @csrf
                <input type="hidden" name="tagihan_id" value="{{ $tagihan->id }}">
                <button type="submit"
                    onclick="return confirm('Proses pembayaran tunai Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }} untuk {{ $pelanggan->nama }}?')"
                    class="flex items-center justify-center gap-2 w-full bg-deep text-white text-center font-semibold py-3.5 mt-4 rounded-2xl text-sm transition active:scale-[0.98]">
                    <i class="fas fa-cash-register"></i>
                    Proses Bayar Tunai
                </button>
                
                <a href="{{ route('teller.kasir.index') }}"
                   class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 font-semibold py-3 rounded-xl transition inline-flex items-center justify-center text-sm">
                    Kembali / Batal
                </a>
            </form>
        </div>

    @else
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-700">
            ✅ Tidak ada tagihan yang belum dibayar untuk pelanggan ini.
        </div>
    @endif

@elseif(! $keyword)
    {{-- Tampilan Awal: List Belum Bayar bergaya Card List --}}
    <div class="flex justify-between items-center mb-3 mt-2">
        <h2 class="font-bold text-gray-700 text-sm">Menunggu Pembayaran</h2>
        <span class="text-xs bg-gray-100 text-gray-600 font-medium px-2 py-0.5 rounded-full">
            {{ $daftarTagihanBelumBayar->count() }} Pelanggan
        </span>
    </div>

    @if($daftarTagihanBelumBayar->isEmpty())
        <div class="text-center text-gray-400 py-12 bg-white rounded-2xl shadow-sm border border-gray-100">
            <div class="text-4xl mb-2">🎉</div>
            <p class="text-sm">Semua tagihan untuk periode ini telah lunas!</p>
        </div>
    @else
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-3 mb-4">
            <div class="divide-y divide-gray-100">
                @foreach($daftarTagihanBelumBayar as $itemTagihan)
                <a href="{{ route('teller.kasir.index', ['q' => $itemTagihan->pelanggan->nomor_sambungan]) }}"
                   class="flex justify-between items-center py-3 hover:bg-gray-50 active:bg-gray-100 -mx-1 px-3 rounded-lg transition gap-3">
                    
                    <div class="flex items-center gap-2.5">
                        <span class="flex-shrink-0 flex items-center justify-center w-9 h-9 rounded-full bg-teal-50 text-teal-600">
                            <i class="fas fa-file-invoice-dollar text-sm"></i>
                        </span>
                        <div>
                            <div class="text-sm text-gray-800 font-medium">{{ $itemTagihan->pelanggan->nama }}</div>
                            <div class="text-xs text-gray-400">{{ $itemTagihan->pelanggan->nomor_sambungan }} · {{ $itemTagihan->periode->labelBulan() }}</div>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-3">
                        <div class="text-right">
                            <div class="text-sm font-bold text-teal-700">
                                Rp {{ number_format($itemTagihan->total_tagihan, 0, ',', '.') }}
                            </div>
                            <div class="text-[10px] text-gray-400">
                                {{ number_format($itemTagihan->total_pemakaian, 1) }} m³
                            </div>
                        </div>
                        
                        <i class="fa-solid fa-chevron-right text-xs text-gray-400 flex-shrink-0"></i>
                    </div>

                </a>
                @endforeach
            </div>
        </div>
    @endif
@endif

@endsection