@extends('layouts.auth')

@section('title')
    Pilih Jenis Akun
@endsection

@section('content')
    <div class="flex flex-col items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden border sm:rounded-[21px]">
            <img src="{{ asset('images/sharebite.png') }}" alt="" class="mx-auto h-20 w-20">
            <h1 class="mt-2 text-center text-[32px] font-bold text-[#126C38]">Daftar Akun ShareBite</h1>
            <p class="mt-2 text-center text-lg text-gray-600">Pilih jenis akun sebelum melanjutkan pendaftaran.</p>
            <div class="flex flex-col gap-5 mt-4">
                <a href="{{ route('register.donatur') }}"
                    class="flex items-center gap-5 w-full rounded-3xl bg-[#4F9D8C] px-8 py-6 text-white shadow-sm transition hover:bg-[#458a7a]">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 flex-shrink-0" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 9l1-4h16l1 4M4 9v9h16V9M8 13h8" />
                    </svg>
                    <span class="text-xl font-light">
                        Daftar sebagai Donatur
                    </span>
                </a>
                <a href="{{ route('register.user') }}"
                    class="flex items-center gap-5 w-full rounded-3xl border border-gray-300 bg-white px-8 py-6 text-gray-900 shadow-sm transition hover:bg-gray-50">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-10 w-10 flex-shrink-0" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15.75 6.75a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.5 20.25a7.5 7.5 0 0115 0" />
                    </svg>
                    <span class="text-xl font-light">
                        Daftar sebagai Pengguna
                    </span>
                </a>
                <a href="{{ route('login') }}" class="font-medium text-[#126C38] hover:text-[#0d4e28] text-center">Sudah punya akun?</a>
            </div>
        </div>
    @endsection
