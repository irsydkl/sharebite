@extends('layouts.app')

@section('title', 'Penerimaan Klaim Makanan')

@section('content')
@include('components.sidebar-donatur')
<main class="flex-1 overflow-y-auto p-8 md:p-12 lg:p-16">
    <div class="max-w-5xl">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Penerimaan Klaim Makanan 📦</h1>
            <p class="text-gray-600">Lihat siapa saja yang telah mengklaim makanan Anda dan kelola status pengambilannya.</p>
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
                <h2 class="text-lg font-semibold text-gray-900">Daftar Pengajuan Klaim</h2>
                <span class="px-3 py-1 bg-gray-50 border border-gray-200 rounded-full text-xs font-medium text-gray-600">
                    Total: {{ $claims->count() }} Klaim
                </span>
            </div>

            @if($claims->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Belum Ada Klaim</h3>
                    <p class="text-gray-500">Makanan Anda belum diklaim oleh siapapun saat ini.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 text-xs font-semibold uppercase tracking-wider border-b border-gray-200">
                                <th class="p-4 pl-6">Makanan & Penerima</th>
                                <th class="p-4">Kode Booking</th>
                                <th class="p-4 text-center">Porsi</th>
                                <th class="p-4">Harga / Status</th>
                                <th class="p-4">Waktu Klaim</th>
                                <th class="p-4 pr-6 text-right">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @foreach($claims as $claim)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <!-- Food & Recipient -->
                                    <td class="p-4 pl-6">
                                        <div class="font-bold text-gray-900">{{ $claim->food->title }}</div>
                                        <div class="text-xs text-gray-500 mt-0.5">
                                            Oleh: <span class="font-medium">{{ $claim->user->name }}</span> ({{ $claim->user->phone ?? 'no telp' }})
                                        </div>
                                    </td>
                                    
                                    <!-- Booking Code -->
                                    <td class="p-4">
                                        <span class="font-mono bg-gray-50 border border-gray-200 px-2 py-1 rounded-md text-xs font-bold text-gray-800">
                                            {{ $claim->booking_code }}
                                        </span>
                                    </td>

                                    <!-- Quantity -->
                                    <td class="p-4 text-center font-semibold text-gray-900">
                                        {{ $claim->quantity_claimed }} {{ $claim->food->unit }}
                                    </td>

                                    <!-- Price & Status -->
                                    <td class="p-4">
                                        <div class="font-medium text-gray-900">Rp {{ number_format($claim->total_price, 0, ',', '.') }}</div>
                                        <div class="mt-1">
                                            @if($claim->claim_status === 'waiting_payment')
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-amber-50 text-amber-700 border border-amber-200">Menunggu Bayar</span>
                                            @elseif($claim->claim_status === 'paid')
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">Dibayar</span>
                                            @elseif($claim->claim_status === 'ready_pickup')
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-200">Siap Diambil</span>
                                            @elseif($claim->claim_status === 'picked_up')
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-green-50 text-green-700 border border-green-200">Sudah Diambil</span>
                                            @elseif($claim->claim_status === 'completed')
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 border border-green-200">Selesai</span>
                                            @elseif($claim->claim_status === 'expired')
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-red-50 text-red-700 border border-red-200">Kadaluwarsa</span>
                                            @else
                                                <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full text-xs font-medium bg-gray-50 text-gray-600 border border-gray-200">Dibatalkan</span>
                                            @endif
                                        </div>
                                    </td>

                                    <!-- Created At -->
                                    <td class="p-4 text-xs text-gray-500">
                                        {{ $claim->created_at->format('d M Y, H:i') }}
                                    </td>

                                    <!-- Actions -->
                                    <td class="p-4 pr-6 text-right">
                                        @if(in_array($claim->claim_status, ['paid', 'ready_pickup']))
                                            <!-- Button Trigger Modal Picked Up -->
                                            <button onclick="openModal('modal-{{ $claim->id }}')" 
                                                class="rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs px-3.5 py-1.5 transition-colors shadow-sm">
                                                Konfirmasi Ambil
                                            </button>

                                            <!-- Modal Konfirmasi Ambil -->
                                            <div id="modal-{{ $claim->id }}" class="hidden fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
                                                <div class="bg-white rounded-2xl border border-gray-200 max-w-md w-full p-6 text-left shadow-xl">
                                                    <h3 class="text-lg font-bold text-gray-900 mb-2">Konfirmasi Pengambilan</h3>
                                                    <p class="text-sm text-gray-500 mb-4">Pastikan kode booking <span class="font-mono font-bold text-gray-800">{{ $claim->booking_code }}</span> telah cocok dan makanan diserahkan.</p>
                                                    
                                                    <form action="{{ route('donatur.claims.update', $claim->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                                                        @csrf
                                                        <input type="hidden" name="status" value="picked_up">

                                                        <div>
                                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Foto Bukti Penyerahan (opsional)</label>
                                                            <input type="file" name="pickup_proof" accept="image/*"
                                                                class="block w-full text-xs text-gray-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-md file:border-0 file:bg-gray-100 file:text-gray-800 hover:file:bg-gray-200">
                                                        </div>

                                                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                                                            <button type="button" onclick="closeModal('modal-{{ $claim->id }}')"
                                                                class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                                                                Tutup
                                                            </button>
                                                            <button type="submit"
                                                                class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 rounded-lg text-sm font-semibold text-white transition-colors shadow-sm">
                                                                Konfirmasi
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        @endif

                                        @if($claim->claim_status === 'waiting_payment')
                                            <!-- Cancel Claim -->
                                            <form action="{{ route('donatur.claims.update', $claim->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin membatalkan klaim ini?')">
                                                @csrf
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit"
                                                    class="rounded-lg border border-red-200 text-red-600 hover:bg-red-50 font-semibold text-xs px-3.5 py-1.5 transition-colors">
                                                    Batalkan
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if(in_array($claim->claim_status, ['picked_up', 'completed']))
                                            <span class="text-xs text-green-600 font-semibold flex items-center justify-end gap-1">
                                                <i class="fa-solid fa-circle-check"></i> Selesai
                                            </span>
                                        @endif
                                        
                                        @if(in_array($claim->claim_status, ['expired', 'cancelled']))
                                            <span class="text-xs text-gray-400 font-semibold">
                                                Tidak Aktif
                                            </span>
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
