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

        </div>
    </main>
@endsection
