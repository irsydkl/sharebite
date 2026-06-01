<x-guest-wide-layout>
    <div class="mb-6">
        <a href="{{ route('register') }}" class="text-sm text-indigo-600 hover:text-indigo-500">&larr; Kembali pilih role</a>
        <h1 class="mt-2 text-xl font-semibold text-gray-900">Daftar sebagai Donatur</h1>
        <p class="mt-1 text-sm text-gray-600">Lengkapi data diri dan informasi toko Anda.</p>
    </div>

    <form method="POST" action="{{ route('register.donatur.store') }}">
        @csrf

        <h2 class="text-sm font-semibold uppercase tracking-wide text-gray-500">Data Diri</h2>

        <div class="mt-4">
            <x-input-label for="name" value="Nama Lengkap" />
            <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>

        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="email" value="Email" />
                <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
                <x-input-error :messages="$errors->get('email')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="phone" value="Nomor Telepon" />
                <x-text-input id="phone" class="block mt-1 w-full" type="tel" name="phone" :value="old('phone')" required placeholder="08xxxxxxxxxx" autocomplete="tel" />
                <x-input-error :messages="$errors->get('phone')" class="mt-2" />
            </div>
        </div>

        <div class="mt-4">
            <x-input-label for="address" value="Alamat Domisili" />
            <textarea id="address" name="address" rows="2" required
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('address') }}</textarea>
            <x-input-error :messages="$errors->get('address')" class="mt-2" />
        </div>

        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            <div>
                <x-input-label for="password" value="Kata Sandi" />
                <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password')" class="mt-2" />
            </div>
            <div>
                <x-input-label for="password_confirmation" value="Konfirmasi Kata Sandi" />
                <x-text-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
            </div>
        </div>

        <h2 class="mt-8 text-sm font-semibold uppercase tracking-wide text-gray-500">Data Toko</h2>

        <div class="mt-4">
            <x-input-label for="store_name" value="Nama Toko" />
            <x-text-input id="store_name" class="block mt-1 w-full" type="text" name="store_name" :value="old('store_name')" required />
            <x-input-error :messages="$errors->get('store_name')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="store_description" value="Deskripsi Toko (opsional)" />
            <textarea id="store_description" name="store_description" rows="3"
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('store_description') }}</textarea>
            <x-input-error :messages="$errors->get('store_description')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-input-label for="store_address" value="Alamat Toko" />
            <textarea id="store_address" name="store_address" rows="2" required
                class="block mt-1 w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('store_address') }}</textarea>
            <x-input-error :messages="$errors->get('store_address')" class="mt-2" />
        </div>

        <div class="mt-4">
            <x-map-picker
                lat-name="store_latitude"
                lng-name="store_longitude"
                label="Lokasi Toko di Peta"
                :required="true"
                height="320px"
            />
        </div>

        <div class="flex items-center justify-end mt-6">
            <x-primary-button>
                Daftar sebagai Donatur
            </x-primary-button>
        </div>
    </form>
</x-guest-wide-layout>
