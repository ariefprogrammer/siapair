<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\CatatanMeter;
use App\Models\OperatorPelanggan;
use App\Models\Pelanggan;
use App\Models\PeriodePencatatan;
use App\Services\TagihanService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CatatanMeterController extends Controller
{
    public function __construct(private TagihanService $tagihanService) {}

    /**
     * Daftar pelanggan yang ditangani operator + status pencatatan periode aktif.
     */
    public function index(Request $request)
    {
        $header = 1;
        $operator     = auth()->user();
        $periodeAktif = PeriodePencatatan::buka()->latest('dibuka_at')->first();

        if (! $periodeAktif) {
            return view('operator.catatan-meter.index', [
                'pelanggan'    => collect(),
                'periodeAktif' => null,
                'daftarRt'     => collect(),
                'header'       => $header,
            ]);
        }

        $pelangganIds = OperatorPelanggan::where('operator_id', $operator->id)
            ->pluck('pelanggan_id');

        $daftarRt = Pelanggan::whereIn('id', $pelangganIds)
            ->whereNotNull('rt')
            ->distinct()
            ->orderBy('rt')
            ->pluck('rt');

        $catatanPeriodeIni = CatatanMeter::where('operator_id', $operator->id)
            ->where('periode_id', $periodeAktif->id)
            ->get()
            ->keyBy('pelanggan_id');

        $query = Pelanggan::whereIn('id', $pelangganIds)->aktif();

        if ($request->filled('rt')) {
            $query->where('rt', $request->query('rt'));
        }

        $pelanggan = $query->orderBy('nomor_sambungan')
            ->get()
            ->map(function ($p) use ($catatanPeriodeIni) {
                $catatan = $catatanPeriodeIni->get($p->id);

                $p->sudah_dicatat = !is_null($catatan);
                $p->catatan_meter_id = $catatan ? $catatan->id : null;

                return $p;
            });

        if ($request->ajax()) {
            return view('operator.catatan-meter._list', compact('pelanggan'))->render();
        }

        return view('operator.catatan-meter.index', compact('pelanggan', 'periodeAktif', 'header', 'daftarRt'));
    }

    /**
     * Form input catatan meter untuk satu pelanggan.
     */
    public function create(Request $request)
    {
        $operator     = auth()->user();
        $periodeAktif = PeriodePencatatan::buka()->latest('dibuka_at')->first();

        abort_if(! $periodeAktif, 403, 'Tidak ada periode pencatatan yang sedang buka.');

        $pelangganId = $request->query('pelanggan_id');
        abort_if(! $pelangganId, 400, 'Pelanggan tidak ditemukan.');

        // Pastikan pelanggan ini memang tanggung jawab operator
        $boleh = OperatorPelanggan::where('operator_id', $operator->id)
            ->where('pelanggan_id', $pelangganId)
            ->exists();
        abort_if(! $boleh, 403, 'Anda tidak menangani pelanggan ini.');

        $pelanggan = Pelanggan::findOrFail($pelangganId);

        // Cek sudah dicatat belum
        $sudahAda = CatatanMeter::where('pelanggan_id', $pelangganId)
            ->where('periode_id', $periodeAktif->id)
            ->first();

        if ($sudahAda) {
            return redirect()->route('operator.catatan-meter.show', $sudahAda->id)
                ->with('info', 'Catatan meter untuk pelanggan ini sudah diinput di periode ' . $periodeAktif->labelBulan() . '.');
        }

        // Ambil angka meter terakhir dari bulan sebelumnya
        $catatanSebelumnya = CatatanMeter::where('pelanggan_id', $pelangganId)
            ->latest('dicatat_at')
            ->first();

        $angkaMeterLalu = $catatanSebelumnya?->angka_meter_sekarang ?? 0;

        return view('operator.catatan-meter.create', compact(
            'pelanggan',
            'periodeAktif',
            'angkaMeterLalu',
        ));
    }

    /**
     * Simpan catatan meter + auto-generate tagihan.
     */
    public function store(Request $request)
    {
        $operator     = auth()->user();
        $periodeAktif = PeriodePencatatan::buka()->latest('dibuka_at')->first();

        abort_if(! $periodeAktif, 403, 'Tidak ada periode pencatatan yang sedang buka.');

        $validated = $request->validate([
            'pelanggan_id'          => ['required', 'exists:pelanggan,id'],
            'angka_meter_lalu'      => ['required', 'numeric', 'min:0'],
            'angka_meter_sekarang'  => [
                'required', 'numeric', 'min:0',
                'gte:angka_meter_lalu',
            ],
            'status_kondisi'        => ['nullable', 'string'],
            'catatan'               => ['nullable', 'string', 'max:500'],
            'foto'                  => ['nullable', 'image', 'max:5120'], // 5MB
        ], [
            'angka_meter_sekarang.gte' => 'Angka meter sekarang tidak boleh kurang dari angka meter lalu.',
        ]);

        // Pastikan pelanggan ini milik operator
        $boleh = OperatorPelanggan::where('operator_id', $operator->id)
            ->where('pelanggan_id', $validated['pelanggan_id'])
            ->exists();
        abort_if(! $boleh, 403);

        // Cek duplikat
        $sudahAda = CatatanMeter::where('pelanggan_id', $validated['pelanggan_id'])
            ->where('periode_id', $periodeAktif->id)
            ->exists();

        if ($sudahAda) {
            return back()->withErrors(['pelanggan_id' => 'Catatan meter sudah pernah diinput untuk periode ini.']);
        }

        DB::transaction(function () use ($validated, $request, $operator, $periodeAktif) {
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $fotoPath = $request->file('foto')->store('catatan-meter', 'public');
            }
            
            $catatan = CatatanMeter::create([
                'pelanggan_id'          => $validated['pelanggan_id'],
                'operator_id'           => $operator->id,
                'periode_id'            => $periodeAktif->id,
                'angka_meter_lalu'      => $validated['angka_meter_lalu'],
                'angka_meter_sekarang'  => $validated['angka_meter_sekarang'],
                'status_kondisi'        => $validated['status_kondisi'],
                'catatan'               => $validated['catatan'],
                'foto_path'             => $fotoPath,
                'dicatat_at'            => now(),
            ]);

            // Auto-generate tagihan 
            $this->tagihanService->generateDariCatatan($catatan);
        });

        return redirect()->route('operator.catatan-meter.index')
            ->with('success', 'Catatan meter berhasil disimpan dan tagihan telah digenerate.');
    }

    /**
     * Detail catatan meter.
     */
    public function show(CatatanMeter $catatanMeter)
    {
        $operator = auth()->user();

        abort_if($catatanMeter->operator_id !== $operator->id, 403);

        $catatanMeter->load(['pelanggan', 'periode', 'tagihan']);

        return view('operator.catatan-meter.show', compact('catatanMeter'));
    }
}