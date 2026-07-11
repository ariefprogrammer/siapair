@extends('layouts.operator')
@section('title', 'Catatan Meter')
@section('header', 'Catatan Meter')

@section('content')
<h1 class="text-xl font-bold text-gray-800 mb-1">Catatan Meter</h1>

@if(! $periodeAktif)
    <div class="bg-yellow-50 border border-yellow-300 text-yellow-800 rounded-lg p-4 text-sm">
        ⚠️ Tidak ada periode pencatatan yang sedang buka. Hubungi administrator.
    </div>
@else
    <p class="text-sm text-gray-500 mb-4">Periode: <strong>{{ $periodeAktif->labelBulan() }}</strong></p>

    <div class="flex gap-2 mb-4">
        <select id="filter-rt" class="w-28 flex-shrink-0 border border-gray-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
            <option value="">Semua RT</option>
            @foreach($daftarRt as $rt)
                <option value="{{ $rt }}">RT {{ $rt }}</option>
            @endforeach
        </select>

        <input type="text" id="search"
            placeholder="Cari nama atau nomor sambungan..."
            class="flex-1 min-w-0 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-400">
    </div>

    <div id="pelanggan-list-wrapper">
        @include('operator.catatan-meter._list')
    </div>
@endif

<script>
const filterRt = document.getElementById('filter-rt');
const listWrapper = document.getElementById('pelanggan-list-wrapper');
const baseUrl = "{{ route('operator.catatan-meter.index') }}";

function attachSearchListener() {
    const search = document.getElementById('search');
    if (! search) return;

    search.addEventListener('input', function () {
        const q = this.value.toLowerCase();
        document.querySelectorAll('.pelanggan-item').forEach(el => {
            const match = el.dataset.nama.includes(q) || el.dataset.nomor.includes(q);
            el.style.display = match ? '' : 'none';
        });
    });
}

function muatDaftarPelanggan() {
    const params = new URLSearchParams();
    if (filterRt.value) params.append('rt', filterRt.value);

    listWrapper.style.opacity = '0.5';

    fetch(`${baseUrl}?${params.toString()}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
    .then(res => res.text())
    .then(html => {
        listWrapper.innerHTML = html;
        listWrapper.style.opacity = '1';
    })
    .catch(() => {
        listWrapper.style.opacity = '1';
    });
}

filterRt.addEventListener('change', muatDaftarPelanggan);

attachSearchListener();
</script>
@endsection