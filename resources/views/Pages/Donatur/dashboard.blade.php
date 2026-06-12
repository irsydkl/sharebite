@extends('layouts.app')

@section('title')
    Dashboard
@endsection

@section('content')
@include('components.sidebar-donatur')
    <main class="flex-1 overflow-y-auto p-8 md:p-12 lg:p-16">
        <div class="max-w-5xl">

            <header class="mb-10">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">
                    Halo, {{ Auth::user()->name }}! 👋
                </h1>
                <p class="text-gray-600 text-lg">Kelola makananmu dan lihat dampaknya.</p>
            </header>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-12">
                <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)]">
                    <h3 class="text-4xl font-bold text-gray-900 mb-1">{{ $totalUnggahan ?? 0 }}</h3>
                    <p class="text-gray-600">Total Unggahan</p>
                </div>
                <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)]">
                    <h3 class="text-4xl font-bold text-gray-900 mb-1">{{ $approved ?? 0 }}</h3>
                    <p class="text-gray-600">Approved</p>
                </div>
                <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)]">
                    <h3 class="text-4xl font-bold text-gray-900 mb-1">{{ $pending ?? 0 }}</h3>
                    <p class="text-gray-600">Pending</p>
                </div>
                <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)]">
                    <h3 class="text-4xl font-bold text-gray-900 mb-1">{{ $nightApproved ?? 0 }}</h3>
                    <p class="text-gray-600">Night Approved</p>
                </div>
            </div>

            <section>
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Ringkasan</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div
                        class="bg-white border border-gray-200 p-8 sm:p-10 rounded-2xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] flex flex-col justify-center">
                        <p class="text-gray-800 text-lg mb-4 font-medium">Total Porsi Disalurkan:</p>
                        <div class="flex justify-center w-full">
                            <h3 class="text-6xl font-extrabold text-gray-900">{{ $totalPorsi ?? 0 }}</h3>
                        </div>
                    </div>
                    <div
                        class="bg-white border border-gray-200 p-8 sm:p-10 rounded-2xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] flex flex-col justify-center">
                        <p class="text-gray-800 text-lg mb-4 font-medium">Food Waste Terselamatkan:</p>
                        <div class="flex justify-center w-full">
                            <h3 class="text-6xl font-extrabold text-gray-900">{{ $foodWaste ?? 0 }}</h3>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Charts Section --}}
            <section class="mt-12 mb-16">
                <h2 class="text-2xl font-semibold text-gray-900 mb-6">Analisis & Dampak</h2>
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    {{-- Portions Saved Trend Chart --}}
                    <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] lg:col-span-2">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-base font-semibold text-gray-900">Tren Porsi Disalurkan (30 Hari Terakhir)</h3>
                            <span class="text-xs text-emerald-600 font-medium bg-emerald-50 px-2.5 py-1 rounded-full">
                                <i class="fa-solid fa-chart-line mr-1"></i> Porsi Makanan
                            </span>
                        </div>
                        <div class="h-80 w-full relative">
                            <canvas id="portionsHistoryChart"></canvas>
                        </div>
                    </div>

                    {{-- Verification Status Chart --}}
                    <div class="bg-white border border-gray-200 p-6 rounded-2xl shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] flex flex-col justify-between">
                        <div>
                            <h3 class="text-base font-semibold text-gray-900 mb-4">Status Verifikasi Makanan</h3>
                        </div>
                        <div class="h-60 w-full relative flex items-center justify-center mb-4">
                            <canvas id="approvalStatusChart"></canvas>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center text-xs font-semibold mt-2">
                            <div>
                                <span class="block text-emerald-600 text-lg font-bold">{{ $approved }}</span>
                                <span class="text-gray-500 text-[10px]">Disetujui</span>
                            </div>
                            <div>
                                <span class="block text-amber-500 text-lg font-bold">{{ $pending }}</span>
                                <span class="text-gray-500 text-[10px]">Menunggu</span>
                            </div>
                            <div>
                                <span class="block text-red-500 text-lg font-bold">{{ $rejected }}</span>
                                <span class="text-gray-500 text-[10px]">Ditolak</span>
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
            // 1. Portions Saved Trend Chart
            const portionsData = @json($portionsHistory);
            const dates = portionsData.map(item => {
                const d = new Date(item.date);
                return d.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
            });
            const totals = portionsData.map(item => item.total);

            const labelDates = dates.length > 0 ? dates : ['Tidak ada data'];
            const labelTotals = totals.length > 0 ? totals : [0];

            const ctxPortions = document.getElementById('portionsHistoryChart').getContext('2d');
            
            const gradient = ctxPortions.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, 'rgba(16, 185, 129, 0.25)');
            gradient.addColorStop(1, 'rgba(16, 185, 129, 0.00)');

            new Chart(ctxPortions, {
                type: 'line',
                data: {
                    labels: labelDates,
                    datasets: [{
                        label: 'Porsi Disalurkan',
                        data: labelTotals,
                        borderColor: '#10b981',
                        borderWidth: 3,
                        backgroundColor: gradient,
                        fill: true,
                        tension: 0.35,
                        pointBackgroundColor: '#10b981',
                        pointHoverRadius: 6,
                        pointHoverBackgroundColor: '#fff',
                        pointHoverBorderColor: '#10b981',
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
                                    return `${context.raw} Porsi`;
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
                                precision: 0,
                                font: { size: 11 }
                            }
                        }
                    }
                }
            });

            // 2. Verification Status Chart
            const ctxApproval = document.getElementById('approvalStatusChart').getContext('2d');
            new Chart(ctxApproval, {
                type: 'doughnut',
                data: {
                    labels: ['Disetujui', 'Menunggu', 'Ditolak'],
                    datasets: [{
                        data: [{{ $approved }}, {{ $pending }}, {{ $rejected }}],
                        backgroundColor: ['#10b981', '#f59e0b', '#ef4444'],
                        borderWidth: 2,
                        hoverOffset: 4
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
                            callbacks: {
                                label: function(context) {
                                    return ` ${context.label}: ${context.raw} Makanan`;
                                }
                            }
                        }
                    },
                    cutout: '70%'
                }
            });
        });
    </script>
@endsection
