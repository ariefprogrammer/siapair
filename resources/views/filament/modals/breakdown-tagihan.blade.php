<div class="space-y-4 p-2">

    {{-- Info umum --}}
    <div class="bg-gray-50 rounded-xl p-4 text-sm space-y-2">
        <div class="flex justify-between">
            <span class="text-gray-500">Periode</span>
            <span class="font-semibold">{{ $tagihan->periode->labelBulan() }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Total Pemakaian</span>
            <span class="font-semibold">{{ number_format($tagihan->total_pemakaian, 2) }} m³</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Status</span>
            <span class="font-semibold
                @if($tagihan->isLunas()) text-green-600
                @elseif($tagihan->isMenungguVerifikasi()) text-yellow-600
                @else text-red-600 @endif">
                {{ $tagihan->labelStatus() }}
            </span>
        </div>
    </div>

    {{-- Breakdown per tier --}}
    <div>
        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-2">
            Rincian Perhitungan Tarif
        </p>
        <div class="space-y-2">
            @foreach($tagihan->breakdown_tarif as $b)
            <div class="bg-white border border-gray-200 rounded-lg px-4 py-3">
                <div class="flex justify-between items-start">
                    <div>
                        <span class="text-xs font-bold text-blue-600 bg-blue-50 px-2 py-0.5 rounded-full">
                            Tier {{ $b['tier'] }}
                        </span>
                        <div class="text-xs text-gray-400 mt-1">
                            {{ number_format($b['batas_bawah'], 0) }} –
                            {{ $b['batas_atas'] ? number_format($b['batas_atas'], 0) : '∞' }} m³
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-semibold text-gray-800">
                            Rp {{ number_format($b['subtotal'], 0, ',', '.') }}
                        </div>
                        <div class="text-xs text-gray-400">
                            {{ number_format($b['pemakaian'], 2) }} m³
                            × Rp {{ number_format($b['harga_per_m3'], 0, ',', '.') }}
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    {{-- Total --}}
    <div class="border-t border-gray-200 pt-3 space-y-2 text-sm">
        <div class="flex justify-between">
            <span class="text-gray-500">Biaya Air</span>
            <span>Rp {{ number_format($tagihan->biaya_air, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Biaya Admin</span>
            <span>Rp {{ number_format($tagihan->biaya_admin, 0, ',', '.') }}</span>
        </div>
        <div class="flex justify-between font-bold text-base border-t border-gray-200 pt-2">
            <span>Total Tagihan</span>
            <span class="text-blue-700">Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</span>
        </div>
    </div>

    {{-- Info pembayaran jika sudah ada --}}
    @if($tagihan->pembayaran)
    <div class="bg-green-50 border border-green-200 rounded-xl p-3 text-sm space-y-1">
        <p class="text-xs font-semibold text-green-700 uppercase tracking-wide">Info Pembayaran</p>
        <div class="flex justify-between">
            <span class="text-gray-500">Metode</span>
            <span class="uppercase font-medium">{{ $tagihan->pembayaran->metode }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Tanggal</span>
            <span>{{ $tagihan->pembayaran->tanggal_bayar->format('d M Y, H:i') }}</span>
        </div>
        @if($tagihan->pembayaran->nomor_nota)
        <div class="flex justify-between">
            <span class="text-gray-500">No. Nota</span>
            <span class="font-mono text-xs">{{ $tagihan->pembayaran->nomor_nota }}</span>
        </div>
        @endif
    </div>
    @endif
</div>