@extends('layouts.app')

@section('title', 'Pembayaran Berhasil')

@section('content')
@include('components.sidebar-user')

<main class="flex-1 overflow-y-auto p-8 md:p-12 lg:p-16">
    <div class="max-w-2xl mx-auto">
        {{-- Success Card --}}
        <div class="bg-white rounded-3xl border border-gray-100 shadow-xl overflow-hidden mb-8 transition-all hover:shadow-2xl">
            {{-- Header/Green Accent --}}
            <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-8 py-12 text-center text-white relative">
                {{-- Decorative background shapes --}}
                <div class="absolute inset-0 bg-repeat opacity-10" style="background-image: url('data:image/svg+xml,%3Csvg width=\"30\" height=\"30\" viewBox=\"0 0 30 30\" xmlns=\"http://www.w3.org/2000/svg\"%3E%3Cpath d=\"M15 0L30 15L15 30L0 15Z\" fill=\"%23ffffff\" fill-rule=\"evenodd\"/%3E%3C/svg%3E');"></div>
                
                {{-- Pulsing Success Checkmark --}}
                <div class="relative z-10 inline-flex items-center justify-center w-20 h-20 rounded-full bg-white/20 backdrop-blur-md mb-4 shadow-inner animate-bounce">
                    <div class="w-16 h-16 rounded-full bg-white flex items-center justify-center text-emerald-600 shadow">
                        <i class="fa-solid fa-check text-3xl"></i>
                    </div>
                </div>
                
                <h1 class="relative z-10 text-3xl font-extrabold tracking-tight mb-2">Pembayaran Berhasil! 🎉</h1>
                <p class="relative z-10 text-emerald-100 text-sm font-medium">Terima kasih atas kontribusi Anda. Pembayaran Anda telah terkonfirmasi.</p>
            </div>

            {{-- Detail Content --}}
            <div class="p-8 space-y-6">
                {{-- Summary Status / Flash message --}}
                @if(session('info'))
                    <div class="bg-amber-50 border-l-4 border-amber-500 p-4 rounded-r-xl flex items-center gap-3">
                        <i class="fa-solid fa-circle-info text-amber-600 text-lg"></i>
                        <p class="text-amber-800 text-sm font-medium">{{ session('info') }}</p>
                    </div>
                @endif

                {{-- Receipt Box --}}
                <div class="bg-gray-50 border border-gray-100 rounded-2xl p-6 space-y-4">
                    <div class="flex items-center justify-between border-b border-gray-200 pb-3">
                        <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Rincian Transaksi</span>
                        <span class="font-mono text-xs font-bold bg-emerald-50 border border-emerald-200 text-emerald-700 px-2 py-1 rounded-md">
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
                            <span>Metode Pembayaran</span>
                            <span class="font-semibold text-gray-700 uppercase">
                                {{ $claim->payment?->payment_method ?? 'QRIS/Transfer' }}
                            </span>
                        </div>

                        @if($claim->payment?->transaction_reference)
                            <div class="flex justify-between items-center text-xs text-gray-500">
                                <span>Referensi Transaksi</span>
                                <span class="font-mono text-gray-600">{{ $claim->payment->transaction_reference }}</span>
                            </div>
                        @endif

                        @if($claim->payment?->paid_at)
                            <div class="flex justify-between items-center text-xs text-gray-500">
                                <span>Waktu Pembayaran</span>
                                <span>{{ $claim->payment->paid_at->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB</span>
                            </div>
                        @endif

                        <div class="flex justify-between items-center pt-3 border-t border-gray-200">
                            <span class="font-bold text-gray-900">Total Pembayaran</span>
                            <span class="font-extrabold text-teal-600 text-lg">
                                Rp {{ number_format($claim->total_price, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                </div>

                {{-- Action Steps --}}
                <div class="space-y-4">
                    <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                        <i class="fa-solid fa-list-check text-teal-500"></i> Langkah Selanjutnya
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="p-4 bg-emerald-50/50 border border-emerald-50 rounded-xl space-y-2 text-center md:text-left">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm mx-auto md:mx-0">1</div>
                            <h5 class="text-xs font-bold text-gray-800">Cek Status</h5>
                            <p class="text-[11px] text-gray-600 leading-relaxed">Pantau kesiapan pengambilan makanan di halaman Riwayat Klaim.</p>
                        </div>
                        <div class="p-4 bg-emerald-50/50 border border-emerald-50 rounded-xl space-y-2 text-center md:text-left">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm mx-auto md:mx-0">2</div>
                            <h5 class="text-xs font-bold text-gray-800">Ambil Makanan</h5>
                            <p class="text-[11px] text-gray-600 leading-relaxed">Kunjungi alamat donatur sebelum batas waktu pengambilan habis.</p>
                        </div>
                        <div class="p-4 bg-emerald-50/50 border border-emerald-50 rounded-xl space-y-2 text-center md:text-left">
                            <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold text-sm mx-auto md:mx-0">3</div>
                            <h5 class="text-xs font-bold text-gray-800">Beri Ulasan</h5>
                            <p class="text-[11px] text-gray-600 leading-relaxed">Berikan rating dan ulasan setelah makanan berhasil diambil.</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer Buttons --}}
            <div class="bg-gray-50 px-8 py-6 flex flex-col sm:flex-row gap-3 border-t border-gray-100">
                <a href="{{ route('riwayat.index') }}"
                    class="flex-1 flex items-center justify-center gap-2 py-3 bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl transition-all shadow-md hover:shadow-lg">
                    <i class="fa-solid fa-receipt"></i> Lihat Riwayat Klaim
                </a>
                <a href="{{ route('user.dashboard') }}"
                    class="flex-1 flex items-center justify-center gap-2 py-3 bg-white hover:bg-gray-100 border border-gray-300 text-gray-700 font-bold rounded-xl transition-all shadow-sm">
                    <i class="fa-solid fa-house"></i> Kembali ke Beranda
                </a>
            </div>
        </div>
    </div>
</main>
@endsection
