<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class ProfileController extends Controller
{
    public function index()
    {
        $header = 1;
        $user      = auth()->user();
        $pelanggan = $user->pelanggan;

        abort_if(! $pelanggan, 403, 'Data pelanggan tidak ditemukan.');

        return view('pelanggan.profile.index', compact('user', 'pelanggan', 'header'));
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'password_lama'  => ['required'],
            'password_baru'  => ['required', 'confirmed', Password::min(8)],
        ], [
            'password_lama.required'          => 'Password lama wajib diisi.',
            'password_baru.required'          => 'Password baru wajib diisi.',
            'password_baru.confirmed'         => 'Konfirmasi password tidak cocok.',
            'password_baru.min'               => 'Password baru minimal 8 karakter.',
        ]);

        if (! Hash::check($request->password_lama, auth()->user()->password)) {
            return back()
                ->withErrors(['password_lama' => 'Password lama tidak sesuai.'])
                ->withInput();
        }

        auth()->user()->update([
            'password' => Hash::make($request->password_baru),
        ]);

        return back()->with('success_password', 'Password berhasil diubah.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}