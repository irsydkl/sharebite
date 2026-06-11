@props([
    'latName' => 'latitude',
    'lngName' => 'longitude',
    'label' => 'Lokasi di Peta',
    'required' => false,
    'height' => '280px',
    'initialLat' => null,
    'initialLng' => null,
])

@php
    $latId = $latName;
    $lngId = $lngName;
    $initialLat = old($latName, $initialLat);
    $initialLng = old($lngName, $initialLng);
@endphp

<div {{ $attributes->merge(['class' => 'map-picker']) }}
     data-map-picker
     data-lat-input="{{ $latId }}"
     data-lng-input="{{ $lngId }}"
     data-required="{{ $required ? '1' : '0' }}"
     data-initial-lat="{{ $initialLat ?? '' }}"
     data-initial-lng="{{ $initialLng ?? '' }}">

    <div class="flex flex-wrap items-center justify-between gap-2">
        <x-input-label :value="$label" />
        <button type="button"
                data-locate-me
                class="text-sm font-medium text-indigo-600 hover:text-indigo-500">
            Gunakan lokasi saya
        </button>
    </div>

    <p class="mt-1 text-xs text-gray-500">
        Ketuk peta untuk menempatkan penanda{{ $required ? '' : ' (opsional)' }}.
    </p>

    <div data-map
         class="mt-2 z-0 w-full rounded-lg border border-gray-300 overflow-hidden"
         style="height: {{ $height }}"></div>

    <p class="mt-2 text-xs text-gray-600">
        Koordinat: <span data-coords-display class="font-mono">{{ $initialLat && $initialLng ? $initialLat.', '.$initialLng : 'belum dipilih' }}</span>
    </p>

    <input type="hidden"
           name="{{ $latName }}"
           id="{{ $latId }}"
           value="{{ $initialLat }}"
           @if($required) required @endif>

    <input type="hidden"
           name="{{ $lngName }}"
           id="{{ $lngId }}"
           value="{{ $initialLng }}"
           @if($required) required @endif>

    <x-input-error :messages="$errors->get($latName)" class="mt-2" />
    <x-input-error :messages="$errors->get($lngName)" class="mt-2" />
</div>

@once
    @push('body-scripts')
        @vite('resources/js/map-picker.js')
    @endpush
@endonce
