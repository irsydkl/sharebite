@extends('layouts.app')

@section('title', 'Pembayaran Gagal / Dibatalkan')

@section('content')
@include('components.sidebar-user')

<main class="flex-1 overflow-y-auto p-8 md:p-12 lg:p-16">
    <div class="max-w-2xl mx-auto">
        {{-- Failed Card --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden mb-8 transition-all hover:shadow-2xl">
            {{-- Header/Red Accent --}}
            <div class="bg-gradient-to-r from-rose-500 to-red-600 px-8 py-12 text-center text-white relative">
                {{-- Decorative background shapes --}}
                <div class="absolute inset-0 bg-repeat opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\"30\" height=\"30\" viewBox=\"0 0 30 30\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M15 0L30 15L15 30L0 15Z\" fill=\"%23ffffff\" fill-rule=\"evenodd\"/%3E%3C/svg%3E');"></div>
                
                {{-- Shaking/Pulsing Failed Cross --}}
                <div class="relative z-10 inline-flex items-center justify-center w-20 h-20 rounded-full bg-white/20 backdrop-blur-md mb-4 shadow-inner">
                    <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center text-rose-600 shadow animate-pulse">
                        <i class="fa-solid fa-xmark text-3xl"></i>
                    </div>
                </div>
                
                <h1 class="relative z-10 text-3xl font-extrabold tracking-tight mb-2">Pembayaran Gagal atau Dibatalkan ❌</h1>
                <p class="relative z-10 text-rose-100 text-sm font-medium">Transaksi dibatalkan atau waktu pembayaran Anda telah berakhir.</p>
            </div>

            {{-- Detail Content --}}
            <div class="p-8 space-y-6">
                {{-- Error explanations --}}
                <div class="bg-rose-50 border-l-4 border-rose-500 p-4 rounded-r-xl space-y-1">
                    <h4 class="text-rose-800 text-sm font-bold">Kenapa hal ini bisa terjadi?</h4>
                    <ul class="text-rose-700 text-xs list-disc list-inside space-y-0.5">
                        <li>Anda menutup jendela pembayaran sebelum menyelesaikan transaksi.</li>
                        <li>Batas waktu pembayaran (5 menit) telah habis.</li>
                        <li>Terjadi masalah dengan jaringan internet atau penyedia pembayaran Anda.</li>
                    </ul>
                </div>

                {{-- Receipt Box (Details) --}}
                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-200 pb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Detail Klaim</span>
                        <span class="font-mono text-xs font-bold bg-rose-50 border border-rose-200 text-rose-700 px-2 py-1 rounded-md">
                            {{ $claim->booking_code }}
                        </span>
                    </div>

                    <div class="space-y-3">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-bold text-gray-800 text-sm">{{ $claim->food->title }}</h4>
                                <p class="text-xs text-gray-500">
                                    {{ $claim->quantity_claimed }} {{ $claim->food->unit }} · Oleh {{ $claim->food->donor->donorProfile->store_name ?? $claim->food->donor->name }}
                                </p>
                            </div>
                            <span class="font-bold text-gray-700 text-sm">
                                Rp {{ number_format($claim->subtotal_price, 0, ',', '.') }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center text-xs text-gray-500">
                            <span>Biaya Layanan (10%)</span>
                            <span>Rp {{ number_format($claim->service_fee, 0, ',', '.') }}</span>
                        </div>

                        <div class="flex justify-between items-center text-xs text-gray-500">
                            <span>Status Klaim saat ini</span>
                            <span class="font-bold text-xs px-2.5 py-0.5 rounded-full {{ $claim->claim_status === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800' }}">
                                {{ $claim->claim_status === 'cancelled' ? 'Klaim Dibatalkan' : 'Menunggu Pembayaran' }}
                            </span>
                        </div>

                        <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                            <span class="font-bold text-gray-900">Total Pembayaran</span>
                            <span class="font-extrabold text-rose-600 text-lg">
                                Rp {{ number_format($claim->total_price, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Buttons --}}
            <div class="bg-gray-50 px-8 py-6 flex flex-col sm:flex-row gap-3 border-t border-gray-100">
                @if($claim->claim_status === 'waiting_payment')
                    <a href="{{ route('user.claims.payment', $claim->id) }}"
                        class="flex-1 flex items-center justify-center gap-2 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl transition-all shadow-md hover:shadow-lg">
                        <i class="fa-solid fa-redo"></i> Coba Bayar Lagi
                    </a>
                @endif
                <a href="{{ route('user.dashboard') }}"
                    class="flex-1 flex items-center justify-center gap-2 py-3 bg-white hover:bg-gray-100 border border-gray-300 text-gray-700 font-bold rounded-xl transition-all shadow-sm">
                    <i class="fa-solid fa-house"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</main>
@endsection
