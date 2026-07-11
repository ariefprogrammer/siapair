<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\PengaduanPesan;
use Illuminate\Support\Facades\Storage;
use App\Models\OperatorPelanggan;
use App\Models\Pengaduan;
use Illuminate\Http\Request;

class PengaduanController extends Controller
{
    public function index()
    {
        $header =1;
        $pelanggan = auth()->user()->pelanggan;

        $pengaduan = Pengaduan::where('pelanggan_id', $pelanggan->id)
            ->latest()
            ->paginate(10);

        $operator = OperatorPelanggan::where('pelanggan_id', $pelanggan->id)
            ->with('operator')
            ->first()?->operator;

        return view('pelanggan.pengaduan.index', compact('pengaduan', 'header', 'operator'));
    }

    public function create()
    {
        return view('pelanggan.pengaduan.create');
    }

    public function store(Request $request)
    {
        $pelanggan = auth()->user()->pelanggan;

        $validated = $request->validate([
            'kategori'  => ['required', 'in:teknis,administrasi,lainnya'],
            'deskripsi' => ['required', 'string', 'min:20', 'max:1000'],
            'lampiran'  => ['nullable', 'image', 'max:5120'],
        ], [
            'deskripsi.min' => 'Deskripsi minimal 20 karakter.',
        ]);

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('pengaduan', 'public');
        }

        $pengaduan = \Illuminate\Support\Facades\DB::transaction(function () use ($validated, $pelanggan, $lampiranPath) {
            $p = Pengaduan::create([
                'pelanggan_id'  => $pelanggan->id,
                'kategori'      => $validated['kategori'],
                'deskripsi'     => $validated['deskripsi'],
                'lampiran_path' => $lampiranPath,
                'status'        => 'masuk',
            ]);

            // Pesan pertama = deskripsi pengaduan itu sendiri
            PengaduanPesan::create([
                'pengaduan_id'  => $p->id,
                'user_id'       => auth()->id(),
                'pesan'         => $validated['deskripsi'],
                'lampiran_path' => $lampiranPath,
            ]);

            return $p;
        });

        return redirect()->route('pelanggan.pengaduan.show', $pengaduan->id)
            ->with('success', 'Pengaduan berhasil dikirim.');
    }

    public function show(Pengaduan $pengaduan)
    {
        $pelanggan = auth()->user()->pelanggan;
        abort_if($pengaduan->pelanggan_id !== $pelanggan->id, 403);

        $pengaduan->load(['pesan.user']);

        return view('pelanggan.pengaduan.show', compact('pengaduan'));
    }

    public function balas(Request $request, Pengaduan $pengaduan)
    {
        $pelanggan = auth()->user()->pelanggan;
        abort_if($pengaduan->pelanggan_id !== $pelanggan->id, 403);
        abort_if($pengaduan->isSelesai(), 403, 'Pengaduan sudah ditutup.');

        $validated = $request->validate([
            'pesan'    => ['required', 'string', 'max:1000'],
            'lampiran' => ['nullable', 'image', 'max:5120'],
        ]);

        $lampiranPath = null;
        if ($request->hasFile('lampiran')) {
            $lampiranPath = $request->file('lampiran')->store('pengaduan-pesan', 'public');
        }

        PengaduanPesan::create([
            'pengaduan_id'  => $pengaduan->id,
            'user_id'       => auth()->id(),
            'pesan'         => $validated['pesan'],
            'lampiran_path' => $lampiranPath,
        ]);

        if ($pengaduan->status === 'diproses') {
            $pengaduan->update(['status' => 'masuk']);
        }

        return back()->with('success', 'Pesan berhasil dikirim.');
    }
}