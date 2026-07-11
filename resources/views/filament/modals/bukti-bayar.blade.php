<div class="space-y-4 p-2">

    {{-- Info transaksi --}}
    <div class="bg-gray-50 rounded-xl p-4 text-sm space-y-2">
        <div class="flex justify-between">
            <span class="text-gray-500">Pelanggan</span>
            <span class="font-semibold">{{ $pembayaran->tagihan->pelanggan->nama }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">No. Sambungan</span>
            <span>{{ $pembayaran->tagihan->pelanggan->nomor_sambungan }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Periode</span>
            <span>{{ $pembayaran->tagihan->periode->labelBulan() }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Jumlah</span>
            <span class="font-bold text-green-700">
                Rp {{ number_format($pembayaran->jumlah_bayar, 0, ',', '.') }}
            </span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Diunggah</span>
            <span>{{ $pembayaran->tanggal_bayar->format('d M Y, H:i') }}</span>
        </div>
    </div>

    {{-- Gambar bukti bayar --}}
    @if($url)
        <div>
            <p class="text-xs text-gray-400 mb-2">Bukti Pembayaran:</p>
            <img src="{{ $url }}" alt="Bukti bayar"
                 class="w-full rounded-xl border border-gray-200 object-contain max-h-96">
            <a href="{{ $url }}" target="_blank"
               class="mt-2 block text-center text-xs text-blue-600 hover:underline">
                Buka gambar di tab baru ↗
            </a>
        </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 text-sm text-yellow-700 text-center">
            ⚠️ File bukti bayar tidak ditemukan.
        </div>
    @endif

    {{-- Catatan verifikasi jika sudah diproses --}}
    @if($pembayaran->catatan_verifikasi)
        <div class="bg-red-50 border border-red-200 rounded-xl p-3 text-sm">
            <span class="font-medium text-red-700">Catatan Penolakan:</span>
            <p class="text-red-600 mt-1">{{ $pembayaran->catatan_verifikasi }}</p>
        </div>
    @endif
</div>