@extends('layouts.app')

@section('title', 'Verifikasi Makanan')

@section('content')
@include('components.sidebar-admin')
<main class="flex-1 overflow-y-auto p-8 md:p-12 lg:p-16">
    <div class="max-w-5xl">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Verifikasi Unggahan Makanan 🍲</h1>
            <p class="text-gray-600">Tinjau kelayakan makanan berlebih yang diajukan oleh donatur sebelum ditampilkan di aplikasi.</p>
        </header>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl">
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl">
                <p class="text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Makanan Menunggu Persetujuan</h2>
                <span class="px-3 py-1 bg-amber-50 border border-amber-200 rounded-full text-xs font-bold text-amber-700">
                    {{ $foods->count() }} Unggahan
                </span>
            </div>

            @if($foods->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                        <i class="fa-solid fa-clipboard-check text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Semua Bersih!</h3>
                    <p class="text-gray-500">Tidak ada makanan yang menunggu verifikasi saat ini.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($foods as $food)
                        <div class="p-6 space-y-4 hover:bg-gray-50/20 transition-colors">
                            <div class="flex flex-col lg:flex-row gap-6">
                                <!-- Food Images (first image or gallery) -->
                                <div class="w-full lg:w-48 shrink-0 space-y-2">
                                    <div class="h-32 bg-gray-100 rounded-xl border border-gray-200 overflow-hidden">
                                        @if($food->images->isEmpty())
                                            <div class="w-full h-full flex items-center justify-center text-gray-400">
                                                <i class="fa-solid fa-image"></i>
                                            </div>
                                        @else
                                            <img src="{{ asset('storage/' . $food->images->first()->image) }}" alt="Foto Makanan" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    @if($food->images->count() > 1)
                                        <div class="grid grid-cols-4 gap-1.5">
                                            @foreach($food->images->skip(1) as $img)
                                                <div class="h-8 bg-gray-100 border border-gray-200 rounded overflow-hidden">
                                                    <img src="{{ asset('storage/' . $img->image) }}" class="w-full h-full object-cover">
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>

                                <!-- Food Info -->
                                <div class="flex-1 flex flex-col justify-between gap-4">
                                    <div class="space-y-2">
                                        <div class="flex flex-wrap items-center gap-2">
                                            <h3 class="font-extrabold text-lg text-gray-900 leading-snug">{{ $food->title }}</h3>
                                            <span class="px-2.5 py-0.5 bg-green-50 text-green-700 text-[10px] font-bold border border-green-200 rounded-full">
                                                {{ $food->category->name }}
                                            </span>
                                        </div>
                                        <p class="text-xs text-gray-500 font-medium">
                                            Donatur: <span class="font-bold text-gray-700">{{ $food->donor->donorProfile->store_name }}</span> (Pemilik: {{ $food->donor->name }})
                                        </p>
                                        <p class="text-sm text-gray-650 leading-relaxed">{{ $food->description ?? 'Tidak ada deskripsi.' }}</p>
                                        
                                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 pt-2 text-xs">
                                            <div>
                                                <p class="text-gray-400 uppercase tracking-wider font-semibold">Stok Porsi</p>
                                                <p class="font-bold text-gray-900">{{ $food->quantity }} {{ $food->unit }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-400 uppercase tracking-wider font-semibold">Harga Asli</p>
                                                <p class="font-bold text-gray-950">Rp {{ number_format($food->original_price, 0, ',', '.') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-400 uppercase tracking-wider font-semibold">Harga Final</p>
                                                <p class="font-bold text-indigo-600">Rp {{ number_format($food->final_price, 0, ',', '.') }}</p>
                                            </div>
                                            <div>
                                                <p class="text-gray-400 uppercase tracking-wider font-semibold">Batas Ambil</p>
                                                <p class="font-bold text-gray-950">{{ $food->pickup_deadline->format('H:i') }} ({{ $food->pickup_deadline->format('d M') }})</p>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center justify-end gap-2 border-t border-gray-100 pt-3">
                                        <!-- Approve button -->
                                        <button onclick="openModal('approve-food-{{ $food->id }}')"
                                            class="rounded-lg bg-green-600 hover:bg-green-700 text-white font-bold text-xs px-4 py-2 transition-colors shadow-sm">
                                            Setujui Makanan
                                        </button>
                                        <!-- Reject button -->
                                        <button onclick="openModal('reject-food-{{ $food->id }}')"
                                            class="rounded-lg border border-red-200 text-red-600 hover:bg-red-50 font-bold text-xs px-4 py-2 transition-colors">
                                            Tolak
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Modal Approve -->
                            <div id="approve-food-{{ $food->id }}" class="hidden fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
                                <div class="bg-white rounded-2xl border border-gray-200 max-w-md w-full p-6 text-left shadow-xl">
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">Setujui Unggahan Makanan</h3>
                                    <p class="text-sm text-gray-500 mb-4">Apakah Anda yakin ingin menyetujui makanan <span class="font-bold text-gray-800">{{ $food->title }}</span>? Makanan akan langsung tayang di beranda penerima.</p>
                                    
                                    <form action="{{ route('admin.verifikasi.food.process', $food->id) }}" method="POST" class="space-y-4">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan Tambahan (opsional)</label>
                                            <textarea name="notes" rows="2" class="block w-full text-sm rounded-lg border border-gray-300 px-3 py-2" placeholder="Catatan persetujuan..."></textarea>
                                        </div>
                                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                                            <button type="button" onclick="closeModal('approve-food-{{ $food->id }}')" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">Tutup</button>
                                            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg text-sm font-semibold text-white shadow-sm">Setujui</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Modal Reject -->
                            <div id="reject-food-{{ $food->id }}" class="hidden fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
                                <div class="bg-white rounded-2xl border border-gray-200 max-w-md w-full p-6 text-left shadow-xl">
                                    <h3 class="text-lg font-bold text-gray-900 mb-2 text-red-600">Tolak Unggahan Makanan</h3>
                                    <p class="text-sm text-gray-500 mb-4">Tuliskan alasan penolakan agar donatur dapat memperbaikinya.</p>
                                    
                                    <form action="{{ route('admin.verifikasi.food.process', $food->id) }}" method="POST" class="space-y-4">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Alasan Penolakan</label>
                                            <textarea name="notes" rows="3" required class="block w-full text-sm rounded-lg border border-gray-300 px-3 py-2 focus:border-red-500 focus:ring-red-550" placeholder="Alasan penolakan (misal: deskripsi kurang jelas/foto melanggar)..."></textarea>
                                        </div>
                                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                                            <button type="button" onclick="closeModal('reject-food-{{ $food->id }}')" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">Tutup</button>
                                            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 rounded-lg text-sm font-semibold text-white shadow-sm">Tolak</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</main>

<script>
    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }
    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }
</script>
@endsection
