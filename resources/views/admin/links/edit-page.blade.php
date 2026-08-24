<x-layouts.admin title="Atur Halaman - {{ $link->title }}">
    <div x-data="linkPageEditorComponent()">
        <!-- Top Bar Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 pb-6 border-b border-slate-200">
            <div>
                <a href="{{ route('admin.links.index') }}" class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-500 hover:text-indigo-600 transition-colors mb-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Back to Links
                </a>
                <div class="flex items-center gap-3">
                    <h1 class="text-2xl font-bold text-slate-900 tracking-tight">Atur Halaman</h1>
                    <span class="px-2.5 py-0.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                        {{ $link->title }}
                    </span>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <!-- Mobile Toggle Live Preview Button -->
                <button 
                    type="button"
                    @click="mobilePreviewOpen = !mobilePreviewOpen"
                    class="lg:hidden inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 font-semibold text-sm shadow-sm"
                >
                    <svg class="w-4 h-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                    </svg>
                    <span x-text="mobilePreviewOpen ? 'Close Preview' : 'Live Preview'"></span>
                </button>

                <!-- Primary Save Button -->
                <button 
                    type="submit"
                    form="page-settings-form"
                    @click="isDirty = false"
                    class="relative inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold text-sm shadow-md shadow-indigo-600/20 hover:bg-indigo-700 transition-all focus:outline-none"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                    Save Changes
                    <span x-show="isDirty" class="w-2 h-2 rounded-full bg-amber-400 animate-ping absolute -top-1 -right-1" x-cloak></span>
                </button>
            </div>
        </div>

        <!-- Global Flash Notifications -->
        @if(session('success'))
            <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-sm font-medium flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>{{ session('success') }}</span>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm space-y-1">
                <div class="font-bold flex items-center gap-2">
                    <svg class="w-5 h-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    Please fix the following validation errors:
                </div>
                @foreach($errors->all() as $error)
                    <p class="pl-7">• {{ $error }}</p>
                @endforeach
            </div>
        @endif

        <!-- Main Layout Grid (Editor Left 60% / Live Preview Right 40%) -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- EDITOR CONTAINER (LEFT COLUMN - 7 COLS / ~60%) -->
            <div class="lg:col-span-7 space-y-6">
                <!-- Tab Navigation Header -->
                <div class="bg-white p-2 rounded-2xl border border-slate-200/80 shadow-sm flex items-center gap-1.5 overflow-x-auto">
                    <button 
                        type="button" 
                        @click="activeTab = 'profile'" 
                        :class="activeTab === 'profile' ? 'bg-indigo-50 text-indigo-700 font-bold shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium'"
                        class="flex-1 min-w-[100px] px-4 py-2.5 rounded-xl text-xs sm:text-sm text-center transition-all"
                    >
                        Profile
                    </button>
                    <button 
                        type="button" 
                        @click="activeTab = 'appearance'" 
                        :class="activeTab === 'appearance' ? 'bg-indigo-50 text-indigo-700 font-bold shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium'"
                        class="flex-1 min-w-[100px] px-4 py-2.5 rounded-xl text-xs sm:text-sm text-center transition-all"
                    >
                        Appearance
                    </button>
                    <button 
                        type="button" 
                        @click="activeTab = 'contact'" 
                        :class="activeTab === 'contact' ? 'bg-indigo-50 text-indigo-700 font-bold shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium'"
                        class="flex-1 min-w-[120px] px-4 py-2.5 rounded-xl text-xs sm:text-sm text-center transition-all"
                    >
                        Contact & Social
                    </button>
                    <button 
                        type="button" 
                        @click="activeTab = 'products'" 
                        :class="activeTab === 'products' ? 'bg-indigo-50 text-indigo-700 font-bold shadow-sm' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50 font-medium'"
                        class="flex-1 min-w-[100px] px-4 py-2.5 rounded-xl text-xs sm:text-sm text-center transition-all"
                    >
                        Products ({{ count($page->products) }})
                    </button>
                </div>

                <!-- Primary Page Settings Form -->
                <form id="page-settings-form" method="POST" action="{{ route('admin.links.update-page', $link) }}" enctype="multipart/form-data" @input="markDirty()" @change="markDirty()" @submit="isDirty = false">
                    @csrf
                    @method('PUT')

                    <!-- SECTION 1: PROFILE -->
                    <div x-show="activeTab === 'profile'" class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-6">
                        <h2 class="text-lg font-bold text-slate-900 pb-3 border-b border-slate-100">Store Profile</h2>

                        <!-- Store Profile Photo -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Store Profile Photo</label>
                            <div class="flex items-center gap-5">
                                <div class="w-20 h-20 rounded-full border-2 border-slate-200 bg-slate-100 overflow-hidden flex items-center justify-center relative shadow-sm">
                                    <template x-if="profilePhoto">
                                        <img :src="profilePhoto" class="w-full h-full object-cover" />
                                    </template>
                                    <template x-if="!profilePhoto">
                                        <div class="text-slate-400 font-bold text-xl uppercase" x-text="storeName ? storeName.substring(0,2) : 'ST'"></div>
                                    </template>
                                </div>
                                <div class="space-y-2">
                                    <input type="file" name="profile_photo_file" accept="image/*" @change="previewProfilePhoto($event)" class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 transition-all cursor-pointer" />
                                    <div class="text-xs text-slate-400">Or paste Image URL:</div>
                                    <input type="url" name="profile_photo" x-model="profilePhoto" placeholder="https://example.com/avatar.jpg" class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-900 focus:border-indigo-500 focus:outline-none" />
                                </div>
                            </div>
                        </div>

                        <!-- Store Name -->
                        <div>
                            <label for="store_name" class="block text-sm font-semibold text-slate-700">Store Name *</label>
                            <input 
                                id="store_name"
                                type="text" 
                                name="store_name" 
                                x-model="storeName"
                                required
                                placeholder="My Store"
                                class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all"
                            />
                        </div>

                        <!-- Short Bio -->
                        <div>
                            <div class="flex items-center justify-between">
                                <label for="bio" class="block text-sm font-semibold text-slate-700">Short Bio</label>
                                <span class="text-xs text-slate-400" x-text="`${bio ? bio.length : 0}/200`"></span>
                            </div>
                            <textarea 
                                id="bio"
                                name="bio" 
                                x-model="bio"
                                maxlength="200"
                                rows="3"
                                placeholder="Your short store description..."
                                class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all"
                            ></textarea>
                        </div>

                        <!-- Cover Type -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-2">Cover Type</label>
                            <div class="grid grid-cols-2 gap-4">
                                <label class="border-2 rounded-xl p-3 flex items-center gap-3 cursor-pointer transition-all" :class="coverType === 'color' ? 'border-indigo-600 bg-indigo-50/50' : 'border-slate-200 bg-white'">
                                    <input type="radio" name="cover_type" value="color" x-model="coverType" class="text-indigo-600 focus:ring-indigo-500" />
                                    <span class="text-sm font-semibold text-slate-800">Color</span>
                                </label>
                                <label class="border-2 rounded-xl p-3 flex items-center gap-3 cursor-pointer transition-all" :class="coverType === 'image' ? 'border-indigo-600 bg-indigo-50/50' : 'border-slate-200 bg-white'">
                                    <input type="radio" name="cover_type" value="image" x-model="coverType" class="text-indigo-600 focus:ring-indigo-500" />
                                    <span class="text-sm font-semibold text-slate-800">Image</span>
                                </label>
                            </div>
                        </div>

                        <!-- Cover Color Picker -->
                        <div x-show="coverType === 'color'">
                            <label for="cover_color" class="block text-sm font-semibold text-slate-700 mb-1">Background Color</label>
                            <div class="flex items-center gap-3">
                                <input type="color" id="cover_color" name="cover_color" x-model="coverColor" class="w-12 h-10 rounded-xl border border-slate-200 cursor-pointer p-1" />
                                <input type="text" x-model="coverColor" class="w-32 rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-900 uppercase font-mono" />
                            </div>
                        </div>

                        <!-- Cover Image Upload -->
                        <div x-show="coverType === 'image'">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Upload Cover Image</label>
                            <div class="space-y-2">
                                <input type="file" name="cover_image_file" accept="image/*" @change="previewCoverImage($event)" class="text-xs text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 cursor-pointer" />
                                <div class="text-xs text-slate-400">Or paste Image URL:</div>
                                <input type="url" name="cover_image" x-model="coverImage" placeholder="https://example.com/cover.jpg" class="w-full text-xs rounded-xl border border-slate-200 px-3 py-2 text-slate-900 focus:border-indigo-500" />
                            </div>
                        </div>
                    </div>

                    <!-- SECTION 2: APPEARANCE -->
                    <div x-show="activeTab === 'appearance'" class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-6">
                        <h2 class="text-lg font-bold text-slate-900 pb-3 border-b border-slate-100">Appearance Settings</h2>

                        <!-- Header Layout Selector Cards -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-3">Header Layout</label>
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Center Card -->
                                <div 
                                    @click="headerAlignment = 'center'; markDirty()"
                                    class="border-2 rounded-2xl p-4 cursor-pointer text-center transition-all"
                                    :class="headerAlignment === 'center' ? 'border-indigo-600 bg-indigo-50/40 ring-2 ring-indigo-500/20' : 'border-slate-200 bg-white hover:border-slate-300'"
                                >
                                    <div class="w-full h-20 bg-slate-100 rounded-xl mb-3 flex flex-col items-center justify-center p-2 space-y-1">
                                        <div class="w-6 h-6 rounded-full bg-indigo-600 mx-auto"></div>
                                        <div class="w-16 h-2 bg-slate-400 rounded mx-auto"></div>
                                        <div class="w-24 h-1.5 bg-slate-300 rounded mx-auto"></div>
                                    </div>
                                    <span class="text-sm font-bold text-slate-800">Center</span>
                                    <input type="radio" name="header_alignment" value="center" x-model="headerAlignment" class="hidden" />
                                </div>

                                <!-- Left Card -->
                                <div 
                                    @click="headerAlignment = 'left'; markDirty()"
                                    class="border-2 rounded-2xl p-4 cursor-pointer text-center transition-all"
                                    :class="headerAlignment === 'left' ? 'border-indigo-600 bg-indigo-50/40 ring-2 ring-indigo-500/20' : 'border-slate-200 bg-white hover:border-slate-300'"
                                >
                                    <div class="w-full h-20 bg-slate-100 rounded-xl mb-3 flex items-center p-3 gap-2">
                                        <div class="w-8 h-8 rounded-full bg-indigo-600 shrink-0"></div>
                                        <div class="space-y-1 text-left">
                                            <div class="w-16 h-2 bg-slate-400 rounded"></div>
                                            <div class="w-20 h-1.5 bg-slate-300 rounded"></div>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold text-slate-800">Left</span>
                                    <input type="radio" name="header_alignment" value="left" x-model="headerAlignment" class="hidden" />
                                </div>
                            </div>
                        </div>

                        <!-- Product Layout Selector Cards -->
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-3">Product Layout</label>
                            <div class="grid grid-cols-2 gap-4">
                                <!-- Grid Card -->
                                <div 
                                    @click="productLayout = 'grid'; markDirty()"
                                    class="border-2 rounded-2xl p-4 cursor-pointer text-center transition-all"
                                    :class="productLayout === 'grid' ? 'border-indigo-600 bg-indigo-50/40 ring-2 ring-indigo-500/20' : 'border-slate-200 bg-white hover:border-slate-300'"
                                >
                                    <div class="w-full h-20 bg-slate-100 rounded-xl mb-3 p-2 grid grid-cols-2 gap-2">
                                        <div class="bg-white rounded-lg p-1 border border-slate-200 flex flex-col items-center justify-center">
                                            <div class="w-6 h-6 bg-slate-300 rounded mb-1"></div>
                                            <div class="w-8 h-1 bg-slate-400 rounded"></div>
                                        </div>
                                        <div class="bg-white rounded-lg p-1 border border-slate-200 flex flex-col items-center justify-center">
                                            <div class="w-6 h-6 bg-slate-300 rounded mb-1"></div>
                                            <div class="w-8 h-1 bg-slate-400 rounded"></div>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold text-slate-800">Grid (2 Columns)</span>
                                    <input type="radio" name="product_layout" value="grid" x-model="productLayout" class="hidden" />
                                </div>

                                <!-- List Card -->
                                <div 
                                    @click="productLayout = 'list'; markDirty()"
                                    class="border-2 rounded-2xl p-4 cursor-pointer text-center transition-all"
                                    :class="productLayout === 'list' ? 'border-indigo-600 bg-indigo-50/40 ring-2 ring-indigo-500/20' : 'border-slate-200 bg-white hover:border-slate-300'"
                                >
                                    <div class="w-full h-20 bg-slate-100 rounded-xl mb-3 p-2 space-y-1.5">
                                        <div class="bg-white rounded-lg p-1.5 border border-slate-200 flex items-center gap-2">
                                            <div class="w-5 h-5 bg-slate-300 rounded shrink-0"></div>
                                            <div class="w-20 h-1.5 bg-slate-400 rounded"></div>
                                        </div>
                                        <div class="bg-white rounded-lg p-1.5 border border-slate-200 flex items-center gap-2">
                                            <div class="w-5 h-5 bg-slate-300 rounded shrink-0"></div>
                                            <div class="w-20 h-1.5 bg-slate-400 rounded"></div>
                                        </div>
                                    </div>
                                    <span class="text-sm font-bold text-slate-800">List (1 Column)</span>
                                    <input type="radio" name="product_layout" value="list" x-model="productLayout" class="hidden" />
                                </div>
                            </div>
                        </div>

                        <!-- Traffic Sources — managed on dedicated page -->
                        <div class="pt-6 border-t border-slate-100">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Traffic Sources</label>
                            <p class="text-xs text-slate-500 mb-4">Kelola source trafik (Instagram, TikTok, dll.) untuk melacak dari mana pengunjung datang menggunakan parameter <code class="bg-slate-100 px-1 py-0.5 rounded font-mono text-xs">?src=</code>.</p>
                            <a
                                href="{{ route('admin.links.sources.index', $link) }}"
                                class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl border-2 border-dashed border-indigo-200 bg-indigo-50/50 text-indigo-700 font-semibold text-sm hover:bg-indigo-50 hover:border-indigo-300 transition-all"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                Kelola Traffic Sources
                                <svg class="w-3.5 h-3.5 ml-1" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>


                    <!-- SECTION 3: CONTACT & SOCIAL -->
                    <div x-show="activeTab === 'contact'" class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm space-y-6">
                        <h2 class="text-lg font-bold text-slate-900 pb-3 border-b border-slate-100">Contact & Social Media</h2>

                        <!-- WhatsApp Number -->
                        <div>
                            <label for="whatsapp_number" class="block text-sm font-semibold text-slate-700">WhatsApp Number</label>
                            <input 
                                id="whatsapp_number"
                                type="text" 
                                name="whatsapp_number" 
                                x-model="whatsappNumber"
                                placeholder="+62 81234567890"
                                class="mt-1 w-full rounded-xl border border-slate-200 px-4 py-2.5 text-sm text-slate-900 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none"
                            />
                            <p class="mt-1.5 text-xs text-slate-500">Customers can contact your store directly via WhatsApp button in live preview.</p>
                        </div>

                        <!-- Social Media Platforms -->
                        <div class="space-y-4 pt-2 border-t border-slate-100">
                            <h3 class="text-sm font-bold text-slate-800">Social Media Links (Optional)</h3>

                            <!-- Facebook -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Facebook URL</label>
                                <input type="url" name="socials[facebook]" x-model="socials.facebook" placeholder="https://facebook.com/yourpage" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none" />
                            </div>

                            <!-- Instagram -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Instagram URL</label>
                                <input type="url" name="socials[instagram]" x-model="socials.instagram" placeholder="https://instagram.com/yourhandle" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none" />
                            </div>

                            <!-- TikTok -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">TikTok URL</label>
                                <input type="url" name="socials[tiktok]" x-model="socials.tiktok" placeholder="https://tiktok.com/@yourhandle" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none" />
                            </div>

                            <!-- X (Twitter) -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">X (Twitter) URL</label>
                                <input type="url" name="socials[x]" x-model="socials.x" placeholder="https://x.com/yourhandle" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none" />
                            </div>

                            <!-- Threads -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Threads URL</label>
                                <input type="url" name="socials[threads]" x-model="socials.threads" placeholder="https://threads.net/@yourhandle" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none" />
                            </div>
                        </div>
                    </div>
                </form>

                <!-- SECTION 4: PRODUCTS & CATEGORIES (MANAGED SEPARATELY) -->
                <div x-show="activeTab === 'products'" class="space-y-6">
                    <!-- Category Management Card -->
                    <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm">
                        <div class="flex items-center justify-between mb-4">
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Product Categories</h3>
                                <p class="text-xs text-slate-500">Organize your products into categories</p>
                            </div>
                            <button 
                                type="button" 
                                @click="categoryModalOpen = true" 
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-indigo-50 text-indigo-700 font-semibold text-xs hover:bg-indigo-100 transition-colors"
                            >
                                + Add Category
                            </button>
                        </div>

                        <!-- Categories List -->
                        <div class="flex flex-wrap gap-2">
                            @forelse($page->categories as $category)
                                <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-xl bg-slate-100 text-slate-700 text-xs font-semibold border border-slate-200">
                                    <span>{{ $category->name }}</span>
                                    <button type="button" @click="openEditCategoryModal({{ json_encode($category) }})" class="text-slate-400 hover:text-indigo-600">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                        </svg>
                                    </button>
                                    <form method="POST" action="{{ route('admin.links.categories.destroy', ['link' => $link, 'category' => $category]) }}" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" onclick="return confirm('Delete this category? Products in this category will also be deleted.')" class="text-slate-400 hover:text-red-600">
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            @empty
                                <p class="text-xs text-slate-400 italic">No categories yet. Click "+ Add Category" to create one.</p>
                            @endforelse
                        </div>
                    </div>

                    <!-- Products List Table Card -->
                    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                        <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                            <div>
                                <h3 class="text-base font-bold text-slate-900">Products Catalog</h3>
                                <p class="text-xs text-slate-500">Manage products listed on your public page</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button 
                                    type="button" 
                                    @click="importModalOpen = true" 
                                    class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl bg-emerald-50 text-emerald-700 font-semibold text-xs border border-emerald-200 hover:bg-emerald-100 transition-all"
                                >
                                    <svg class="w-4 h-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                                    Bulk Upload Excel
                                </button>
                                <button 
                                    type="button" 
                                    @click="productModalOpen = true" 
                                    @if(count($page->categories) === 0) disabled title="Create a category first" @endif
                                    class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 text-white font-semibold text-xs shadow-md shadow-indigo-600/20 hover:bg-indigo-700 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    + Add Product
                                </button>
                            </div>
                        </div>

                        @if(count($page->products) === 0)
                            <div class="p-8 text-center">
                                <p class="text-sm font-semibold text-slate-700">No products added yet.</p>
                                <p class="text-xs text-slate-400 mt-1">Create categories and click "+ Add Product" to build your catalog.</p>
                            </div>
                        @else
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse text-xs">
                                    <thead>
                                        <tr class="border-b border-slate-100 bg-slate-50/50 font-bold text-slate-500 uppercase tracking-wider">
                                            <th class="py-3 px-4">Product</th>
                                            <th class="py-3 px-4">Category</th>
                                            <th class="py-3 px-4">Status</th>
                                            <th class="py-3 px-4">Order</th>
                                            <th class="py-3 px-4 text-right">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100">
                                        @foreach($page->products as $product)
                                            <tr class="hover:bg-slate-50/80">
                                                <td class="py-3 px-4 font-semibold text-slate-900">
                                                    <div class="flex items-center gap-2.5">
                                                        <div class="w-8 h-8 rounded-lg bg-slate-100 overflow-hidden shrink-0">
                                                            @if($product->image_url)
                                                                <img src="{{ $product->image_url }}" class="w-full h-full object-cover" />
                                                            @else
                                                                <div class="w-full h-full flex items-center justify-center text-slate-400 font-bold">P</div>
                                                            @endif
                                                        </div>
                                                        <div>
                                                            <div class="font-bold text-slate-900 truncate max-w-[150px]">{{ $product->name }}</div>
                                                            <a href="{{ $product->product_url }}" target="_blank" class="text-[10px] text-indigo-600 hover:underline truncate block max-w-[150px]">{{ $product->product_url }}</a>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3 px-4 text-slate-600">
                                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 font-medium">
                                                        {{ $product->category->name ?? '-' }}
                                                    </span>
                                                </td>
                                                <td class="py-3 px-4">
                                                    @if($product->status === 'active')
                                                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 text-emerald-700 font-bold text-[10px]">Active</span>
                                                    @else
                                                        <span class="px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 font-bold text-[10px]">Inactive</span>
                                                    @endif
                                                </td>
                                                <td class="py-3 px-4 text-slate-500 font-mono">
                                                    {{ $product->display_order }}
                                                </td>
                                                <td class="py-3 px-4 text-right space-x-1 whitespace-nowrap">
                                                    <button type="button" @click="openEditProductModal({{ json_encode($product) }})" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                        </svg>
                                                    </button>
                                                    <form method="POST" action="{{ route('admin.links.products.toggle-status', ['link' => $link, 'product' => $product]) }}" class="inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="p-1.5 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50">
                                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636" />
                                                            </svg>
                                                        </button>
                                                    </form>
                                                    <button type="button" @click="openDeleteProductModal({{ json_encode($product) }})" class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                        </svg>
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- LIVE PREVIEW CONTAINER (RIGHT COLUMN - 5 COLS / ~40%) -->
            <div 
                class="lg:col-span-5 lg:sticky lg:top-6"
                :class="mobilePreviewOpen ? 'fixed inset-0 z-40 bg-slate-900/80 backdrop-blur-sm p-4 overflow-y-auto flex items-center justify-center' : 'hidden lg:block'"
            >
                <div class="w-full max-w-sm mx-auto bg-slate-900 p-4 rounded-[40px] shadow-2xl border-4 border-slate-800 relative">
                    <!-- Mobile Close Button -->
                    <button 
                        type="button" 
                        @click="mobilePreviewOpen = false" 
                        class="lg:hidden absolute top-3 right-4 z-50 text-white bg-slate-800 hover:bg-slate-700 p-2 rounded-full"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>

                    <!-- Phone Camera Speaker Notch -->
                    <div class="w-32 h-4 bg-slate-800 rounded-b-xl mx-auto mb-3"></div>

                    <!-- Screen Viewport -->
                    <div class="bg-slate-50 min-h-[560px] max-h-[640px] overflow-y-auto rounded-[28px] text-slate-900 text-xs pb-6 border border-slate-200 flex flex-col">
                        <!-- Cover Banner -->
                        <div class="h-28 w-full relative transition-all bg-indigo-600" :style="coverType === 'color' ? `background-color: ${coverColor};` : (coverImage ? `background-image: url('${coverImage}'); background-size: cover; background-position: center;` : `background-color: #4f46e5;`)">
                        </div>

                        <!-- Header Info Section -->
                        <div class="px-5 -mt-10 mb-4 relative z-10" :class="headerAlignment === 'left' ? 'text-left flex items-end gap-3' : 'text-center'">
                            <!-- Avatar -->
                            <div class="w-16 h-16 rounded-full border-2 border-white bg-white shadow-md overflow-hidden shrink-0" :class="headerAlignment === 'center' ? 'mx-auto' : ''">
                                <template x-if="profilePhoto">
                                    <img :src="profilePhoto" class="w-full h-full object-cover" />
                                </template>
                                <template x-if="!profilePhoto">
                                    <div class="w-full h-full bg-indigo-600 text-white font-bold text-lg flex items-center justify-center" x-text="storeName ? storeName.substring(0,2).toUpperCase() : 'ST'"></div>
                                </template>
                            </div>

                            <!-- Name & Bio -->
                            <div :class="headerAlignment === 'left' ? 'pb-1' : 'mt-2'">
                                <h3 class="font-extrabold text-sm text-slate-900 tracking-tight" x-text="storeName || 'Store Name'"></h3>
                                <p class="text-[11px] text-slate-500 leading-tight mt-0.5 max-w-xs" x-text="bio || 'Short store bio...'"></p>
                            </div>
                        </div>

                        <!-- WhatsApp CTA Button -->
                        <div class="px-5 mb-4" x-show="whatsappNumber">
                            <a 
                                :href="`https://wa.me/${whatsappNumber}`" 
                                target="_blank" 
                                class="w-full inline-flex items-center justify-center gap-2 py-2.5 px-4 rounded-xl bg-emerald-600 text-white font-bold text-xs shadow-sm hover:bg-emerald-700 transition-colors"
                            >
                                <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                                    <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.245 2.248 3.481 5.236 3.48 8.414-.003 6.557-5.338 11.892-11.893 11.892-1.99-.001-3.951-.5-5.688-1.448l-6.305 1.654zm6.597-3.807c1.676.995 3.276 1.591 5.392 1.592 5.448 0 9.886-4.434 9.889-9.885.002-5.462-4.415-9.89-9.881-9.892-5.452 0-9.887 4.434-9.889 9.884-.001 2.225.651 3.891 1.746 5.634l-.999 3.648 3.742-.981z"/>
                                </svg>
                                Hubungi via WhatsApp
                            </a>
                        </div>

                        <!-- Social Icons Row -->
                        <div class="px-5 mb-4 flex items-center justify-center gap-3" x-show="socials.facebook || socials.instagram || socials.tiktok || socials.x || socials.threads">
                            <template x-if="socials.facebook">
                                <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-[10px] font-bold">FB</span>
                            </template>
                            <template x-if="socials.instagram">
                                <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-[10px] font-bold">IG</span>
                            </template>
                            <template x-if="socials.tiktok">
                                <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-[10px] font-bold">TT</span>
                            </template>
                            <template x-if="socials.x">
                                <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-[10px] font-bold">X</span>
                            </template>
                            <template x-if="socials.threads">
                                <span class="w-7 h-7 rounded-full bg-slate-100 text-slate-600 flex items-center justify-center text-[10px] font-bold">TH</span>
                            </template>
                        </div>

                        <!-- Category Filter Pills in Preview -->
                        <div class="px-4 mb-3 flex gap-1.5 overflow-x-auto pb-1" x-show="categories.length > 0">
                            <button type="button" @click="selectedCategoryFilter = 'all'" class="px-2.5 py-1 rounded-full text-[10px] font-semibold whitespace-nowrap transition-colors" :class="selectedCategoryFilter === 'all' ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-700'">All</button>
                            <template x-for="cat in categories" :key="cat.id">
                                <button type="button" @click="selectedCategoryFilter = cat.id" class="px-2.5 py-1 rounded-full text-[10px] font-semibold whitespace-nowrap transition-colors" :class="selectedCategoryFilter == cat.id ? 'bg-indigo-600 text-white' : 'bg-slate-200 text-slate-700'" x-text="cat.name"></button>
                            </template>
                        </div>

                        <!-- Product Catalog Preview -->
                        <div class="px-4 flex-1">
                            <template x-if="filteredProducts.length === 0">
                                <div class="p-6 text-center text-slate-400 italic text-[11px]">
                                    No active products in catalog.
                                </div>
                            </template>

                            <!-- GRID LAYOUT -->
                            <div x-show="productLayout === 'grid' && filteredProducts.length > 0" class="grid grid-cols-2 gap-2.5">
                                <template x-for="product in filteredProducts" :key="product.id">
                                    <div class="bg-white rounded-xl border border-slate-200 p-2 shadow-2xs flex flex-col justify-between">
                                        <div>
                                            <div class="w-full h-20 bg-slate-100 rounded-lg overflow-hidden mb-1.5">
                                                <template x-if="product.image_url">
                                                    <img :src="product.image_url" class="w-full h-full object-cover" />
                                                </template>
                                                <template x-if="!product.image_url">
                                                    <div class="w-full h-full flex items-center justify-center text-slate-300 font-bold text-xs">NO IMG</div>
                                                </template>
                                            </div>
                                            <div class="font-bold text-[11px] text-slate-900 leading-tight line-clamp-1" x-text="product.name"></div>
                                            <p class="text-[9px] text-slate-400 line-clamp-1 mt-0.5" x-text="product.description"></p>
                                        </div>
                                        <button type="button" class="mt-2 w-full py-1 px-2 rounded-lg bg-indigo-50 text-indigo-700 font-bold text-[9px] text-center hover:bg-indigo-100">Beli / View</button>
                                    </div>
                                </template>
                            </div>

                            <!-- LIST LAYOUT -->
                            <div x-show="productLayout === 'list' && filteredProducts.length > 0" class="space-y-2">
                                <template x-for="product in filteredProducts" :key="product.id">
                                    <div class="bg-white rounded-xl border border-slate-200 p-2 shadow-2xs flex items-center gap-2.5">
                                        <div class="w-12 h-12 bg-slate-100 rounded-lg overflow-hidden shrink-0">
                                            <template x-if="product.image_url">
                                                <img :src="product.image_url" class="w-full h-full object-cover" />
                                            </template>
                                            <template x-if="!product.image_url">
                                                <div class="w-full h-full flex items-center justify-center text-slate-300 font-bold text-[9px]">NO IMG</div>
                                            </template>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="font-bold text-[11px] text-slate-900 truncate" x-text="product.name"></div>
                                            <p class="text-[9px] text-slate-400 truncate" x-text="product.description"></p>
                                        </div>
                                        <button type="button" class="py-1 px-2.5 rounded-lg bg-indigo-50 text-indigo-700 font-bold text-[9px] shrink-0">View</button>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ADD CATEGORY MODAL -->
        <div x-show="categoryModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
            <div @click="categoryModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div @click.stop class="relative z-10 w-full max-w-md bg-white rounded-2xl p-6 shadow-2xl border border-slate-200">
                <h3 class="text-base font-bold text-slate-900 mb-4">Add New Category</h3>
                <form method="POST" action="{{ route('admin.links.categories.store', $link) }}" class="space-y-4">
                    @csrf
                    <div>
                        <label for="cat_name" class="block text-xs font-semibold text-slate-700 mb-1">Category Name *</label>
                        <input id="cat_name" type="text" name="name" required placeholder="e.g. Kaos, Hoodie" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none" />
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="categoryModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600">Cancel</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 text-white font-semibold text-xs hover:bg-indigo-700">Create Category</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- EDIT CATEGORY MODAL -->
        <div x-show="editCategoryModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
            <div @click="editCategoryModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div @click.stop class="relative z-10 w-full max-w-md bg-white rounded-2xl p-6 shadow-2xl border border-slate-200">
                <h3 class="text-base font-bold text-slate-900 mb-4">Edit Category</h3>
                <form method="POST" :action="`/admin/links/{{ $link->id }}/categories/${editCategoryId}`" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="edit_cat_name" class="block text-xs font-semibold text-slate-700 mb-1">Category Name *</label>
                        <input id="edit_cat_name" type="text" name="name" x-model="editCategoryName" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-900 focus:border-indigo-500 focus:outline-none" />
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="editCategoryModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600">Cancel</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 text-white font-semibold text-xs hover:bg-indigo-700">Update Category</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ADD PRODUCT MODAL -->
        <div x-show="productModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" x-cloak>
            <div @click="productModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div @click.stop class="relative z-10 w-full max-w-lg bg-white rounded-2xl p-6 shadow-2xl border border-slate-200 my-auto">
                <h3 class="text-base font-bold text-slate-900 mb-4">Add New Product</h3>
                <form method="POST" action="{{ route('admin.links.products.store', $link) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    <div>
                        <label for="prod_name" class="block text-xs font-semibold text-slate-700 mb-1">Product Name *</label>
                        <input id="prod_name" type="text" name="name" required placeholder="Kaos Hitam Basic" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-900 focus:border-indigo-500" />
                    </div>
                    <div>
                        <label for="prod_cat" class="block text-xs font-semibold text-slate-700 mb-1">Category *</label>
                        <select id="prod_cat" name="category_id" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-900 bg-white focus:border-indigo-500">
                            <option value="">Select Category</option>
                            @foreach($page->categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="prod_url" class="block text-xs font-semibold text-slate-700 mb-1">Product URL *</label>
                        <input id="prod_url" type="url" name="product_url" required placeholder="https://shopee.co.id/..." class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-900 focus:border-indigo-500" />
                    </div>
                    <div>
                        <label for="prod_desc" class="block text-xs font-semibold text-slate-700 mb-1">Description</label>
                        <textarea id="prod_desc" name="description" rows="2" placeholder="Product short details..." class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-900 focus:border-indigo-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Product Image</label>
                        <input type="file" name="image_file" accept="image/*" class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 mb-1.5" />
                        <input type="url" name="image_url" placeholder="Or paste Image URL (https://...)" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs text-slate-900 focus:border-indigo-500" />
                    </div>
                    <div>
                        <label for="prod_status" class="block text-xs font-semibold text-slate-700 mb-1">Status</label>
                        <select id="prod_status" name="status" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-900 bg-white focus:border-indigo-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="productModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600">Cancel</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 text-white font-semibold text-xs hover:bg-indigo-700">Add Product</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- EDIT PRODUCT MODAL -->
        <div x-show="editProductModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4 overflow-y-auto" x-cloak>
            <div @click="editProductModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div @click.stop class="relative z-10 w-full max-w-lg bg-white rounded-2xl p-6 shadow-2xl border border-slate-200 my-auto">
                <h3 class="text-base font-bold text-slate-900 mb-4">Edit Product</h3>
                <form method="POST" :action="`/admin/links/{{ $link->id }}/products/${productForm.id}`" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    @method('PUT')
                    <div>
                        <label for="edit_prod_name" class="block text-xs font-semibold text-slate-700 mb-1">Product Name *</label>
                        <input id="edit_prod_name" type="text" name="name" x-model="productForm.name" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-900 focus:border-indigo-500" />
                    </div>
                    <div>
                        <label for="edit_prod_cat" class="block text-xs font-semibold text-slate-700 mb-1">Category *</label>
                        <select id="edit_prod_cat" name="category_id" x-model="productForm.category_id" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-900 bg-white focus:border-indigo-500">
                            @foreach($page->categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label for="edit_prod_url" class="block text-xs font-semibold text-slate-700 mb-1">Product URL *</label>
                        <input id="edit_prod_url" type="url" name="product_url" x-model="productForm.product_url" required class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-900 focus:border-indigo-500" />
                    </div>
                    <div>
                        <label for="edit_prod_desc" class="block text-xs font-semibold text-slate-700 mb-1">Description</label>
                        <textarea id="edit_prod_desc" name="description" x-model="productForm.description" rows="2" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-900 focus:border-indigo-500"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1">Product Image</label>
                        <input type="file" name="image_file" accept="image/*" class="text-xs text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-indigo-50 file:text-indigo-700 mb-1.5" />
                        <input type="url" name="image_url" x-model="productForm.image_url" placeholder="Image URL (https://...)" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs text-slate-900 focus:border-indigo-500" />
                    </div>
                    <div>
                        <label for="edit_prod_status" class="block text-xs font-semibold text-slate-700 mb-1">Status</label>
                        <select id="edit_prod_status" name="status" x-model="productForm.status" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-sm text-slate-900 bg-white focus:border-indigo-500">
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                    <div class="flex justify-end gap-2 pt-2">
                        <button type="button" @click="editProductModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600">Cancel</button>
                        <button type="submit" class="px-4 py-2 rounded-xl bg-indigo-600 text-white font-semibold text-xs hover:bg-indigo-700">Update Product</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- DELETE PRODUCT MODAL -->
        <div x-show="deleteProductModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
            <div @click="deleteProductModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div @click.stop class="relative z-10 w-full max-w-md bg-white rounded-2xl p-6 shadow-2xl border border-slate-200">
                <h3 class="text-base font-bold text-slate-900 mb-2">Delete Product?</h3>
                <p class="text-xs text-slate-500 mb-4">Are you sure you want to delete <span class="font-bold text-slate-900" x-text="activeProductToDelete ? activeProductToDelete.name : ''"></span>?</p>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="deleteProductModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600">Cancel</button>
                    <form method="POST" :action="activeProductToDelete ? `/admin/links/{{ $link->id }}/products/${activeProductToDelete.id}` : ''">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-4 py-2 rounded-xl bg-red-600 text-white font-semibold text-xs hover:bg-red-700">Delete Product</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- BULK IMPORT PRODUCTS MODAL -->
        <div x-show="importModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4" x-cloak>
            <div @click="importModalOpen = false" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div @click.stop class="relative z-10 w-full max-w-lg bg-white rounded-2xl p-6 shadow-2xl border border-slate-200">
                <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-100">
                    <h3 class="text-base font-bold text-slate-900">Bulk Upload Products</h3>
                    <a 
                        href="{{ route('admin.links.products.template', $link) }}" 
                        class="inline-flex items-center gap-1.5 text-xs font-semibold text-indigo-600 hover:underline"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                        </svg>
                        Download Template CSV
                    </a>
                </div>

                <form method="POST" action="{{ route('admin.links.products.import', $link) }}" enctype="multipart/form-data" class="space-y-4">
                    @csrf
                    
                    <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-600 space-y-1">
                        <div class="font-bold text-slate-800">Field Header Excel / CSV:</div>
                        <ul class="list-disc pl-4 space-y-0.5">
                            <li><span class="font-semibold text-slate-800">Product Name</span> (Wajib)</li>
                            <li><span class="font-semibold text-slate-800">Category</span> (Wajib — otomatis dibuat jika belum ada)</li>
                            <li><span class="font-semibold text-slate-800">Product URL</span> (Wajib)</li>
                            <li><span class="font-semibold text-slate-800">Description</span> (Opsional)</li>
                            <li><span class="font-semibold text-slate-800">Product Image</span> (Opsional URL gambar)</li>
                        </ul>
                    </div>

                    <div>
                        <label for="import_file" class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih File Excel / CSV (.xlsx, .xls, .csv)</label>
                        <input 
                            id="import_file"
                            type="file" 
                            name="file" 
                            accept=".xlsx,.xls,.csv"
                            required
                            class="w-full text-xs text-slate-500 file:mr-3 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 transition-all cursor-pointer border border-slate-200 rounded-xl p-1"
                        />
                    </div>

                    <div class="flex justify-end gap-2 pt-2 border-t border-slate-100">
                        <button type="button" @click="importModalOpen = false" class="px-4 py-2 rounded-xl border border-slate-200 text-xs font-semibold text-slate-600">Batal</button>
                        <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 text-white font-semibold text-xs hover:bg-emerald-700 shadow-sm">Upload & Import</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Alpine Component JavaScript Definition -->
    <script>
        function linkPageEditorComponent() {
            return {
                activeTab: @json(request('tab', 'profile')),
                mobilePreviewOpen: false,
                isDirty: false,
                importModalOpen: false,
                
                // Form State
                storeName: @json(old('store_name', $page->store_name)),
                bio: @json(old('bio', $page->bio ?? '')),
                profilePhoto: @json(old('profile_photo', $page->profile_photo ?? '')),
                coverType: @json(old('cover_type', $page->cover_type)),
                coverColor: @json(old('cover_color', $page->cover_color ?? '#4f46e5')),
                coverImage: @json(old('cover_image', $page->cover_image ?? '')),
                headerAlignment: @json(old('header_alignment', $page->header_alignment)),
                productLayout: @json(old('product_layout', $page->product_layout)),
                whatsappNumber: @json(old('whatsapp_number', $page->whatsapp_number ?? '')),
                source: @json(old('source', $page->source ?? '')),
                newSourceInput: '',
                sources: @json(old('sources', !empty($page->sources) ? $page->sources : (!empty($page->source) ? [$page->source] : []))),

                addSourceTag() {
                    const val = this.newSourceInput.trim().toLowerCase().replace(/[^a-z0-9_-]/g, '');
                    if (val && !this.sources.includes(val)) {
                        this.sources.push(val);
                        this.newSourceInput = '';
                        this.markDirty();
                    }
                },

                addPresetSource(preset) {
                    if (!this.sources.includes(preset)) {
                        this.sources.push(preset);
                        this.markDirty();
                    }
                },

                removeSourceTag(index) {
                    this.sources.splice(index, 1);
                    this.markDirty();
                },
                
                // Socials State
                socials: {
                    facebook: @json(old('socials.facebook', $socialsMap['facebook'] ?? '')),
                    instagram: @json(old('socials.instagram', $socialsMap['instagram'] ?? '')),
                    tiktok: @json(old('socials.tiktok', $socialsMap['tiktok'] ?? '')),
                    x: @json(old('socials.x', $socialsMap['x'] ?? '')),
                    threads: @json(old('socials.threads', $socialsMap['threads'] ?? ''))
                },
                
                // Categories & Products Data
                categories: @json($page->categories),
                products: @json($page->products),
                selectedCategoryFilter: 'all',

                // Category Modal State
                categoryModalOpen: false,
                editCategoryModalOpen: false,
                categoryName: '',
                editCategoryId: null,
                editCategoryName: '',

                // Product Modal State
                productModalOpen: false,
                editProductModalOpen: false,
                deleteProductModalOpen: false,
                productForm: {
                    id: null,
                    name: '',
                    category_id: '',
                    product_url: '',
                    description: '',
                    image_url: '',
                    status: 'active'
                },

                activeProductToDelete: null,

                get filteredProducts() {
                    let activeOnly = this.products.filter(function(p) {
                        return p.status === 'active';
                    });
                    if (this.selectedCategoryFilter === 'all') {
                        return activeOnly;
                    }
                    var self = this;
                    return activeOnly.filter(function(p) {
                        return p.category_id == self.selectedCategoryFilter;
                    });
                },

                markDirty() {
                    this.isDirty = true;
                },

                previewProfilePhoto(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.profilePhoto = URL.createObjectURL(file);
                        this.markDirty();
                    }
                },

                previewCoverImage(event) {
                    const file = event.target.files[0];
                    if (file) {
                        this.coverImage = URL.createObjectURL(file);
                        this.markDirty();
                    }
                },

                openEditCategoryModal(cat) {
                    this.editCategoryId = cat.id;
                    this.editCategoryName = cat.name;
                    this.editCategoryModalOpen = true;
                },

                openEditProductModal(product) {
                    this.productForm = {
                        id: product.id,
                        name: product.name,
                        category_id: product.category_id,
                        product_url: product.product_url,
                        description: product.description || '',
                        image_url: product.image_url || '',
                        status: product.status
                    };
                    this.editProductModalOpen = true;
                },

                openDeleteProductModal(product) {
                    this.activeProductToDelete = product;
                    this.deleteProductModalOpen = true;
                },

                init() {
                    window.addEventListener('submit', () => {
                        this.isDirty = false;
                    });

                    window.addEventListener('beforeunload', (e) => {
                        if (this.isDirty) {
                            e.preventDefault();
                            e.returnValue = '';
                        }
                    });
                }
            };
        }
    </script>
</x-layouts.admin>
