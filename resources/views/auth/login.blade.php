<x-layouts.app title="Sign In - LinkHub">
    <div class="flex-1 flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md text-center">
            <!-- App Logo / Header -->
            <a href="/" class="inline-flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-indigo-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-indigo-100">
                    L
                </div>
            </a>
            <h2 class="mt-6 text-2xl sm:text-3xl font-bold text-slate-900 tracking-tight">
                Sign in to LinkHub
            </h2>
            <p class="mt-2 text-sm text-slate-500">
                Enter your administrative credentials to access your dashboard
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow-xl shadow-slate-200/50 sm:rounded-2xl sm:px-10 border border-slate-200/80">
                <!-- Global / Field Error Banners -->
                @if ($errors->any())
                    <div class="mb-6 p-4 rounded-xl bg-red-50 border border-red-200/80 text-sm text-red-700 space-y-1">
                        <div class="font-semibold flex items-center gap-2">
                            <svg class="w-4 h-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                            Authentication Error
                        </div>
                        @foreach ($errors->all() as $error)
                            <p class="pl-6">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700">
                            Email
                        </label>
                        <div class="mt-1">
                            <input 
                                id="email" 
                                name="email" 
                                type="email" 
                                autocomplete="email" 
                                required 
                                value="{{ old('email') }}"
                                placeholder="Email"
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all"
                            />
                        </div>
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700">
                            Password
                        </label>
                        <div class="mt-1">
                            <input 
                                id="password" 
                                name="password" 
                                type="password" 
                                autocomplete="current-password" 
                                required 
                                placeholder="Password"
                                class="w-full rounded-xl border border-slate-200 px-4 py-3 text-sm text-slate-900 placeholder-slate-400 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none transition-all"
                            />
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember" name="remember" type="checkbox" class="h-4 w-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500">
                            <label for="remember" class="ml-2 block text-sm text-slate-600">
                                Remember me
                            </label>
                        </div>
                    </div>

                    <div>
                        <button type="submit" class="w-full flex justify-center py-3 px-4 rounded-xl text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 transition-all shadow-md shadow-indigo-600/20 hover:shadow-indigo-600/30 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">
                            Sign In
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="mt-6 text-center text-xs text-slate-400">
                <a href="/" class="hover:text-slate-600 transition-colors">← Back to LinkHub</a>
            </div>
        </div>
    </div>
</x-layouts.app>
