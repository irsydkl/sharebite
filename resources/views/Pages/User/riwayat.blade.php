@extends('layouts.app')

@section('title', 'Riwayat Klaim Makanan')

@section('content')
@include('components.sidebar-user')
<main class="flex-1 overflow-y-auto p-8 md:p-12 lg:p-16">
    <div class="max-w-5xl">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Riwayat Klaim Makanan 📝</h1>
            <p class="text-gray-600">Pantau seluruh klaim makanan penyelamatan Anda, kode booking pengambilan, dan statusnya.</p>
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
            <div class="p-6 border-b border-gray-100">
                <h2 class="text-lg font-semibold text-gray-900">Klaim Saya</h2>
            </div>

            @if($claims->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                        <i class="fa-solid fa-receipt text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Klaim Makanan</h3>
                    <p class="text-gray-500 mb-4">Anda belum pernah melakukan klaim makanan penyelamatan.</p>
                    <a href="{{ route('user.dashboard') }}" class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-5 py-2 text-sm font-bold text-white shadow-sm hover:bg-green-700 transition-colors">
                        Mulai Cari Makanan
                    </a>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 text-xs font-semibold uppercase tracking-wider border-b border-gray-200">
                                <th class="p-4 pl-6">Makanan & Toko Donatur</th>
                                <th class="p-4">Kode Booking</th>
                                <th class="p-4 text-center">Kuantitas</th>
                                <th class="p-4">Total Harga</th>
                                <th class="p-4">Status</th>
                                <th class="p-4 pr-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @foreach($claims as $claim)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <!-- Food & Store -->
                                    <td class="p-4 pl-6">
                                        <div class="font-bold text-gray-900">{{ $claim->food->title }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            Toko: <span class="font-medium text-gray-700">{{ $claim->food->donor->donorProfile->store_name ?? 'Donatur' }}</span>
                                        </div>
                                    </td>

                                    <!-- Booking Code -->
                                    <td class="p-4">
                                        <span class="font-mono bg-gray-50 border border-gray-200 px-2 py-1 rounded text-xs font-bold text-gray-850">
                                            {{ $claim->booking_code }}
                                        </span>
                                    </td>

                                    <!-- Quantity -->
                                    <td class="p-4 text-center font-medium">
                                        {{ $claim->quantity_claimed }} {{ $claim->food->unit }}
                                    </td>

                                    <!-- Total Price -->
                                    <td class="p-4 font-semibold text-gray-900">
                                        Rp {{ number_format($claim->total_price, 0, ',', '.') }}
                                    </td>

                                    <!-- Status -->
                                    <td class="p-4">
                                        @if($claim->claim_status === 'waiting_payment')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-50 text-amber-700 border border-amber-200">Menunggu Bayar</span>
                                        @elseif($claim->claim_status === 'paid')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-blue-50 text-blue-700 border border-blue-200">Dibayar</span>
                                        @elseif($claim->claim_status === 'ready_pickup')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 border border-indigo-200">Siap Diambil</span>
                                        @elseif($claim->claim_status === 'picked_up')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-200">Sudah Diambil</span>
                                        @elseif($claim->claim_status === 'completed')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-800 border border-green-200">Selesai</span>
                                        @elseif($claim->claim_status === 'expired')
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-red-50 text-red-700 border border-red-200">Kadaluwarsa</span>
                                        @else
                                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-gray-50 text-gray-650 border border-gray-200">Dibatalkan</span>
                                        @endif
                                    </td>

                                    <!-- Actions -->
                                    <td class="p-4 pr-6 text-right">
                                        @if($claim->claim_status === 'waiting_payment')
                                            <a href="{{ route('user.claims.payment', $claim->id) }}"
                                                class="rounded-lg bg-amber-500 hover:bg-amber-600 text-white font-bold text-xs px-3.5 py-1.5 transition-colors shadow-sm">
                                                Bayar Sekarang
                                            </a>
                                        @elseif($claim->claim_status === 'ready_pickup')
                                            <div class="text-xs text-indigo-600 font-semibold bg-indigo-50 border border-indigo-200 px-2 py-1 rounded inline-block">
                                                Tunjukkan Kode Booking saat ambil
                                            </div>
                                        @elseif(in_array($claim->claim_status, ['picked_up', 'completed']))
                                            @if(!$claim->rating)
                                                <!-- Review trigger button -->
                                                <button onclick="openModal('rate-{{ $claim->id }}')"
                                                    class="rounded-lg bg-green-600 hover:bg-green-700 text-white font-bold text-xs px-3.5 py-1.5 transition-colors shadow-sm">
                                                    Beri Ulasan
                                                </button>

                                                <!-- Review Modal -->
                                                <div id="rate-{{ $claim->id }}" class="hidden fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
                                                    <div class="bg-white rounded-2xl border border-gray-200 max-w-md w-full p-6 text-left shadow-xl">
                                                        <h3 class="text-lg font-bold text-gray-900 mb-2">Beri Ulasan Makanan</h3>
                                                        <p class="text-xs text-gray-500 mb-4">Bagikan pengalaman Anda menyelamatkan makanan ini untuk membantu pengguna lain.</p>
                                                        
                                                        <form action="{{ route('user.claims.rate', $claim->id) }}" method="POST" class="space-y-4">
                                                            @csrf
                                                            <div>
                                                                <label class="block text-sm font-semibold text-gray-700 mb-1">Rating</label>
                                                                <div class="flex items-center gap-1.5 text-amber-400 text-2xl">
                                                                    <select name="rating" required class="block w-full text-sm rounded-lg border border-gray-300 px-3 py-2 bg-white">
                                                                        <option value="5">⭐⭐⭐⭐⭐ (5 - Sangat Bagus)</option>
                                                                        <option value="4">⭐⭐⭐⭐ (4 - Bagus)</option>
                                                                        <option value="3">⭐⭐⭐ (3 - Cukup)</option>
                                                                        <option value="2">⭐⭐ (2 - Kurang)</option>
                                                                        <option value="1">⭐ (1 - Buruk)</option>
                                                                    </select>
                                                                </div>
                                                            </div>

                                                            <div>
                                                                <label class="block text-sm font-semibold text-gray-700 mb-1">Ulasan</label>
                                                                <textarea name="review" rows="3" class="block w-full text-sm rounded-lg border border-gray-300 px-3 py-2 focus:border-green-500 focus:outline-none"
                                                                    placeholder="Rasa makanan, keramahan donatur, dll... (opsional)"></textarea>
                                                            </div>

                                                            <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                                                                <button type="button" onclick="closeModal('rate-{{ $claim->id }}')"
                                                                    class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                                                                    Tutup
                                                                </button>
                                                                <button type="submit"
                                                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg text-sm font-semibold text-white transition-colors shadow-sm">
                                                                    Kirim Ulasan
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="flex items-center justify-end gap-1 text-xs text-amber-500 font-bold">
                                                    <i class="fa-solid fa-star"></i> {{ $claim->rating->rating }} / 5 (Telah diulas)
                                                </div>
                                            @endif
                                        @else
                                            <span class="text-xs text-gray-400 font-semibold">Tidak Ada Aksi</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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
