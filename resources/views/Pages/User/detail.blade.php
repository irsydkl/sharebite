@extends('layouts.app')

@section('title', 'Detail Makanan')

@section('content')
@include('components.sidebar-user')
<main class="flex-1 overflow-y-auto p-8 md:p-12 lg:p-16">
    <div class="max-w-4xl">
        <!-- Back Button -->
        <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 font-semibold mb-6 transition-colors">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
        </a>

        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl">
                <p class="text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Side: Food Detail Cards -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Food Card -->
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                    <!-- Images Carousel -->
                    <div class="h-80 bg-gray-100 relative">
                        @if($food->images->isEmpty())
                            <div class="w-full h-full flex items-center justify-center text-gray-400 bg-gray-50">
                                <i class="fa-solid fa-image text-4xl"></i>
                            </div>
                        @else
                            <img src="{{ asset('storage/' . $food->images->first()->image) }}" alt="{{ $food->title }}" class="w-full h-full object-cover">
                        @endif
                        <span class="absolute top-4 left-4 px-3 py-1 rounded-full text-xs font-bold bg-green-600 text-white shadow-md">
                            {{ $food->category->name }}
                        </span>
                    </div>

                    <!-- Details -->
                    <div class="p-6 sm:p-8 space-y-4">
                        <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 leading-tight">{{ $food->title }}</h1>
                        <p class="text-gray-700 text-sm leading-relaxed whitespace-pre-line">
                            {{ $food->description ?? 'Tidak ada deskripsi rinci untuk makanan ini.' }}
                        </p>
                    </div>
                </div>

                <!-- Pickup & Location Map Card -->
                <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200 shadow-sm space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-3">Lokasi & Jadwal Pengambilan</h3>
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Mulai Ambil</p>
                            <p class="font-bold text-gray-800">{{ $food->pickup_start->format('d M Y, H:i') }}</p>
                        </div>
                        <div class="bg-gray-50 p-4 rounded-xl border border-gray-100">
                            <p class="text-xs text-gray-500 font-medium uppercase tracking-wider mb-1">Selesai Ambil</p>
                            <p class="font-bold text-gray-800">{{ $food->pickup_end->format('d M Y, H:i') }}</p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm font-semibold text-gray-700 mb-1">Alamat Penjemputan:</p>
                        <p class="text-sm text-gray-600 bg-gray-50 border border-gray-100 p-4 rounded-xl font-medium">
                            {{ $food->pickup_address }}
                        </p>
                    </div>

                    <!-- Leaflet Map -->
                    <div>
                        <p class="text-sm font-semibold text-gray-700 mb-2">Peta Lokasi Donatur:</p>
                        <div id="food-detail-map" class="h-64 z-0 rounded-xl border border-gray-200 shadow-inner"></div>
                    </div>
                </div>
            </div>

            <!-- Right Side: Claim Widget -->
            <div class="space-y-6">
                <!-- Donor Store Card -->
                <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 bg-green-50 border border-green-200 rounded-full flex items-center justify-center text-green-700 overflow-hidden font-extrabold text-lg">
                            @if($food->donor->donorProfile->store_image)
                                <img src="{{ asset('storage/' . $food->donor->donorProfile->store_image) }}" alt="Toko" class="w-full h-full object-cover">
                            @else
                                {{ strtoupper(substr($food->donor->donorProfile->store_name ?? $food->donor->name, 0, 2)) }}
                            @endif
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-900">{{ $food->donor->donorProfile->store_name ?? 'Toko Donatur' }}</h4>
                            <span class="inline-flex items-center gap-1 text-[10px] font-bold text-green-600 uppercase tracking-wide">
                                <i class="fa-solid fa-circle-check"></i> Donatur Terverifikasi
                            </span>
                        </div>
                    </div>
                    <p class="text-xs text-gray-500 italic">
                        "{{ $food->donor->donorProfile->store_description ?? 'Menyediakan makanan berlebih berkualitas.' }}"
                    </p>
                </div>

                <!-- Purchase Card -->
                <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
                    <h3 class="text-lg font-bold text-gray-900 border-b border-gray-100 pb-2">Simpan Makanan</h3>
                    
                    <div class="space-y-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Stok Tersedia</span>
                            <span class="font-bold text-gray-900">{{ $food->remaining_quantity }} {{ $food->unit }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Harga Satuan</span>
                            <span class="font-bold text-gray-900">Rp {{ number_format($food->original_price / $food->quantity, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-gray-500">Biaya Layanan (10%)</span>
                            <span class="font-bold text-gray-900">Rp {{ number_format($food->service_fee / $food->quantity, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between pt-2 border-t border-gray-100">
                            <span class="font-semibold text-gray-900">Harga Penyelamatan</span>
                            <span class="font-extrabold text-indigo-600 text-xl">Rp {{ number_format($food->final_price, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <!-- Claim Form -->
                    <form action="{{ route('user.food.claim', $food->id) }}" method="POST" class="space-y-4 pt-2">
                        @csrf
                        <div>
                            <label for="quantity_claimed" class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-1">Jumlah Porsi yang Diklaim</label>
                            <div class="flex items-center">
                                <button type="button" onclick="decrementQuantity()" class="w-10 h-10 border border-gray-300 rounded-l-lg bg-gray-50 text-gray-700 font-bold hover:bg-gray-100">-</button>
                                <input type="number" name="quantity_claimed" id="quantity_claimed" value="1" min="1" max="{{ $food->remaining_quantity }}" required readonly
                                    class="w-full h-10 text-center border-y border-gray-300 font-bold text-gray-900 focus:outline-none">
                                <button type="button" onclick="incrementQuantity()" class="w-10 h-10 border border-gray-300 rounded-r-lg bg-gray-50 text-gray-700 font-bold hover:bg-gray-100">+</button>
                            </div>
                        </div>

                        <div class="bg-amber-50 text-amber-800 text-[11px] p-3 rounded-lg border border-amber-200">
                            <i class="fa-solid fa-triangle-exclamation"></i> Setelah mengklaim, Anda memiliki waktu 30 menit untuk menyelesaikan pembayaran.
                        </div>

                        <button type="submit" 
                            class="w-full flex items-center justify-center gap-2 py-3 bg-green-600 hover:bg-green-700 text-white font-bold text-sm rounded-xl transition-colors shadow-sm">
                            Klaim Makanan Sekarang
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

@push('body-scripts')
<script>
    // Quantity adjustments
    const maxVal = {{ $food->remaining_quantity }};
    const input = document.getElementById('quantity_claimed');

    function incrementQuantity() {
        let current = parseInt(input.value) || 1;
        if (current < maxVal) {
            input.value = current + 1;
        }
    }

    function decrementQuantity() {
        let current = parseInt(input.value) || 1;
        if (current > 1) {
            input.value = current - 1;
        }
    }

    // Leaflet map initialization
    document.addEventListener('DOMContentLoaded', function() {
        const lat = {{ $food->latitude }};
        const lng = {{ $food->longitude }};
        
        const map = L.map('food-detail-map').setView([lat, lng], 15);
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; OpenStreetMap contributors'
        }).addTo(map);

        L.marker([lat, lng]).addTo(map)
            .bindPopup("<b>{{ $food->title }}</b><br>{{ $food->pickup_address }}")
            .openPopup();
    });
</script>
@endpush
