@extends('layouts.pelanggan')
@section('title', 'Bayar via QRIS')
@section('header', 'Bayar via QRIS')

@section('content')
<a href="{{ route('pelanggan.tagihan.show', $tagihan->id) }}"
   class="text-sm color-siap mt-8 mb-6 block">
   <i class="fa-solid fa-angle-left text-sm mr-1"></i>
   Kembali</a>

{{-- Info tagihan --}}
<div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mb-5 text-center">
    <div class="text-xs text-blue-500">Total yang harus dibayar</div>
    <div class="text-3xl font-bold text-blue-700 mt-1">
        Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}
    </div>
    <div class="text-xs text-blue-400 mt-1">{{ $tagihan->periode->labelBulan() }}</div>
</div>

{{-- Kode QRIS --}}
<div class="bg-white rounded-2xl shadow-sm p-6 mb-5 text-center">
    <h2 class="font-semibold text-gray-700 mb-4 text-sm">Scan QRIS Berikut</h2>
    @if($qrisPath)
        <img src="{{ $qrisPath }}" alt="QRIS SIAP AIR"
             class="mx-auto w-56 h-56 object-contain border-4 border-blue-100 rounded-xl p-2">
             
        {{-- Menampilkan Nama Pemilik QRIS jika diisi oleh Admin --}}
        @if(isset($qrisSetting) && $qrisSetting->nama_pemilik)
            <div class="mt-2 text-sm font-semibold text-gray-700">
                A/N: {{ $qrisSetting->nama_pemilik }}
            </div>
        @endif
    @else
        <div class="mx-auto w-56 h-56 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400 text-sm border-2 border-dashed border-gray-300">
            <div class="text-center">
                <div class="text-4xl mb-2">📷</div>
                <div>Gambar QRIS<br>belum diupload</div>
            </div>
        </div>
        <p class="text-xs text-gray-400 mt-2">
            Hubungi admin untuk mendapatkan kode QRIS.
        </p>
    @endif
    <div class="mt-4 bg-gray-50 rounded-lg p-3 text-xs text-gray-500 text-left space-y-1">
        <div>1. Buka aplikasi e-wallet / mobile banking</div>
        <div>2. Pilih menu <strong>Bayar / Scan QR</strong></div>
        <div>3. Scan kode QRIS di atas</div>
        <div>4. Masukkan nominal: <strong>Rp {{ number_format($tagihan->total_tagihan, 0, ',', '.') }}</strong></div>
        <div>5. Selesaikan pembayaran</div>
        <div>6. Screenshot bukti bayar, lalu upload di bawah</div>
    </div>
</div>

{{-- Upload bukti bayar --}}
<div class="bg-white rounded-2xl shadow-sm p-4 mb-4">
    <h2 class="font-semibold text-gray-700 mb-3 text-sm">Upload Bukti Bayar</h2>
    <p class="text-xs text-gray-400 mb-4">
        Setelah membayar, upload screenshot atau foto bukti pembayaran dari e-wallet / mobile banking Anda.
    </p>

    <form method="POST"
          action="{{ route('pelanggan.tagihan.upload-bukti', $tagihan->id) }}"
          enctype="multipart/form-data">
        @csrf

        {{-- Preview gambar sebelum upload --}}
        <div id="preview-wrap" class="hidden mb-3">
            <img id="preview-img" src="" alt="Preview"
                 class="w-full rounded-xl max-h-64 object-contain border border-gray-200">
        </div>

        <label class="block w-full border-2 border-dashed border-blue-300 rounded-xl p-6 text-center cursor-pointer hover:bg-blue-50 transition"
               id="upload-label">
            <input type="file" name="bukti_bayar" accept="image/*"
                   class="hidden" id="bukti-input" required>
            <div id="upload-placeholder">
                <div class="text-3xl mb-2">📎</div>
                <div class="text-sm text-blue-600 font-medium">Pilih atau ambil foto</div>
                <div class="text-xs text-gray-400 mt-1">JPG, PNG, maks. 5MB</div>
            </div>
        </label>

        @error('bukti_bayar')
            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
        @enderror

        <button type="submit"
            class="flex items-center justify-center gap-2 w-full bg-deep text-white text-center font-semibold py-3.5 mt-4 rounded-2xl text-sm transition active:scale-[0.98]">
            Kirim Bukti Bayar
        </button>
    </form>
</div>

<script>
document.getElementById('bukti-input').addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('preview-img').src = e.target.result;
        document.getElementById('preview-wrap').classList.remove('hidden');
        document.getElementById('upload-placeholder').innerHTML =
            '<div class="text-xs text-green-600 font-medium">📎 ' + file.name + '</div>';
    };
    reader.readAsDataURL(file);
});
</script>
@endsection