<x-layouts.admin title="Dashboard - LinkHub">
    <!-- Welcome Header Section -->
    <div class="mb-8">
        <h1 class="text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
            Welcome back, Admin
        </h1>
        <p class="mt-1 text-sm sm:text-base text-slate-500">
            Here's an overview of your link performance and activity.
        </p>
    </div>

    <!-- Overview Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Links Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-slate-500">Total Links</span>
                <div class="w-10 h-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-extrabold text-slate-900 tracking-tight">0</span>
            </div>
        </div>

        <!-- Active Links Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-slate-500">Active Links</span>
                <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-extrabold text-slate-900 tracking-tight">0</span>
            </div>
        </div>

        <!-- Total Clicks Card -->
        <div class="bg-white p-6 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <span class="text-sm font-semibold text-slate-500">Total Clicks</span>
                <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5M7.188 2.239l.777 2.897M5.136 7.965l-2.898-.777M13.95 4.05l-2.122 2.122m-5.657 5.656l-2.12 2.122" />
                    </svg>
                </div>
            </div>
            <div class="mt-4">
                <span class="text-3xl font-extrabold text-slate-900 tracking-tight">0</span>
            </div>
        </div>
    </div>
</x-layouts.admin>
