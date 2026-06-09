@extends('layouts.auth')

@section('title')
    Verifikasi Email
@endsection

@section('content')
    <div class="flex flex-col items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden border sm:rounded-[21px]">
            <img src="{{ asset('images/sharebite.png') }}" alt="" class="mx-auto h-20 w-20">
            <h1 class="mt-2 text-center text-[32px] font-bold text-[#126C38]">Verifikasi Email</h1>
            <p class="mt-2 text-center text-lg text-gray-600">Terima kasih telah mendaftar! Silakan verifikasi email Anda dengan mengklik tautan yang sudah kami kirimkan.</p>

            @if (session('status') == 'verification-link-sent')
                <div class="mt-4 text-sm font-medium text-green-600 text-center">
                    Tautan verifikasi baru telah dikirim ke alamat email Anda.
                </div>
            @endif

            <div class="flex flex-col gap-4 mt-6">
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit"
                        class="w-full rounded-3xl bg-[#126C38] px-8 py-4 text-white text-xl font-light shadow-sm transition hover:bg-[#0d4e28]">
                        Kirim Ulang Email Verifikasi
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full font-medium text-[#126C38] hover:text-[#0d4e28] text-center">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
@endsection