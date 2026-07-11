<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan {{ $periode->labelBulan() }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 11px; color: #1a1a1a; }

        .header { text-align: center; border-bottom: 2px solid #1d4ed8; padding-bottom: 12px; margin-bottom: 16px; }
        .header h1 { font-size: 18px; color: #1d4ed8; font-weight: bold; }
        .header p  { font-size: 10px; color: #6b7280; margin-top: 2px; }

        .periode-badge {
            display: inline-block;
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
            padding: 3px 12px;
            border-radius: 20px;
            font-size: 10px;
            margin-top: 6px;
        }

        .section { margin-bottom: 16px; }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #374151;
            border-left: 3px solid #1d4ed8;
            padding-left: 8px;
            margin-bottom: 8px;
        }

        .stats-grid { display: table; width: 100%; border-collapse: separate; border-spacing: 6px; }
        .stat-box {
            display: table-cell;
            width: 25%;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 8px 10px;
            text-align: center;
        }
        .stat-box .value { font-size: 16px; font-weight: bold; color: #1d4ed8; }
        .stat-box .label { font-size: 9px; color: #6b7280; margin-top: 2px; }

        .pendapatan-grid { display: table; width: 100%; border-collapse: separate; border-spacing: 6px; }
        .pendapatan-box {
            display: table-cell;
            width: 33%;
            border: 1px solid #e5e7eb;
            border-radius: 6px;
            padding: 8px 10px;
        }
        .pendapatan-box.total { background: #1d4ed8; color: white; }
        .pendapatan-box .p-label { font-size: 9px; color: #6b7280; margin-bottom: 3px; }
        .pendapatan-box.total .p-label { color: #bfdbfe; }
        .pendapatan-box .p-value { font-size: 13px; font-weight: bold; color: #111827; }
        .pendapatan-box.total .p-value { color: white; }

        table { width: 100%; border-collapse: collapse; font-size: 10px; }
        thead tr { background: #f3f4f6; }
        th { padding: 6px 8px; text-align: left; color: #6b7280; font-weight: 600; font-size: 9px; text-transform: uppercase; }
        td { padding: 6px 8px; border-bottom: 1px solid #f3f4f6; }
        tr:last-child td { border-bottom: none; }

        .badge {
            display: inline-block;
            padding: 1px 8px;
            border-radius: 10px;
            font-size: 9px;
            font-weight: 600;
        }
        .badge-lunas    { background: #d1fae5; color: #065f46; }
        .badge-belum    { background: #fee2e2; color: #991b1b; }
        .badge-menunggu { background: #fef3c7; color: #92400e; }
        .badge-anomali  { background: #fef3c7; color: #92400e; }

        .footer { margin-top: 20px; border-top: 1px solid #e5e7eb; padding-top: 8px; text-align: center; font-size: 9px; color: #9ca3af; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }

        .no-data { text-align: center; color: #9ca3af; padding: 16px; font-style: italic; }
    </style>
</head>
<body>

    {{-- Header --}}
    <div class="header">
        <h1>💧 SIAP AIR</h1>
        <p>Sistem Informasi Air Perpipaan</p>
        <div class="periode-badge">Laporan Periode {{ $periode->labelBulan() }}</div>
        <p style="margin-top: 6px; font-size: 9px; color: #9ca3af;">
            Dicetak pada: {{ now()->format('d M Y, H:i') }} · oleh {{ auth()->user()->name }}
        </p>
    </div>

    {{-- Statistik --}}
    <div class="section">
        <div class="section-title">Ringkasan Pencatatan & Tagihan</div>
        <div class="stats-grid">
            <div class="stat-box">
                <div class="value">{{ $totalPelangganDicatat }}</div>
                <div class="label">Pelanggan Dicatat</div>
            </div>
            <div class="stat-box">
                <div class="value">{{ number_format($totalPemakaian, 1) }} m³</div>
                <div class="label">Total Pemakaian</div>
            </div>
            <div class="stat-box">
                <div class="value" style="color: #059669;">{{ $totalLunas }}</div>
                <div class="label">Tagihan Lunas</div>
            </div>
            <div class="stat-box">
                <div class="value" style="color: #dc2626;">{{ $totalBelumBayar }}</div>
                <div class="label">Belum Dibayar</div>
            </div>
        </div>
    </div>

    {{-- Pendapatan --}}
    <div class="section">
        <div class="section-title">Rekap Pendapatan</div>
        <div class="pendapatan-grid">
            <div class="pendapatan-box">
                <div class="p-label">Tunai (Loket)</div>
                <div class="p-value">Rp {{ number_format($pendapatanTunai, 0, ',', '.') }}</div>
            </div>
            <div class="pendapatan-box">
                <div class="p-label">QRIS (Terverifikasi)</div>
                <div class="p-value">Rp {{ number_format($pendapatanQris, 0, ',', '.') }}</div>
            </div>
            <div class="pendapatan-box total">
                <div class="p-label">Total Pendapatan</div>
                <div class="p-value">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    {{-- Detail Pelanggan --}}
    <div class="section">
        <div class="section-title">Detail Pembayaran per Pelanggan</div>
        @if($detailPelanggan->isEmpty())
            <p class="no-data">Belum ada data tagihan untuk periode ini.</p>
        @else
        <table>
            <thead>
                <tr>
                    <th>No. Sambungan</th>
                    <th>Nama Pelanggan</th>
                    <th>Wilayah</th>
                    <th class="text-right">Pemakaian</th>
                    <th class="text-right">Total Tagihan</th>
                    <th class="text-center">Status</th>
                    <th class="text-center">Metode</th>
                </tr>
            </thead>
            <tbody>
                @foreach($detailPelanggan as $t)
                <tr>
                    <td style="font-family: monospace; color: #6b7280;">
                        {{ $t->pelanggan->nomor_sambungan }}
                    </td>
                    <td style="font-weight: 600;">{{ $t->pelanggan->nama }}</td>
                    <td>{{ $t->pelanggan->wilayah ?? '-' }}</td>
                    <td class="text-right">{{ number_format($t->total_pemakaian, 2) }} m³</td>
                    <td class="text-right" style="font-weight: 600;">
                        Rp {{ number_format($t->total_tagihan, 0, ',', '.') }}
                    </td>
                    <td class="text-center">
                        <span class="badge
                            @if($t->isLunas()) badge-lunas
                            @elseif($t->isMenungguVerifikasi()) badge-menunggu
                            @else badge-belum @endif">
                            {{ $t->labelStatus() }}
                        </span>
                    </td>
                    <td class="text-center" style="text-transform: uppercase; color: #6b7280;">
                        {{ $t->pembayaran?->metode ?? '-' }}
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif
    </div>

    {{-- Anomali --}}
    @if($anomali->isNotEmpty())
    <div class="section">
        <div class="section-title">⚠️ Anomali Meteran ({{ $anomali->count() }} pelanggan)</div>
        <table>
            <thead>
                <tr>
                    <th>No. Sambungan</th>
                    <th>Nama</th>
                    <th>Kondisi</th>
                    <th>Catatan Operator</th>
                </tr>
            </thead>
            <tbody>
                @foreach($anomali as $a)
                <tr>
                    <td style="font-family: monospace; color: #6b7280;">
                        {{ $a->pelanggan->nomor_sambungan }}
                    </td>
                    <td>{{ $a->pelanggan->nama }}</td>
                    <td>
                        <span class="badge badge-anomali">
                            {{ str_replace('_', ' ', $a->status_kondisi) }}
                        </span>
                    </td>
                    <td style="color: #6b7280;">{{ $a->catatan ?? '-' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <p>SIAP AIR — Sistem Informasi Air Perpipaan | Laporan ini digenerate otomatis oleh sistem.</p>
    </div>

</body>
</html>