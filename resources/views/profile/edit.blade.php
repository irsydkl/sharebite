@extends('layouts.app')

@section('title', 'Pengaturan Profil')

@section('content')

{{-- Role-based Sidebar --}}
@if(Auth::user()->isAdmin())
    @include('components.sidebar-admin')
@elseif(Auth::user()->isDonatur())
    @include('components.sidebar-donatur')
@else
    @include('components.sidebar-user')
@endif

<main class="flex-1 overflow-y-auto p-8 md:p-10 lg:p-12">
    <div class="max-w-3xl">

        {{-- Page Header --}}
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-1">Pengaturan Profil</h1>
            <p class="text-gray-500">Kelola informasi pribadi dan keamanan akun Anda.</p>
        </header>

        {{-- Flash Messages --}}
        @if(session('status') === 'profile-updated')
            <div id="flash-success" class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-5 py-4 rounded-xl">
                <i class="fa-solid fa-circle-check text-green-600"></i>
                <span class="font-medium text-sm">Profil berhasil diperbarui.</span>
                <button onclick="document.getElementById('flash-success').remove()" class="ml-auto text-green-500 hover:text-green-700">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
        @if(session('status') === 'store-updated')
            <div id="flash-store" class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-5 py-4 rounded-xl">
                <i class="fa-solid fa-circle-check text-green-600"></i>
                <span class="font-medium text-sm">Informasi toko berhasil diperbarui.</span>
                <button onclick="document.getElementById('flash-store').remove()" class="ml-auto text-green-500 hover:text-green-700">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
        @if(session('status') === 'store-updated-pending')
            <div id="flash-store-pending" class="mb-6 flex items-center gap-3 bg-amber-50 border border-amber-200 text-amber-800 px-5 py-4 rounded-xl">
                <i class="fa-solid fa-clock text-amber-600"></i>
                <span class="font-medium text-sm">Toko diperbarui. Perubahan data penting memerlukan verifikasi ulang admin.</span>
                <button onclick="document.getElementById('flash-store-pending').remove()" class="ml-auto text-amber-500 hover:text-amber-700">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif
        @if(session('status') === 'password-updated')
            <div id="flash-pw" class="mb-6 flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 px-5 py-4 rounded-xl">
                <i class="fa-solid fa-circle-check text-green-600"></i>
                <span class="font-medium text-sm">Password berhasil diperbarui.</span>
                <button onclick="document.getElementById('flash-pw').remove()" class="ml-auto text-green-500 hover:text-green-700">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
        @endif

        {{-- ============================================================ --}}
        {{-- 1. Profile Information --}}
        {{-- ============================================================ --}}
        <section class="bg-white border border-gray-200 rounded-2xl shadow-sm mb-6 overflow-hidden">
            {{-- Section Header --}}
            <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
                <div class="w-9 h-9 bg-emerald-50 text-emerald-700 rounded-xl flex items-center justify-center border border-emerald-100">
                    <i class="fa-solid fa-user text-sm"></i>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Informasi Profil</h2>
                    <p class="text-xs text-gray-500">Nama, email, nomor telepon, dan alamat Anda.</p>
                </div>
            </div>

            <div class="px-6 py-6">
                {{-- Avatar + Role Badge --}}
                <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                    <div class="w-16 h-16 rounded-full bg-emerald-100 border-2 border-emerald-200 flex items-center justify-center shrink-0">
                        <span class="text-2xl font-bold text-emerald-700">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                    </div>
                    <div>
                        <p class="font-bold text-gray-900">{{ $user->name }}</p>
                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        <span class="inline-flex items-center mt-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                            @if($user->isAdmin()) bg-violet-100 text-violet-700 border border-violet-200
                            @elseif($user->isDonatur()) bg-emerald-100 text-emerald-700 border border-emerald-200
                            @else bg-blue-100 text-blue-700 border border-blue-200 @endif">
                            @if($user->isAdmin()) Admin
                            @elseif($user->isDonatur()) Donatur
                            @else Penerima @endif
                        </span>
                    </div>
                </div>

                {{-- Hidden verification form --}}
                <form id="send-verification" method="post" action="{{ route('verification.send') }}">@csrf</form>

                <form method="post" action="{{ route('profile.update') }}" class="space-y-5">
                    @csrf
                    @method('patch')

                    {{-- Name + Email (2 columns) --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Nama Lengkap <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm">
                                    <i class="fa-solid fa-user"></i>
                                </span>
                                <input id="name" name="name" type="text"
                                    value="{{ old('name', $user->name) }}"
                                    required autofocus autocomplete="name"
                                    class="w-full pl-9 pr-4 py-2.5 rounded-xl border text-sm text-gray-900
                                        {{ $errors->has('name') ? 'border-red-400 bg-red-50 focus:ring-red-500' : 'border-gray-300 focus:border-emerald-500 focus:ring-emerald-500' }}
                                        focus:outline-none focus:ring-1 transition">
                            </div>
                            @error('name')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Alamat Email <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm">
                                    <i class="fa-solid fa-envelope"></i>
                                </span>
                                <input id="email" name="email" type="email"
                                    value="{{ old('email', $user->email) }}"
                                    required autocomplete="username"
                                    class="w-full pl-9 pr-4 py-2.5 rounded-xl border text-sm text-gray-900
                                        {{ $errors->has('email') ? 'border-red-400 bg-red-50 focus:ring-red-500' : 'border-gray-300 focus:border-emerald-500 focus:ring-emerald-500' }}
                                        focus:outline-none focus:ring-1 transition">
                            </div>
                            @error('email')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </p>
                            @enderror
                            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                                <div class="mt-2 bg-amber-50 border border-amber-200 rounded-lg px-3 py-2">
                                    <p class="text-xs text-amber-800">
                                        Email belum diverifikasi.
                                        <button form="send-verification" class="font-semibold underline ml-1 hover:text-amber-900">
                                            Kirim ulang
                                        </button>
                                    </p>
                                    @if (session('status') === 'verification-link-sent')
                                        <p class="mt-1 text-xs text-green-700 font-medium">Tautan terkirim ke email Anda.</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Phone + Address --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="phone" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Nomor Telepon
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm">
                                    <i class="fa-solid fa-phone"></i>
                                </span>
                                <input id="phone" name="phone" type="tel"
                                    value="{{ old('phone', $user->phone) }}"
                                    autocomplete="tel"
                                    placeholder="08xxxxxxxxxx"
                                    class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-300 text-sm text-gray-900
                                        focus:border-emerald-500 focus:ring-emerald-500 focus:outline-none focus:ring-1 transition">
                            </div>
                            @error('phone')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="address" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Alamat
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-3 text-gray-400 text-sm">
                                    <i class="fa-solid fa-location-dot"></i>
                                </span>
                                <textarea id="address" name="address" rows="2"
                                    placeholder="Jl. Contoh No. 1, Jakarta"
                                    class="w-full pl-9 pr-4 py-2.5 rounded-xl border border-gray-300 text-sm text-gray-900
                                        focus:border-emerald-500 focus:ring-emerald-500 focus:outline-none focus:ring-1 transition resize-none">{{ old('address', $user->address) }}</textarea>
                            </div>
                            @error('address')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
            </div>
        </section>

        @if($user->isDonatur() && $donorProfile)
        {{-- ============================================================ --}}
        {{-- 2. Store Information (Donatur) --}}
        {{-- ============================================================ --}}
        <section class="bg-white border border-gray-200 rounded-2xl shadow-sm mb-6 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-orange-50 text-orange-700 rounded-xl flex items-center justify-center border border-orange-100">
                        <i class="fa-solid fa-store text-sm"></i>
                    </div>
                    <div>
                        <h2 class="text-base font-semibold text-gray-900">Informasi Toko</h2>
                        <p class="text-xs text-gray-500">Nama, alamat, dan lokasi toko donatur Anda.</p>
                    </div>
                </div>
                @php
                    $statusColors = [
                        'approved' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                        'pending' => 'bg-amber-100 text-amber-700 border-amber-200',
                        'rejected' => 'bg-red-100 text-red-700 border-red-200',
                    ];
                    $statusLabels = [
                        'approved' => 'Disetujui',
                        'pending' => 'Menunggu Verifikasi',
                        'rejected' => 'Ditolak',
                    ];
                @endphp
                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider border {{ $statusColors[$donorProfile->approval_status] ?? 'bg-gray-100 text-gray-700 border-gray-200' }}">
                    {{ $statusLabels[$donorProfile->approval_status] ?? $donorProfile->approval_status }}
                </span>
            </div>

            <div class="px-6 py-6">
                <form method="post" action="{{ route('profile.store.update') }}" class="space-y-5">
                    @csrf
                    @method('patch')

                    <div>
                        <label for="store_name" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Nama Toko <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm">
                                <i class="fa-solid fa-shop"></i>
                            </span>
                            <input id="store_name" name="store_name" type="text"
                                value="{{ old('store_name', $donorProfile->store_name) }}"
                                required
                                class="w-full pl-9 pr-4 py-2.5 rounded-xl border text-sm text-gray-900
                                    {{ $errors->has('store_name') ? 'border-red-400 bg-red-50 focus:ring-red-500' : 'border-gray-300 focus:border-emerald-500 focus:ring-emerald-500' }}
                                    focus:outline-none focus:ring-1 transition">
                        </div>
                        @error('store_name')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="store_address" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Alamat Toko <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-3 text-gray-400 text-sm">
                                <i class="fa-solid fa-location-dot"></i>
                            </span>
                            <textarea id="store_address" name="store_address" rows="2" required
                                class="w-full pl-9 pr-4 py-2.5 rounded-xl border text-sm text-gray-900
                                    {{ $errors->has('store_address') ? 'border-red-400 bg-red-50 focus:ring-red-500' : 'border-gray-300 focus:border-emerald-500 focus:ring-emerald-500' }}
                                    focus:outline-none focus:ring-1 transition resize-none">{{ old('store_address', $donorProfile->store_address) }}</textarea>
                        </div>
                        @error('store_address')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label for="store_description" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Deskripsi Toko
                        </label>
                        <textarea id="store_description" name="store_description" rows="3"
                            placeholder="Ceritakan jenis makanan yang biasa Anda bagikan..."
                            class="w-full px-4 py-2.5 rounded-xl border border-gray-300 text-sm text-gray-900
                                focus:border-emerald-500 focus:ring-emerald-500 focus:outline-none focus:ring-1 transition resize-none">{{ old('store_description', $donorProfile->store_description) }}</textarea>
                        @error('store_description')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <x-map-picker
                            lat-name="store_latitude"
                            lng-name="store_longitude"
                            label="Lokasi Toko di Peta"
                            :required="true"
                            height="320px"
                            :initial-lat="old('store_latitude', $donorProfile->latitude)"
                            :initial-lng="old('store_longitude', $donorProfile->longitude)"
                            class="[&_button]:text-emerald-600 [&_button]:hover:text-emerald-700"
                        />
                    </div>

                    <p class="text-xs text-gray-500">
                        <i class="fa-solid fa-circle-info mr-1"></i>
                        Perubahan nama, alamat, atau lokasi toko akan memerlukan verifikasi ulang admin jika toko sudah disetujui.
                    </p>

                    <div class="pt-2">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-orange-600 hover:bg-orange-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Simpan Informasi Toko
                        </button>
                    </div>
                </form>
            </div>
        </section>
        @endif

        {{-- ============================================================ --}}
        {{-- 3. Update Password --}}
        {{-- ============================================================ --}}
        <section class="bg-white border border-gray-200 rounded-2xl shadow-sm mb-6 overflow-hidden">
            <div class="px-6 py-5 border-b border-gray-100 flex items-center gap-3">
                <div class="w-9 h-9 bg-blue-50 text-blue-700 rounded-xl flex items-center justify-center border border-blue-100">
                    <i class="fa-solid fa-lock text-sm"></i>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-gray-900">Ubah Password</h2>
                    <p class="text-xs text-gray-500">Gunakan password yang kuat agar akun tetap aman.</p>
                </div>
            </div>

            <div class="px-6 py-6">
                <form method="post" action="{{ route('password.update') }}" class="space-y-5">
                    @csrf
                    @method('put')

                    <div>
                        <label for="update_password_current_password" class="block text-sm font-medium text-gray-700 mb-1.5">
                            Password Saat Ini
                        </label>
                        <div class="relative">
                            <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm">
                                <i class="fa-solid fa-key"></i>
                            </span>
                            <input id="update_password_current_password" name="current_password" type="password"
                                autocomplete="current-password"
                                placeholder="••••••••"
                                class="w-full pl-9 pr-4 py-2.5 rounded-xl border text-sm text-gray-900
                                    {{ $errors->updatePassword->has('current_password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}
                                    focus:border-blue-500 focus:ring-blue-500 focus:outline-none focus:ring-1 transition">
                        </div>
                        @error('current_password', 'updatePassword')
                            <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="update_password_password" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Password Baru
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm">
                                    <i class="fa-solid fa-lock-open"></i>
                                </span>
                                <input id="update_password_password" name="password" type="password"
                                    autocomplete="new-password"
                                    placeholder="Min. 8 karakter"
                                    class="w-full pl-9 pr-4 py-2.5 rounded-xl border text-sm text-gray-900
                                        {{ $errors->updatePassword->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}
                                        focus:border-blue-500 focus:ring-blue-500 focus:outline-none focus:ring-1 transition">
                            </div>
                            @error('password', 'updatePassword')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div>
                            <label for="update_password_password_confirmation" class="block text-sm font-medium text-gray-700 mb-1.5">
                                Konfirmasi Password
                            </label>
                            <div class="relative">
                                <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm">
                                    <i class="fa-solid fa-shield-check"></i>
                                </span>
                                <input id="update_password_password_confirmation" name="password_confirmation" type="password"
                                    autocomplete="new-password"
                                    placeholder="Ulangi password baru"
                                    class="w-full pl-9 pr-4 py-2.5 rounded-xl border text-sm text-gray-900
                                        {{ $errors->updatePassword->has('password_confirmation') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}
                                        focus:border-blue-500 focus:ring-blue-500 focus:outline-none focus:ring-1 transition">
                            </div>
                            @error('password_confirmation', 'updatePassword')
                                <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                                    <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                            <i class="fa-solid fa-floppy-disk"></i>
                            Perbarui Password
                        </button>
                    </div>
                </form>
            </div>
        </section>

        {{-- ============================================================ --}}
        {{-- 4. Delete Account --}}
        {{-- ============================================================ --}}
        <section class="bg-white border border-red-100 rounded-2xl shadow-sm overflow-hidden">
            <div class="px-6 py-5 border-b border-red-100 flex items-center gap-3">
                <div class="w-9 h-9 bg-red-50 text-red-700 rounded-xl flex items-center justify-center border border-red-100">
                    <i class="fa-solid fa-trash text-sm"></i>
                </div>
                <div>
                    <h2 class="text-base font-semibold text-red-700">Hapus Akun</h2>
                    <p class="text-xs text-gray-500">Tindakan ini tidak dapat dibatalkan.</p>
                </div>
            </div>

            <div class="px-6 py-6">
                <p class="text-sm text-gray-600 mb-5">
                    Setelah akun dihapus, semua data Anda akan dihapus secara permanen termasuk riwayat klaim, pembayaran, dan notifikasi.
                    Unduh data Anda terlebih dahulu jika diperlukan.
                </p>
                <button id="deleteBtn" type="button"
                    onclick="document.getElementById('deleteModal').classList.remove('hidden')"
                    class="inline-flex items-center gap-2 px-6 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                    <i class="fa-solid fa-trash"></i>
                    Hapus Akun Saya
                </button>
            </div>
        </section>

    </div>
</main>

{{-- ================================================================ --}}
{{-- Delete Account Modal (pure CSS/JS, no Alpine) --}}
{{-- ================================================================ --}}
<div id="deleteModal" class="hidden fixed inset-0 z-50 flex items-center justify-center px-4">
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
         onclick="document.getElementById('deleteModal').classList.add('hidden')"></div>

    {{-- Dialog --}}
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6 z-10">
        {{-- Icon --}}
        <div class="w-12 h-12 bg-red-100 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4 border border-red-200">
            <i class="fa-solid fa-triangle-exclamation text-lg"></i>
        </div>

        <h3 class="text-center text-lg font-bold text-gray-900 mb-2">Hapus Akun?</h3>
        <p class="text-center text-sm text-gray-600 mb-6">
            Semua data Anda akan dihapus secara permanen. Tindakan ini <strong>tidak dapat dibatalkan</strong>.
            Masukkan password Anda untuk melanjutkan.
        </p>

        <form method="post" action="{{ route('profile.destroy') }}" class="space-y-4">
            @csrf
            @method('delete')

            <div>
                <label for="delete_password" class="block text-sm font-medium text-gray-700 mb-1.5">
                    Password
                </label>
                <div class="relative">
                    <span class="absolute left-3.5 top-1/2 -translate-y-1/2 text-gray-400 text-sm">
                        <i class="fa-solid fa-lock"></i>
                    </span>
                    <input id="delete_password" name="password" type="password"
                        placeholder="Masukkan password Anda"
                        class="w-full pl-9 pr-4 py-2.5 rounded-xl border text-sm text-gray-900
                            {{ $errors->userDeletion->has('password') ? 'border-red-400 bg-red-50' : 'border-gray-300' }}
                            focus:border-red-500 focus:ring-red-500 focus:outline-none focus:ring-1 transition">
                </div>
                @error('password', 'userDeletion')
                    <p class="mt-1.5 text-xs text-red-600 flex items-center gap-1">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button"
                    onclick="document.getElementById('deleteModal').classList.add('hidden')"
                    class="flex-1 py-2.5 border border-gray-300 text-gray-700 text-sm font-semibold rounded-xl hover:bg-gray-50 transition-colors">
                    Batal
                </button>
                <button type="submit"
                    class="flex-1 py-2.5 bg-red-600 hover:bg-red-700 text-white text-sm font-semibold rounded-xl transition-colors shadow-sm">
                    Ya, Hapus Akun
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Auto-open modal if there were userDeletion errors --}}
@if($errors->userDeletion->isNotEmpty())
<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('deleteModal').classList.remove('hidden');
    });
</script>
@endif

@endsection