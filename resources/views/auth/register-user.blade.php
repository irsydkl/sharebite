@extends('layouts.auth')

@section('title')
    Daftar Pengguna
@endsection

@section('content')
    <div class="flex flex-col items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md border sm:rounded-[21px]">
            <a href="{{ route('register') }}" class="text-sm text-[#126C38] hover:text-[#0d4e28]">&larr; Kembali pilih
                role</a>
            <h1 class="mt-2 text-[28px] font-bold text-[#126C38]">Daftar sebagai Pengguna</h1>
            <p class="mt-2 text-gray-600">Lengkapi data diri Anda untuk membuat akun.</p>
            <form method="POST" action="{{ route('register.user.store') }}" class="mt-6 space-y-4">
                @csrf

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
                    <label for="address" class="block text-sm font-medium text-gray-700">Alamat</label>
                    <textarea id="address" name="address" rows="2" required
                        class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2 focus:border-[#126C38] focus:outline-none">{{ old('address') }}</textarea>
                    @error('address')
                        <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div data-map-picker data-lat-input="latitude" data-lng-input="longitude" data-required="0"
                    data-initial-lat="{{ old('latitude') }}" data-initial-lng="{{ old('longitude') }}">

                    <div class="flex items-center justify-between">
                        <label class="block text-sm font-medium text-gray-700">Lokasi Domisili (Opsional)</label>
                        <button type="button" data-locate-me
                            class="text-sm font-medium text-[#126C38] hover:text-[#0d4e28]">
                            Gunakan lokasi saya
                        </button>
                    </div>

                    <div data-map class="mt-2 w-full overflow-hidden rounded-xl border border-gray-300"
                        style="height:220px"></div>
                    <input type="hidden" name="latitude" id="latitude" value="{{ old('latitude') }}">
                    <input type="hidden" name="longitude" id="longitude" value="{{ old('longitude') }}">
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
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Konfirmasi
                            Password</label>
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            autocomplete="new-password"
                            class="mt-1 w-full rounded-xl border border-gray-300 px-4 py-2 focus:border-[#126C38] focus:outline-none">
                        @error('password_confirmation')
                            <p class="mt-1 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <button type="submit"
                    class="w-full rounded-2xl bg-[#4F9D8C] py-3 text-base font-medium text-white transition hover:bg-[#458a7a]">
                    Daftar sebagai Pengguna
                </button>

                <a href="{{ route('login') }}"
                    class="block text-center text-sm font-medium text-[#126C38] hover:text-[#0d4e28]">
                    Sudah punya akun?
                </a>
            </form>
        </div>
    </div>
@endsection
