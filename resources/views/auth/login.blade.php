@extends('layouts.auth')

@section('title')
    Login
@endsection

@section('content')
    <div class="flex flex-col items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden border sm:rounded-[21px]">
            <img src="{{ asset('images/sharebite.png') }}" alt="" class="mx-auto h-20 w-20">
            <h1 class="mt-2 text-center text-[32px] font-bold text-[#126C38]">ShareBite</h1>
            <p class="mt-2 text-center text-sm text-gray-600">Selamatkan makanan, bagikan harapan. <br>Platform redistribusi
                makanan surplus real-time.</p>
            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf
                <div class="mb-2 mt-4">
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                        autocomplete="username"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1D9E87] focus:border-[#1D9E87] outline-none">
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="mb-1">
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#1D9E87] focus:border-[#1D9E87] outline-none">
                    @error('password')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex flex-col items-center gap-4">

                    <div class="flex items-center justify-between gap-4 w-full">
                        <a href="{{ route('password.request') }}" class="text-sm text-gray-600 hover:text-gray-900 hover:underline">Lupa Password?</a>
                        <a href="{{ route('register') }}" class="text-sm text-gray-600 hover:text-gray-900 hover:underline">Buat Akun</a>
                    </div>
                    <button type="submit" class="px-6 py-2 bg-[#1D9E87] text-white font-medium rounded-lg hover:bg-[#1a806d] transition duration-200">
                        Log In
                    </button>

                </div>
            </form>
        </div>
    @endsection
