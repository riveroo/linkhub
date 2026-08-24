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
                <div class="px-6 mb-6 flex flex-wrap items-center justify-center gap-3">
                    @if(!empty($socials['instagram']))
                        <a href="{{ $socials['instagram'] }}" target="_blank" rel="noopener noreferrer" title="Instagram" class="w-10 h-10 rounded-full bg-slate-100 text-slate-700 hover:text-pink-600 hover:bg-pink-50 border border-slate-200/80 flex items-center justify-center transition-all hover:scale-110">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                            </svg>
                        </a>
                    @endif

                    @if(!empty($socials['tiktok']))
                        <a href="{{ $socials['tiktok'] }}" target="_blank" rel="noopener noreferrer" title="TikTok" class="w-10 h-10 rounded-full bg-slate-100 text-slate-700 hover:text-slate-900 hover:bg-slate-200 border border-slate-200/80 flex items-center justify-center transition-all hover:scale-110">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64c.29 0 .57.04.84.12V9.4a6.27 6.27 0 0 0-1-.08A6.34 6.34 0 0 0 3 15.66a6.34 6.34 0 0 0 10.86 4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-3.04-1.52z"/>
                            </svg>
                        </a>
                    @endif

                    @if(!empty($socials['facebook']))
                        <a href="{{ $socials['facebook'] }}" target="_blank" rel="noopener noreferrer" title="Facebook" class="w-10 h-10 rounded-full bg-slate-100 text-slate-700 hover:text-blue-600 hover:bg-blue-50 border border-slate-200/80 flex items-center justify-center transition-all hover:scale-110">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                            </svg>
                        </a>
                    @endif

                    @if(!empty($socials['x']))
                        <a href="{{ $socials['x'] }}" target="_blank" rel="noopener noreferrer" title="X (Twitter)" class="w-10 h-10 rounded-full bg-slate-100 text-slate-700 hover:text-slate-900 hover:bg-slate-200 border border-slate-200/80 flex items-center justify-center transition-all hover:scale-110">
                            <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                <path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/>
                            </svg>
                        </a>
                    @endif

                    @if(!empty($socials['threads']))
                        <a href="{{ $socials['threads'] }}" target="_blank" rel="noopener noreferrer" title="Threads" class="w-10 h-10 rounded-full bg-slate-100 text-slate-700 hover:text-slate-900 hover:bg-slate-200 border border-slate-200/80 flex items-center justify-center transition-all hover:scale-110">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M12.186 24.007c-3.55 0-6.386-1.178-8.203-3.407-1.748-2.146-2.502-5.185-2.242-9.034.276-4.088 1.956-7.39 4.858-9.544C8.75 0.443 11.83 0 15.424 0c3.784 0 6.953.947 9.167 2.738 2.062 1.668 3.123 4.103 3.068 7.042-.057 3.056-1.22 5.56-3.36 7.243-1.89 1.486-4.398 2.148-7.452 1.97-.246-.014-.492-.03-.738-.048-1.574 1.764-3.528 2.518-5.807 2.24-1.704-.207-3.037-1.096-3.856-2.57-.834-1.503-.9-3.365-.183-5.244.664-1.742 2.063-3.146 3.94-3.953 1.737-.746 3.82-.937 6.195-.568.106-1.558-.225-2.738-1.01-3.606-.827-.914-2.147-1.344-4.04-1.321-1.637.02-3.04.436-4.17 1.237l-.808-1.793c1.472-1.042 3.376-1.583 5.66-1.61 2.553-.03 4.457.6 5.66 1.874 1.157 1.22 1.635 2.923 1.42 5.064-.09 1.002-.09 2.003-.09 3.005 0 2.203.484 3.328 1.453 3.328.706 0 1.547-.48 2.37-1.354 1.052-1.116 1.707-2.678 1.846-4.4.156-1.933-.404-3.52-1.666-4.587-1.558-1.318-3.968-2.023-6.97-2.023-2.915 0-5.385.34-7.143 2.02-1.758 1.68-2.83 4.22-3.02 7.35-.19 3.13.393 5.6 1.742 7.34 1.348 1.74 3.518 2.65 6.455 2.65zm-2.015-7.395c1.408.17 2.66-.27 3.725-1.31.06-.576.095-1.17.106-1.782-1.656-.25-3.06-.11-4.17.41-.95.447-1.597 1.173-1.92 2.157-.324.984-.136 1.84.56 2.525z"/>
                            </svg>
                        </a>
                    @endif

                    @if(!empty($socials['shopee']))
                        <a href="{{ $socials['shopee'] }}" target="_blank" rel="noopener noreferrer" title="Shopee" class="w-10 h-10 rounded-full bg-orange-50 text-orange-600 hover:bg-orange-100 border border-orange-200/80 flex items-center justify-center transition-all hover:scale-110">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M19.8 8.2h-3.3C16.1 5.3 14.3 3 12 3S7.9 5.3 7.5 8.2H4.2c-.7 0-1.2.6-1.2 1.3l1.1 10.8c.1.9.8 1.7 1.7 1.7h12.4c.9 0 1.6-.8 1.7-1.7l1.1-10.8c0-.7-.5-1.3-1.2-1.3zM12 4.6c1.4 0 2.6 1.6 3 3.6H9c.4-2 1.6-3.6 3-3.6zm.1 13.8c-2.3 0-3.7-1.2-3.8-2.6h1.7c.1.6.8 1.1 1.9 1.1 1.2 0 1.8-.6 1.8-1.2 0-.8-.8-1.1-2-1.4-1.8-.4-3.2-1.1-3.2-2.7 0-1.5 1.3-2.6 3.4-2.6 2.1 0 3.3 1.1 3.5 2.4H13.7c-.1-.5-.6-.9-1.6-.9-1 0-1.6.5-1.6 1.1 0 .6.5.9 1.8 1.2 1.9.5 3.4 1.1 3.4 2.8-.1 1.6-1.4 2.8-3.5 2.8z"/>
                            </svg>
                        </a>
                    @endif

                    @if(!empty($socials['tokopedia']))
                        <a href="{{ $socials['tokopedia'] }}" target="_blank" rel="noopener noreferrer" title="Tokopedia" class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 hover:bg-emerald-100 border border-emerald-200/80 flex items-center justify-center transition-all hover:scale-110">
                            <svg class="w-5 h-5 fill-current" viewBox="0 0 24 24">
                                <path d="M20.2 7.7c-.3-.4-.8-.6-1.3-.5l-3 .6c-.7-1.2-1.9-2.1-3.3-2.3V4.2c.6-.2 1-.8 1-1.4 0-.8-.7-1.5-1.5-1.5S10.6 2 10.6 2.8c0 .7.4 1.2 1 1.4v1.3C10.2 5.7 9 6.6 8.3 7.8l-3-.6c-.5-.1-1 .1-1.3.5-.3.4-.4.9-.2 1.4l1.6 4.3C4.5 14.6 4 16.2 4 18c0 3.3 3.6 6 8 6s8-2.7 8-6c0-1.8-.5-3.4-1.4-4.6l1.6-4.3c.3-.5.2-1-.0-1.4zM12 7.5c2.2 0 4 1.8 4 4s-1.8 4-4 4-4-1.8-4-4 1.8-4 4-4zm0 8.5c-1.4 0-2.5-1.1-2.5-2.5S10.6 11 12 11s2.5 1.1 2.5 2.5S13.4 16 12 16z"/>
                            </svg>
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
