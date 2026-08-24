<x-layouts.admin>
    <x-slot:title>Analytics — LinkHub</x-slot:title>

    <div>
        <!-- Page Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Analytics</h1>
                <p class="text-sm text-slate-500 mt-1">Pantau performa semua link Anda dalam satu tampilan.</p>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" action="{{ route('admin.analytics.index') }}" class="bg-white border border-slate-200/80 rounded-2xl p-4 mb-6 shadow-sm">
            <div class="flex flex-wrap items-end gap-3">
                <!-- Link Filter -->
                <div class="flex-1 min-w-40">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Link</label>
                    <select name="link_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none bg-white">
                        <option value="">All Active Links</option>
                        @foreach($links as $link)
                            <option value="{{ $link->id }}" {{ $linkFilter == $link->id ? 'selected' : '' }}>{{ $link->title }}</option>
                        @endforeach
                    </select>
                </div>
                <!-- Source Filter -->
                <div class="flex-1 min-w-40">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Source</label>
                    <select name="source_id" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none bg-white">
                        <option value="">All Sources</option>
                        <option value="direct" {{ $sourceFilter === 'direct' ? 'selected' : '' }}>Direct / Unknown</option>
                        @foreach($sources as $src)
                            <option value="{{ $src->id }}" {{ $sourceFilter == $src->id ? 'selected' : '' }}>{{ $src->name }} ({{ $src->link->title ?? '' }})</option>
                        @endforeach
                    </select>
                </div>
                <!-- Date Range Filter -->
                <div class="flex-1 min-w-40">
                    <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-1.5">Date Range</label>
                    <select name="range" id="rangeSelect" onchange="this.form.submit()" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-800 focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/20 focus:outline-none bg-white">
                        <option value="today" {{ request('range') === 'today' ? 'selected' : '' }}>Today</option>
                        <option value="yesterday" {{ request('range') === 'yesterday' ? 'selected' : '' }}>Yesterday</option>
                        <option value="last7" {{ !request('range') || request('range') === 'last7' ? 'selected' : '' }}>Last 7 Days</option>
                        <option value="last30" {{ request('range') === 'last30' ? 'selected' : '' }}>Last 30 Days</option>
                        <option value="last90" {{ request('range') === 'last90' ? 'selected' : '' }}>Last 90 Days</option>
                    </select>
                </div>
                <button type="submit" class="px-5 py-2 rounded-xl bg-indigo-600 text-white font-semibold text-sm hover:bg-indigo-700 transition-colors shrink-0">
                    Apply
                </button>
            </div>
        </form>

        @if($totalVisits === 0 && $productClicks === 0)
            <!-- Empty State -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm py-20 text-center">
                <div class="w-16 h-16 mx-auto rounded-2xl bg-slate-100 flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                </div>
                <p class="text-base font-bold text-slate-700 mb-1">No analytics data yet</p>
                <p class="text-sm text-slate-500">Share your link to start collecting data.</p>
            </div>
        @else

        <!-- KPI Cards -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Total Visits</p>
                <p class="text-3xl font-extrabold text-slate-900">{{ number_format($totalVisits) }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ $dateLabel }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Product Clicks</p>
                <p class="text-3xl font-extrabold text-indigo-600">{{ number_format($productClicks) }}</p>
                <p class="text-xs text-slate-500 mt-1">{{ $dateLabel }}</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">CTR</p>
                <p class="text-3xl font-extrabold {{ $ctr >= 30 ? 'text-emerald-600' : 'text-slate-900' }}">{{ $ctr }}%</p>
                <p class="text-xs text-slate-500 mt-1">Clicks / Visits</p>
            </div>
            <div class="bg-white rounded-2xl border border-slate-200/80 p-5 shadow-sm">
                <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Unique Visitors</p>
                <p class="text-3xl font-extrabold text-slate-900">{{ number_format($uniqueVisitors) }}</p>
                <p class="text-xs text-slate-500 mt-1">by visitor ID</p>
            </div>
        </div>

        <!-- Traffic Trend Chart -->
        <div class="bg-white rounded-2xl border border-slate-200/80 p-6 mb-6 shadow-sm">
            <h2 class="text-sm font-bold text-slate-900 mb-4">Traffic Trend</h2>
            @if(array_sum($trendVisits) === 0 && array_sum($trendClicks) === 0)
                <p class="text-sm text-slate-400 text-center py-8">No traffic data in this period.</p>
            @else
                <div class="relative h-64">
                    <canvas id="trendChart"></canvas>
                </div>
            @endif
        </div>

        <!-- Sources & Top Links Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Traffic Sources -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="text-sm font-bold text-slate-900">Traffic Sources</h2>
                </div>
                @if(empty($sourcesData))
                    <p class="text-sm text-slate-400 text-center py-10">No source data.</p>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="text-left px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Source</th>
                                <th class="text-right px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Visits</th>
                                <th class="text-right px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Clicks</th>
                                <th class="text-right px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">CTR</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($sourcesData as $src)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-6 py-3 font-semibold text-slate-800 text-xs">
                                        {{ $src['name'] }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-slate-700 text-xs font-semibold">{{ number_format($src['visits']) }}</td>
                                    <td class="px-4 py-3 text-right text-indigo-600 text-xs font-semibold">{{ number_format($src['clicks']) }}</td>
                                    <td class="px-6 py-3 text-right text-xs font-bold {{ $src['ctr'] >= 30 ? 'text-emerald-600' : 'text-slate-600' }}">{{ $src['ctr'] }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>

            <!-- Top Links -->
            <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-100">
                    <h2 class="text-sm font-bold text-slate-900">Top Links</h2>
                </div>
                @if($topLinks->isEmpty())
                    <p class="text-sm text-slate-400 text-center py-10">No link data.</p>
                @else
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-100">
                            <tr>
                                <th class="text-left px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Link</th>
                                <th class="text-right px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Visits</th>
                                <th class="text-right px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Clicks</th>
                                <th class="text-right px-6 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">CTR</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach($topLinks as $item)
                                <tr class="hover:bg-slate-50/50">
                                    <td class="px-6 py-3">
                                        <a href="{{ route('admin.analytics.show', $item['link']) }}" class="font-semibold text-indigo-600 hover:text-indigo-700 text-xs hover:underline">
                                            {{ $item['link']->title }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-right text-slate-700 text-xs font-semibold">{{ number_format($item['visits']) }}</td>
                                    <td class="px-4 py-3 text-right text-indigo-600 text-xs font-semibold">{{ number_format($item['clicks']) }}</td>
                                    <td class="px-6 py-3 text-right text-xs font-bold {{ $item['ctr'] >= 30 ? 'text-emerald-600' : 'text-slate-600' }}">{{ $item['ctr'] }}%</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @endif
            </div>
        </div>

        <!-- Top Products -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-sm overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-slate-100">
                <h2 class="text-sm font-bold text-slate-900">Top Products</h2>
            </div>
            @if($topProducts->isEmpty())
                <p class="text-sm text-slate-400 text-center py-10">No product clicks yet.</p>
            @else
            <div class="divide-y divide-slate-100">
                @foreach($topProducts as $i => $row)
                    @if($row->product)
                    <div class="px-6 py-3.5 flex items-center gap-4 hover:bg-slate-50/50">
                        <span class="w-6 text-xs font-extrabold text-slate-400 shrink-0">#{{ $i + 1 }}</span>
                        @if($row->product->image_url)
                            <img src="{{ $row->product->image_url }}" class="w-9 h-9 rounded-lg object-cover shrink-0 border border-slate-100" alt="{{ $row->product->name }}" />
                        @else
                            <div class="w-9 h-9 rounded-lg bg-slate-100 shrink-0 flex items-center justify-center text-slate-400">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-900 truncate">{{ $row->product->name }}</p>
                            @if($row->product->page && $row->product->page->link)
                                <p class="text-xs text-slate-500 truncate">{{ $row->product->page->link->title }}</p>
                            @endif
                        </div>
                        <span class="text-sm font-extrabold text-indigo-600 shrink-0">{{ number_format($row->total) }} clicks</span>
                    </div>
                    @endif
                @endforeach
            </div>
            @endif
        </div>

        <!-- Peak Hours Heatmap & Recent Activity Row -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <!-- Peak Hours -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <h2 class="text-sm font-bold text-slate-900 mb-4">Peak Hours</h2>
                @php
                    $allValues = collect($peakHours)->flatten()->filter()->values();
                    $maxVal = $allValues->max() ?: 1;
                    $hours = [8, 10, 12, 14, 16, 18, 20, 22];
                @endphp
                <div class="overflow-x-auto">
                    <table class="w-full text-[11px]">
                        <thead>
                            <tr>
                                <th class="text-left pr-3 py-1 text-slate-400 font-semibold w-10">Day</th>
                                @foreach($hours as $h)
                                    <th class="text-center px-0.5 py-1 text-slate-400 font-semibold">{{ str_pad($h, 2, '0', STR_PAD_LEFT) }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($peakHours as $day => $hourData)
                                <tr>
                                    <td class="pr-3 py-1 font-semibold text-slate-600">{{ $day }}</td>
                                    @foreach($hours as $h)
                                        @php
                                            $val = $hourData[$h] ?? 0;
                                            $intensity = $maxVal > 0 ? $val / $maxVal : 0;
                                            if ($intensity === 0) $bg = 'bg-slate-100';
                                            elseif ($intensity < 0.25) $bg = 'bg-indigo-100';
                                            elseif ($intensity < 0.5) $bg = 'bg-indigo-200';
                                            elseif ($intensity < 0.75) $bg = 'bg-indigo-400';
                                            else $bg = 'bg-indigo-600';
                                        @endphp
                                        <td class="px-0.5 py-1 text-center">
                                            <div class="w-7 h-7 rounded {{ $bg }} mx-auto" title="{{ $val }} visits"></div>
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div class="flex items-center gap-1.5 mt-3 text-[10px] text-slate-400">
                    <span>Low</span>
                    <div class="w-4 h-4 rounded bg-slate-100"></div>
                    <div class="w-4 h-4 rounded bg-indigo-100"></div>
                    <div class="w-4 h-4 rounded bg-indigo-200"></div>
                    <div class="w-4 h-4 rounded bg-indigo-400"></div>
                    <div class="w-4 h-4 rounded bg-indigo-600"></div>
                    <span>High</span>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="bg-white rounded-2xl border border-slate-200/80 p-6 shadow-sm">
                <h2 class="text-sm font-bold text-slate-900 mb-4">Recent Activity</h2>
                @if($recentActivity->isEmpty())
                    <p class="text-sm text-slate-400 text-center py-8">No recent activity.</p>
                @else
                    <div class="space-y-3">
                        @foreach($recentActivity as $event)
                            <div class="flex items-start gap-3">
                                <div class="w-8 h-8 rounded-xl shrink-0 flex items-center justify-center {{ $event->event_type === 'product_click' ? 'bg-indigo-50 text-indigo-600' : 'bg-slate-100 text-slate-500' }}">
                                    @if($event->event_type === 'product_click')
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 15l-2 5L9 9l11 4-5 2zm0 0l5 5"/></svg>
                                    @else
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs font-semibold text-slate-900">
                                        {{ $event->event_type === 'product_click' ? 'Product Click' : 'Page Visit' }}
                                        @if($event->product) — <span class="text-indigo-600">{{ $event->product->name }}</span>@endif
                                    </p>
                                    <p class="text-[11px] text-slate-500 truncate">
                                        {{ $event->link?->title ?? '—' }}
                                        @if($event->source) · <span class="font-medium">{{ $event->source->name }}</span>
                                        @else · <span class="italic">Direct</span>
                                        @endif
                                    </p>
                                </div>
                                <span class="text-[10px] text-slate-400 shrink-0">{{ $event->clicked_at->format('H:i') }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        @endif
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    @if(array_sum($trendVisits) > 0 || array_sum($trendClicks) > 0)
    const ctx = document.getElementById('trendChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($trendDates),
                datasets: [
                    {
                        label: 'Visits',
                        data: @json($trendVisits),
                        borderColor: '#6366f1',
                        backgroundColor: 'rgba(99,102,241,0.08)',
                        borderWidth: 2,
                        pointRadius: 3,
                        fill: true,
                        tension: 0.4,
                    },
                    {
                        label: 'Product Clicks',
                        data: @json($trendClicks),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16,185,129,0.06)',
                        borderWidth: 2,
                        pointRadius: 3,
                        fill: true,
                        tension: 0.4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { intersect: false, mode: 'index' },
                plugins: {
                    legend: { position: 'top', labels: { font: { size: 12, family: 'Plus Jakarta Sans' } } }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { font: { size: 11 } } },
                    y: { beginAtZero: true, ticks: { font: { size: 11 } } }
                }
            }
        });
    }
    @endif
    </script>
    @endpush
</x-layouts.admin>
