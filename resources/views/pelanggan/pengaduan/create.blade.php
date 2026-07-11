@extends('layouts.pelanggan')
@section('title', 'Buat Pengaduan')
@section('header', 'Buat Pengaduan')

@section('content')
<a href="{{ route('pelanggan.pengaduan.index') }}" 
    class="text-sm color-siap mt-8 mb-6 block">
    <i class="fa-solid fa-angle-left text-sm mr-1"></i>
    Kembali</a>

<form method="POST" action="{{ route('pelanggan.pengaduan.store') }}" enctype="multipart/form-data">
    @csrf
    <div class="bg-white rounded-2xl shadow-sm p-4 space-y-4">

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Kategori <span class="text-red-500">*</span>
            </label>
            <select name="kategori"
                class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm bg-white" required>
                <option value="">-- Pilih Kategori --</option>
                <option value="teknis"       {{ old('kategori') === 'teknis' ? 'selected' : '' }}>Teknis (Meteran, Pipa Bocor, dsb.)</option>
                <option value="administrasi" {{ old('kategori') === 'administrasi' ? 'selected' : '' }}>Administrasi (Tagihan, Data, dsb.)</option>
                <option value="lainnya"      {{ old('kategori') === 'lainnya' ? 'selected' : '' }}>Lainnya</option>
            </select>
            @error('kategori')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Deskripsi <span class="text-red-500">*</span>
            </label>
            <textarea name="deskripsi" rows="5"
                class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm"
                placeholder="Jelaskan masalah Anda secara detail (minimal 20 karakter)..."
                required>{{ old('deskripsi') }}</textarea>
            @error('deskripsi')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Foto Lampiran (Opsional)
            </label>
            <input type="file" name="lampiran" accept="image/*"
                class="w-full text-sm text-gray-500 file:mr-3 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700">
            <p class="text-xs text-gray-400 mt-1">Maks. 5MB.</p>
            @error('lampiran')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
        </div>

    </div>

    <button type="submit"
        class="flex items-center justify-center gap-2 w-full bg-deep text-white text-center font-semibold py-3.5 mt-4 rounded-2xl text-sm transition active:scale-[0.98]">
        Kirim Pengaduan
    </button>
</form>
@endsection