<x-filament-panels::page>

    {{-- Filter Periode --}}
    <x-filament::section>
        <form wire:change="$refresh">
            {{ $this->form }}
        </form>
    </x-filament::section>

    @php $data = $this->getData(); @endphp

    @if(! $data['periode'])
        <x-filament::section>
            <p class="text-gray-400 text-sm text-center py-4">Pilih periode untuk menampilkan laporan.</p>
        </x-filament::section>
    @else

    {{-- Ringkasan --}}
    <x-filament::section heading="Ringkasan {{ $data['periode']->labelBulan() }}">
        <div class="grid grid-cols-2 md:grid-cols-2 gap-4">
            <div class="bg-blue-50 border border-gray-200 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-blue-700">{{ $data['totalPelangganDicatat'] }}</div>
                <div class="text-xs text-blue-500 mt-1">Pelanggan Dicatat</div>
            </div>
            <div class="bg-teal-50 border border-gray-200 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-teal-700">
                    {{ number_format($data['totalPemakaian'], 1) }} m³
                </div>
                <div class="text-xs text-teal-500 mt-1">Total Pemakaian</div>
            </div>
            <div class="bg-green-50 border border-gray-200 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-green-700">{{ $data['totalLunas'] }}</div>
                <div class="text-xs text-green-500 mt-1">Tagihan Lunas</div>
            </div>
            <div class="bg-red-50 border border-gray-200 rounded-xl p-4 text-center">
                <div class="text-2xl font-bold text-red-700">{{ $data['totalBelumBayar'] }}</div>
                <div class="text-xs text-red-500 mt-1">Belum Dibayar</div>
            </div>
        </div>
    </x-filament::section>

    {{-- Pendapatan --}}
    <x-filament::section heading="Rekap Pendapatan">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-green-50 border border-gray-200 rounded-xl p-4">
                <div class="text-xs text-gray-400 mb-1">Tunai (Loket)</div>
                <div class="text-xl font-bold text-gray-800">
                    Rp {{ number_format($data['pendapatanTunai'], 0, ',', '.') }}
                </div>
            </div>
            <div class="bg-green-50 border border-gray-200 rounded-xl p-4">
                <div class="text-xs text-gray-400 mb-1">QRIS (Diverifikasi)</div>
                <div class="text-xl font-bold text-gray-800">
                    Rp {{ number_format($data['pendapatanQris'], 0, ',', '.') }}
                </div>
            </div>
            <div class="bg-green-50 border border-gray-200 rounded-xl p-4">
                <div class="text-xs text-gray-400 mb-1">Total Pendapatan</div>
                <div class="text-xl font-bold text-gray-800">
                    Rp {{ number_format($data['totalPendapatan'], 0, ',', '.') }}
                </div>
            </div>
        </div>
    </x-filament::section>

    {{-- Tabel Detail Pelanggan --}}
    <x-filament::section heading="Detail per Pelanggan">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-gray-50 text-gray-500 text-xs uppercase">
                        <th class="px-4 py-3 text-left">No. Sambungan</th>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Wilayah</th>
                        <th class="px-4 py-3 text-right">Pemakaian</th>
                        <th class="px-4 py-3 text-right">Total Tagihan</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Metode</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($data['detailPelanggan'] as $row)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">{{ $row['nomor_sambungan'] }}</td>
                        <td class="px-4 py-3 font-medium text-gray-800">{{ $row['nama'] }}</td>
                        <td class="px-4 py-3 text-gray-500">{{ $row['wilayah'] ?? '-' }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($row['pemakaian'], 2) }} m³</td>
                        <td class="px-4 py-3 text-right font-semibold">
                            Rp {{ number_format($row['total_tagihan'], 0, ',', '.') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <span class="text-xs px-2 py-0.5 rounded-full
                                @if($row['status'] === 'Lunas') bg-green-100 text-green-700
                                @elseif($row['status'] === 'Menunggu Verifikasi') bg-yellow-100 text-yellow-700
                                @else bg-red-100 text-red-700 @endif">
                                {{ $row['status'] }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center text-xs uppercase text-gray-400">
                            {{ $row['metode'] }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-4 py-8 text-center text-gray-400">
                            Belum ada data tagihan untuk periode ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </x-filament::section>

    {{-- Anomali --}}
    @if($data['anomali']->isNotEmpty())
    <x-filament::section heading="⚠️ Anomali Meter ({{ $data['anomali']->count() }} pelanggan)">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-yellow-50 text-yellow-700 text-xs uppercase">
                        <th class="px-4 py-3 text-left">No. Sambungan</th>
                        <th class="px-4 py-3 text-left">Nama</th>
                        <th class="px-4 py-3 text-left">Kondisi</th>
                        <th class="px-4 py-3 text-left">Catatan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-yellow-100">
                    @foreach($data['anomali'] as $a)
                    <tr class="hover:bg-yellow-50">
                        <td class="px-4 py-3 font-mono text-xs text-gray-500">
                            {{ $a->pelanggan->nomor_sambungan }}
                        </td>
                        <td class="px-4 py-3 font-medium">{{ $a->pelanggan->nama }}</td>
                        <td class="px-4 py-3">
                            <span class="text-xs bg-yellow-100 text-yellow-700 px-2 py-0.5 rounded-full">
                                {{ str_replace('_', ' ', $a->status_kondisi) }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-gray-400 text-xs">{{ $a->catatan ?? '-' }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </x-filament::section>
    @endif

    @endif
</x-filament-panels::page>