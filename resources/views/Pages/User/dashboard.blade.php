@extends('layouts.app')

@section('title', 'Cari Makanan')

@section('content')
@include('components.sidebar-user')
<main class="flex-1 overflow-y-auto p-8 md:p-12 lg:p-16">
    <div class="max-w-6xl">
        <header class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">Selamat Datang, {{ Auth::user()->name }}! 👋</h1>
            <p class="text-gray-600">Temukan makanan berkualitas terdekat dari donatur tepercaya sebelum masa berlakunya berakhir.</p>
        </header>

        <!-- Search Bar placeholder or filters -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm mb-8 flex flex-col md:flex-row items-center justify-between gap-4">
            <div class="flex-1 w-full relative">
                <input type="text" id="searchInput" onkeyup="filterFoods()"
                    class="w-full rounded-lg border border-gray-300 pl-10 pr-4 py-2.5 text-sm focus:border-green-500 focus:outline-none focus:ring-1 focus:ring-green-500"
                    placeholder="Cari makanan (contoh: Nasi, Roti)...">
                <span class="absolute left-3.5 top-3 text-gray-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
            </div>
            <div class="flex items-center gap-2 w-full md:w-auto overflow-x-auto whitespace-nowrap">
                <button onclick="filterCategory('All')" class="category-btn px-4 py-2 rounded-lg text-xs font-semibold bg-green-600 text-white shadow-sm transition-colors border border-green-600">Semua</button>
                @php
                    $categories = $foods->pluck('category.name')->unique();
                @endphp
                @foreach($categories as $categoryName)
                    <button onclick="filterCategory('{{ $categoryName }}')" class="category-btn px-4 py-2 rounded-lg text-xs font-semibold bg-gray-50 text-gray-700 hover:bg-gray-100 transition-colors border border-gray-200">{{ $categoryName }}</button>
                @endforeach
            </div>
        </div>

        <!-- Food List Grid -->
        @if($foods->isEmpty())
            <div class="bg-white rounded-2xl border border-gray-200 p-12 text-center">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                    <i class="fa-solid fa-basket-shopping text-2xl text-gray-400"></i>
                </div>
                <h3 class="text-lg font-bold text-gray-900 mb-1">Belum Ada Makanan Tersedia</h3>
                <p class="text-gray-500">Saat ini tidak ada makanan yang terdaftar untuk diklaim. Cek kembali nanti.</p>
            </div>
        @else
            <div id="foodGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($foods as $food)
                    @php
                        $firstImage = $food->images->first();
                        $imagePath = $firstImage ? asset('storage/' . $firstImage->image) : asset('images/default-food.png');
                    @endphp
                    <div class="food-card bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col hover:shadow-md transition-shadow" 
                        data-title="{{ strtolower($food->title) }}" 
                        data-category="{{ $food->category->name }}">
                        <!-- Image Container -->
                        <div class="h-48 bg-gray-100 relative overflow-hidden">
                            <img src="{{ $imagePath }}" alt="{{ $food->title }}" class="w-full h-full object-cover">
                            <span class="absolute top-3 right-3 px-2.5 py-1 rounded-full text-[10px] font-bold tracking-wide uppercase bg-white/90 text-gray-800 backdrop-blur-sm border border-gray-200 shadow-sm">
                                {{ $food->category->name }}
                            </span>
                        </div>

                        <!-- Card Content -->
                        <div class="p-5 flex-1 flex flex-col justify-between">
                            <div class="space-y-2 mb-4">
                                <span class="text-xs text-gray-500 font-medium flex items-center gap-1.5">
                                    <i class="fa-solid fa-store text-gray-400"></i> {{ $food->donor->donorProfile->store_name ?? 'Donatur' }}
                                </span>
                                <h3 class="font-bold text-lg text-gray-900 line-clamp-1 leading-snug">{{ $food->title }}</h3>
                                <p class="text-xs text-gray-500 line-clamp-2">{{ $food->description ?? 'Tidak ada deskripsi.' }}</p>
                            </div>

                            <div class="space-y-3 pt-3 border-t border-gray-100">
                                <!-- Portion & Price -->
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wider">Tersedia</p>
                                        <p class="font-bold text-gray-900">{{ $food->remaining_quantity }} {{ $food->unit }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] text-gray-500 font-medium uppercase tracking-wider">Harga Penyelamatan</p>
                                        <p class="font-extrabold text-indigo-600 text-lg">Rp {{ number_format($food->final_price, 0, ',', '.') }}</p>
                                    </div>
                                </div>

                                <!-- Deadlines -->
                                <div class="flex items-center gap-1.5 text-xs text-amber-700 bg-amber-50 px-3 py-2 rounded-lg border border-amber-200">
                                    <i class="fa-solid fa-clock"></i>
                                    <span>Tenggat Ambil: {{ $food->pickup_deadline->format('H:i') }} ({{ $food->pickup_deadline->format('d M') }})</span>
                                </div>

                                <!-- Action Link -->
                                <a href="{{ route('user.food.show', $food->id) }}"
                                    class="w-full flex items-center justify-center gap-2 py-2.5 bg-green-600 hover:bg-green-700 text-white font-bold text-sm rounded-xl transition-colors shadow-sm">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</main>

<script>
    let activeCategory = 'All';

    function filterFoods() {
        const query = document.getElementById('searchInput').value.toLowerCase();
        const cards = document.querySelectorAll('.food-card');

        cards.forEach(card => {
            const title = card.getAttribute('data-title');
            const category = card.getAttribute('data-category');
            
            const matchesQuery = title.includes(query);
            const matchesCategory = activeCategory === 'All' || category === activeCategory;

            if (matchesQuery && matchesCategory) {
                card.classList.remove('hidden');
            } else {
                card.classList.add('hidden');
            }
        });
    }

    function filterCategory(category) {
        activeCategory = category;
        
        // Update button active state
        const buttons = document.querySelectorAll('.category-btn');
        buttons.forEach(btn => {
            if (btn.textContent === category || (category === 'All' && btn.textContent === 'Semua')) {
                btn.classList.add('bg-green-600', 'text-white', 'border-green-600');
                btn.classList.remove('bg-gray-50', 'text-gray-700', 'border-gray-200');
            } else {
                btn.classList.remove('bg-green-600', 'text-white', 'border-green-600');
                btn.classList.add('bg-gray-50', 'text-gray-700', 'border-gray-200');
            }
        });

        filterFoods();
    }
</script>
@endsection
