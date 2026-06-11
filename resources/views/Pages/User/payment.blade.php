@extends('layouts.app')

@section('title', 'Selesaikan Pembayaran')

@section('content')
@include('components.sidebar-user')

<main class="flex-1 overflow-y-auto p-8 md:p-12 lg:p-16">
    <div class="max-w-2xl">
        <a href="{{ route('riwayat.index') }}"
            class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 font-semibold mb-6 transition-colors">
            <i class="fa-solid fa-arrow-left"></i> Lihat Riwayat
        </a>

        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl">
                <p class="text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Selesaikan Pembayaran 💳</h1>
            <p class="text-gray-600">Klik tombol bayar di bawah untuk membuka jendela pembayaran Midtrans.</p>
        </header>

        {{-- Countdown Timer --}}
        <div class="mb-6 bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center gap-3">
            <i class="fa-solid fa-clock text-amber-600 text-xl"></i>
            <div>
                <p class="text-sm font-semibold text-amber-800">Selesaikan pembayaran sebelum waktu habis</p>
                <p class="text-xs text-amber-700">
                    Batas waktu:
                    <strong>{{ $claim->payment_expired_at->format('H:i') }}
                    ({{ $claim->payment_expired_at->format('d M Y') }})</strong>
                </p>
            </div>
            <div class="ml-auto">
                <span id="countdown"
                    class="text-xl font-mono font-bold text-amber-700"
                    data-deadline="{{ $claim->payment_expired_at->toISOString() }}">
                    --:--
                </span>
            </div>
        </div>

        {{-- Invoice --}}
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200 shadow-sm space-y-4 mb-6">
            <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                <h3 class="text-lg font-bold text-gray-900">Rincian Tagihan</h3>
                <span class="font-mono text-xs font-bold bg-gray-50 border border-gray-200 px-2 py-1 rounded text-gray-700">
                    {{ $claim->booking_code }}
                </span>
            </div>

            <div class="space-y-3">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="font-bold text-gray-900">{{ $claim->food->title }}</p>
                        <p class="text-xs text-gray-500">
                            {{ $claim->quantity_claimed }} {{ $claim->food->unit }}
                            dari {{ $claim->food->donor->donorProfile->store_name ?? $claim->food->donor->name }}
                        </p>
                    </div>
                    <span class="font-semibold text-gray-800">
                        Rp {{ number_format($claim->subtotal_price, 0, ',', '.') }}
                    </span>
                </div>

                <div class="flex items-center justify-between text-sm text-gray-600">
                    <span>Biaya Layanan (10%)</span>
                    <span>Rp {{ number_format($claim->service_fee, 0, ',', '.') }}</span>
                </div>

                <div class="flex items-center justify-between pt-3 border-t border-gray-100">
                    <span class="font-extrabold text-gray-900 text-lg">Total Pembayaran</span>
                    <span class="font-extrabold text-indigo-600 text-2xl">
                        Rp {{ number_format($claim->total_price, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Pay Button --}}
        <div class="bg-white p-6 sm:p-8 rounded-2xl border border-gray-200 shadow-sm">
            <h3 class="text-base font-bold text-gray-900 mb-4">Pembayaran via Midtrans</h3>

            <p class="text-sm text-gray-500 mb-6">
                Klik tombol di bawah untuk membuka jendela pembayaran. Anda dapat memilih metode pembayaran
                yang tersedia (QRIS, Virtual Account, E-Wallet, dan lainnya).
            </p>

            @if($snapToken)
                {{-- Main Pay Button --}}
                <button id="pay-button"
                    data-snap-token="{{ $snapToken }}"
                    data-return-url="{{ route('user.claims.payment.return', $claim->id) }}"
                    class="w-full flex items-center justify-center gap-3 py-4 bg-indigo-600 hover:bg-indigo-700
                        text-white font-bold text-base rounded-xl transition-all shadow-md hover:shadow-lg
                        active:scale-[0.98]">
                    <i class="fa-solid fa-credit-card text-lg"></i>
                    Bayar Sekarang — Rp {{ number_format($claim->total_price, 0, ',', '.') }}
                </button>

                <p class="text-center text-xs text-gray-400 mt-3">
                    <i class="fa-solid fa-shield-check mr-1 text-green-500"></i>
                    Transaksi aman &amp; terenkripsi · Powered by Midtrans
                </p>
            @else
                <div class="text-center py-8 text-gray-500">
                    <i class="fa-solid fa-triangle-exclamation text-3xl text-amber-400 mb-3"></i>
                    <p class="font-medium">Gagal memuat payment gateway.</p>
                    <p class="text-sm mt-1">Silakan refresh halaman atau hubungi admin.</p>
                    <a href="{{ route('user.claims.payment', $claim->id) }}"
                        class="mt-4 inline-block text-sm text-indigo-600 underline">Coba lagi</a>
                </div>
            @endif
        </div>
    </div>
</main>

{{-- Midtrans Snap.js --}}
@if($snapToken)
<script src="https://app.sandbox.midtrans.com/snap/snap.js"
    data-client-key="{{ $clientKey }}"></script>
<script>
    const payBtn     = document.getElementById('pay-button');
    const snapToken  = payBtn.dataset.snapToken;
    const returnUrl  = payBtn.dataset.returnUrl;
    const countdown  = document.getElementById('countdown');
    const deadline   = new Date(countdown.dataset.deadline);

    // ── Countdown ──────────────────────────────────────────────────
    function updateCountdown() {
        const diff = deadline - Date.now();
        if (diff <= 0) {
            countdown.textContent = '00:00';
            countdown.classList.add('text-red-600');
            return;
        }
        const m = String(Math.floor(diff / 60000)).padStart(2, '0');
        const s = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');
        countdown.textContent = `${m}:${s}`;
        if (diff < 60000) countdown.classList.add('text-red-600');
    }
    updateCountdown();
    setInterval(updateCountdown, 1000);

    // ── Midtrans Snap ──────────────────────────────────────────────
    payBtn.addEventListener('click', function () {
        snap.pay(snapToken, {
            onSuccess: function (result) {
                window.location.href = returnUrl;
            },
            onPending: function (result) {
                window.location.href = returnUrl;
            },
            onError: function (result) {
                window.location.href = returnUrl;
            },
            onClose: function () {
                // User closed the popup — stay on page
                console.log('Payment popup closed.');
            }
        });
    });
</script>
@endif
@endsection
