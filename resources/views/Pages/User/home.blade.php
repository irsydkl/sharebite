<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>ShareBite - Hemat Makanan, Hemat Dompet</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS (CDN same as layouts/app.blade.php but for stand-alone pages) -->
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
    
    <!-- FontAwesome for icons -->
    <script src="https://kit.fontawesome.com/05aea6e721.js" crossorigin="anonymous"></script>

    <style>
        body {
            font-family: 'Montserrat', sans-serif;
            background-color: #f8fafc;
        }
    </style>
</head>
<body class="antialiased bg-[#f8f9fa] text-gray-800">

    <!-- Header Navbar -->
    <header class="bg-white border-b border-gray-150 sticky top-0 z-50 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <img src="{{ asset('images/sharebite.png') }}" alt="Logo ShareBite" class="w-9 h-9 object-contain">
                <span class="text-[#0e7a44] font-extrabold text-xl tracking-tight">ShareBite</span>
            </a>
            
            <nav class="flex items-center gap-4 text-sm font-semibold">
                @auth
                    <a href="{{ route(auth()->user()->dashboardRouteName()) }}" class="text-gray-700 hover:text-emerald-600 transition-colors">Dashboard</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-700 hover:text-emerald-600 transition-colors cursor-pointer">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="text-gray-700 hover:text-emerald-600 transition-colors">Masuk</a>
                    <a href="{{ route('register') }}" class="rounded-xl bg-emerald-600 px-4 py-2 text-white hover:bg-emerald-700 transition-all shadow-sm">Daftar</a>
                @endauth
            </nav>
        </div>
    </header>

    <!-- Hero Section -->
    <section class="bg-gradient-to-b from-white to-[#f0fdf4] py-16 md:py-24 border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-emerald-50 text-emerald-700 text-xs font-semibold rounded-full border border-emerald-100 mb-6">
                <i class="fa-solid fa-leaf"></i> Selamatkan Makanan, Lindungi Bumi
            </span>
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight text-gray-900 leading-tight max-w-4xl mx-auto">
                Hemat makanan, <span class="text-emerald-600">hemat dompet</span> Anda
            </h1>
            <p class="mt-6 text-base sm:text-lg md:text-xl text-gray-600 max-w-2xl mx-auto font-medium leading-relaxed">
                Temukan makanan berlebih berkualitas tinggi dari donatur terpercaya dengan harga penyelamatan yang terjangkau.
            </p>
            @guest
                <div class="mt-10 flex justify-center gap-4">
                    <a href="{{ route('register') }}" class="rounded-xl bg-emerald-600 px-6 py-3.5 text-white font-bold hover:bg-emerald-700 transition-all shadow-md hover:shadow-lg">
                        Mulai Bergabung
                    </a>
                    <a href="#explore-food" class="rounded-xl border border-gray-300 bg-white px-6 py-3.5 text-gray-700 font-bold hover:bg-gray-50 transition-all shadow-sm">
                        Jelajahi Makanan
                    </a>
                </div>
            @endguest
        </div>
    </section>

    <!-- Food Dashboard Section -->
    <main id="explore-food" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="mb-10 text-center md:text-left">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-2">Makanan Tersedia Hari Ini 🍲</h2>
            <p class="text-gray-600 font-medium">Jelajahi menu lezat terdekat. Silakan login untuk mengklaim atau melihat detail lengkap.</p>
        </div>

        <!-- Search Bar & Filters -->
        <div class="bg-white p-5 rounded-2xl border border-gray-200 shadow-[0_2px_10px_-4px_rgba(0,0,0,0.05)] mb-10 flex flex-col lg:flex-row items-center justify-between gap-4">
            <div class="flex-1 w-full relative">
                <input type="text" id="searchInput" onkeyup="filterFoods()"
                    class="w-full rounded-xl border border-gray-200 pl-10 pr-4 py-3 text-sm focus:border-emerald-500 focus:outline-none focus:ring-1 focus:ring-emerald-500 transition-all"
                    placeholder="Cari makanan yang ingin diselamatkan (contoh: Nasi, Roti)...">
                <span class="absolute left-3.5 top-3.5 text-gray-400">
                    <i class="fa-solid fa-magnifying-glass"></i>
                </span>
            </div>
            
            <div class="flex items-center gap-2 w-full lg:w-auto overflow-x-auto whitespace-nowrap pb-1 lg:pb-0">
                <button onclick="filterCategory('All')" class="category-btn px-4 py-2.5 rounded-xl text-xs font-bold bg-emerald-600 text-white shadow-sm transition-all border border-emerald-600 cursor-pointer">Semua</button>
                @php
                    $categories = $foods->pluck('category.name')->unique();
                @endphp
                @foreach($categories as $categoryName)
                    <button onclick="filterCategory('{{ $categoryName }}')" class="category-btn px-4 py-2.5 rounded-xl text-xs font-bold bg-gray-50 text-gray-600 hover:bg-gray-100 transition-all border border-gray-200 cursor-pointer">{{ $categoryName }}</button>
                @endforeach
            </div>
        </div>

        <!-- Food List Grid -->
        @if($foods->isEmpty())
            <div class="bg-white rounded-3xl border border-gray-200 p-16 text-center shadow-[0_4px_20px_-4px_rgba(0,0,0,0.02)]">
                <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-6 border border-gray-100">
                    <i class="fa-solid fa-basket-shopping text-3xl text-gray-300"></i>
                </div>
                <h3 class="text-xl font-bold text-gray-900 mb-2">Belum Ada Makanan Tersedia</h3>
                <p class="text-gray-500 font-medium max-w-md mx-auto">Saat ini donatur belum mengunggah makanan baru. Silakan cek kembali nanti atau daftarkan akun Anda terlebih dahulu.</p>
            </div>
        @else
            <div id="foodGrid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($foods as $food)
                    @php
                        $firstImage = $food->images->first();
                        $imagePath = $firstImage ? asset('storage/' . $firstImage->image) : asset('images/default-food.png');
                    @endphp
                    <div class="food-card bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col hover:shadow-md transition-all group" 
                        data-title="{{ strtolower($food->title) }}" 
                        data-category="{{ $food->category->name }}">
                        
                        <!-- Image Container -->
                        <div class="h-52 bg-gray-100 relative overflow-hidden shrink-0">
                            <img src="{{ $imagePath }}" alt="{{ $food->title }}" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-105">
                            <span class="absolute top-4 right-4 px-3 py-1 rounded-full text-[10px] font-bold tracking-wider uppercase bg-white/95 text-gray-800 backdrop-blur-sm border border-gray-150 shadow-sm">
                                {{ $food->category->name }}
                            </span>
                        </div>

                        <!-- Card Content -->
                        <div class="p-6 flex-1 flex flex-col justify-between">
                            <div class="space-y-2.5 mb-5">
                                <span class="text-xs text-gray-500 font-semibold flex items-center gap-1.5">
                                    <i class="fa-solid fa-store text-gray-400"></i> {{ $food->donor->donorProfile->store_name ?? 'Donatur' }}
                                </span>
                                <h3 class="font-bold text-lg text-gray-900 line-clamp-1 leading-snug">{{ $food->title }}</h3>
                                <p class="text-xs text-gray-500 line-clamp-2 leading-relaxed">{{ $food->description ?? 'Tidak ada deskripsi.' }}</p>
                            </div>

                            <div class="space-y-4 pt-4 border-t border-gray-100">
                                <!-- Portion & Price -->
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider">Tersedia</p>
                                        <p class="font-extrabold text-gray-900 text-sm sm:text-base">{{ $food->remaining_quantity }} {{ $food->unit }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[9px] text-gray-400 font-bold uppercase tracking-wider">Harga Penyelamatan</p>
                                        <p class="font-extrabold text-emerald-600 text-base sm:text-lg">Rp {{ number_format($food->final_price, 0, ',', '.') }}</p>
                                    </div>
                                </div>

                                <!-- Deadlines -->
                                <div class="flex items-center gap-2 text-xs text-amber-700 bg-amber-50/75 px-3.5 py-2.5 rounded-xl border border-amber-100 font-semibold">
                                    <i class="fa-solid fa-clock text-amber-600"></i>
                                    <span>Tenggat Ambil: {{ $food->pickup_deadline->format('H:i') }} ({{ $food->pickup_deadline->format('d M') }})</span>
                                </div>

                                <!-- Action Link -->
                                <a href="{{ route('user.food.show', $food->id) }}"
                                    class="w-full flex items-center justify-center gap-2 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm rounded-xl transition-all shadow-sm cursor-pointer">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-gray-200 mt-24 py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6 text-center md:text-left">
            <div class="flex items-center gap-2.5">
                <img src="{{ asset('images/sharebite.png') }}" alt="Logo ShareBite" class="w-8 h-8 object-contain">
                <span class="text-[#0e7a44] font-bold text-lg tracking-tight">ShareBite</span>
            </div>
            <p class="text-sm text-gray-500 font-medium">&copy; {{ date('Y') }} ShareBite. Hak Cipta Dilindungi.</p>
            <div class="flex gap-4 text-gray-400 text-lg">
                <a href="#" class="hover:text-emerald-600 transition-colors"><i class="fa-brands fa-instagram"></i></a>
                <a href="#" class="hover:text-emerald-600 transition-colors"><i class="fa-brands fa-facebook"></i></a>
                <a href="#" class="hover:text-emerald-600 transition-colors"><i class="fa-brands fa-twitter"></i></a>
            </div>
        </div>
    </footer>

    <!-- Filter JS Script -->
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
                const isAllText = (category === 'All' && btn.textContent === 'Semua');
                const isMatchText = btn.textContent === category;

                if (isAllText || isMatchText) {
                    btn.classList.add('bg-emerald-600', 'text-white', 'border-emerald-600');
                    btn.classList.remove('bg-gray-50', 'text-gray-600', 'border-gray-200');
                } else {
                    btn.classList.remove('bg-emerald-600', 'text-white', 'border-emerald-600');
                    btn.classList.add('bg-gray-50', 'text-gray-600', 'border-gray-200');
                }
            });

            filterFoods();
        }
    </script>
</body>
</html>
