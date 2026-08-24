@php
    $storeName = $page->store_name ?? $link->title;
    $bio = $page->bio ?? null;
    $coverType = $page->cover_type ?? 'color';
    $coverColor = $page->cover_color ?? '#4f46e5';
    $coverImage = $page->cover_image ?? null;
    $profilePhoto = $page->profile_photo ?? null;
    $headerAlignment = $page->header_alignment ?? 'center';
    $productLayout = $page->product_layout ?? 'grid';
    $metaDescription = !empty($bio) ? $bio : 'Visit ' . $storeName . ' on LinkHub.';
    $ogImage = $profilePhoto ?? $coverImage ?? asset('images/og-default.png');
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Primary SEO Meta Tags -->
    <title>{{ $storeName }}</title>
    <meta name="title" content="{{ $storeName }}">
    <meta name="description" content="{{ $metaDescription }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="profile">
    <meta property="og:url" content="{{ url('/' . $link->slug) }}">
    <meta property="og:title" content="{{ $storeName }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    @if($ogImage)
        <meta property="og:image" content="{{ $ogImage }}">
    @endif

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url('/' . $link->slug) }}">
    <meta property="twitter:title" content="{{ $storeName }}">
    <meta property="twitter:description" content="{{ $metaDescription }}">
    @if($ogImage)
        <meta property="twitter:image" content="{{ $ogImage }}">
    @endif

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans antialiased text-slate-900 bg-slate-100 flex flex-col items-center">
    <!-- Main Content Shell (Mobile-First Max Width Container) -->
    <div class="w-full max-w-xl min-h-screen bg-white shadow-md border-x border-slate-200/80 flex flex-col justify-between">
        <div>
            <!-- Cover Banner -->
            <div 
                class="w-full h-36 sm:h-44 relative bg-indigo-600 transition-colors"
                style="{{ $coverType === 'image' && $coverImage ? "background-image: url('{$coverImage}'); background-size: cover; background-position: center;" : "background-color: {$coverColor};" }}"
            >
            </div>

            <!-- Profile Info Header -->
            <div class="px-6 -mt-12 sm:-mt-14 mb-6 relative z-10 {{ $headerAlignment === 'left' ? 'text-left flex items-end gap-4' : 'text-center' }}">
                <!-- Avatar -->
                <div class="w-24 h-24 sm:w-28 sm:h-28 rounded-full border-4 border-white bg-white shadow-md overflow-hidden shrink-0 {{ $headerAlignment === 'center' ? 'mx-auto' : '' }}">
                    @if($profilePhoto)
                        <img src="{{ $profilePhoto }}" alt="{{ $storeName }}" class="w-full h-full object-cover" />
                    @else
                        <div class="w-full h-full bg-indigo-600 text-white font-extrabold text-2xl flex items-center justify-center">
                            {{ strtoupper(substr($storeName, 0, 2)) }}
                        </div>
                    @endif
                </div>

                <!-- Store Name & Bio -->
                <div class="{{ $headerAlignment === 'left' ? 'pb-2' : 'mt-3' }}">
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        {{ $storeName }}
                    </h1>
                    @if($bio)
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed mt-1 max-w-md {{ $headerAlignment === 'center' ? 'mx-auto' : '' }}">
                            {{ $bio }}
                        </p>
                    @endif
                </div>
            </div>

            <!-- WhatsApp CTA Button -->
            @if($normalizedWa)
                <div class="px-6 mb-5">
                    <a 
                        href="https://wa.me/{{ $normalizedWa }}" 
                        target="_blank" 
                        rel="noopener noreferrer"
                        class="w-full inline-flex items-center justify-center gap-2.5 py-3 px-5 rounded-2xl bg-emerald-600 text-white font-bold text-sm shadow-md shadow-emerald-600/20 hover:bg-emerald-700 transition-all hover:scale-[1.01] active:scale-[0.99]"
                    >
                        <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                            <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                        </svg>
                        Hubungi via WhatsApp
                    </a>
                </div>
            @endif

            <!-- Social Media Links Row -->
            @if(!empty(array_filter($socials)))
                <div class="px-6 mb-6 flex flex-wrap items-center justify-center gap-2.5">
                    @if(!empty($socials['instagram']))
                        <a href="{{ $socials['instagram'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-700 hover:text-pink-600 hover:bg-pink-50 border border-slate-200/80 text-xs font-semibold transition-colors">
                            <span class="font-bold">Instagram</span>
                        </a>
                    @endif

                    @if(!empty($socials['tiktok']))
                        <a href="{{ $socials['tiktok'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-700 hover:text-slate-900 hover:bg-slate-200 border border-slate-200/80 text-xs font-semibold transition-colors">
                            <span class="font-bold">TikTok</span>
                        </a>
                    @endif

                    @if(!empty($socials['facebook']))
                        <a href="{{ $socials['facebook'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-700 hover:text-blue-600 hover:bg-blue-50 border border-slate-200/80 text-xs font-semibold transition-colors">
                            <span class="font-bold">Facebook</span>
                        </a>
                    @endif

                    @if(!empty($socials['x']))
                        <a href="{{ $socials['x'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-700 hover:text-slate-900 hover:bg-slate-200 border border-slate-200/80 text-xs font-semibold transition-colors">
                            <span class="font-bold">X (Twitter)</span>
                        </a>
                    @endif

                    @if(!empty($socials['threads']))
                        <a href="{{ $socials['threads'] }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-full bg-slate-100 text-slate-700 hover:text-slate-900 hover:bg-slate-200 border border-slate-200/80 text-xs font-semibold transition-colors">
                            <span class="font-bold">Threads</span>
                        </a>
                    @endif
                </div>
            @endif

            <!-- Products Catalog Section with Interactive Category Filter -->
            @if($products->count() > 0)
                <div class="px-6 mb-8" x-data="{ selectedCategory: 'all' }">
                    <div class="mb-3 flex items-center justify-between pb-2 border-b border-slate-100">
                        <h2 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Katalog Produk</h2>
                        <span class="text-xs font-semibold text-slate-400">{{ $products->count() }} Produk</span>
                    </div>

                    <!-- Category Filter Pills Bar -->
                    @if($categories->count() > 0)
                        <div class="mb-4 flex items-center gap-1.5 overflow-x-auto pb-1.5 scrollbar-none">
                            <button 
                                type="button" 
                                @click="selectedCategory = 'all'" 
                                class="px-3 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all shrink-0"
                                :class="selectedCategory === 'all' ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                            >
                                Semua ({{ $products->count() }})
                            </button>
                            @foreach($categories as $category)
                                @php
                                    $catCount = $products->where('category_id', $category->id)->count();
                                @endphp
                                @if($catCount > 0)
                                    <button 
                                        type="button" 
                                        @click="selectedCategory = {{ $category->id }}" 
                                        class="px-3 py-1.5 rounded-full text-xs font-semibold whitespace-nowrap transition-all shrink-0"
                                        :class="selectedCategory === {{ $category->id }} ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-600/20' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'"
                                    >
                                        {{ $category->name }} ({{ $catCount }})
                                    </button>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    @if($productLayout === 'grid')
                        <!-- GRID LAYOUT (2 Columns) -->
                        <div class="grid grid-cols-2 gap-3 sm:gap-4">
                            @foreach($products as $product)
                                <div 
                                    x-show="selectedCategory === 'all' || selectedCategory === {{ $product->category_id }}"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    class="bg-white rounded-2xl border border-slate-200/80 p-3 shadow-sm hover:shadow-md transition-shadow flex flex-col justify-between group"
                                >
                                    <div>
                                        <!-- Product Image -->
                                        <div class="w-full aspect-square bg-slate-100 rounded-xl overflow-hidden mb-2.5 relative">
                                            @if($product->image_url)
                                                <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-slate-300 font-extrabold text-sm">
                                                    NO IMAGE
                                                </div>
                                            @endif

                                            <!-- Category Badge -->
                                            @if($product->category)
                                                <span class="absolute top-2 left-2 px-2 py-0.5 rounded-md bg-slate-900/70 backdrop-blur-xs text-white text-[10px] font-semibold">
                                                    {{ $product->category->name }}
                                                </span>
                                            @endif
                                        </div>

                                        <!-- Product Title -->
                                        <h3 class="font-bold text-xs sm:text-sm text-slate-900 leading-snug line-clamp-2">
                                            {{ $product->name }}
                                        </h3>

                                        <!-- Product Description -->
                                        @if($product->description)
                                            <p class="text-[11px] text-slate-500 leading-relaxed line-clamp-2 mt-1">
                                                {{ $product->description }}
                                            </p>
                                        @endif
                                    </div>

                                    <!-- CTA Button (routes through tracking endpoint) -->
                                    <a 
                                        href="{{ route('public.product.click', ['link' => $link, 'product' => $product]) }}" 
                                        class="mt-3 w-full inline-flex items-center justify-center gap-1 py-2 px-3 rounded-xl bg-indigo-50 text-indigo-700 font-bold text-xs hover:bg-indigo-600 hover:text-white transition-colors"
                                    >
                                        Lihat Produk
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                        </svg>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <!-- LIST LAYOUT (1 Column Horizontal Cards) -->
                        <div class="space-y-3">
                            @foreach($products as $product)
                                <div 
                                    x-show="selectedCategory === 'all' || selectedCategory === {{ $product->category_id }}"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="opacity-0 scale-95"
                                    x-transition:enter-end="opacity-100 scale-100"
                                    class="bg-white rounded-2xl border border-slate-200/80 p-3 shadow-sm hover:shadow-md transition-shadow flex items-center gap-3.5 group"
                                >
                                    <!-- Product Thumbnail -->
                                    <div class="w-20 h-20 sm:w-24 sm:h-24 bg-slate-100 rounded-xl overflow-hidden shrink-0 relative">
                                        @if($product->image_url)
                                            <img src="{{ $product->image_url }}" alt="{{ $product->name }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
                                        @else
                                            <div class="w-full h-full flex items-center justify-center text-slate-300 font-extrabold text-xs">
                                                NO IMG
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Product Details -->
                                    <div class="flex-1 min-w-0">
                                        @if($product->category)
                                            <span class="inline-block px-2 py-0.5 rounded-md bg-slate-100 text-slate-600 text-[10px] font-semibold mb-1">
                                                {{ $product->category->name }}
                                            </span>
                                        @endif
                                        <h3 class="font-bold text-xs sm:text-sm text-slate-900 truncate">
                                            {{ $product->name }}
                                        </h3>
                                        @if($product->description)
                                            <p class="text-[11px] text-slate-500 truncate mt-0.5">
                                                {{ $product->description }}
                                            </p>
                                        @endif

                                        <a 
                                            href="{{ route('public.product.click', ['link' => $link, 'product' => $product]) }}" 
                                            class="mt-2 inline-flex items-center gap-1 text-xs font-bold text-indigo-600 hover:text-indigo-700 hover:underline"
                                        >
                                            Lihat Produk &rarr;
                                        </a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Footer -->
        <footer class="py-6 px-6 text-center border-t border-slate-100 mt-auto">
            <a 
                href="{{ route('landing') }}" 
                class="inline-flex items-center gap-1 text-xs font-semibold text-slate-400 hover:text-indigo-600 transition-colors"
            >
                Powered by <span class="font-bold text-slate-600 hover:text-indigo-600">LinkHub</span>
            </a>
        </footer>
    </div>
</body>
</html>
