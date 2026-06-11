@extends('layouts.app')

@section('title', 'Riwayat Pencairan Dana')

@section('content')
@include('components.sidebar-donatur')
<main class="flex-1 overflow-y-auto p-8 md:p-12 lg:p-16">
    <div class="max-w-5xl">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Pencairan Dana (Payouts) 💰</h1>
            <p class="text-gray-600">Pantau seluruh dana hasil penjualan makanan Anda yang telah dicairkan oleh sistem.</p>
        </header>

        <!-- Balance display card -->
        <div class="bg-gradient-to-r from-green-700 to-green-600 p-6 sm:p-8 rounded-3xl text-white shadow-md border border-green-800 mb-8 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <p class="text-green-100 text-sm font-medium uppercase tracking-wider mb-1">Saldo Tersedia ShareBite</p>
                <h2 class="text-4xl font-extrabold">Rp {{ number_format(Auth::user()->balance, 0, ',', '.') }}</h2>
            </div>
            <div class="text-xs text-green-50 bg-green-800/40 border border-green-500/30 px-4 py-3 rounded-2xl max-w-sm">
                Dana yang masuk dari transaksi penjualan makanan akan otomatis diproses pencairannya oleh Admin ke saldo Anda.
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Riwayat Transaksi Pencairan</h2>
                <span class="px-3 py-1 bg-gray-50 border border-gray-200 rounded-full text-xs font-medium text-gray-600">
                    Total: {{ $payouts->count() }} Pencairan
                </span>
            </div>

            @if($payouts->isEmpty())
                <div class="p-12 text-center">
                    <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                        <i class="fa-solid fa-money-bill-transfer text-2xl text-gray-400"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-1">Belum Ada Pencairan</h3>
                    <p class="text-gray-500">Saldo Anda masih kosong atau belum ada pencairan dana yang diselesaikan oleh Admin.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 text-xs font-semibold uppercase tracking-wider border-b border-gray-200">
                                <th class="p-4 pl-6">Makanan</th>
                                <th class="p-4">Kode Booking</th>
                                <th class="p-4">Jumlah Dicairkan</th>
                                <th class="p-4">Metode Asal</th>
                                <th class="p-4">Waktu Pencairan</th>
                                <th class="p-4 pr-6">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                            @foreach($payouts as $payout)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="p-4 pl-6">
                                        <div class="font-semibold text-gray-900">{{ $payout->payment->claim->food->title }}</div>
                                        <div class="text-xs text-gray-500">Penerima: {{ $payout->payment->claim->user->name }}</div>
                                    </td>
                                    <td class="p-4 font-mono text-xs font-bold text-gray-700">
                                        {{ $payout->payment->claim->booking_code }}
                                    </td>
                                    <td class="p-4 font-bold text-green-600 text-base">
                                        + Rp {{ number_format($payout->amount, 0, ',', '.') }}
                                    </td>
                                    <td class="p-4 uppercase text-xs text-gray-600 font-medium">
                                        {{ str_replace('_', ' ', $payout->payment->payment_method) }}
                                    </td>
                                    <td class="p-4 text-xs text-gray-500">
                                        {{ $payout->sent_at ? $payout->sent_at->format('d M Y, H:i') : $payout->created_at->format('d M Y, H:i') }}
                                    </td>
                                    <td class="p-4 pr-6">
                                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-200">
                                            Berhasil
                                        </span>
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
@endsection
