@extends('layouts.operator')
@section('title', 'Input Catatan Meter')

@section('content')
<div>
    <a href="{{ route('operator.catatan-meter.index') }}" 
        class="text-sm color-siap mt-8 mb-6 block">
        <i class="fa-solid fa-angle-left text-sm mr-1"></i>
        Kembali
    </a>
</div>
<h1 class="text-xl font-bold text-gray-800 mb-1">Input Catatan Meter</h1>

{{-- Info pelanggan --}}
<div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-5">
    <div class="font-semibold text-blue-800">{{ $pelanggan->nama }}</div>
    <div class="text-sm text-blue-600">{{ $pelanggan->nomor_sambungan }} · {{ $pelanggan->alamat }}</div>
    <div class="text-xs text-blue-500 mt-1">Periode: {{ $periodeAktif->labelBulan() }}</div>
</div>

<form method="POST" action="{{ route('operator.catatan-meter.store') }}" enctype="multipart/form-data">
    @csrf
    <input type="hidden" name="pelanggan_id" value="{{ $pelanggan->id }}">

    <div class="bg-white rounded-xl shadow-sm p-4 space-y-4">

        {{-- Angka meter lalu --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Angka Meter Lalu (m³)
            </label>
            <input type="number" name="angka_meter_lalu" step="0.01"
                value="{{ old('angka_meter_lalu', $angkaMeterLalu) }}"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm bg-gray-50"
                readonly>
            <p class="text-xs text-gray-400 mt-1">Otomatis dari catatan bulan sebelumnya.</p>
        </div>

        {{-- Angka meter sekarang --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Angka Meter Sekarang (m³) <span class="text-red-500">*</span>
            </label>
            <input type="number" name="angka_meter_sekarang" step="0.01" min="{{ $angkaMeterLalu }}"
                value="{{ old('angka_meter_sekarang') }}"
                id="meter-sekarang"
                class="w-full border @error('angka_meter_sekarang') border-red-400 @enderror border-gray-300 rounded-lg px-3 py-2 text-sm"
                required>
            @error('angka_meter_sekarang')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Preview pemakaian --}}
        <div class="bg-gray-50 rounded-lg px-4 py-3 flex justify-between items-center">
            <span class="text-sm text-gray-600">Estimasi Pemakaian</span>
            <span id="preview-pemakaian" class="font-bold text-blue-700 text-lg">— m³</span>
        </div>

        {{-- Status Kondisi --}}
        @php
            $opsiStatusKondisi = [
                'normal'         => 'Normal',
                'meteran_rusak'  => 'Meteran Rusak',
                'tidak_terbaca'  => 'Tidak Terbaca',
                'tidak_tercatat' => 'Tidak Tercatat',
                'angka_minus'    => 'Angka Minus',
                'laju_tinggi'    => 'Laju Tinggi',
            ];
            $statusLama   = old('status_kondisi', 'normal');
            $adalahCustom = $statusLama && ! array_key_exists($statusLama, $opsiStatusKondisi);
        @endphp
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Status Kondisi <span class="text-red-500">*</span>
            </label>

            <select id="status-kondisi-select"
                class="w-full border @error('status_kondisi') border-red-400 @enderror border-gray-300 rounded-lg px-3 py-2 text-sm">
                @foreach($opsiStatusKondisi as $value => $label)
                    <option value="{{ $value }}" @selected(! $adalahCustom && $statusLama === $value)>
                        {{ $label }}
                    </option>
                @endforeach
                <option value="__custom__" @selected($adalahCustom)>
                    Lainnya (ketik manual)
                </option>
            </select>

            <input type="text" id="status-kondisi-custom"
                value="{{ $adalahCustom ? $statusLama : '' }}"
                placeholder="Tulis status kondisi, contoh: pipa_bocor"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm mt-2 {{ $adalahCustom ? '' : 'hidden' }}">

            <input type="hidden" name="status_kondisi" id="status-kondisi-hidden"
                value="{{ $statusLama }}">

            @error('status_kondisi')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Catatan --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Catatan (Opsional)</label>
            <textarea name="catatan" rows="3"
                class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm"
                placeholder="Contoh: meteran susah dibaca karena kotor">{{ old('catatan') }}</textarea>
        </div>

        {{-- Foto meter --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Foto Meteran (Opsional)</label>
            <input type="file" 
                name="foto" 
                accept="image/jpeg, image/png" 
                capture="environment"
                class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700">
            <p class="text-xs text-gray-400 mt-1">Maks. 5MB. Wajib ambil langsung menggunakan kamera HP.</p>
        </div>

    </div>

    <button type="submit"
        class="flex items-center justify-center gap-2 w-full bg-deep text-white text-center font-semibold py-3.5 mt-4 rounded-2xl text-sm transition active:scale-[0.98]">
        Simpan & Generate Tagihan
    </button>
</form>

<script>
const meterLalu    = {{ $angkaMeterLalu }};
const meterInput   = document.getElementById('meter-sekarang');
const previewEl    = document.getElementById('preview-pemakaian');

meterInput.addEventListener('input', function () {
    const sekarang = parseFloat(this.value) || 0;
    const pemakaian = sekarang - meterLalu;
    previewEl.textContent = pemakaian >= 0
        ? pemakaian.toFixed(2) + ' m³'
        : '— m³';
    previewEl.className = pemakaian < 0
        ? 'font-bold text-red-500 text-lg'
        : 'font-bold text-blue-700 text-lg';
});

const selectStatus  = document.getElementById('status-kondisi-select');
const customStatus  = document.getElementById('status-kondisi-custom');
const hiddenStatus  = document.getElementById('status-kondisi-hidden');

function syncStatusKondisi() {
    if (selectStatus.value === '__custom__') {
        customStatus.classList.remove('hidden');
        hiddenStatus.value = customStatus.value;
    } else {
        customStatus.classList.add('hidden');
        hiddenStatus.value = selectStatus.value;
    }
}

selectStatus.addEventListener('change', syncStatusKondisi);
customStatus.addEventListener('input', function () {
    hiddenStatus.value = this.value;
});

// Inisialisasi saat halaman dimuat (menangani kasus validasi gagal / old value custom)
syncStatusKondisi();
</script>
</script>
@endsection