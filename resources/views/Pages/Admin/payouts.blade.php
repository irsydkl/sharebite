@extends('layouts.app')

@section('title', 'Kelola Payouts')

@section('content')
@include('components.sidebar-admin')
<main class="flex-1 overflow-y-auto p-8 md:p-12 lg:p-16">
    <div class="max-w-5xl space-y-8">
        <header>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Pencairan Saldo Donatur (Payouts) 💸</h1>
            <p class="text-gray-600">Saldo escrow sudah masuk ke akun donatur saat pickup selesai. Admin mencairkan dana ke rekening donatur.</p>
        </header>

        @if(session('success'))
            <div class="bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl">
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl">
                <p class="text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <div class="grid grid-cols-1 gap-8">
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Antrean Pencairan Dana</h2>
                    <span class="px-3 py-1 bg-amber-50 border border-amber-200 rounded-full text-xs font-bold text-amber-700">
                        {{ $pendingPayouts->count() }} Payout
                    </span>
                </div>

                @if($pendingPayouts->isEmpty())
                    <div class="p-12 text-center text-gray-500">
                        <i class="fa-solid fa-circle-check text-2xl text-green-500 mb-3"></i>
                        <p class="font-medium">Tidak ada antrean pencairan dana saat ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500 text-xs font-semibold uppercase tracking-wider border-b border-gray-200">
                                    <th class="p-4 pl-6">Donatur / Toko</th>
                                    <th class="p-4">Kode Booking</th>
                                    <th class="p-4">Saldo Donatur</th>
                                    <th class="p-4">Dana Dicairkan</th>
                                    <th class="p-4 pr-6 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm text-gray-750">
                                @foreach($pendingPayouts as $payout)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="p-4 pl-6">
                                            <div class="font-bold text-gray-900">{{ $payout->donor->donorProfile->store_name ?? $payout->donor->name }}</div>
                                            <div class="text-xs text-gray-500">Pemilik: {{ $payout->donor->name }}</div>
                                        </td>
                                        <td class="p-4 font-mono text-xs font-bold text-gray-700">
                                            {{ $payout->payment->claim->booking_code ?? '-' }}
                                        </td>
                                        <td class="p-4 font-medium text-gray-900">
                                            Rp {{ number_format($payout->donor->balance, 0, ',', '.') }}
                                        </td>
                                        <td class="p-4 font-bold text-green-600">
                                            Rp {{ number_format($payout->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="p-4 pr-6 text-right">
                                            <form action="{{ route('admin.payouts.process', $payout->id) }}" method="POST"
                                                onsubmit="return confirm('Cairkan dana sebesar Rp {{ number_format($payout->amount, 0, ',', '.') }} ke rekening donatur? Saldo donatur akan dikurangi.')">
                                                @csrf
                                                <button type="submit"
                                                    class="rounded-lg bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs px-3.5 py-1.5 transition-colors shadow-sm">
                                                    Cairkan Dana
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-semibold text-gray-900">Riwayat Pencairan Selesai</h2>
                    <span class="px-3 py-1 bg-gray-50 border border-gray-200 rounded-full text-xs font-medium text-gray-650">
                        Total: {{ $completedPayouts->count() }} Payout
                    </span>
                </div>

                @if($completedPayouts->isEmpty())
                    <div class="p-12 text-center text-gray-500">
                        <i class="fa-solid fa-receipt text-2xl text-gray-400 mb-3"></i>
                        <p class="font-medium">Belum ada riwayat transaksi pencairan dana saat ini.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-gray-500 text-xs font-semibold uppercase tracking-wider border-b border-gray-200">
                                    <th class="p-4 pl-6">Donatur / Toko</th>
                                    <th class="p-4">Kode Booking</th>
                                    <th class="p-4">Dana Dicairkan</th>
                                    <th class="p-4">Waktu Pencairan</th>
                                    <th class="p-4 pr-6">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                                @foreach($completedPayouts as $payout)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="p-4 pl-6">
                                            <div class="font-semibold text-gray-900">{{ $payout->donor->donorProfile->store_name ?? 'Donatur' }}</div>
                                            <div class="text-xs text-gray-500">Pemilik: {{ $payout->donor->name }}</div>
                                        </td>
                                        <td class="p-4 font-mono text-xs text-gray-700">
                                            {{ $payout->payment->claim->booking_code ?? '-' }}
                                        </td>
                                        <td class="p-4 font-extrabold text-green-600">
                                            Rp {{ number_format($payout->amount, 0, ',', '.') }}
                                        </td>
                                        <td class="p-4 text-xs text-gray-500">
                                            {{ $payout->sent_at ? $payout->sent_at->format('d M Y, H:i') : $payout->created_at->format('d M Y, H:i') }}
                                        </td>
                                        <td class="p-4 pr-6">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-bold bg-green-50 text-green-700 border border-green-200">
                                                Sukses
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
    </div>
</main>
@endsection
