<x-layouts.admin>
    <x-slot:title>Sources — {{ $link->title }} | LinkHub</x-slot:title>

    <div x-data="{
        createOpen: false,
        editOpen: false,
        deleteOpen: false,
        editId: null,
        editName: '',
        editCode: '',
        deleteId: null,
        deleteName: '',
        autoCode: '',
        newName: '',
        generateCode(name) {
            return name.toLowerCase().trim().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');
        },
        openEdit(id, name, code) {
            this.editId = id;
            this.editName = name;
            this.editCode = code;
            this.editOpen = true;
        },
        openDelete(id, name) {
            this.deleteId = id;
            this.deleteName = name;
            this.deleteOpen = true;
        },
        syncCode() {
            if (!this.autoCode || this.autoCode === this.generateCode(this.newName.slice(0, -1))) {
                this.autoCode = this.generateCode(this.newName);
            }
        }
    }">

        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
            <div>
                <div class="flex items-center gap-2 text-xs text-slate-500 mb-1">
                    <a href="{{ route('admin.links.index') }}" class="hover:text-indigo-600 transition-colors">Links</a>
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="font-semibold text-slate-700 truncate max-w-[150px]">{{ $link->title }}</span>
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    <span class="text-slate-900 font-semibold">Sources</span>
                </div>
                <h1 class="text-2xl font-bold text-slate-900">Traffic Sources</h1>
                <p class="text-sm text-slate-500 mt-1">
                    Kelola link sumber trafik untuk <span class="font-semibold text-slate-700">{{ $link->title }}</span>
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('admin.links.edit-page', $link) }}" class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-200 bg-white text-sm font-medium text-slate-600 hover:bg-slate-50 transition-colors shadow-xs">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Atur Halaman
                </a>
                <button
                    type="button"
                    @click="createOpen = true"
                    class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold text-sm shadow-md shadow-indigo-600/20 hover:bg-indigo-700 transition-all"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                    Add Source
                </button>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-sm text-emerald-800 font-medium flex items-center gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-sm text-red-800 font-medium flex items-center gap-2">
                <svg class="w-4 h-4 text-red-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Info Card -->
        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-4 mb-6 flex items-start gap-3">
            <svg class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            <div class="text-sm text-blue-800">
                <p class="font-semibold mb-0.5">Cara menggunakan Traffic Source</p>
                <p class="text-xs text-blue-700 leading-relaxed">Setiap source memiliki <strong>tracking URL</strong> unik dengan parameter <code class="bg-blue-100 px-1 py-0.5 rounded font-mono">?src=code</code>. Bagikan URL tersebut ke channel yang berbeda untuk melacak trafik masuk dari mana saja.</p>
            </div>
        </div>

        <!-- Sources Table -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between">
                <h2 class="text-sm font-bold text-slate-900">Daftar Sources</h2>
                <span class="text-xs font-semibold text-slate-500 bg-slate-100 px-2.5 py-1 rounded-full">{{ $sources->count() }} source</span>
            </div>

            @if($sources->isEmpty())
                <!-- Empty State -->
                <div class="py-16 text-center">
                    <div class="w-14 h-14 mx-auto rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
                        <svg class="w-7 h-7 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                    </div>
                    <p class="text-sm font-semibold text-slate-700 mb-1">Belum ada source</p>
                    <p class="text-xs text-slate-500 mb-5">Tambahkan source untuk mulai melacak trafik dari berbagai channel.</p>
                    <button @click="createOpen = true" type="button" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition-colors">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Source Pertama
                    </button>
                </div>
            @else
                <!-- Sources Table -->
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100">
                                <th class="text-left px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Name</th>
                                <th class="text-left px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Code</th>
                                <th class="text-left px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Tracking URL</th>
                                <th class="text-left px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Clicks</th>
                                <th class="text-right px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($sources as $source)
                                <tr class="hover:bg-slate-50/50 transition-colors" x-data="{ copied: false }">
                                    <td class="px-6 py-4">
                                        <span class="font-semibold text-slate-900">{{ $source->name }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <code class="px-2.5 py-1 rounded-lg bg-slate-100 text-slate-700 text-xs font-mono font-bold">{{ $source->code }}</code>
                                    </td>
                                    <td class="px-6 py-4 max-w-xs">
                                        <div class="flex items-center gap-2">
                                            <span class="text-xs text-slate-500 font-mono truncate">{{ url('/' . $link->slug) }}?src={{ $source->code }}</span>
                                            <button
                                                type="button"
                                                @click="navigator.clipboard.writeText('{{ url('/' . $link->slug) }}?src={{ $source->code }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                                class="shrink-0 p-1.5 rounded-lg hover:bg-indigo-50 text-slate-400 hover:text-indigo-600 transition-colors"
                                                title="Copy Tracking URL"
                                            >
                                                <svg x-show="!copied" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                                <svg x-show="copied" class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="text-sm font-semibold text-slate-700">{{ number_format($source->click_events_count) }}</span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center justify-end gap-2">
                                            <button
                                                type="button"
                                                @click="openEdit({{ $source->id }}, '{{ addslashes($source->name) }}', '{{ addslashes($source->code) }}')"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors"
                                                title="Edit"
                                            >
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </button>
                                            <button
                                                type="button"
                                                @click="openDelete({{ $source->id }}, '{{ addslashes($source->name) }}')"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                                title="Delete"
                                            >
                                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        <!-- CREATE MODAL -->
        <div x-show="createOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="createOpen = false">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="createOpen = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6" @click.stop>
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-base font-bold text-slate-900">Add Traffic Source</h3>
                    <button @click="createOpen = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <form method="POST" action="{{ route('admin.links.sources.store', $link) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Name <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            name="name"
                            x-model="newName"
                            @input="syncCode()"
                            placeholder="Misal: Instagram Bio, TikTok, Promo Agustus"
                            class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none"
                            required
                        />
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Code (URL-safe) <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input
                                type="text"
                                name="code"
                                x-model="autoCode"
                                placeholder="Misal: instagram-bio, tiktok, promo-agustus"
                                pattern="[a-z0-9][a-z0-9_-]*"
                                class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 font-mono focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none"
                                required
                            />
                        </div>
                        <p class="mt-1.5 text-xs text-slate-500">Hanya huruf kecil, angka, <code class="bg-slate-100 px-1 rounded">-</code> dan <code class="bg-slate-100 px-1 rounded">_</code>. Ini yang digunakan sebagai <code class="bg-slate-100 px-1 rounded font-mono">?src=</code></p>
                        <div x-show="autoCode" class="mt-2 p-2.5 rounded-lg bg-slate-50 border border-slate-200">
                            <span class="text-[11px] text-slate-500 font-semibold">Preview URL:</span>
                            <p class="text-xs text-indigo-700 font-mono mt-0.5 break-all">{{ url('/' . $link->slug) }}?src=<span x-text="autoCode"></span></p>
                        </div>
                    </div>
                    @error('code') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    @error('name') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="createOpen = false" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">Batal</button>
                        <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition-colors">Simpan Source</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- EDIT MODAL -->
        <div x-show="editOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="editOpen = false">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="editOpen = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md p-6" @click.stop>
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-base font-bold text-slate-900">Edit Source</h3>
                    <button @click="editOpen = false" class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 transition-colors">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                <template x-if="editOpen">
                    <form :action="'{{ route('admin.links.sources.update', [$link, '__ID__']) }}'.replace('__ID__', editId)" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Name <span class="text-red-500">*</span></label>
                            <input type="text" name="name" x-model="editName" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none" required />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-600 uppercase tracking-wider mb-1.5">Code (URL-safe) <span class="text-red-500">*</span></label>
                            <input type="text" name="code" x-model="editCode" pattern="[a-z0-9][a-z0-9_-]*" class="w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 font-mono focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none" required />
                            <div class="mt-2 p-2.5 rounded-lg bg-slate-50 border border-slate-200">
                                <span class="text-[11px] text-slate-500 font-semibold">Preview URL:</span>
                                <p class="text-xs text-indigo-700 font-mono mt-0.5 break-all">{{ url('/' . $link->slug) }}?src=<span x-text="editCode"></span></p>
                            </div>
                        </div>
                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="editOpen = false" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">Batal</button>
                            <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition-colors">Update Source</button>
                        </div>
                    </form>
                </template>
            </div>
        </div>

        <!-- DELETE MODAL -->
        <div x-show="deleteOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4" @keydown.escape.window="deleteOpen = false">
            <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="deleteOpen = false"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6" @click.stop>
                <div class="text-center mb-5">
                    <div class="w-12 h-12 rounded-2xl bg-red-50 flex items-center justify-center mx-auto mb-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </div>
                    <h3 class="text-base font-bold text-slate-900">Hapus Source?</h3>
                    <p class="text-sm text-slate-600 mt-1">Source <strong x-text="deleteName" class="text-slate-900"></strong> akan dihapus. Data klik yang sudah ada akan tetap tersimpan tanpa source.</p>
                </div>
                <template x-if="deleteOpen">
                    <form :action="'{{ route('admin.links.sources.destroy', [$link, '__ID__']) }}'.replace('__ID__', deleteId)" method="POST" class="flex gap-3">
                        @csrf
                        @method('DELETE')
                        <button type="button" @click="deleteOpen = false" class="flex-1 px-4 py-2.5 rounded-xl border border-slate-200 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition-colors">Batal</button>
                        <button type="submit" class="flex-1 px-4 py-2.5 rounded-xl bg-red-600 text-white text-sm font-semibold hover:bg-red-700 transition-colors">Hapus</button>
                    </form>
                </template>
            </div>
        </div>

    </div>
</x-layouts.admin>
