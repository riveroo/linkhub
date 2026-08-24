<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Link Unavailable - {{ config('app.name', 'LinkHub') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700,800&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full flex items-center justify-center p-4 font-sans antialiased text-slate-800 bg-slate-50">
    <div class="w-full max-w-md bg-white rounded-3xl p-8 shadow-xl border border-slate-200/80 text-center">
        <div class="w-16 h-16 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center mx-auto mb-4 border border-amber-100">
            <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
        </div>
        <h1 class="text-xl font-extrabold text-slate-900 mb-2">Link Unavailable</h1>
        <p class="text-sm text-slate-500 mb-6 leading-relaxed">
            This link is currently inactive or temporarily unavailable.
        </p>
        <a 
            href="{{ route('landing') }}" 
            class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl bg-indigo-600 text-white font-semibold text-sm shadow-md shadow-indigo-600/20 hover:bg-indigo-700 transition-colors"
        >
            Go to LinkHub
        </a>
    </div>
</body>
</html>
