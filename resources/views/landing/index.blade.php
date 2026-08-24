<x-layouts.app title="LinkHub - Simple Link Sharing">
    <!-- Navigation Header -->
    <header class="w-full border-b border-slate-200/80 bg-white/80 backdrop-blur-md sticky top-0 z-30">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 h-20 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-600 flex items-center justify-center text-white font-bold text-lg shadow-md shadow-indigo-100">
                    L
                </div>
                <span class="text-xl font-bold tracking-tight text-slate-900">LinkHub</span>
            </div>

            <div class="flex items-center gap-4">
                @auth
                    <a href="{{ route('admin.dashboard') }}" class="inline-flex items-center justify-center px-4 py-2 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-all shadow-sm">
                        Dashboard
                    </a>
                @else
                    <a href="{{ route('login') }}" class="inline-flex items-center justify-center px-5 py-2.5 rounded-xl text-sm font-semibold text-slate-700 hover:text-indigo-600 hover:bg-slate-100/80 transition-all">
                        Login
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Hero Section -->
    <main class="flex-1 flex items-center justify-center py-20 lg:py-32 px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mx-auto text-center space-y-8">
            <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 border border-indigo-100">
                <span class="w-2 h-2 rounded-full bg-indigo-600 animate-pulse"></span>
                Clean & Fast Personal Hub
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold text-slate-900 tracking-tight leading-[1.15]">
                Simple Link Sharing
            </h1>

            <p class="text-lg sm:text-xl text-slate-600 max-w-2xl mx-auto leading-relaxed font-normal">
                Manage and share your important links from one simple dashboard.
            </p>

            <div class="pt-4 flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('login') }}" class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 rounded-xl text-base font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-600/20 hover:shadow-indigo-600/30">
                    Get Started
                    <svg class="w-5 h-5 ml-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                    </svg>
                </a>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer class="border-t border-slate-200/60 py-8 text-center text-xs text-slate-400">
        <p>&copy; {{ date('Y') }} LinkHub. All rights reserved.</p>
    </footer>
</x-layouts.app>
