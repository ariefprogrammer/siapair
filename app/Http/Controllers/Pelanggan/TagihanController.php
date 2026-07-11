<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\CatatanMeter;
use App\Models\Tagihan;
use App\Models\QrisSetting;
use App\Services\PembayaranService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TagihanController extends Controller
{
    public function __construct(private PembayaranService $pembayaranService) {}

    /**
     * Daftar semua tagihan pelanggan.
     */
    public function index()
    {
        $header =1;
        $pelanggan = auth()->user()->pelanggan;

        $tagihan = Tagihan::where('pelanggan_id', $pelanggan->id)
            ->with(['periode', 'pembayaran'])
            ->latest()
            ->paginate(10);

        $tagihanAktif = Tagihan::where('pelanggan_id', $pelanggan->id)
            ->whereIn('status', ['belum_dibayar', 'menunggu_verifikasi'])
            ->with('periode')
            ->latest()
            ->first();

        return view('pelanggan.tagihan.index', compact('tagihan', 'tagihanAktif', 'header'));
    }

    /**
     * Detail satu tagihan.
     */
    public function show(Tagihan $tagihan)
    {
        $this->otorisasiTagihan($tagihan);

        $tagihan->load(['periode', 'pembayaran', 'catatanMeter']);

        return view('pelanggan.tagihan.show', compact('tagihan'));
    }

    /**
     * Halaman QRIS — tampilkan kode QRIS statis + form upload bukti.
     */
    public function qris(Tagihan $tagihan)
    {
        $this->otorisasiTagihan($tagihan);

        abort_if(! $tagihan->isBelumDibayar(), 403, 'Tagihan ini tidak dalam status Belum Dibayar.');

        $tagihan->load('periode');

        $qrisSetting = QrisSetting::first(); 
    
        $qrisPath = ($qrisSetting && $qrisSetting->image_path) 
            ? asset('storage/' . $qrisSetting->image_path) 
            : null;

        return view('pelanggan.tagihan.qris', compact('tagihan', 'qrisPath', 'qrisSetting'));
    }

    /**
     * Proses upload bukti bayar QRIS.
     */
    public function uploadBukti(Request $request, Tagihan $tagihan)
    {
        $this->otorisasiTagihan($tagihan);

        abort_if(! $tagihan->isBelumDibayar(), 403, 'Tagihan ini tidak dalam status Belum Dibayar.');

        $request->validate([
            'bukti_bayar' => ['required', 'image', 'max:5120'],
        ], [
            'bukti_bayar.required' => 'Bukti bayar wajib diunggah.',
            'bukti_bayar.image'    => 'File harus berupa gambar.',
            'bukti_bayar.max'      => 'Ukuran file maksimal 5MB.',
        ]);

        $path = $request->file('bukti_bayar')
            ->store('bukti-bayar', 'public');

        try {
            $this->pembayaranService->uploadBuktiBayar($tagihan, $path);
        } catch (\RuntimeException $e) {
            Storage::disk('public')->delete($path);
            return back()->withErrors(['bukti_bayar' => $e->getMessage()]);
        }

        return redirect()->route('pelanggan.tagihan.show', $tagihan->id)
            ->with('success', 'Bukti bayar berhasil diunggah. Menunggu verifikasi administrator.');
    }

    /**
     * Riwayat pemakaian air (catatan meter).
     */
    public function riwayat()
    {
        $header =1;

        $pelanggan = auth()->user()->pelanggan;

        $riwayat = CatatanMeter::where('pelanggan_id', $pelanggan->id)
            ->with(['periode', 'tagihan'])
            ->latest('dicatat_at')
            ->paginate(12);

        return view('pelanggan.riwayat.index', compact('riwayat', 'header'));
    }

    public function notaRiwayat(CatatanMeter $catatan)
    {
        $pelanggan = auth()->user()->pelanggan;
        abort_if($catatan->pelanggan_id !== $pelanggan->id, 403);

        abort_if(! $catatan->tagihan || ! $catatan->tagihan->isLunas(), 404);

        $catatan->load(['tagihan.periode', 'tagihan.pelanggan', 'tagihan.pembayaran.teller']);

        $pembayaran = $catatan->tagihan->pembayaran;

        return view('pelanggan.riwayat.nota', compact('pembayaran'));
    }

    private function otorisasiTagihan(Tagihan $tagihan): void
    {
        $pelanggan = auth()->user()->pelanggan;
        abort_if($tagihan->pelanggan_id !== $pelanggan->id, 403);
    }
}