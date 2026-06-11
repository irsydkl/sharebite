@extends('layouts.app')

@section('title', 'Unggah Makanan Baru')

@section('content')
@include('components.sidebar-donatur')
<main class="flex-1 overflow-y-auto p-8 md:p-12 lg:p-16">
    <div class="max-w-3xl">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Unggah Makanan 🍲</h1>
            <p class="text-gray-600">Bagikan makanan berlebih berkualitas Anda agar dapat diselamatkan oleh penerima.</p>
        </header>

        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl">
                <p class="text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <form action="{{ route('donasi.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- Card: Detail Makanan -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200 shadow-sm space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-3">Detail Makanan</h3>
                
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700">Nama Makanan</label>
                    <input type="text" name="title" id="title" value="{{ old('title') }}" required
                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                        placeholder="Contoh: Nasi Goreng Box, Roti Bakar Cokelat">
                    <x-input-error :messages="$errors->get('title')" class="mt-1" />
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="category_id" class="block text-sm font-medium text-gray-700">Kategori</label>
                        <select name="category_id" id="category_id" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500 bg-white">
                            <option value="">-- Pilih Kategori --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('category_id')" class="mt-1" />
                    </div>

                    <div>
                        <label for="unit" class="block text-sm font-medium text-gray-700">Satuan Makanan</label>
                        <input type="text" name="unit" id="unit" value="{{ old('unit', 'porsi') }}" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                            placeholder="Contoh: box, porsi, pcs">
                        <x-input-error :messages="$errors->get('unit')" class="mt-1" />
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="quantity" class="block text-sm font-medium text-gray-700">Jumlah Porsi (Stok)</label>
                        <input type="number" name="quantity" id="quantity" value="{{ old('quantity') }}" min="1" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                        <x-input-error :messages="$errors->get('quantity')" class="mt-1" />
                    </div>

                    <div>
                        <label for="original_price" class="block text-sm font-medium text-gray-700">Harga per Porsi (Rp)</label>
                        <input type="number" name="original_price" id="original_price" value="{{ old('original_price', '0') }}" min="0" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                        <p class="mt-1 text-xs text-gray-500">*Akan dikenakan biaya layanan 10% di aplikasi.</p>
                        <x-input-error :messages="$errors->get('original_price')" class="mt-1" />
                    </div>
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700">Deskripsi & Catatan Tambahan</label>
                    <textarea name="description" id="description" rows="3"
                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                        placeholder="Tuliskan catatan seperti alergi, bahan baku, atau kondisi makanan... (opsional)">{{ old('description') }}</textarea>
                    <x-input-error :messages="$errors->get('description')" class="mt-1" />
                </div>
            </div>

            <!-- Card: Jadwal & Lokasi Pengambilan -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200 shadow-sm space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-3">Waktu & Lokasi Pengambilan</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="pickup_start" class="block text-sm font-medium text-gray-700">Mulai Pengambilan</label>
                        <input type="datetime-local" name="pickup_start" id="pickup_start" value="{{ old('pickup_start') }}" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                        <x-input-error :messages="$errors->get('pickup_start')" class="mt-1" />
                    </div>

                    <div>
                        <label for="pickup_end" class="block text-sm font-medium text-gray-700">Selesai Pengambilan</label>
                        <input type="datetime-local" name="pickup_end" id="pickup_end" value="{{ old('pickup_end') }}" required
                            class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500">
                        <x-input-error :messages="$errors->get('pickup_end')" class="mt-1" />
                    </div>
                </div>

                <div>
                    <label for="pickup_address" class="block text-sm font-medium text-gray-700">Alamat Lengkap Pengambilan</label>
                    <textarea name="pickup_address" id="pickup_address" rows="2" required
                        class="mt-1 block w-full rounded-lg border border-gray-300 px-3 py-2 shadow-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                        placeholder="Contoh: Jl. Diponegoro No. 4, depan Alfamart">{{ old('pickup_address', $profile->store_address ?? '') }}</textarea>
                    <x-input-error :messages="$errors->get('pickup_address')" class="mt-1" />
                </div>

                <div class="mt-4">
                    <x-map-picker
                        latName="latitude"
                        lngName="longitude"
                        label="Pilih Lokasi Pengambilan di Peta"
                        required="true" />
                </div>
            </div>

            <!-- Card: Foto Makanan -->
            <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200 shadow-sm space-y-4">
                <h3 class="text-lg font-semibold text-gray-900 border-b border-gray-100 pb-3">Foto Makanan</h3>
                
                <div>
                    <label for="images" class="block text-sm font-medium text-gray-700">Unggah Foto Makanan (Maks. 5 file, maks 2MB)</label>
                    <input type="file" name="images[]" id="images" multiple required accept="image/*"
                        class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 file:cursor-pointer hover:file:bg-green-100">
                    <p class="mt-2 text-xs text-gray-500">Gunakan foto asli beresolusi baik agar makanan cepat diselamatkan.</p>
                    <x-input-error :messages="$errors->get('images')" class="mt-1" />
                    <x-input-error :messages="$errors->get('images.*')" class="mt-1" />
                </div>
            </div>

            <!-- Submit Button -->
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('donatur.dashboard') }}" class="rounded-lg px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-100 transition-colors">
                    Batal
                </a>
                <button type="submit" class="rounded-lg bg-green-600 px-6 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 transition-colors">
                    Unggah Makanan
                </button>
            </div>
        </form>
    </div>
</main>
@endsection

@push('body-scripts')
<script>
    // Initialize map coordinate default based on donor profile if empty
    document.addEventListener('DOMContentLoaded', function() {
        const latInput = document.getElementById('latitude');
        const lngInput = document.getElementById('longitude');
        if (latInput && (!latInput.value || latInput.value === '')) {
            latInput.value = "{{ $profile->latitude ?? -6.2000000 }}";
            lngInput.value = "{{ $profile->longitude ?? 106.8166667 }}";
        }
    });
</script>
@endpush
