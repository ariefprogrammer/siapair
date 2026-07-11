<div class="flex flex-col gap-3 p-2"
     style="max-height: 480px; overflow-y: auto;"
     id="chat-scroll">

    @forelse($pengaduan->pesan as $pesan)
    @php $dariAdmin = $pesan->dariAdmin(); @endphp

    <div style="display: flex; {{ $dariAdmin ? 'justify-content: flex-end;' : 'justify-content: flex-start;' }}">
        <div style="max-width: 80%;">

            {{-- Label pengirim --}}
            <div style="font-size: 11px; color: #9ca3af; margin-bottom: 4px;
                        {{ $dariAdmin ? 'text-align: right;' : 'text-align: left;' }}">
                @if($dariAdmin)
                    <span style="font-weight: 600; color: #2563eb;">{{ $pesan->user->name }}</span>
                @else
                    <span style="font-weight: 600; color: #4b5563;">{{ $pesan->user->name }}</span>
                    <span style="color: #9ca3af;">(Pelanggan)</span>
                @endif
                · {{ $pesan->created_at->format('d M, H:i') }}
            </div>

            {{-- Bubble pesan --}}
            <div style="border-radius: 16px; padding: 10px 14px; font-size: 14px; line-height: 1.5;
                        {{ $dariAdmin
                            ? 'background-color: #2563eb; color: #ffffff; border-top-right-radius: 4px;'
                            : 'background-color: #f3f4f6; color: #1f2937; border: 1px solid #e5e7eb; border-top-left-radius: 4px;' }}">
                {{ $pesan->pesan }}
            </div>

            {{-- Lampiran --}}
            @if($pesan->lampiran_path)
                <div style="margin-top: 6px;">
                    <img src="{{ asset('storage/' . $pesan->lampiran_path) }}"
                         alt="Lampiran"
                         style="border-radius: 12px; max-height: 160px; object-fit: contain;
                                border: 1px solid #e5e7eb; width: 100%;">
                    <a href="{{ asset('storage/' . $pesan->lampiran_path) }}"
                       target="_blank"
                       style="font-size: 11px; color: #3b82f6; display: block;
                              margin-top: 2px; text-align: right;">
                        Buka gambar ↗
                    </a>
                </div>
            @endif

        </div>
    </div>
    @empty
        <p style="text-align: center; color: #9ca3af; font-size: 14px; padding: 16px;">
            Belum ada pesan.
        </p>
    @endforelse
</div>

<script>
    const el = document.getElementById('chat-scroll');
    if (el) el.scrollTop = el.scrollHeight;
</script>