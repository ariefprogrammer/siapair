<?php

namespace App\Filament\Pages;

use App\Models\CatatanMeter;
use App\Models\Pembayaran;
use App\Models\PeriodePencatatan;
use App\Models\Tagihan;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Pages\Page;

class LaporanPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-document-chart-bar';
    protected static ?string $navigationGroup = 'Pembayaran';
    protected static ?string $navigationLabel = 'Laporan';
    protected static ?string $title           = 'Laporan';
    protected static ?int $navigationSort     = 2;

    protected static string $view = 'filament.pages.laporan';

    // Filter state
    public ?string $periode_id = null;

    public function mount(): void
    {
        // Default ke periode aktif
        $periodeAktif = PeriodePencatatan::buka()->latest('dibuka_at')->first()
            ?? PeriodePencatatan::latest('tahun')->latest('bulan')->first();

        $this->periode_id = $periodeAktif?->id;
    }

    public function form(Form $form): Form
    {
        return $form->schema([
            Select::make('periode_id')
                ->label('Periode')
                ->options(
                    PeriodePencatatan::orderByDesc('tahun')
                        ->orderByDesc('bulan')
                        ->get()
                        ->mapWithKeys(fn ($p) => [$p->id => $p->labelBulan()])
                )
                ->live()
                ->native(false),
        ])->statePath('');
    }

    // Data laporan berdasarkan periode dipilih
    public function getData(): array
    {
        if (! $this->periode_id) {
            return $this->emptyData();
        }

        $periode = PeriodePencatatan::find($this->periode_id);
        if (! $periode) return $this->emptyData();

        // Ringkasan
        $totalPelangganDicatat = CatatanMeter::where('periode_id', $periode->id)->count();
        $totalPemakaian        = CatatanMeter::where('periode_id', $periode->id)
            ->where('status_kondisi', 'normal')
            ->sum('pemakaian');

        $totalTagihan     = Tagihan::where('periode_id', $periode->id)->count();
        $totalLunas       = Tagihan::where('periode_id', $periode->id)->lunas()->count();
        $totalBelumBayar  = Tagihan::where('periode_id', $periode->id)->belumDibayar()->count();
        $totalMenunggu    = Tagihan::where('periode_id', $periode->id)->menungguVerifikasi()->count();

        $pendapatanTunai  = Pembayaran::tunai()
            ->whereHas('tagihan', fn ($q) => $q->where('periode_id', $periode->id)->where('status', 'lunas'))
            ->sum('jumlah_bayar');

        $pendapatanQris   = Pembayaran::qris()
            ->where('status_verifikasi', 'disetujui')
            ->whereHas('tagihan', fn ($q) => $q->where('periode_id', $periode->id))
            ->sum('jumlah_bayar');

        $totalPendapatan  = $pendapatanTunai + $pendapatanQris;

        // Data per pelanggan
        $detailPelanggan = Tagihan::where('periode_id', $periode->id)
            ->with(['pelanggan', 'pembayaran', 'catatanMeter'])
            ->get()
            ->map(fn ($t) => [
                'nomor_sambungan' => $t->pelanggan->nomor_sambungan,
                'nama'            => $t->pelanggan->nama,
                'wilayah'         => $t->pelanggan->wilayah,
                'pemakaian'       => $t->total_pemakaian,
                'total_tagihan'   => $t->total_tagihan,
                'status'          => $t->labelStatus(),
                'metode'          => $t->pembayaran?->metode ?? '-',
            ]);

        // Anomali meter
        $anomali = CatatanMeter::where('periode_id', $periode->id)
            ->where('status_kondisi', '!=', 'normal')
            ->with('pelanggan')
            ->get();

        return compact(
            'periode',
            'totalPelangganDicatat',
            'totalPemakaian',
            'totalTagihan',
            'totalLunas',
            'totalBelumBayar',
            'totalMenunggu',
            'pendapatanTunai',
            'pendapatanQris',
            'totalPendapatan',
            'detailPelanggan',
            'anomali',
        );
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('export_pdf')
                ->label('Export PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('danger')
                ->url(fn () => route('admin.laporan.pdf', ['periode_id' => $this->periode_id]))
                ->openUrlInNewTab(),
        ];
    }

    private function emptyData(): array
    {
        return [
            'periode'                => null,
            'totalPelangganDicatat'  => 0,
            'totalPemakaian'         => 0,
            'totalTagihan'           => 0,
            'totalLunas'             => 0,
            'totalBelumBayar'        => 0,
            'totalMenunggu'          => 0,
            'pendapatanTunai'        => 0,
            'pendapatanQris'         => 0,
            'totalPendapatan'        => 0,
            'detailPelanggan'        => collect(),
            'anomali'                => collect(),
        ];
    }
}