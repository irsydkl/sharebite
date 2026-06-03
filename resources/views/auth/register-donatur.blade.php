@extends('layouts.auth')

@section('title')
    Daftar Donatur
@endsection

@section('content')
    <div class="flex flex-col items-center justify-center min-h-screen px-4 py-12 sm:px-6 lg:px-8">
        <div class="w-full max-w-3xl px-6 py-4 bg-white shadow-md border sm:rounded-[21px]">

            <a href="{{ route('register') }}" class="text-sm text-[#126C38] hover:text-[#0d4e28]">
                &larr; Kembali pilih role
            </a>

            <h1 class="mt-2 text-[28px] font-bold text-[#126C38]">
                Daftar sebagai Donatur
            </h1>

            <p class="mt-2 text-gray-600">
                Lengkapi data diri dan informasi toko Anda.
            </p>

            <form method="POST" action="{{ route('register.donatur.store') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">
                        Data Diri
                    </h2>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                            autocomplete="name"
                            class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2 focus:border-[#126C38] focus:outline-none">
                        @error('name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700">Nomor Telepon</label>
                        <input id="phone" type="tel" name="phone" value="{{ old('phone') }}" required
                            autocomplete="tel" placeholder="08xxxxxxxxxx"
                            class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2 focus:border-[#126C38] focus:outline-none">
                        @error('phone')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required
                        autocomplete="username"
                        class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2 focus:border-[#126C38] focus:outline-none">
                    @error('email')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat Domisili</label>
                    <textarea id="address" name="address" rows="2" required
                        class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2 focus:border-[#126C38] focus:outline-none">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
                        <input id="password" type="password" name="password" required autocomplete="new-password"
                            class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2 focus:border-[#126C38] focus:outline-none">
                        @error('password')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                            Konfirmasi Password
                        </label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            autocomplete="new-password"
                            class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2 focus:border-[#126C38] focus:outline-none">
                        @error('password_confirmation')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="pt-2">
                    <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">
                        Data Toko
                    </h2>
                </div>

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label for="store_name" class="block text-sm font-medium text-gray-700">
                            Nama Toko
                        </label>
                        <input id="store_name" type="text" name="store_name" value="{{ old('store_name') }}" required
                            class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2 focus:border-[#126C38] focus:outline-none">
                        @error('store_name')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="store_address" class="block text-sm font-medium text-gray-700">
                            Alamat Toko
                        </label>
                        <input id="store_address" type="text" name="store_address" value="{{ old('store_address') }}"
                            required
                            class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2 focus:border-[#126C38] focus:outline-none">
                        @error('store_address')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="store_description" class="block text-sm font-medium text-gray-700">
                        Deskripsi Toko
                    </label>
                    <textarea id="store_description" name="store_description" rows="2"
                        class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2 focus:border-[#126C38] focus:outline-none">{{ old('store_description') }}</textarea>
                    @error('store_description')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div data-map-picker data-lat-input="store_latitude" data-lng-input="store_longitude" data-required="1"
                    data-initial-lat="{{ old('store_latitude') }}" data-initial-lng="{{ old('store_longitude') }}">

                    <div class="flex items-center justify-between">
                        <label class="block text-sm font-medium text-gray-700">
                            Lokasi Toko
                        </label>

                        <button type="button" data-locate-me
                            class="text-sm font-medium text-[#126C38] hover:text-[#0d4e28]">
                            Gunakan lokasi saya
                        </button>
                    </div>

                    <div data-map class="mt-2 w-full overflow-hidden rounded-xl border border-gray-300"
                        style="height:280px">
                    </div>

                    <input type="hidden" name="store_latitude" id="store_latitude"
                        value="{{ old('store_latitude') }}">

                    <input type="hidden" name="store_longitude" id="store_longitude"
                        value="{{ old('store_longitude') }}">

                    @error('store_latitude')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror

                    @error('store_longitude')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit"
                    class="w-full rounded-2xl bg-[#4F9D8C] py-3 text-base font-medium text-white transition hover:bg-[#458a7a]">
                    Daftar sebagai Donatur
                </button>

                <a href="{{ route('login') }}"
                    class="block text-center text-sm font-medium text-[#126C38] hover:text-[#0d4e28]">
                    Sudah punya akun?
                </a>

            </form>
        </div>
    </div>
@endsection
