@extends('layouts.app')

@section('title', 'Verifikasi Donatur')

@section('content')
@include('components.sidebar-admin')
<main class="flex-1 overflow-y-auto p-8 md:p-12 lg:p-16">
    <div class="max-w-5xl">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Verifikasi Pendaftaran Donatur 🛡️</h1>
            <p class="text-gray-600">Tinjau profil toko donatur dan bukti lokasi fisik untuk memberikan persetujuan pendaftaran.</p>
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
                <h2 class="text-lg font-semibold text-gray-900">Menunggu Verifikasi</h2>
                <span class="px-3 py-1 bg-amber-50 border border-amber-200 rounded-full text-xs font-bold text-amber-700">
                    {{ $donaturs->count() }} Pendaftaran
                </span>
            </div>

            @if($donaturs->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                        <i class="fa-solid fa-user-check text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-bold text-gray-900 mb-1">Semua Bersih!</h3>
                    <p class="text-gray-500">Tidak ada pendaftaran donatur yang perlu diverifikasi saat ini.</p>
                </div>
            @else
                <div class="divide-y divide-gray-100">
                    @foreach($donaturs as $donorProfile)
                        <div class="p-6 space-y-4 hover:bg-gray-50/20 transition-colors">
                            <div class="flex flex-col md:flex-row md:items-start justify-between gap-4">
                                <!-- Donatur details -->
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-extrabold text-xl text-gray-900">{{ $donorProfile->store_name }}</h3>
                                        <span class="px-2 py-0.5 bg-amber-100 text-amber-800 text-[10px] font-bold uppercase rounded-md">Pending</span>
                                    </div>
                                    <p class="text-sm text-gray-600 font-medium">{{ $donorProfile->store_description ?? 'Tidak ada deskripsi.' }}</p>
                                    <div class="text-xs text-gray-500 space-y-1">
                                        <p>Pemilik: <span class="font-semibold text-gray-800">{{ $donorProfile->user->name }}</span> ({{ $donorProfile->user->email }})</p>
                                        <p>No. Telepon: <span class="font-semibold text-gray-850">{{ $donorProfile->user->phone ?? '-' }}</span></p>
                                        <p>Alamat Toko: <span class="font-semibold text-gray-850">{{ $donorProfile->store_address }}</span></p>
                                        <p class="font-mono text-gray-500">Koordinat: {{ $donorProfile->latitude }}, {{ $donorProfile->longitude }}</p>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <div class="flex items-center gap-2">
                                    <!-- Approve Button -->
                                    <button onclick="openModal('approve-donatur-{{ $donorProfile->id }}')" 
                                        class="rounded-lg bg-green-600 hover:bg-green-700 text-white font-bold text-xs px-4 py-2 transition-colors shadow-sm">
                                        Setujui Donatur
                                    </button>

                                    <!-- Reject Button -->
                                    <button onclick="openModal('reject-donatur-{{ $donorProfile->id }}')" 
                                        class="rounded-lg border border-red-200 text-red-650 hover:bg-red-50 font-bold text-xs px-4 py-2 transition-colors">
                                        Tolak
                                    </button>
                                </div>
                            </div>

                            <!-- Document Proofs -->
                            @if(!$donorProfile->locationProofs->isEmpty())
                                <div class="bg-gray-50 p-4 rounded-xl border border-gray-100 space-y-3">
                                    <p class="text-xs font-bold text-gray-500 uppercase tracking-wider">Dokumen Bukti Lokasi (Verifikasi Fisik)</p>
                                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                                        @foreach($donorProfile->locationProofs as $proof)
                                            <div class="space-y-1">
                                                <a href="{{ asset('storage/' . $proof->image) }}" target="_blank" class="block h-28 bg-gray-200 rounded-lg overflow-hidden border border-gray-300 hover:opacity-95 transition-opacity">
                                                    <img src="{{ asset('storage/' . $proof->image) }}" alt="Bukti Lokasi" class="w-full h-full object-cover">
                                                </a>
                                                @if($proof->notes)
                                                    <p class="text-[10px] text-gray-500 truncate italic">{{ $proof->notes }}</p>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <!-- Modal Approve -->
                            <div id="approve-donatur-{{ $donorProfile->id }}" class="hidden fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
                                <div class="bg-white rounded-2xl border border-gray-200 max-w-md w-full p-6 text-left shadow-xl">
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">Setujui Pendaftaran Donatur</h3>
                                    <p class="text-sm text-gray-500 mb-4">Apakah Anda yakin ingin menyetujui akun donatur untuk toko <span class="font-bold text-gray-800">{{ $donorProfile->store_name }}</span>?</p>
                                    
                                    <form action="{{ route('admin.verifikasi.donatur.process', $donorProfile->id) }}" method="POST" class="space-y-4">
                                        @csrf
                                        <input type="hidden" name="status" value="approved">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Catatan Tambahan (opsional)</label>
                                            <textarea name="notes" rows="2" class="block w-full text-sm rounded-lg border border-gray-300 px-3 py-2" placeholder="Catatan persetujuan..."></textarea>
                                        </div>
                                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                                            <button type="button" onclick="closeModal('approve-donatur-{{ $donorProfile->id }}')" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">Tutup</button>
                                            <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 rounded-lg text-sm font-semibold text-white shadow-sm">Setujui</button>
                                        </div>
                                    </form>
                                </div>
                            </div>

                            <!-- Modal Reject -->
                            <div id="reject-donatur-{{ $donorProfile->id }}" class="hidden fixed inset-0 z-50 bg-gray-900/50 backdrop-blur-sm flex items-center justify-center p-4">
                                <div class="bg-white rounded-2xl border border-gray-200 max-w-md w-full p-6 text-left shadow-xl">
                                    <h3 class="text-lg font-bold text-gray-900 mb-2 text-red-600">Tolak Pendaftaran Donatur</h3>
                                    <p class="text-sm text-gray-500 mb-4">Tuliskan alasan penolakan pendaftaran donatur <span class="font-bold text-gray-800">{{ $donorProfile->store_name }}</span> agar dapat diperbaiki oleh donatur.</p>
                                    
                                    <form action="{{ route('admin.verifikasi.donatur.process', $donorProfile->id) }}" method="POST" class="space-y-4">
                                        @csrf
                                        <input type="hidden" name="status" value="rejected">
                                        <div>
                                            <label class="block text-sm font-semibold text-gray-700 mb-1">Alasan Penolakan</label>
                                            <textarea name="notes" rows="3" required class="block w-full text-sm rounded-lg border border-gray-300 px-3 py-2 focus:border-red-500 focus:ring-red-550" placeholder="Alasan penolakan (misal: Bukti lokasi buram)..."></textarea>
                                        </div>
                                        <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                                            <button type="button" onclick="closeModal('reject-donatur-{{ $donorProfile->id }}')" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-semibold text-gray-700 hover:bg-gray-50">Tutup</button>
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
