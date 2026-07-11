@extends('layouts.teller')
@section('title', 'Profile')
@section('header', 'Profile')

@section('content')

{{-- Avatar & info --}}
<div class="flex flex-col items-center py-6 mt-2">
    <div class="w-20 h-20 rounded-full bg-blue-100 flex items-center justify-center text-3xl mb-3 shadow">
        <i class="fas fa-user text-blue-600"></i>
    </div>
    <h2 class="text-lg font-bold text-gray-800">{{ $user->name }}</h2>
    <p class="text-sm text-gray-400">{{ $user->email }}</p>
    <span class="mt-2 text-xs px-3 py-1 rounded-full
        {{ $user->is_active ? 'bg-blue-100 text-blue-700' : 'bg-red-100 text-red-700' }}">
        {{ $user->is_active ? '● Teller Aktif' : '● Nonaktif' }}
    </span>
</div>

{{-- Flash sukses password --}}
@if(session('success_password'))
    <div class="bg-green-100 border border-green-300 text-green-800 text-sm rounded-xl px-4 py-3 mb-4">
        ✅ {{ session('success_password') }}
    </div>
@endif

{{-- Data Akun --}}
<div class="bg-white rounded-2xl shadow-sm p-4 mb-4">
    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
        Informasi Akun
    </h3>
    <div class="space-y-3">
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-500">Nama</span>
            <span class="text-sm font-medium text-gray-800">{{ $user->name }}</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-500">Email</span>
            <span class="text-sm text-gray-800">{{ $user->email }}</span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-500">Role</span>
            <span class="text-xs bg-blue-100 text-blue-700 px-3 py-1 rounded-full font-medium">
                Operator
            </span>
        </div>
        <div class="flex justify-between items-center">
            <span class="text-sm text-gray-500">Bergabung</span>
            <span class="text-sm text-gray-800">
                {{ $user->created_at->format('d M Y') }}
            </span>
        </div>
    </div>
</div>

{{-- Ubah Password --}}
<div class="bg-white rounded-2xl shadow-sm p-4 mb-4">
    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
        Ubah Password
    </h3>

    @if($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3 mb-4 space-y-1">
            @foreach($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('teller.profile.password') }}">
        @csrf

        <div class="space-y-4">

            {{-- Password lama --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Password Lama
                </label>
                <div class="relative">
                    <input type="password"
                           name="password_lama"
                           id="password_lama"
                           class="w-full border @error('password_lama') border-red-400 @else border-gray-300 @enderror
                                  rounded-xl px-3 py-2.5 text-sm pr-10
                                  focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <button type="button"
                            onclick="togglePassword('password_lama', 'eye_lama')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">
                        <span id="eye_lama">👁</span>
                    </button>
                </div>
            </div>

            {{-- Password baru --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Password Baru
                </label>
                <div class="relative">
                    <input type="password"
                           name="password_baru"
                           id="password_baru"
                           class="w-full border @error('password_baru') border-red-400 @else border-gray-300 @enderror
                                  rounded-xl px-3 py-2.5 text-sm pr-10
                                  focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <button type="button"
                            onclick="togglePassword('password_baru', 'eye_baru')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">
                        <span id="eye_baru">👁</span>
                    </button>
                </div>
                <p class="text-xs text-gray-400 mt-1">Minimal 8 karakter.</p>
            </div>

            {{-- Konfirmasi --}}
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">
                    Konfirmasi Password Baru
                </label>
                <div class="relative">
                    <input type="password"
                           name="password_baru_confirmation"
                           id="password_konfirmasi"
                           class="w-full border border-gray-300 rounded-xl px-3 py-2.5 text-sm pr-10
                                  focus:outline-none focus:ring-2 focus:ring-blue-400">
                    <button type="button"
                            onclick="togglePassword('password_konfirmasi', 'eye_konfirmasi')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm">
                        <span id="eye_konfirmasi">👁</span>
                    </button>
                </div>
            </div>

            {{-- Strength indicator --}}
            <div id="strength-wrap" class="hidden">
                <div class="flex gap-1 mb-1">
                    <div id="s1" class="h-1 flex-1 rounded-full bg-gray-200"></div>
                    <div id="s2" class="h-1 flex-1 rounded-full bg-gray-200"></div>
                    <div id="s3" class="h-1 flex-1 rounded-full bg-gray-200"></div>
                    <div id="s4" class="h-1 flex-1 rounded-full bg-gray-200"></div>
                </div>
                <p id="strength-label" class="text-xs text-gray-400"></p>
            </div>

        </div>

        <button type="submit"
                class="flex items-center justify-center gap-2 w-full bg-deep text-white text-center font-semibold py-3.5 mt-4 rounded-2xl text-sm transition active:scale-[0.98]">
            Simpan Password Baru
        </button>
    </form>
</div>

{{-- Logout --}}
<div class="bg-white rounded-2xl shadow-sm p-4 mb-6">
    <h3 class="text-xs font-semibold text-gray-400 uppercase tracking-wide mb-3">
        Sesi
    </h3>
    <form method="POST"
          action="{{ route('teller.profile.logout') }}"
          onsubmit="return confirm('Yakin ingin keluar?')">
        @csrf
        <button type="submit"
                class="w-full flex items-center justify-center gap-2 bg-red-50 hover:bg-red-100
                       text-red-600 font-semibold py-3 rounded-xl transition text-sm border border-red-200">
            <i class="fas fa-sign-out-alt"></i>
            <span>Logout</span>
        </button>
    </form>
</div>

<p class="text-center text-xs text-gray-300 mb-2">SIAP AIR v1.0.1</p>

<script>
function togglePassword(inputId, eyeId) {
    const input = document.getElementById(inputId);
    const eye   = document.getElementById(eyeId);
    if (input.type === 'password') {
        input.type = 'text';
        eye.textContent = '🙈';
    } else {
        input.type = 'password';
        eye.textContent = '👁';
    }
}

document.getElementById('password_baru').addEventListener('input', function () {
    const val   = this.value;
    const wrap  = document.getElementById('strength-wrap');
    const bars  = ['s1','s2','s3','s4'].map(id => document.getElementById(id));
    const label = document.getElementById('strength-label');

    if (val.length === 0) { wrap.classList.add('hidden'); return; }
    wrap.classList.remove('hidden');

    let score = 0;
    if (val.length >= 8)           score++;
    if (/[A-Z]/.test(val))         score++;
    if (/[0-9]/.test(val))         score++;
    if (/[^A-Za-z0-9]/.test(val)) score++;

    const colors = ['bg-red-400', 'bg-orange-400', 'bg-yellow-400', 'bg-green-500'];
    const labels = ['Sangat lemah', 'Lemah', 'Cukup', 'Kuat'];
    const textColors = ['text-red-500', 'text-orange-500', 'text-yellow-600', 'text-green-600'];

    bars.forEach((bar, i) => {
        bar.className = 'h-1 flex-1 rounded-full ' + (i < score ? colors[score - 1] : 'bg-gray-200');
    });

    label.textContent = labels[score - 1] ?? '';
    label.className   = 'text-xs ' + (textColors[score - 1] ?? 'text-gray-400');
});
</script>

@endsection