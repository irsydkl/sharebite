<x-guest-layout>
    <div class="mb-6 text-center">
        <h1 class="text-xl font-semibold text-gray-900">Daftar Akun Sharebite</h1>
        <p class="mt-2 text-sm text-gray-600">Pilih jenis akun sebelum melanjutkan pendaftaran.</p>
    </div>

    <div class="grid gap-4 sm:grid-cols-2">
        <a href="{{ route('register.user') }}"
           class="group block rounded-lg border-2 border-gray-200 p-5 text-center transition hover:border-indigo-500 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <div class="text-lg font-semibold text-gray-900 group-hover:text-indigo-700">Pengguna</div>
            <p class="mt-2 text-sm text-gray-600">Mencari dan mengklaim makanan berlebih dari donatur.</p>
        </a>

        <a href="{{ route('register.donatur') }}"
           class="group block rounded-lg border-2 border-gray-200 p-5 text-center transition hover:border-indigo-500 hover:bg-indigo-50 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <div class="text-lg font-semibold text-gray-900 group-hover:text-indigo-700">Donatur</div>
            <p class="mt-2 text-sm text-gray-600">Menjual atau membagikan makanan berlebih dari toko Anda.</p>
        </a>
    </div>

    <div class="mt-6 text-center text-sm text-gray-600">
        Sudah punya akun?
        <a href="{{ route('login') }}" class="font-medium text-indigo-600 hover:text-indigo-500">Masuk di sini</a>
    </div>
</x-guest-layout>
