@extends('layouts.pelanggan')
@section('title', 'Detail Pengaduan')
@section('header', 'Detail Pengaduan')

@section('content')

{{-- Info pengaduan --}}
<div class="bg-white shadow-sm p-4 mb-6 w-screen relative left-1/2 -translate-x-1/2 px-[max(1rem,calc((100vw-100%)/2))]">
    <div class="flex justify-between items-start max-w-full">
        <a href="{{ route('pelanggan.pengaduan.index') }}" 
            class="text-sm color-siap mt-4 mb-2 block">
            <i class="fa-solid fa-angle-left text-sm mr-1"></i>
            Kembali</a>
        <div>
            <span class="text-sm color-siap mt-4 mb-2 block">
                {{ $pengaduan->labelKategori() }} - {{ $pengaduan->created_at->format('d M Y, H:i') }}
            </span>
        </div>
        <span class="text-xs px-3 py-1 rounded-full font-semibold mt-4 mb-2 block
            @if($pengaduan->status === 'selesai') bg-green-100 text-green-700
            @elseif($pengaduan->status === 'diproses') bg-yellow-100 text-yellow-700
            @else bg-blue-100 text-blue-700 @endif">
            {{ $pengaduan->labelStatus() }}
        </span>
    </div>
</div>

{{-- Thread percakapan --}}
<div class="space-y-3 mb-4">
    @foreach($pengaduan->pesan as $pesan)
    @php $dariSaya = $pesan->dariPelanggan(); @endphp

    <div class="flex {{ $dariSaya ? 'justify-end' : 'justify-start' }}">
        <div class="max-w-[85%]">

            {{-- Label pengirim --}}
            <div class="text-xs text-gray-400 mb-1 {{ $dariSaya ? 'text-right' : 'text-left' }}">
                @if($dariSaya)
                    Anda
                @else
                    <span class="font-medium text-blue-600">Admin SIAP AIR</span>
                @endif
                · {{ $pesan->created_at->format('d M, H:i') }}
            </div>

            {{-- Bubble pesan --}}
            <div class="rounded-2xl px-4 py-3 text-sm
                {{ $dariSaya
                    ? 'bg-blue-600 text-white rounded-tr-none'
                    : 'bg-white border border-gray-200 text-gray-800 rounded-tl-none shadow-sm' }}">
                {{ $pesan->pesan }}
            </div>

            {{-- Lampiran --}}
            @if($pesan->lampiran_path)
                <div class="mt-2">
                    <img src="{{ asset('storage/' . $pesan->lampiran_path) }}"
                         alt="Lampiran"
                         class="rounded-xl max-h-48 object-contain border border-gray-200 w-full">
                </div>
            @endif

        </div>
    </div>
    @endforeach
</div>

{{-- Form balas --}}
<div class="fixed bottom-0 left-0 right-0 z-50 bg-gray-50 border-t border-gray-200 p-4">
    <div class="max-w-4xl mx-auto">
        @if(! $pengaduan->isSelesai())
        <div class="bg-white rounded-2xl shadow-md p-4">
            <form method="POST"
                  action="{{ route('pelanggan.pengaduan.balas', $pengaduan->id) }}"
                  enctype="multipart/form-data">
                @csrf

                <div id="lampiran-preview" class="hidden mb-2">
                    <img id="lampiran-img" src="" alt="Preview"
                         class="rounded-xl max-h-32 object-contain border border-gray-200 w-full">
                </div>

                <div class="flex gap-2 items-end">
                    <div class="flex-1">
                        <textarea name="pesan" rows="3" required
                            placeholder="Tulis pesan..."
                            class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-blue-400
                            @error('pesan') border-red-400 @enderror">{{ old('pesan') }}</textarea>
                        @error('pesan')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="flex flex-col gap-2 pb-0.5">
                        {{-- Tombol lampiran --}}
                        <label class="cursor-pointer bg-gray-100 hover:bg-gray-200 text-gray-600 p-2.5 rounded-md transition"
                               title="Tambah lampiran">
                            <input type="file" name="lampiran" accept="image/*"
                                   class="hidden" id="lampiran-input">
                            📎
                        </label>

                        {{-- Tombol kirim --}}
                        <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white p-2.5 rounded-xl transition">
                            ➤
                        </button>
                    </div>
                </div>
            </form>
        </div>
        @else
        <div class="bg-green-50 border border-green-200 rounded-2xl p-4 text-sm text-green-700 text-center shadow-sm">
            ✅ Pengaduan ini sudah ditutup. Buat pengaduan baru jika masalah muncul kembali.
        </div>
        @endif
    </div>
</div>

<script>
// Preview lampiran sebelum kirim
document.getElementById('lampiran-input')?.addEventListener('change', function () {
    const file = this.files[0];
    if (!file) return;
    const reader = new FileReader();
    reader.onload = e => {
        document.getElementById('lampiran-img').src = e.target.result;
        document.getElementById('lampiran-preview').classList.remove('hidden');
    };
    reader.readAsDataURL(file);
});

// Auto scroll ke bawah saat halaman load
window.addEventListener('load', () => {
    window.scrollTo(0, document.body.scrollHeight);
});
</script>
@endsection