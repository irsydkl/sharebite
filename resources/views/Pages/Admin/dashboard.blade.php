@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
@include('components.sidebar-admin')
<main class="flex-1 overflow-y-auto p-8 md:p-12 lg:p-16">
    <div class="max-w-6xl">
        <!-- Page Header -->
        <header class="mb-10">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                Selamat Datang, {{ Auth::user()->name }}! 🛡️
            </h1>
            <p class="text-gray-600 text-lg">Pantau platform ShareBite dan kelola seluruh aktivitas dari sini.</p>
        </header>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl flex items-start gap-3">
                <i class="fa-solid fa-circle-check text-green-600 mt-0.5"></i>
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl flex items-start gap-3">
                <i class="fa-solid fa-circle-xmark text-red-600 mt-0.5"></i>
                <p class="text-red-700 font-medium">{{ session('error') }}</p>
            </div>
        @endif

        <!-- KPI Stats Grid -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5 mb-10">
            <!-- Total Users -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center border border-indigo-100">
                        <i class="fa-solid fa-users text-sm"></i>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Pengguna</span>
                </div>
                <p class="text-3xl font-extrabold text-gray-900">{{ number_format($totalUsers) }}</p>
                <p class="text-sm text-gray-500 mt-1">Total Penerima</p>
            </div>

            <!-- Total Donaturs -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center border border-emerald-100">
                        <i class="fa-solid fa-hand-holding-heart text-sm"></i>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Donatur</span>
                </div>
                <p class="text-3xl font-extrabold text-gray-900">{{ number_format($totalDonaturs) }}</p>
                <p class="text-sm text-gray-500 mt-1">Total Donatur</p>
            </div>

            <!-- Total Foods -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center border border-amber-100">
                        <i class="fa-solid fa-bowl-food text-sm"></i>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Makanan</span>
                </div>
                <p class="text-3xl font-extrabold text-gray-900">{{ number_format($totalFoods) }}</p>
                <p class="text-sm text-gray-500 mt-1">Total Listing Makanan</p>
            </div>

            <!-- Platform Revenue -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-10 h-10 bg-violet-50 text-violet-600 rounded-xl flex items-center justify-center border border-violet-100">
                        <i class="fa-solid fa-circle-dollar-to-slot text-sm"></i>
                    </div>
                    <span class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Pendapatan</span>
                </div>
                <p class="text-2xl font-extrabold text-gray-900">Rp {{ number_format($totalPayments - $totalPayouts, 0, ',', '.') }}</p>
                <p class="text-sm text-gray-500 mt-1">Komisi Platform</p>
            </div>
        </div>

        <!-- Pending Actions Alert -->
        @if($pendingDonatursCount > 0 || $pendingFoodsCount > 0)
            <div class="bg-amber-50 border border-amber-200 rounded-2xl p-6 mb-8">
                <div class="flex items-start gap-4">
                    <div class="w-10 h-10 bg-amber-100 text-amber-700 rounded-xl flex items-center justify-center shrink-0 border border-amber-200">
                        <i class="fa-solid fa-triangle-exclamation"></i>
                    </div>
                    <div class="flex-1">
                        <h3 class="font-bold text-amber-900 mb-1">Tindakan Diperlukan</h3>
                        <p class="text-amber-800 text-sm mb-4">Ada permohonan yang membutuhkan persetujuan Anda segera.</p>
                        <div class="flex flex-wrap gap-3">
                            @if($pendingDonatursCount > 0)
                                <a href="{{ route('admin.verifikasi.donatur') }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                                    <i class="fa-solid fa-user-check"></i>
                                    {{ $pendingDonatursCount }} Donatur Menunggu
                                </a>
                            @endif
                            @if($pendingFoodsCount > 0)
                                <a href="{{ route('admin.verifikasi.food') }}"
                                    class="inline-flex items-center gap-2 px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                                    <i class="fa-solid fa-bowl-food"></i>
                                    {{ $pendingFoodsCount }} Makanan Menunggu
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-green-50 border border-green-200 rounded-2xl p-6 mb-8">
                <div class="flex items-center gap-4">
                    <div class="w-10 h-10 bg-green-100 text-green-700 rounded-xl flex items-center justify-center shrink-0 border border-green-200">
                        <i class="fa-solid fa-circle-check"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-green-900">Semua Bersih!</h3>
                        <p class="text-green-700 text-sm">Tidak ada permohonan yang menunggu persetujuan saat ini.</p>
                    </div>
                </div>
            </div>
        @endif

        <!-- Finance Overview -->
        <section class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-10">
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <div class="flex items-center justify-between mb-5">
                    <h2 class="text-lg font-semibold text-gray-900">Ringkasan Keuangan</h2>
                    <a href="{{ route('admin.payouts') }}" class="text-xs font-semibold text-indigo-600 hover:underline">
                        Kelola Payout →
                    </a>
                </div>
                <div class="space-y-4">
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-blue-50 text-blue-600 rounded-lg flex items-center justify-center border border-blue-100 text-sm">
                                <i class="fa-solid fa-money-bill-transfer"></i>
                            </div>
                            <span class="text-sm text-gray-700 font-medium">Total Pembayaran Diterima</span>
                        </div>
                        <span class="font-bold text-gray-900">Rp {{ number_format($totalPayments, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-3 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-green-50 text-green-600 rounded-lg flex items-center justify-center border border-green-100 text-sm">
                                <i class="fa-solid fa-arrow-up-from-bracket"></i>
                            </div>
                            <span class="text-sm text-gray-700 font-medium">Total Payout ke Donatur</span>
                        </div>
                        <span class="font-bold text-gray-900">Rp {{ number_format($totalPayouts, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 bg-violet-50 text-violet-600 rounded-lg flex items-center justify-center border border-violet-100 text-sm">
                                <i class="fa-solid fa-piggy-bank"></i>
                            </div>
                            <span class="text-sm text-gray-700 font-medium">Komisi Platform (10%)</span>
                        </div>
                        <span class="font-extrabold text-violet-700">Rp {{ number_format($totalPayments - $totalPayouts, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                <h2 class="text-lg font-semibold text-gray-900 mb-5">Aksi Cepat</h2>
                <div class="grid grid-cols-1 gap-3">
                    <a href="{{ route('admin.verifikasi.donatur') }}"
                        class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 bg-gray-50 hover:bg-indigo-50 hover:border-indigo-200 transition-colors group">
                        <div class="w-9 h-9 bg-white text-indigo-600 rounded-lg flex items-center justify-center border border-gray-200 group-hover:border-indigo-200 text-sm shadow-sm">
                            <i class="fa-solid fa-user-check"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">Verifikasi Donatur</p>
                            <p class="text-xs text-gray-500">{{ $pendingDonatursCount }} menunggu</p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-gray-400 text-xs ml-auto group-hover:text-indigo-500"></i>
                    </a>
                    <a href="{{ route('admin.verifikasi.food') }}"
                        class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 bg-gray-50 hover:bg-amber-50 hover:border-amber-200 transition-colors group">
                        <div class="w-9 h-9 bg-white text-amber-600 rounded-lg flex items-center justify-center border border-gray-200 group-hover:border-amber-200 text-sm shadow-sm">
                            <i class="fa-solid fa-bowl-food"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">Verifikasi Makanan</p>
                            <p class="text-xs text-gray-500">{{ $pendingFoodsCount }} menunggu</p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-gray-400 text-xs ml-auto group-hover:text-amber-500"></i>
                    </a>
                    <a href="{{ route('admin.payouts') }}"
                        class="flex items-center gap-4 p-4 rounded-xl border border-gray-100 bg-gray-50 hover:bg-green-50 hover:border-green-200 transition-colors group">
                        <div class="w-9 h-9 bg-white text-green-600 rounded-lg flex items-center justify-center border border-gray-200 group-hover:border-green-200 text-sm shadow-sm">
                            <i class="fa-solid fa-money-bill-wave"></i>
                        </div>
                        <div>
                            <p class="font-semibold text-gray-900 text-sm">Kelola Payout</p>
                            <p class="text-xs text-gray-500">Proses pencairan dana donatur</p>
                        </div>
                        <i class="fa-solid fa-chevron-right text-gray-400 text-xs ml-auto group-hover:text-green-500"></i>
                    </a>
                </div>
            </div>
        </section>

        <!-- Charts Section -->
        <section class="mt-8 mb-12">
            <h2 class="text-xl font-bold text-gray-900 mb-6">Analisis Platform</h2>
            <div class="grid grid-cols-1 gap-6 mb-6">
                <!-- Transaction History Chart -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-semibold text-gray-900">Tren Transaksi Harian (30 Hari Terakhir)</h3>
                        <span class="text-xs text-indigo-600 font-medium bg-indigo-50 px-2.5 py-1 rounded-full">
                            <i class="fa-solid fa-chart-line mr-1"></i> Total Pembayaran
                        </span>
                    </div>
                    <div class="h-80 w-full relative">
                        <canvas id="paymentsHistoryChart"></canvas>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Food Category Distribution -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm">
                    <h3 class="text-base font-semibold text-gray-900 mb-4">Distribusi Makanan per Kategori</h3>
                    <div class="h-80 w-full relative">
                        <canvas id="foodCategoriesChart"></canvas>
                    </div>
                </div>

                <!-- User Roles Proportion -->
                <div class="bg-white border border-gray-200 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900 mb-4">Proporsi Peran Pengguna</h3>
                    </div>
                    <div class="h-64 w-full relative flex items-center justify-center mb-4">
                        <canvas id="userRolesChart"></canvas>
                    </div>
                    <div class="grid grid-cols-3 gap-2 text-center text-xs font-semibold mt-2 border-t border-gray-100 pt-4">
                        @php
                            $adminCount = $userRoles->firstWhere('role', 'admin')->total ?? 0;
                            $donaturCount = $userRoles->firstWhere('role', 'donatur')->total ?? 0;
                            $recipientCount = $userRoles->firstWhere('role', 'user')->total ?? 0;
                        @endphp
                        <div>
                            <span class="block text-violet-700 text-lg font-bold">{{ $adminCount }}</span>
                            <span class="text-gray-500 text-[10px]">Admin</span>
                        </div>
                        <div>
                            <span class="block text-emerald-600 text-lg font-bold">{{ $donaturCount }}</span>
                            <span class="text-gray-500 text-[10px]">Donatur</span>
                        </div>
                        <div>
                            <span class="block text-blue-600 text-lg font-bold">{{ $recipientCount }}</span>
                            <span class="text-gray-500 text-[10px]">Penerima</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
</main>

{{-- ChartJS Integration --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Transaction History Chart
        const paymentsData = @json($paymentsHistory);
        const dates = paymentsData.map(item => {
            const d = new Date(item.date);
            return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        });
        const amounts = paymentsData.map(item => item.total);

        const labelDates = dates.length > 0 ? dates : ['Tidak ada data'];
        const labelAmounts = amounts.length > 0 ? amounts : [0];

        const ctxPayments = document.getElementById('paymentsHistoryChart').getContext('2d');
        const gradient = ctxPayments.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.25)');
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0.00)');

        new Chart(ctxPayments, {
            type: 'line',
            data: {
                labels: labelDates,
                datasets: [{
                    label: 'Total Pendapatan',
                    data: labelAmounts,
                    borderColor: '#4f46e5',
                    borderWidth: 3,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.35,
                    pointBackgroundColor: '#4f46e5',
                    pointHoverRadius: 6,
                    pointHoverBackgroundColor: '#fff',
                    pointHoverBorderColor: '#4f46e5',
                    pointHoverBorderWidth: 3
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#1e293b',
                        titleFont: { size: 12, weight: 'bold' },
                        bodyFont: { size: 12 },
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return ` Rp ${new Intl.NumberFormat('id-ID').format(context.raw)}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: { size: 11 }
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(241, 245, 249, 1)',
                            drawTicks: false
                        },
                        ticks: {
                            font: { size: 11 },
                            callback: function(value) {
                                return 'Rp ' + new Intl.NumberFormat('id-ID', { notation: 'compact' }).format(value);
                            }
                        }
                    }
                }
            }
        });

        // 2. Food Category Chart
        const categoriesData = @json($foodCategories);
        const categoryLabels = categoriesData.map(item => item.name);
        const categoryTotals = categoriesData.map(item => item.total);

        const ctxCategories = document.getElementById('foodCategoriesChart').getContext('2d');
        new Chart(ctxCategories, {
            type: 'bar',
            data: {
                labels: categoryLabels.length > 0 ? categoryLabels : ['Tidak ada data'],
                datasets: [{
                    label: 'Jumlah Makanan',
                    data: categoryTotals.length > 0 ? categoryTotals : [0],
                    backgroundColor: 'rgba(16, 185, 129, 0.85)',
                    borderRadius: 8,
                    maxBarThickness: 35
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 10,
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 11 } }
                    },
                    y: {
                        grid: { color: 'rgba(241, 245, 249, 1)', drawTicks: false },
                        ticks: { precision: 0, font: { size: 11 } }
                    }
                }
            }
        });

        // 3. User Roles Proportion Chart
        const ctxRoles = document.getElementById('userRolesChart').getContext('2d');
        new Chart(ctxRoles, {
            type: 'pie',
            data: {
                labels: ['Admin', 'Donatur', 'Penerima'],
                datasets: [{
                    data: [{{ $adminCount }}, {{ $donaturCount }}, {{ $recipientCount }}],
                    backgroundColor: ['#7c3aed', '#10b981', '#2563eb'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            font: { size: 11 },
                            padding: 15
                        }
                    },
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return ` ${context.label}: ${context.raw} Pengguna`;
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
