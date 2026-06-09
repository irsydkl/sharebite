@extends('layouts.auth')

@section('title')
    Lupa Password
@endsection

@section('content')
    <div class="flex flex-col items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden border sm:rounded-[21px]">
            <img src="{{ asset('images/sharebite.png') }}" alt="" class="mx-auto h-20 w-20">
            <h1 class="mt-2 text-center text-[32px] font-bold text-[#126C38]">Lupa Password</h1>
            <p class="mt-2 text-center text-lg text-gray-600">Masukkan email Anda dan kami akan mengirimkan tautan untuk mengatur ulang password.</p>

            @if (session('status'))
                <div class="mt-4 text-sm font-medium text-green-600 text-center">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}" class="flex flex-col gap-4 mt-4">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input id="email" type="email" name="email" :value="old('email')" required autofocus
                        class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 text-gray-900 shadow-sm focus:border-[#126C38] focus:ring-[#126C38] focus:outline-none">
                    @error('email')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full rounded-3xl bg-[#126C38] px-8 py-4 text-white text-xl font-light shadow-sm transition hover:bg-[#0d4e28]">
                    Kirim Tautan Reset Password
                </button>

                <a href="{{ route('login') }}" class="font-medium text-[#126C38] hover:text-[#0d4e28] text-center">Kembali ke halaman login</a>
            </form>
        </div>
    </div>
@endsection