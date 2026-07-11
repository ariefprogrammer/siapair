<div class="space-y-4 p-2">

    {{-- Meta --}}
    <div class="bg-gray-50 rounded-xl p-4 text-sm space-y-2">
        <div class="flex justify-between">
            <span class="text-gray-500">Kategori</span>
            <span class="font-semibold">{{ $pengaduan->labelKategori() }}</span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Status</span>
            <span class="font-semibold
                @if($pengaduan->status === 'selesai') text-green-600
                @elseif($pengaduan->status === 'diproses') text-yellow-600
                @else text-blue-600 @endif">
                {{ $pengaduan->labelStatus() }}
            </span>
        </div>
        <div class="flex justify-between">
            <span class="text-gray-500">Dikirim</span>
            <span>{{ $pengaduan->created_at->format('d M Y, H:i') }}</span>
        </div>
    </div>

    {{-- Deskripsi --}}
    <div>
        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-2">Deskripsi Pengaduan</p>
        <div class="bg-white border border-gray-200 rounded-xl p-4 text-sm text-gray-700 leading-relaxed">
            {{ $pengaduan->deskripsi }}
        </div>
    </div>

    {{-- Lampiran --}}
    @if($pengaduan->lampiran_path)
    <div>
        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-2">Lampiran</p>
        <img src="{{ asset('storage/' . $pengaduan->lampiran_path) }}"
             alt="Lampiran pengaduan"
             class="w-full rounded-xl border border-gray-200 object-contain max-h-64">
        <a href="{{ asset('storage/' . $pengaduan->lampiran_path) }}"
           target="_blank"
           class="mt-1 block text-center text-xs text-blue-600 hover:underline">
            Buka di tab baru ↗
        </a>
    </div>
    @endif

    {{-- Respons admin --}}
    @if($pengaduan->respons_admin)
    <div>
        <p class="text-xs text-gray-400 font-semibold uppercase tracking-wide mb-2">Respons Administrator</p>
        <div class="bg-green-50 border border-green-200 rounded-xl p-4 text-sm text-green-800 leading-relaxed">
            {{ $pengaduan->respons_admin }}
        </div>
        <p class="text-xs text-gray-400 mt-1">
            Direspons oleh {{ $pengaduan->admin?->name ?? '—' }}
            pada {{ $pengaduan->tanggal_respons?->format('d M Y, H:i') }}
        </p>
    </div>
    @else
        <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-3 text-sm text-yellow-700 text-center">
            ⏳ Belum ada respons dari administrator.
        </div>
    @endif
</div>