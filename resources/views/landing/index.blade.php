<x-layouts.app title="LinkHub — Semua yang ingin Anda bagikan, dalam satu link.">
    <!-- SECTION 1 — NAVBAR -->
    <header class="w-full bg-slate-50/80 backdrop-blur-md sticky top-0 z-40 border-b border-slate-200/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 sm:h-20 flex items-center justify-between">
            <!-- Brand Logo / Name -->
            <a href="{{ route('landing') }}" class="flex items-center gap-2.5 group">
                <div
                    class="w-9 h-9 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-md shadow-indigo-600/20 group-hover:scale-105 transition-transform">
                    L
                </div>
                <span class="text-xl font-bold tracking-tight text-slate-900">LinkHub</span>
            </a>

            <!-- Right Nav Action -->
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('admin.dashboard') }}"
                        class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-all shadow-sm">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 hover:text-indigo-600 hover:bg-slate-100 transition-all">
                        Masuk
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-1 overflow-x-hidden">
        <!-- SECTION 2 — HERO -->
        <section class="py-16 sm:py-20 lg:py-24 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 lg:gap-8 items-center">
                <!-- LEFT COLUMN -->
                <div class="lg:col-span-7 space-y-6 sm:space-y-8 text-left">
                    <div
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100/80">
                        <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                        Solusi Berbagi Link & Etalase Sederhana
                    </div>

                    <h1
                        class="text-3xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-[1.15]">
                        Semua yang ingin Anda bagikan, <span class="text-indigo-600">dalam satu link.</span>
                    </h1>

                    <p class="text-base sm:text-lg text-slate-600 max-w-2xl leading-relaxed font-normal">
                        Buat halaman sederhana untuk membagikan profil, produk, sosial media, dan informasi penting Anda
                        — lalu lihat bagaimana pengunjung berinteraksi dengan link Anda.
                    </p>

                    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 pt-2">
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center px-7 py-3.5 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-600/20 hover:shadow-indigo-600/30">
                            Mulai Sekarang
                            <svg class="w-4 h-4 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </a>
                        <a href="{{ route('login') }}"
                            class="inline-flex items-center justify-center px-6 py-3.5 rounded-xl text-sm font-semibold text-slate-700 border border-slate-200 bg-white hover:bg-slate-50 hover:text-slate-900 transition-all">
                            Masuk
                        </a>
                    </div>

                    <p class="text-xs text-slate-400 font-medium">
                        Kelola link Anda. Bagikan dengan mudah. Pahami performanya.
                    </p>
                </div>

                <!-- RIGHT COLUMN: MOCKUP PUBLIC LINK PAGE -->
                <div class="lg:col-span-5 flex justify-center">
                    <div class="w-full max-w-xs sm:max-w-sm bg-white rounded-[32px] border border-slate-200/90 shadow-xl p-4 space-y-3 transform lg:rotate-1 hover:rotate-0 transition-all duration-300">
                        <!-- Compact Banner Image -->
                        <div class="relative w-full h-24 rounded-xl overflow-hidden bg-emerald-900">
                            <img src="{{ asset('images/mockup.jpg') }}" alt="Natural Beauty Skin Care" class="w-full h-full object-cover opacity-90" />
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent"></div>
                            <div class="absolute top-2.5 left-2.5 text-white">
                                <p class="text-[9px] uppercase font-bold tracking-widest text-emerald-200">Natural Beauty</p>
                                <p class="text-xs font-black tracking-tight drop-shadow-sm">SKIN CARE</p>
                            </div>
                        </div>

                        <!-- Compact Profile Avatar & Store Info -->
                        <div class="text-center space-y-1.5 -mt-9 relative z-10">
                            <div class="w-14 h-14 mx-auto rounded-full border-2 border-white bg-slate-100 shadow-sm overflow-hidden">
                                <img src="{{ asset('images/mockup.jpg') }}" alt="Toko Saya Profile" class="w-full h-full object-cover" />
                            </div>
                            <div>
                                <h3 class="font-black text-slate-900 text-sm">Toko Saya</h3>
                                <p class="text-[11px] text-slate-500 font-medium px-2 leading-tight line-clamp-2">
                                    Skincare 100% original, BPOM, &amp; halal untuk semua jenis kulit.
                                </p>
                            </div>

                            <!-- Compact WhatsApp CTA -->
                            <div class="pt-0.5">
                                <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-[11px] shadow-sm transition-all">
                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                                    </svg>
                                    Hubungi via WhatsApp
                                </a>
                            </div>

                            <!-- Compact Social Icons -->
                            <div class="flex justify-center gap-1.5 pt-1">
                                <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center border border-slate-200/80">
                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                </span>
                                <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center border border-slate-200/80">
                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64c.29 0 .57.04.84.12V9.4a6.27 6.27 0 0 0-1-.08A6.34 6.34 0 0 0 3 15.66a6.34 6.34 0 0 0 10.86 4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-3.04-1.52z"/></svg>
                                </span>
                                <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center border border-slate-200/80">
                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                </span>
                                <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center border border-slate-200/80">
                                    <svg class="w-3 h-3 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                </span>
                            </div>
                        </div>

                        <!-- Compact Category Filter Pills -->
                        <div class="flex items-center justify-center gap-1 pt-0.5 overflow-x-auto">
                            <span class="px-2.5 py-0.5 rounded-full bg-indigo-600 text-white font-bold text-[10px]">All</span>
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 font-semibold text-[10px]">Cleanser</span>
                            <span class="px-2.5 py-0.5 rounded-full bg-slate-100 text-slate-600 font-semibold text-[10px]">Treatment</span>
                        </div>

                        <!-- Compact Product Cards Grid -->
                        <div class="grid grid-cols-2 gap-2 pt-0.5">
                            <div class="bg-white rounded-xl p-2 border border-slate-200/80 shadow-xs flex flex-col justify-between space-y-1">
                                <div class="w-full h-16 bg-slate-50 rounded-lg overflow-hidden flex items-center justify-center">
                                    <img src="{{ asset('images/mockup.jpg') }}" alt="Pelembab" class="w-full h-full object-cover" />
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 text-[11px]">Pelembab</h4>
                                </div>
                                <a href="{{ route('login') }}" class="w-full py-1 text-center rounded-lg bg-indigo-50 text-indigo-700 font-bold text-[10px] hover:bg-indigo-600 hover:text-white transition-colors">
                                    Beli / View
                                </a>
                            </div>

                            <div class="bg-white rounded-xl p-2 border border-slate-200/80 shadow-xs flex flex-col justify-between space-y-1">
                                <div class="w-full h-16 bg-slate-50 rounded-lg overflow-hidden flex items-center justify-center">
                                    <img src="{{ asset('images/mockup.jpg') }}" alt="Face wash" class="w-full h-full object-cover" />
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-900 text-[11px]">Face wash</h4>
                                </div>
                                <a href="{{ route('login') }}" class="w-full py-1 text-center rounded-lg bg-indigo-50 text-indigo-700 font-bold text-[10px] hover:bg-indigo-600 hover:text-white transition-colors">
                                    Beli / View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 3 — CORE FEATURES -->
        <section class="py-16 sm:py-24 bg-white border-y border-slate-200/60 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto space-y-12">
                <div class="text-center max-w-2xl mx-auto space-y-3">
                    <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                        Satu halaman. Banyak kemungkinan.
                    </h2>
                    <p class="text-sm sm:text-base text-slate-600">
                        Kelola semua yang ingin Anda bagikan dari satu tempat.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-8">
                    <!-- CARD 1 -->
                    <div
                        class="bg-slate-50/60 rounded-2xl p-6 sm:p-8 border border-slate-200/80 hover:border-indigo-200 hover:shadow-md transition-all space-y-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Bagikan Satu Link</h3>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Buat satu link yang menjadi pusat semua informasi yang ingin Anda bagikan.
                        </p>
                    </div>

                    <!-- CARD 2 -->
                    <div
                        class="bg-slate-50/60 rounded-2xl p-6 sm:p-8 border border-slate-200/80 hover:border-indigo-200 hover:shadow-md transition-all space-y-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 11h14l1 12H4L5 11z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Tampilkan Produk</h3>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Tambahkan produk, kategori, foto, deskripsi, dan link produk dalam satu halaman.
                        </p>
                    </div>

                    <!-- CARD 3 -->
                    <div
                        class="bg-slate-50/60 rounded-2xl p-6 sm:p-8 border border-slate-200/80 hover:border-indigo-200 hover:shadow-md transition-all space-y-4">
                        <div
                            class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-slate-900">Lihat Analytics</h3>
                        <p class="text-sm text-slate-600 leading-relaxed">
                            Ketahui jumlah kunjungan, klik produk, sumber traffic, dan waktu paling aktif.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 4 — ANALYTICS SHOWCASE -->
        <section class="py-16 sm:py-24 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- LEFT: ANALYTICS MOCKUP -->
                <div class="lg:col-span-6 order-2 lg:order-1">
                    <div class="bg-white rounded-3xl border border-slate-200/90 shadow-xl p-6 space-y-6">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                            <h3 class="font-bold text-slate-900 text-base">Ringkasan Performa</h3>
                            <span class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-600 text-xs font-semibold">7
                                Hari Terakhir</span>
                        </div>

                        <!-- KPI Cards Mockup -->
                        <div class="grid grid-cols-3 gap-3">
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <p class="text-[11px] font-bold text-slate-400 uppercase">Kunjungan</p>
                                <p class="text-lg font-extrabold text-slate-900 mt-1">4.821</p>
                            </div>
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <p class="text-[11px] font-bold text-slate-400 uppercase">Klik Produk</p>
                                <p class="text-lg font-extrabold text-indigo-600 mt-1">2.341</p>
                            </div>
                            <div class="bg-slate-50 p-3 rounded-xl border border-slate-100">
                                <p class="text-[11px] font-bold text-slate-400 uppercase">CTR</p>
                                <p class="text-lg font-extrabold text-emerald-600 mt-1">48,6%</p>
                            </div>
                        </div>

                        <!-- Simple Chart Mockup -->
                        <div class="space-y-2">
                            <p class="text-xs font-bold text-slate-600">Tren Trafik</p>
                            <div
                                class="h-28 bg-indigo-50/40 rounded-xl border border-indigo-100/50 p-3 flex items-end justify-between gap-1.5">
                                <div class="w-full bg-indigo-300 rounded-t h-[40%]"></div>
                                <div class="w-full bg-indigo-400 rounded-t h-[60%]"></div>
                                <div class="w-full bg-indigo-500 rounded-t h-[80%]"></div>
                                <div class="w-full bg-indigo-600 rounded-t h-[100%]"></div>
                                <div class="w-full bg-indigo-500 rounded-t h-[75%]"></div>
                                <div class="w-full bg-indigo-400 rounded-t h-[50%]"></div>
                                <div class="w-full bg-indigo-600 rounded-t h-[90%]"></div>
                            </div>
                        </div>

                        <!-- Sources Progress Mockup -->
                        <div class="space-y-2.5 pt-2">
                            <p class="text-xs font-bold text-slate-600">Sumber Trafik Utama</p>
                            <div class="space-y-2 text-xs">
                                <div>
                                    <div class="flex justify-between font-medium mb-1">
                                        <span class="text-slate-700">Instagram Bio</span>
                                        <span class="text-slate-500 font-semibold">52%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2">
                                        <div class="bg-indigo-600 h-2 rounded-full" style="width: 52%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex justify-between font-medium mb-1">
                                        <span class="text-slate-700">TikTok Bio</span>
                                        <span class="text-slate-500 font-semibold">31%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2">
                                        <div class="bg-indigo-400 h-2 rounded-full" style="width: 31%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- RIGHT: TEXT -->
                <div class="lg:col-span-6 order-1 lg:order-2 space-y-6">
                    <div
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 uppercase tracking-wider">
                        Analytics
                    </div>

                    <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        Jangan hanya membagikan link. Pahami apa yang terjadi setelahnya.
                    </h2>

                    <p class="text-slate-600 text-sm sm:text-base leading-relaxed">
                        Lihat bagaimana link Anda digunakan. Ketahui dari mana pengunjung datang, link mana yang paling
                        banyak dikunjungi, dan produk apa yang paling sering mereka klik.
                    </p>

                    <div class="space-y-3 pt-2">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-sm font-semibold text-slate-700">Pantau jumlah kunjungan secara
                                real-time</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div
                                class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-sm font-semibold text-slate-700">Ketahui sumber traffic dari setiap
                                channel</span>
                        </div>
                        <div class="flex items-center gap-3">
                            <div
                                class="w-5 h-5 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <span class="text-sm font-semibold text-slate-700">Temukan produk yang paling populer</span>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 5 — SOURCE TRACKING -->
        <section class="py-16 sm:py-24 bg-slate-100/70 border-y border-slate-200/60 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto space-y-12">
                <div class="text-center max-w-2xl mx-auto space-y-3">
                    <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                        Ketahui dari mana pengunjung Anda datang.
                    </h2>
                    <p class="text-sm sm:text-base text-slate-600">
                        Bagikan link yang berbeda untuk Instagram, TikTok, WhatsApp, atau campaign tertentu. Analytics
                        akan membantu Anda melihat performa masing-masing sumber.
                    </p>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">
                    <!-- VISUAL TRACKING FLOW -->
                    <div class="lg:col-span-7 space-y-4">
                        <div
                            class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <span class="text-xs font-bold text-slate-700">Instagram Bio</span>
                            <div
                                class="font-mono text-xs text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100 truncate">
                                domain.com/tokosaya<span class="font-bold text-indigo-700">?src=instagram</span>
                            </div>
                        </div>
                        <div
                            class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <span class="text-xs font-bold text-slate-700">TikTok Bio</span>
                            <div
                                class="font-mono text-xs text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100 truncate">
                                domain.com/tokosaya<span class="font-bold text-indigo-700">?src=tiktok</span>
                            </div>
                        </div>
                        <div
                            class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <span class="text-xs font-bold text-slate-700">WhatsApp Broadcast</span>
                            <div
                                class="font-mono text-xs text-indigo-600 bg-indigo-50 px-3 py-1.5 rounded-lg border border-indigo-100 truncate">
                                domain.com/tokosaya<span class="font-bold text-indigo-700">?src=whatsapp</span>
                            </div>
                        </div>
                    </div>

                    <!-- ANALYTICS PREVIEW -->
                    <div class="lg:col-span-5">
                        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm p-6 space-y-4">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Hasil Pelacakan
                                Traffic</h4>
                            <div class="space-y-3">
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-semibold text-slate-800">Instagram</span>
                                    <span class="font-bold text-indigo-600">52%</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-semibold text-slate-800">TikTok</span>
                                    <span class="font-bold text-indigo-600">31%</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-semibold text-slate-800">WhatsApp</span>
                                    <span class="font-bold text-indigo-600">12%</span>
                                </div>
                                <div class="flex items-center justify-between text-sm">
                                    <span class="font-semibold text-slate-800">Lainnya</span>
                                    <span class="font-bold text-slate-400">5%</span>
                                </div>
                            </div>
                            <p class="text-xs text-slate-400 pt-2 text-center border-t border-slate-100">
                                Bagikan link yang tepat. Ukur hasilnya.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 6 — PRODUCT SHOWCASE -->
        <section class="py-16 sm:py-24 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- LEFT TEXT -->
                <div class="lg:col-span-5 space-y-6">
                    <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight leading-tight">
                        Ubah link Anda menjadi etalase sederhana.
                    </h2>
                    <p class="text-sm sm:text-base text-slate-600 leading-relaxed">
                        Tambahkan produk dan kategori ke halaman Anda tanpa perlu membuat website ecommerce yang
                        kompleks.
                    </p>
                </div>

                <!-- RIGHT MOCKUP PAGE -->
                <div class="lg:col-span-7 flex justify-center">
                    <div class="w-full max-w-md bg-white rounded-[36px] border border-slate-200/90 shadow-xl p-6 space-y-5">
                        <!-- Banner Image -->
                        <div class="relative w-full h-36 rounded-2xl overflow-hidden bg-emerald-900">
                            <img src="{{ asset('images/mockup.jpg') }}" alt="Natural Beauty Skin Care" class="w-full h-full object-cover opacity-90" />
                            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/60 via-transparent to-transparent"></div>
                            <div class="absolute top-4 left-4 text-white">
                                <p class="text-[11px] uppercase font-bold tracking-widest text-emerald-200">Natural Beauty</p>
                                <p class="text-sm font-black tracking-tight drop-shadow-sm">SKIN CARE</p>
                            </div>
                        </div>

                        <!-- Store Header Info -->
                        <div class="text-center space-y-2 -mt-11 relative z-10">
                            <div class="w-20 h-20 mx-auto rounded-full border-4 border-white bg-slate-100 shadow-md overflow-hidden">
                                <img src="{{ asset('images/mockup.jpg') }}" alt="Toko Saya Profile" class="w-full h-full object-cover" />
                            </div>
                            <div>
                                <h3 class="font-black text-slate-900 text-lg">Toko Saya</h3>
                                <p class="text-xs text-slate-500 font-medium px-4 mt-1 leading-relaxed">
                                    Menyediakan skincare 100% original, BPOM, dan halal untuk semua jenis kulit.
                                </p>
                            </div>

                            <!-- WhatsApp Button -->
                            <div class="pt-1">
                                <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-2xl bg-emerald-600 text-white font-bold text-xs shadow-md shadow-emerald-600/20">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/></svg>
                                    Hubungi via WhatsApp
                                </a>
                            </div>

                            <!-- Social Icons -->
                            <div class="flex justify-center gap-2 pt-2">
                                <span class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center border border-slate-200/80">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg>
                                </span>
                                <span class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center border border-slate-200/80">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64c.29 0 .57.04.84.12V9.4a6.27 6.27 0 0 0-1-.08A6.34 6.34 0 0 0 3 15.66a6.34 6.34 0 0 0 10.86 4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-3.04-1.52z"/></svg>
                                </span>
                                <span class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center border border-slate-200/80">
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/></svg>
                                </span>
                                <span class="w-8 h-8 rounded-full bg-slate-100 text-slate-700 flex items-center justify-center border border-slate-200/80">
                                    <svg class="w-3.5 h-3.5 fill-current" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                </span>
                            </div>
                        </div>

                        <!-- Category Filter Pills -->
                        <div class="flex items-center justify-center gap-1.5 pt-1 overflow-x-auto">
                            <span class="px-3.5 py-1 rounded-full bg-indigo-600 text-white font-bold text-[11px] shadow-xs">All</span>
                            <span class="px-3.5 py-1 rounded-full bg-slate-100 text-slate-600 font-semibold text-[11px]">Cleanser</span>
                            <span class="px-3.5 py-1 rounded-full bg-slate-100 text-slate-600 font-semibold text-[11px]">Treatment</span>
                            <span class="px-3.5 py-1 rounded-full bg-slate-100 text-slate-600 font-semibold text-[11px]">Moisturize</span>
                        </div>

                        <!-- Product Grid Preview -->
                        <div class="grid grid-cols-2 gap-3.5 pt-1">
                            <div class="bg-white rounded-2xl p-3.5 border border-slate-200/90 shadow-xs flex flex-col justify-between space-y-2">
                                <div class="w-full h-28 bg-slate-50 rounded-xl overflow-hidden flex items-center justify-center">
                                    <img src="{{ asset('images/mockup.jpg') }}" alt="Pelembab" class="w-full h-full object-cover" />
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-slate-900 text-xs">Pelembab</h4>
                                    <p class="text-[10px] text-slate-400">Moisturizer</p>
                                </div>
                                <a href="{{ route('login') }}" class="w-full py-2 text-center rounded-xl bg-indigo-50 text-indigo-700 font-bold text-xs hover:bg-indigo-600 hover:text-white transition-colors">
                                    Beli / View
                                </a>
                            </div>

                            <div class="bg-white rounded-2xl p-3.5 border border-slate-200/90 shadow-xs flex flex-col justify-between space-y-2">
                                <div class="w-full h-28 bg-slate-50 rounded-xl overflow-hidden flex items-center justify-center">
                                    <img src="{{ asset('images/mockup.jpg') }}" alt="Face wash" class="w-full h-full object-cover" />
                                </div>
                                <div>
                                    <h4 class="font-extrabold text-slate-900 text-xs">Face wash</h4>
                                    <p class="text-[10px] text-slate-400">Cleanser</p>
                                </div>
                                <a href="{{ route('login') }}" class="w-full py-2 text-center rounded-xl bg-indigo-50 text-indigo-700 font-bold text-xs hover:bg-indigo-600 hover:text-white transition-colors">
                                    Beli / View
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 7 — HOW IT WORKS -->
        <section class="py-16 sm:py-24 bg-white border-t border-slate-200/60 px-4 sm:px-6 lg:px-8">
            <div class="max-w-7xl mx-auto space-y-12">
                <div class="text-center max-w-2xl mx-auto space-y-3">
                    <h2 class="text-2xl sm:text-4xl font-extrabold text-slate-900 tracking-tight">
                        Mulai dalam beberapa langkah.
                    </h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
                    <!-- STEP 01 -->
                    <div class="bg-slate-50/60 rounded-2xl p-6 border border-slate-200/80 space-y-3">
                        <span class="text-3xl font-black text-indigo-600">01</span>
                        <h3 class="text-base font-bold text-slate-900">Buat Link</h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            Buat halaman link Anda dalam hitungan detik.
                        </p>
                    </div>

                    <!-- STEP 02 -->
                    <div class="bg-slate-50/60 rounded-2xl p-6 border border-slate-200/80 space-y-3">
                        <span class="text-3xl font-black text-indigo-600">02</span>
                        <h3 class="text-base font-bold text-slate-900">Atur Halaman</h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            Tambahkan profil, produk, sosial media, dan informasi lainnya.
                        </p>
                    </div>

                    <!-- STEP 03 -->
                    <div class="bg-slate-50/60 rounded-2xl p-6 border border-slate-200/80 space-y-3">
                        <span class="text-3xl font-black text-indigo-600">03</span>
                        <h3 class="text-base font-bold text-slate-900">Bagikan</h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            Gunakan satu link untuk semua channel Anda.
                        </p>
                    </div>

                    <!-- STEP 04 -->
                    <div class="bg-slate-50/60 rounded-2xl p-6 border border-slate-200/80 space-y-3">
                        <span class="text-3xl font-black text-indigo-600">04</span>
                        <h3 class="text-base font-bold text-slate-900">Lihat Analytics</h3>
                        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed">
                            Pantau kunjungan dan interaksi pengunjung.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- SECTION 8 — FINAL CTA -->
        <section class="py-16 sm:py-20 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto">
            <div
                class="bg-indigo-600 rounded-3xl p-8 sm:p-12 text-center text-white space-y-6 shadow-xl shadow-indigo-600/20">
                <h2 class="text-2xl sm:text-4xl font-extrabold tracking-tight max-w-2xl mx-auto">
                    Semua yang ingin Anda bagikan, dalam satu tempat.
                </h2>
                <p class="text-indigo-100 text-sm sm:text-base max-w-xl mx-auto">
                    Mulai buat halaman link Anda dan bagikan dengan lebih mudah.
                </p>
                <div class="pt-2">
                    <a href="{{ route('login') }}"
                        class="inline-flex items-center justify-center px-8 py-3.5 rounded-xl text-sm font-bold text-indigo-600 bg-white hover:bg-slate-100 transition-all shadow-md">
                        Mulai Sekarang
                    </a>
                </div>
            </div>
        </section>
    </main>

    <!-- SECTION 9 — FOOTER -->
    <footer class="border-t border-slate-200/60 py-10 bg-white px-4 sm:px-6 lg:px-8">
        <div class="max-w-7xl mx-auto flex flex-col sm:flex-row items-center justify-between gap-6">
            <div class="flex items-center gap-2.5">
                <div
                    class="w-7 h-7 rounded-lg bg-indigo-600 flex items-center justify-center text-white font-bold text-sm">
                    L
                </div>
                <span class="text-base font-bold text-slate-900">LinkHub</span>
                <span class="text-xs text-slate-400 ml-2 hidden sm:inline">• Satu link untuk semua yang ingin Anda
                    bagikan.</span>
            </div>

            <a href="{{ route('login') }}"
                class="text-xs font-semibold text-slate-600 hover:text-indigo-600 transition-colors">
                Masuk
            </a>
        </div>
        <div class="max-w-7xl mx-auto mt-6 pt-6 border-t border-slate-100 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} LinkHub. Semua hak dilindungi.
        </div>
    </footer>
</x-layouts.app>