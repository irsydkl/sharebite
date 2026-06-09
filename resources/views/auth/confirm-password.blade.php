@extends('layouts.auth')

@section('title')
    Konfirmasi Password
@endsection

@section('content')
    <div class="flex flex-col items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden border sm:rounded-[21px]">
            <img src="{{ asset('images/sharebite.png') }}" alt="" class="mx-auto h-20 w-20">
            <h1 class="mt-2 text-center text-[32px] font-bold text-[#126C38]">Konfirmasi Password</h1>
            <p class="mt-2 text-center text-lg text-gray-600">Ini adalah area aman. Harap konfirmasi password Anda sebelum melanjutkan.</p>

            <form method="POST" action="{{ route('password.confirm') }}" class="flex flex-col gap-4 mt-4">
                @csrf

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password"
                        class="mt-1 block w-full rounded-xl border border-gray-300 px-4 py-3 text-gray-900 shadow-sm focus:border-[#126C38] focus:ring-[#126C38] focus:outline-none">
                    @error('password')
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full rounded-3xl bg-[#126C38] px-8 py-4 text-white text-xl font-light shadow-sm transition hover:bg-[#0d4e28]">
                    Konfirmasi
                </button>
            </form>
        </div>
    </div>
@endsection