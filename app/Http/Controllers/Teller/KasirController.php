<?php

namespace App\Http\Controllers\Teller;

use App\Http\Controllers\Controller;
use App\Models\Pelanggan;
use App\Models\Pembayaran;
use App\Models\Tagihan;
use App\Services\PembayaranService;
use Illuminate\Http\Request;

class KasirController extends Controller
{
    public function __construct(private PembayaranService $pembayaranService) {}

    /**
     * Halaman kasir — cari pelanggan via nomor sambungan atau nama.
     */
    public function index(Request $request)
    {
        $header = 1;
        $keyword   = $request->query('q');
        $pelanggan = null;
        $tagihan   = null;
        $daftarTagihanBelumBayar = collect();

        if ($keyword) {
            $pelanggan = Pelanggan::where('nomor_sambungan', 'like', "%{$keyword}%")
                ->orWhere('nama', 'like', "%{$keyword}%")
                ->with(['tagihan' => fn ($q) => $q->belumDibayar()->with('pembayaran')->latest()])
                ->first();

            if ($pelanggan) {
                $tagihan = $pelanggan->tagihan
                    ->whereIn('status', ['belum_dibayar'])
                    ->sortByDesc('created_at')
                    ->first();
            }
        }else {            
            $daftarTagihanBelumBayar = Tagihan::with(['pelanggan', 'periode'])
                ->where('status', 'belum_dibayar')
                ->latest()
                ->get();
        }

        return view('teller.kasir.index', compact('keyword', 'pelanggan', 'tagihan', 'daftarTagihanBelumBayar', 'header'));
    }

    /**
     * Proses pembayaran tunai.
     */
    public function bayar(Request $request)
    {
        $request->validate([
            'tagihan_id' => ['required', 'exists:tagihan,id'],
        ]);

        $tagihan = Tagihan::with('pelanggan')->findOrFail($request->tagihan_id);

        if (! $tagihan->isBelumDibayar()) {
            return back()->withErrors(['tagihan_id' => 'Tagihan ini sudah dibayar atau sedang menunggu verifikasi.']);
        }

        try {
            $pembayaran = $this->pembayaranService->bayarTunai($tagihan, auth()->id());
        } catch (\RuntimeException $e) {
            return back()->withErrors(['tagihan_id' => $e->getMessage()]);
        }

        return redirect()->route('teller.kasir.nota', $pembayaran->id)
            ->with('success', 'Pembayaran berhasil diproses.');
    }

    /**
     * Halaman nota / struk.
     */
    public function nota(Pembayaran $pembayaran)
    {
        // Pastikan nota ini milik teller yang login
        abort_if($pembayaran->teller_id !== auth()->id(), 403);

        $pembayaran->load(['tagihan.pelanggan', 'tagihan.periode', 'teller']);

        return view('teller.kasir.nota', compact('pembayaran'));
    }

    /**
     * Riwayat transaksi teller hari ini.
     */
    public function riwayat(Request $request)
    {
        $header = 1;
        $teller = auth()->user();
        $tanggal = $request->query('tanggal', today()->toDateString());

        $transaksi = Pembayaran::with(['tagihan.pelanggan', 'tagihan.periode'])
            ->where('teller_id', $teller->id)
            ->whereDate('tanggal_bayar', $tanggal)
            ->latest('tanggal_bayar')
            ->paginate(20);

        $totalPendapatan = Pembayaran::where('teller_id', $teller->id)
            ->whereDate('tanggal_bayar', $tanggal)
            ->sum('jumlah_bayar');

        return view('teller.kasir.riwayat', compact('transaksi', 'totalPendapatan', 'tanggal', 'header'));
    }
}