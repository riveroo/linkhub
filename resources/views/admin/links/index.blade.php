<x-layouts.admin title="Links - LinkHub">
    <div x-data="{
        createOpen: {{ $errors->any() && !old('_method') ? 'true' : 'false' }},
        editOpen: {{ $errors->any() && old('_method') === 'PUT' ? 'true' : 'false' }},
        deleteOpen: false,
        toastOpen: false,
        toastMessage: '',
        activeLink: { id: null, title: '', url: '', slug: '' },
        createTitle: '{{ old('title', '') }}',
        createSlug: '{{ old('slug', '') }}',
        createSlugManual: false,
        editTitle: '{{ old('_method') === 'PUT' ? old('title', '') : '' }}',
        editSlug: '{{ old('_method') === 'PUT' ? old('slug', '') : '' }}',
        editActionUrl: '{{ old('_method') === 'PUT' && session('edit_link_id') ? route('admin.links.update', session('edit_link_id')) : '' }}',
        
        slugify(text) {
            return text.toString().toLowerCase().trim()
                .replace(/\s+/g, '-')           // Replace spaces with -
                .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
                .replace(/\-\-+/g, '-');        // Replace multiple - with single -
        },
        
        onCreateTitleInput() {
            if (!this.createSlugManual) {
                this.createSlug = this.slugify(this.createTitle);
            }
        },

        openEditModal(link) {
            this.activeLink = link;
            this.editTitle = link.title;
            this.editSlug = link.slug;
            this.editActionUrl = '/admin/links/' + link.id;
            this.editOpen = true;
        },

        openDeleteModal(link) {
            this.activeLink = link;
            this.deleteOpen = true;
        },

        copyToClipboard(text) {
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text).then(() => {
                    this.showToast('Link copied!');
                }).catch(() => {
                    this.fallbackCopyText(text);
                });
            } else {
                this.fallbackCopyText(text);
            }
        },

        fallbackCopyText(text) {
            let textArea = document.createElement('textarea');
            textArea.value = text;
            textArea.style.position = 'fixed';
            textArea.style.left = '-999999px';
            document.body.appendChild(textArea);
            textArea.focus();
            textArea.select();
            try {
                document.execCommand('copy');
                this.showToast('Link copied!');
            } catch (err) {
                console.error('Copy failed', err);
            }
            document.body.removeChild(textArea);
        },

        showToast(message) {
            this.toastMessage = message;
            this.toastOpen = true;
            setTimeout(() => { this.toastOpen = false; }, 3000);
        },

        init() {
            @if(session('success'))
                this.showToast('{{ session('success') }}');
            @endif
        }
    }">
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                    Links
                </h1>
                <p class="mt-1 text-sm text-slate-500">
                    Manage your shared links.
                </p>
            </div>
            <div>
                <button 
                    type="button"
                    @click="createOpen = true"
                    class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold text-sm shadow-md shadow-indigo-600/20 hover:bg-indigo-700 hover:shadow-indigo-600/30 transition-all focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Link
                </button>
            </div>
        </div>

        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <!-- Total Links -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between text-slate-500 text-xs font-semibold uppercase tracking-wider">
                    <span>Total Links</span>
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ $totalLinksCount }}</span>
                </div>
            </div>

            <!-- Active Links -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between text-slate-500 text-xs font-semibold uppercase tracking-wider">
                    <span>Active</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl font-extrabold text-emerald-600 tracking-tight">{{ $activeLinksCount }}</span>
                </div>
            </div>

            <!-- Inactive Links -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between text-slate-500 text-xs font-semibold uppercase tracking-wider">
                    <span>Inactive</span>
                    <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl font-extrabold text-slate-600 tracking-tight">{{ $inactiveLinksCount }}</span>
                </div>
            </div>

            <!-- Total Clicks -->
            <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm">
                <div class="flex items-center justify-between text-slate-500 text-xs font-semibold uppercase tracking-wider">
                    <span>Total Clicks</span>
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                        </svg>
                    </div>
                </div>
                <div class="mt-3">
                    <span class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ number_format($totalClicksCount) }}</span>
                </div>
            </div>
        </div>

        <!-- Search & Filter Controls -->
        <div class="bg-white p-4 rounded-2xl border border-slate-200/80 shadow-sm mb-6">
            <form method="GET" action="{{ route('admin.links.index') }}" class="flex flex-col sm:flex-row gap-3">
                <!-- Search Input -->
                <div class="relative flex-1">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <input 
                        type="text" 
                        name="search" 
                        value="{{ $search }}" 
                        placeholder="Search links..." 
                        class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all"
                    />
                </div>

                <!-- Status Filter Dropdown -->
                <div class="sm:w-48">
                    <select 
                        name="status" 
                        onchange="this.form.submit()" 
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm text-slate-700 bg-white focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all"
                    >
                        <option value="all" {{ $status === 'all' || empty($status) ? 'selected' : '' }}>All Status</option>
                        <option value="active" {{ $status === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <!-- Filter Submit Button / Reset -->
                <div class="flex gap-2">
                    <button type="submit" class="px-4 py-2.5 rounded-xl bg-slate-900 text-white font-medium text-sm hover:bg-slate-800 transition-colors">
                        Filter
                    </button>
                    @if(!empty($search) || (!empty($status) && $status !== 'all'))
                        <a href="{{ route('admin.links.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-100 text-slate-600 font-medium text-sm hover:bg-slate-200 transition-colors flex items-center justify-center">
                            Reset
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- Links Table / Empty States -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm">
            @if($totalLinksCount === 0)
                <!-- Empty State: No links created yet -->
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-indigo-50 text-indigo-600 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">No links yet</h3>
                    <p class="mt-1 text-sm text-slate-500 max-w-sm mx-auto">Create your first link to get started.</p>
                    <div class="mt-6">
                        <button 
                            type="button"
                            @click="createOpen = true"
                            class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold text-sm shadow-md shadow-indigo-600/20 hover:bg-indigo-700 transition-all"
                        >
                            + Create Link
                        </button>
                    </div>
                </div>
            @elseif($links->count() === 0)
                <!-- Empty State: Search or filter returned no matches -->
                <div class="p-12 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 text-slate-400 flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900">No links found</h3>
                    <p class="mt-1 text-sm text-slate-500 max-w-sm mx-auto">Try changing your search or filter criteria.</p>
                    <div class="mt-6">
                        <a href="{{ route('admin.links.index') }}" class="inline-flex items-center px-4 py-2 rounded-xl bg-slate-100 text-slate-700 font-semibold text-sm hover:bg-slate-200 transition-colors">
                            Clear Filters
                        </a>
                    </div>
                </div>
            @else
                <!-- Responsive Table -->
                <div class="overflow-x-auto sm:overflow-visible min-h-[220px]">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-100 bg-slate-50/50 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                                <th class="py-3.5 px-6">Title</th>
                                <th class="py-3.5 px-6">Short Link</th>
                                <th class="py-3.5 px-6">Clicks</th>
                                <th class="py-3.5 px-6">Status</th>
                                <th class="py-3.5 px-6">Created</th>
                                <th class="py-3.5 px-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-sm">
                            @foreach($links as $link)
                                @php
                                    $appUrlHost = parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost';
                                    $shortPath = '/' . $link->slug;
                                    $fullShortUrl = url('/' . $link->slug);
                                    $displayShortUrl = $appUrlHost . $shortPath;
                                @endphp
                                <tr class="hover:bg-slate-50/80 transition-colors" x-data="{ menuOpen: false }" :class="{ 'relative z-30': menuOpen }">
                                    <!-- Title -->
                                    <td class="py-4 px-6 font-semibold text-slate-900">
                                        {{ $link->title }}
                                    </td>

                                    <!-- Short Link -->
                                    <td class="py-4 px-6">
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-indigo-50 text-indigo-700 border border-indigo-100">
                                            {{ $displayShortUrl }}
                                        </span>
                                    </td>

                                    <!-- Clicks Counter -->
                                    <td class="py-4 px-6 font-semibold text-slate-700 whitespace-nowrap">
                                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-bold">
                                            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                                            </svg>
                                            {{ number_format($link->clicks) }}
                                        </span>
                                    </td>

                                    <!-- Status -->
                                    <td class="py-4 px-6">
                                        @if($link->status === 'active')
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-600 border border-slate-200">
                                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                                Inactive
                                            </span>
                                        @endif
                                    </td>

                                    <!-- Created Date -->
                                    <td class="py-4 px-6 text-slate-500 text-xs whitespace-nowrap">
                                        {{ $link->created_at->format('M d, Y') }}
                                    </td>

                                    <!-- Actions Dropdown -->
                                    <td class="py-4 px-6 text-right relative">
                                        <button 
                                            type="button"
                                            @click.stop="menuOpen = !menuOpen" 
                                            @click.away="menuOpen = false"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 transition-colors focus:outline-none"
                                        >
                                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                                            </svg>
                                        </button>

                                        <!-- Dropdown Menu -->
                                        <div 
                                            x-show="menuOpen" 
                                            x-transition:enter="transition ease-out duration-100"
                                            x-transition:enter-start="transform opacity-0 scale-95"
                                            x-transition:enter-end="transform opacity-100 scale-100"
                                            x-transition:leave="transition ease-in duration-75"
                                            x-transition:leave-start="transform opacity-100 scale-100"
                                            x-transition:leave-end="transform opacity-0 scale-95"
                                            class="absolute right-6 top-full mt-1 w-48 bg-white rounded-xl shadow-xl border border-slate-200 py-1.5 z-50 text-left"
                                            x-cloak
                                        >
                                            <!-- Atur Halaman -->
                                            <a 
                                                href="{{ route('admin.links.edit-page', $link) }}" 
                                                class="w-full flex items-center gap-2.5 px-3.5 py-2 text-xs font-semibold text-indigo-600 hover:bg-indigo-50 transition-colors"
                                            >
                                                <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                                                </svg>
                                                Atur Halaman
                                            </a>

                                            <div class="border-t border-slate-100 my-1"></div>

                                            <!-- Copy Link -->
                                            <button 
                                                type="button"
                                                @click="copyToClipboard('{{ $fullShortUrl }}'); menuOpen = false" 
                                                class="w-full flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 transition-colors"
                                            >
                                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                                                </svg>
                                                Copy Link
                                            </button>

                                            <!-- Open Link -->
                                            <a 
                                                href="{{ url('/' . $link->slug) }}" 
                                                target="_blank" 
                                                rel="noopener noreferrer" 
                                                @click="menuOpen = false"
                                                class="w-full flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 transition-colors"
                                            >
                                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                                </svg>
                                                Open Link
                                            </a>

                                            <!-- Edit -->
                                            <button 
                                                type="button"
                                                @click="openEditModal({{ json_encode($link) }}); menuOpen = false" 
                                                class="w-full flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 transition-colors"
                                            >
                                                <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                                Edit
                                            </button>

                                            <!-- Enable / Disable -->
                                            <form method="POST" action="{{ route('admin.links.toggle-status', $link) }}">
                                                @csrf
                                                @method('PATCH')
                                                <button 
                                                    type="submit" 
                                                    class="w-full flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-slate-700 hover:bg-slate-50 transition-colors"
                                                >
                                                    @if($link->status === 'active')
                                                        <svg class="w-4 h-4 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                        </svg>
                                                        Disable
                                                    @else
                                                        <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                        </svg>
                                                        Enable
                                                    @endif
                                                </button>
                                            </form>

                                            <div class="border-t border-slate-100 my-1"></div>

                                            <!-- Delete -->
                                            <button 
                                                type="button"
                                                @click="openDeleteModal({{ json_encode($link) }}); menuOpen = false" 
                                                class="w-full flex items-center gap-2.5 px-3.5 py-2 text-xs font-medium text-red-600 hover:bg-red-50 transition-colors"
                                            >
                                                <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                @if($links->hasPages())
                    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                        {{ $links->links() }}
                    </div>
                @endif
            @endif
        </div>

        <!-- CREATE LINK MODAL -->
        <div 
            x-show="createOpen" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto"
            x-cloak
        >
            <!-- Backdrop -->
            <div 
                @click="createOpen = false" 
                x-show="createOpen" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            ></div>

            <!-- Modal Panel Card -->
            <div 
                x-show="createOpen" 
                @click.stop
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative z-10 w-full max-w-lg bg-white rounded-2xl text-left overflow-hidden shadow-2xl border border-slate-200 my-auto"
            >
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900">Create Link</h3>
                    <button type="button" @click="createOpen = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" action="{{ route('admin.links.store') }}" class="p-6 space-y-5">
                    @csrf

                    <!-- Title -->
                    <div>
                        <label for="create_title" class="block text-sm font-semibold text-slate-700">Title *</label>
                        <input 
                            id="create_title" 
                            type="text" 
                            name="title" 
                            x-model="createTitle"
                            @input="onCreateTitleInput()"
                            required 
                            placeholder="My Portfolio" 
                            class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all"
                        />
                        @error('title')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div>
                        <label for="create_slug" class="block text-sm font-semibold text-slate-700">Slug *</label>
                        <div class="mt-1 flex rounded-xl border border-slate-200 overflow-hidden focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all">
                            <span class="inline-flex items-center px-3 text-xs text-slate-400 bg-slate-50 border-r border-slate-200 select-none">
                                {{ parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost' }}/p/
                            </span>
                            <input 
                                id="create_slug" 
                                type="text" 
                                name="slug" 
                                x-model="createSlug"
                                @input="createSlugManual = true"
                                required 
                                placeholder="my-portfolio" 
                                class="w-full px-3 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none"
                            />
                        </div>
                        @error('slug')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="pt-3 flex items-center justify-end gap-3">
                        <button 
                            type="button" 
                            @click="createOpen = false" 
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold text-sm hover:bg-indigo-700 transition-all shadow-md shadow-indigo-600/20"
                        >
                            Create Link
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- EDIT LINK MODAL -->
        <div 
            x-show="editOpen" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto"
            x-cloak
        >
            <!-- Backdrop -->
            <div 
                @click="editOpen = false" 
                x-show="editOpen" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            ></div>

            <!-- Modal Panel Card -->
            <div 
                x-show="editOpen" 
                @click.stop
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative z-10 w-full max-w-lg bg-white rounded-2xl text-left overflow-hidden shadow-2xl border border-slate-200 my-auto"
            >
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="text-lg font-bold text-slate-900">Edit Link</h3>
                    <button type="button" @click="editOpen = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form method="POST" :action="editActionUrl" class="p-6 space-y-5">
                    @csrf
                    @method('PUT')

                    <!-- Title -->
                    <div>
                        <label for="edit_title" class="block text-sm font-semibold text-slate-700">Title *</label>
                        <input 
                            id="edit_title" 
                            type="text" 
                            name="title" 
                            x-model="editTitle"
                            required 
                            class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all"
                        />
                        @error('title')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Slug -->
                    <div>
                        <label for="edit_slug" class="block text-sm font-semibold text-slate-700">Slug *</label>
                        <div class="mt-1 flex rounded-xl border border-slate-200 overflow-hidden focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-500/20 transition-all">
                            <span class="inline-flex items-center px-3 text-xs text-slate-400 bg-slate-50 border-r border-slate-200 select-none">
                                {{ parse_url(config('app.url'), PHP_URL_HOST) ?? 'localhost' }}/p/
                            </span>
                            <input 
                                id="edit_slug" 
                                type="text" 
                                name="slug" 
                                x-model="editSlug"
                                required 
                                class="w-full px-3 py-2.5 text-sm text-slate-900 placeholder-slate-400 focus:outline-none"
                            />
                        </div>
                        @error('slug')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="pt-3 flex items-center justify-end gap-3">
                        <button 
                            type="button" 
                            @click="editOpen = false" 
                            class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors"
                        >
                            Cancel
                        </button>
                        <button 
                            type="submit" 
                            class="px-5 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold text-sm hover:bg-indigo-700 transition-all shadow-md shadow-indigo-600/20"
                        >
                            Update Link
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- DELETE CONFIRMATION MODAL -->
        <div 
            x-show="deleteOpen" 
            class="fixed inset-0 z-50 flex items-center justify-center p-4 sm:p-6 overflow-y-auto"
            x-cloak
        >
            <!-- Backdrop -->
            <div 
                @click="deleteOpen = false" 
                x-show="deleteOpen" 
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0"
                x-transition:enter-end="opacity-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0"
                class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity"
            ></div>

            <!-- Modal Panel Card -->
            <div 
                x-show="deleteOpen" 
                @click.stop
                x-transition:enter="ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95"
                x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="ease-in duration-200"
                x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="relative z-10 w-full max-w-md bg-white rounded-2xl text-left overflow-hidden shadow-2xl border border-slate-200 p-6 my-auto"
            >
                <div class="w-12 h-12 rounded-full bg-red-50 text-red-500 flex items-center justify-center mb-4">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>

                <h3 class="text-lg font-bold text-slate-900">Delete Link?</h3>
                <p class="mt-2 text-sm text-slate-500">
                    Are you sure you want to delete <span class="font-semibold text-slate-900" x-text="`&quot;${activeLink.title}&quot;`"></span>? This action cannot be undone.
                </p>

                <div class="mt-6 flex items-center justify-end gap-3">
                    <button 
                        type="button" 
                        @click="deleteOpen = false" 
                        class="px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors"
                    >
                        Cancel
                    </button>
                    
                    <form method="POST" :action="`/admin/links/${activeLink.id}`">
                        @csrf
                        @method('DELETE')
                        <button 
                            type="submit" 
                            class="px-5 py-2.5 rounded-xl bg-red-600 text-white font-semibold text-sm hover:bg-red-700 transition-all shadow-md shadow-red-600/20"
                        >
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- TOAST NOTIFICATION -->
        <div 
            x-show="toastOpen" 
            x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
            class="fixed bottom-5 right-5 z-50 flex items-center gap-3 bg-slate-900 text-white px-4 py-3 rounded-xl shadow-2xl border border-slate-800 text-sm font-medium"
            x-cloak
        >
            <svg class="w-5 h-5 text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span x-text="toastMessage"></span>
        </div>
    </div>
</x-layouts.admin>
