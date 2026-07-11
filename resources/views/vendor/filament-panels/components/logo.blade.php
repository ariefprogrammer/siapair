@php
    $brandLogo ??= null;
    $darkModeBrandLogo ??= null;
    $hasDarkModeBrandLogo ??= false;
    $brandName ??= filament()->getBrandName();
    $logoStyles ??= '';
    $getLogoClasses ??= fn ($isDarkMode = false) => 'flex-shrink-0 h-8 w-auto';
@endphp

@capture($content, $logo, $isDarkMode = false)
    @if ($logo instanceof \Illuminate\Contracts\Support\Htmlable)
        <div
            {{
                $attributes
                    ->class([$getLogoClasses($isDarkMode)])
                    ->style([$logoStyles])
            }}
        >
            {{ $logo }}
        </div>
    @elseif (filled($logo))
        <div class="flex items-center gap-2">
            <img
                alt="{{ __('filament-panels::layout.logo.alt', ['name' => $brandName]) }}"
                src="{{ $logo }}"
                class="flex-shrink-0 h-8 w-auto"
            />
            <span class="font-semibold text-sm truncate text-gray-950 dark:text-white">
                {{ $brandName }}
            </span>
        </div>
    @else
        <div
            {{
                $attributes->class([
                    $getLogoClasses($isDarkMode),
                    'text-xl font-bold leading-5 tracking-tight text-gray-950 dark:text-white',
                ])
            }}
        >
            {{ $brandName }}
        </div>
    @endif
@endcapture

{{ $content($brandLogo) }}

@if ($hasDarkModeBrandLogo)
    {{ $content($darkModeBrandLogo, isDarkMode: true) }}
@endif