@extends('layouts.pelanggan')
@section('title', 'Pengaduan')
@section('header', 'Pengaduan Saya')

@section('content')

<div class="mt-8 mb-4 grid grid-cols-2 gap-2">
    <a href="{{ route('pelanggan.pengaduan.create') }}"
        class="flex items-center justify-center gap-2 bg-deep text-white text-center font-semibold py-3.5 rounded-2xl text-sm transition active:scale-[0.98]">
        <i class="fa-solid fa-plus text-sm"></i> Buat Pengaduan
    </a>

    @if($operator && $operator->wa_number)
        <a href="https://wa.me/{{ $operator->wa_number }}?text={{ urlencode('Halo ' . $operator->name . ', saya ingin bertanya terkait layanan air di rumah saya.') }}"
            target="_blank"
            class="flex items-center justify-center gap-2 bg-green-600 text-white text-center font-semibold py-3.5 rounded-2xl text-sm transition active:scale-[0.98]">
            <i class="fa-brands fa-whatsapp text-sm"></i> Hubungi Operator
        </a>
    @else
        <button type="button" disabled
            class="flex items-center justify-center gap-2 bg-gray-200 text-gray-400 text-center font-semibold py-3.5 rounded-2xl text-sm cursor-not-allowed">
            <i class="fa-brands fa-whatsapp text-sm"></i> WA Operator Null
        </button>
    @endif
</div>

<!-- Daftar Pengaduan -->
<div class="space-y-3">
    @forelse($pengaduan as $p)
    <a href="{{ route('pelanggan.pengaduan.show', $p->id) }}"
       class="block bg-white rounded-2xl shadow-card p-4 border border-gray-50 transition active:bg-surface">
        
        <!-- Baris Atas: Kategori & Status Badge -->
        <div class="flex justify-between items-center mb-2.5">
            <span class="text-[10px] font-bold text-deep bg-deep/5 px-2.5 py-1 rounded-lg uppercase tracking-wider">
                {{ $p->labelKategori() }}
            </span>
            <span class="text-[11px] font-semibold px-2.5 py-0.5 rounded-full
                @if($p->status === 'selesai') bg-green-50 text-green-600
                @elseif($p->status === 'diproses') bg-amber-50 text-amber-600
                @else bg-blue-50 text-blue-600 @endif">
                {{ $p->labelStatus() }}
            </span>
        </div>
        
        <!-- Baris Tengah: Deskripsi -->
        <p class="text-sm text-ink leading-relaxed line-clamp-2 mb-3">{{ $p->deskripsi }}</p>
        
        <!-- Baris Bawah: Tanggal, Jumlah Pesan, & Chevron -->
        <div class="flex justify-between items-center pt-2.5 border-t border-gray-100/70">
            <span class="text-xs text-muted flex items-center gap-1.5">
                <i class="fa-solid fa-calendar text-gray-300 text-[11px]"></i>
                {{ $p->created_at->format('d M Y') }}
            </span>
            
            <div class="flex items-center gap-2">
                @php $jumlahPesan = $p->pesan()->count(); @endphp
                <span class="text-xs font-medium text-muted flex items-center gap-1">
                    <i class="fa-solid fa-comments text-gray-400 text-xs"></i>
                    {{ $jumlahPesan }} pesan
                </span>
                <i class="fa-solid fa-chevron-right text-xs text-gray-400"></i>
            </div>
        </div>
    </a>
    @empty
        <!-- Tampilan Kosong -->
        <div class="text-center text-muted text-sm py-12">
            <div class="w-12 h-12 rounded-full bg-gray-50 flex items-center justify-center mx-auto mb-3">
                <i class="fa-solid fa-bullhorn text-gray-300 text-lg"></i>
            </div>
            Belum ada pengaduan.
        </div>
    @endforelse
</div>

<div class="mt-4">{{ $pengaduan->links() }}</div>
@endsection