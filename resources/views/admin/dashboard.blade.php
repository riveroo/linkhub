<x-layouts.admin title="Dashboard - LinkHub">
    <div class="space-y-8">
        <!-- 1. WELCOME HEADER -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-6 border-b border-slate-200">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                    Selamat datang kembali, {{ $userName }} 👋
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Berikut ringkasan aktivitas link Anda hari ini.
                </p>
            </div>
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-slate-100/80 text-slate-600 text-xs font-semibold border border-slate-200/80 self-start sm:self-auto">
                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                <span>{{ $currentDateFormatted }}</span>
            </div>
        </div>

        <!-- 2. QUICK STATS (4 KPI Cards) -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
            <!-- Card 1: ACTIVE LINKS -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Active Links</span>
                    <div class="w-9 h-9 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($activeLinksCount) }}</span>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Link aktif</p>
                </div>
            </div>

            <!-- Card 2: PRODUCTS -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Products</span>
                    <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 11h14l1 12H4L5 11z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($productsCount) }}</span>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Produk tersedia</p>
                </div>
            </div>

            <!-- Card 3: VISITS TODAY -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Visits Today</span>
                    <div class="w-9 h-9 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($visitsTodayCount) }}</span>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Kunjungan hari ini</p>
                </div>
            </div>

            <!-- Card 4: CLICKS TODAY -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-xs flex flex-col justify-between">
                <div class="flex items-center justify-between gap-2">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Clicks Today</span>
                    <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl sm:text-3xl font-extrabold text-slate-900 tracking-tight">{{ number_format($clicksTodayCount) }}</span>
                    <p class="text-xs text-slate-500 font-medium mt-0.5">Klik produk hari ini</p>
                </div>
            </div>
        </div>

        <!-- 3. QUICK ACTIONS -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-4">
            <h2 class="text-base font-bold text-slate-900">Yang ingin Anda lakukan?</h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <!-- Action 1: Buat Link -->
                <a href="{{ route('admin.links.index') }}" class="group p-4 rounded-xl border border-slate-200/80 hover:border-indigo-300 hover:bg-indigo-50/50 transition-all flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-indigo-600 text-white flex items-center justify-center shrink-0 font-bold text-lg shadow-sm shadow-indigo-600/20 group-hover:scale-105 transition-transform">
                        +
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 group-hover:text-indigo-600 transition-colors">Buat Link</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Buat halaman link baru.</p>
                    </div>
                </a>

                <!-- Action 2: Kelola Links -->
                <a href="{{ route('admin.links.index') }}" class="group p-4 rounded-xl border border-slate-200/80 hover:border-slate-300 hover:bg-slate-50/80 transition-all flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0 group-hover:bg-slate-200 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 transition-colors">Kelola Links</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Lihat dan atur semua link Anda.</p>
                    </div>
                </a>

                <!-- Action 3: lihat Analytics -->
                <a href="{{ route('admin.analytics.index') }}" class="group p-4 rounded-xl border border-slate-200/80 hover:border-slate-300 hover:bg-slate-50/80 transition-all flex items-start gap-3.5">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 text-slate-700 flex items-center justify-center shrink-0 group-hover:bg-slate-200 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 transition-colors">Lihat Analytics</h3>
                        <p class="text-xs text-slate-500 mt-0.5">Analisis performa link Anda.</p>
                    </div>
                </a>
            </div>
        </div>

        <!-- 4 & 5. MAIN CONTENT GRID (My Links 65% / Recent Activity 35%) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- LEFT COLUMN: MY LINKS (65% / 8 cols) -->
            <div class="lg:col-span-8 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <div>
                        <h2 class="text-base font-bold text-slate-900">Link Saya</h2>
                        <p class="text-xs text-slate-500">Link aktif yang Anda kelola.</p>
                    </div>
                    <a href="{{ route('admin.links.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 transition-colors">
                        Lihat Semua &rarr;
                    </a>
                </div>

                @if($myLinks->isEmpty())
                    <!-- Empty State -->
                    <div class="py-12 text-center space-y-3">
                        <div class="w-12 h-12 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto">
                            <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                            </svg>
                        </div>
                        <div class="space-y-1 max-w-sm mx-auto">
                            <h3 class="text-sm font-bold text-slate-900">Belum ada link.</h3>
                            <p class="text-xs text-slate-500">
                                Buat link pertama Anda untuk mulai membagikan semua yang penting dalam satu halaman.
                            </p>
                        </div>
                        <div class="pt-2">
                            <a href="{{ route('admin.links.index') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-xs font-bold text-white bg-indigo-600 hover:bg-indigo-700 transition-all shadow-xs">
                                + Buat Link
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Links List (Max 5) -->
                    <div class="space-y-3">
                        @foreach($myLinks as $linkItem)
                            <div class="p-4 rounded-xl border border-slate-200/80 hover:border-slate-300 transition-all flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                <div class="space-y-1 min-w-0">
                                    <div class="flex items-center gap-2">
                                        <h3 class="font-bold text-sm text-slate-900 truncate">{{ $linkItem->title }}</h3>
                                        @if($linkItem->status === 'active')
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 text-[11px] font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Aktif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 text-[11px] font-semibold">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                Tidak Aktif
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-xs text-slate-500 font-mono truncate">
                                        {{ url('/' . $linkItem->slug) }}
                                    </p>
                                    <div class="flex items-center gap-1.5 text-xs text-slate-500 pt-1">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                        <span>{{ number_format($linkItem->visits_today ?? 0) }} kunjungan hari ini</span>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="flex items-center gap-2 shrink-0 self-end sm:self-auto">
                                    <a href="{{ url('/' . $linkItem->slug) }}" target="_blank" class="px-3 py-1.5 rounded-lg border border-slate-200 text-xs font-semibold text-slate-700 hover:bg-slate-50 transition-colors">
                                        Buka ↗
                                    </a>
                                    <a href="{{ route('admin.links.edit-page', $linkItem) }}" class="px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 text-xs font-bold hover:bg-indigo-100 transition-colors">
                                        Atur
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- RIGHT COLUMN: RECENT ACTIVITY (35% / 4 cols) -->
            <div class="lg:col-span-4 bg-white p-6 rounded-2xl border border-slate-200/80 shadow-xs space-y-6">
                <div class="flex items-center justify-between pb-4 border-b border-slate-100">
                    <h2 class="text-base font-bold text-slate-900">Aktivitas Terbaru</h2>
                    <a href="{{ route('admin.analytics.index') }}" class="text-xs font-bold text-indigo-600 hover:text-indigo-700 transition-colors">
                        Analytics &rarr;
                    </a>
                </div>

                @if($recentActivities->isEmpty())
                    <div class="py-8 text-center text-xs text-slate-400 italic">
                        Belum ada aktivitas tercatat.
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($recentActivities as $activity)
                            <div class="flex items-start gap-3">
                                <div class="w-2.5 h-2.5 rounded-full mt-1.5 shrink-0 {{ $activity->event_type === 'product_click' ? 'bg-purple-500' : 'bg-blue-500' }}"></div>
                                <div class="space-y-0.5 text-xs min-w-0 flex-1">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="font-bold text-slate-900">
                                            {{ $activity->event_type === 'product_click' ? 'Klik produk' : 'Kunjungan' }}
                                        </span>
                                        <span class="text-[11px] text-slate-400 shrink-0 font-mono">
                                            {{ $activity->clicked_at ? $activity->clicked_at->format('H:i') : '' }}
                                        </span>
                                    </div>
                                    <p class="text-slate-600 font-medium truncate">
                                        @if($activity->event_type === 'product_click')
                                            {{ $activity->product?->name ?? 'Produk' }} &middot; {{ $activity->link?->title ?? 'Link' }}
                                        @else
                                            {{ $activity->link?->title ?? 'Link' }}
                                        @endif
                                        &middot; {{ $activity->source?->name ?? 'Direct' }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.admin>
