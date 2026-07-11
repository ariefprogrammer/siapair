<!DOCTYPE html>
<html lang="id" class="h-full bg-[#0B3D3D]">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, viewport-fit=cover">
    <title>Login — {{ $configGeneral->app_name ?? 'SIAP AIR' }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        body { 
            font-family: 'Inter', sans-serif; 
            -webkit-tap-highlight-color: transparent;
        }
        h1, h2, h3, .font-display { font-family: 'Plus Jakarta Sans', sans-serif; }
        ::-webkit-scrollbar { display: none; }
    </style>
</head>
<body class="h-full flex justify-center items-center bg-gray-50 select-none">

    <div class="w-full max-w-md h-full sm:h-[85vh] sm:max-h-[800px] sm:rounded-3xl sm:shadow-2xl bg-white flex flex-col justify-between overflow-hidden relative">
        
        <div class="pt-[env(safe-area-inset-top)] bg-white"></div>

        <div class="flex-1 flex flex-col justify-center px-6 py-8 overflow-y-auto">
            
            <div class="text-center mb-8">
                <div class="w-20 h-20 bg-[#0B3D3D]/10 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-sm">
                    @if($configGeneral && $configGeneral->app_logo)
                        <img src="{{ asset('storage/' . $configGeneral->app_logo) }}" alt="Logo" class="w-16 h-16 rounded-full object-cover">
                    @else
                        <span>💧</span>
                    @endif
                </div>
                <h1 class="text-2xl font-bold text-[#0B3D3D] tracking-tight">{{ $configGeneral->app_name ?? 'SIAP AIR' }}</h1>
                <p class="text-gray-500 text-xs mt-1 font-medium">Sistem Informasi Air Perpipaan</p>
            </div>

            @if($errors->any())
                <div class="bg-red-50 border-l-4 border-red-500 text-red-800 text-sm rounded-r-xl px-4 py-3 mb-5 shadow-sm flex items-center gap-2">
                    <span class="text-red-500">⚠️</span>
                    <p class="font-medium">{{ $errors->first() }}</p>
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5 ml-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm transition-all focus:outline-none focus:border-[#0B3D3D] focus:bg-white focus:ring-4 focus:ring-[#0B3D3D]/10"
                        placeholder="email@contoh.com"
                        required autofocus>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5 ml-1">Password</label>
                    <input type="password" name="password"
                        class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-3 text-sm transition-all focus:outline-none focus:border-[#0B3D3D] focus:bg-white focus:ring-4 focus:ring-[#0B3D3D]/10"
                        placeholder="••••••••"
                        required>
                </div>

                <div class="flex items-center justify-between pt-1">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="checkbox" name="remember" id="remember" 
                            class="w-4 h-4 rounded text-[#0B3D3D] border-gray-300 focus:ring-[#0B3D3D]/20 accent-[#0B3D3D]">
                        <span class="text-sm text-gray-600 font-medium group-hover:text-gray-900">Ingat saya</span>
                    </label>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full bg-[#0B3D3D] hover:bg-[#072929] active:scale-[0.99] text-white font-semibold py-3.5 rounded-xl transition-all text-sm shadow-md shadow-[#0B3D3D]/20 block text-center">
                        Masuk
                    </button>
                </div>
            </form>
        </div>

        <div class="pb-[calc(env(safe-area-inset-bottom)+1.5rem)] px-6 text-center">
            <p class="text-xs font-medium text-gray-400">
                &copy; {{ date('Y') }} {{ $configGeneral->app_name ?? 'SIAP AIR' }}. All rights reserved.
            </p>
        </div>

    </div>

</body>
</html>