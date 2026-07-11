@if($pelanggan->isEmpty())
    <div class="text-center text-gray-400 text-sm py-8">Tidak ada pelanggan yang cocok.</div>
@else
    <div class="bg-white rounded-2xl shadow-card p-3 mb-3">
        <div class="divide-y divide-gray-100" id="pelanggan-list">
            @foreach($pelanggan as $p)
            <a href="{{ $p->sudah_dicatat ? route('operator.catatan-meter.show', $p->catatan_meter_id) : route('operator.catatan-meter.create', ['pelanggan_id' => $p->id]) }}"
            class="pelanggan-item flex justify-between items-center py-3 -mx-1 px-3 rounded-lg transition gap-3 hover:bg-gray-50 active:bg-gray-100"
            data-nama="{{ strtolower($p->nama) }}"
            data-nomor="{{ strtolower($p->nomor_sambungan) }}">

                <div class="flex items-center gap-2.5">
                    <span class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full {{ $p->sudah_dicatat ? 'bg-green-50 text-green-600' : 'bg-deep/5 text-deep' }}">
                        @if($p->sudah_dicatat)
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M20 6L9 17l-5-5"/></svg>
                        @else
                            <i class="fas fa-file-invoice text-xs"></i>
                        @endif
                    </span>
                    <div>
                        <div class="text-sm text-ink font-medium">{{ $p->nama }}</div>
                        <div class="text-xs text-muted">{{ $p->nomor_sambungan }} · {{ $p->wilayah }} RT {{ $p->rt }}</div>
                    </div>
                </div>

                <div class="flex items-center gap-4">
                    <div class="text-right">
                        @if($p->sudah_dicatat)
                            <span class="text-xs bg-green-100 text-green-700 font-medium px-3 py-1 rounded-full">
                                ✓ Sudah
                            </span>
                        @else
                            <span class="text-xs bg-blue-600 text-white font-medium px-3 py-1 rounded-full inline-flex items-center gap-1">
                                <i class="fas fa-plus"></i> Input
                            </span>
                        @endif
                    </div>

                    <i class="fa-solid fa-chevron-right text-sm text-gray-400 flex-shrink-0"></i>
                </div>

            </a>
            @endforeach
        </div>
    </div>
@endif