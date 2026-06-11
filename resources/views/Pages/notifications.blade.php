@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<!-- Dynamic Sidebar depending on user role -->
@if(Auth::user()->isAdmin())
    @include('components.sidebar-admin')
@elseif(Auth::user()->isDonatur())
    @include('components.sidebar-donatur')
@else
    @include('components.sidebar-user')
@endif

<main class="flex-1 overflow-y-auto p-8 md:p-12 lg:p-16">
    <div class="max-w-3xl">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Notifikasi Saya 🔔</h1>
            <p class="text-gray-600">Dapatkan pembaruan langsung tentang aktivitas transaksi, verifikasi, dan status klaim Anda.</p>
        </header>

        @if(session('success'))
            <div class="mb-6 bg-green-50 border-l-4 border-green-500 p-4 rounded-r-xl">
                <p class="text-green-700 font-medium">{{ session('success') }}</p>
            </div>
        @endif

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden divide-y divide-gray-100">
            <div class="p-6 border-b border-gray-100 flex items-center justify-between">
                <h2 class="text-lg font-semibold text-gray-900">Pemberitahuan Terbaru</h2>
                <span class="px-3 py-1 bg-gray-50 border border-gray-200 rounded-full text-xs font-medium text-gray-600">
                    Total: {{ $notifications->count() }} Notifikasi
                </span>
            </div>

            @if($notifications->isEmpty())
                <div class="p-12 text-center text-gray-500">
                    <i class="fa-solid fa-bell-slash text-2xl text-gray-400 mb-3"></i>
                    <p class="font-medium">Tidak ada notifikasi untuk Anda saat ini.</p>
                </div>
            @else
                @foreach($notifications as $notification)
                    <div class="p-6 flex items-start justify-between gap-4 transition-colors {{ !$notification->is_read ? 'bg-green-50/20' : '' }}">
                        <div class="flex items-start gap-4">
                            <!-- Icon depending on notification type -->
                            <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 border
                                @if($notification->type === 'success')
                                    bg-green-50 text-green-700 border-green-200
                                @elseif($notification->type === 'warning')
                                    bg-amber-50 text-amber-700 border-amber-200
                                @elseif($notification->type === 'error')
                                    bg-red-50 text-red-700 border-red-200
                                @else
                                    bg-blue-50 text-blue-700 border-blue-200
                                @endif">
                                @if($notification->type === 'success')
                                    <i class="fa-solid fa-circle-check"></i>
                                @elseif($notification->type === 'warning')
                                    <i class="fa-solid fa-triangle-exclamation"></i>
                                @elseif($notification->type === 'error')
                                    <i class="fa-solid fa-circle-xmark"></i>
                                @else
                                    <i class="fa-solid fa-circle-info"></i>
                                @endif
                            </div>

                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <h4 class="font-bold text-gray-900 text-sm sm:text-base leading-snug">{{ $notification->title }}</h4>
                                    @if(!$notification->is_read)
                                        <span class="w-2 h-2 rounded-full bg-green-600 inline-block"></span>
                                    @endif
                                </div>
                                <p class="text-sm text-gray-650">{{ $notification->message }}</p>
                                <p class="text-[11px] text-gray-400 font-medium">
                                    {{ $notification->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>

                        <!-- Action (Mark as Read) -->
                        @if(!$notification->is_read)
                            <form action="{{ route('notifikasi.read', $notification->id) }}" method="POST" class="shrink-0">
                                @csrf
                                <button type="submit" class="text-xs font-semibold text-green-600 hover:text-green-700 hover:underline">
                                    Tandai dibaca
                                </button>
                            </form>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</main>
@endsection
