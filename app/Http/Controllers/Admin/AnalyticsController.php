<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClickEvent;
use App\Models\Link;
use App\Models\Source;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class AnalyticsController extends Controller
{
    /**
     * Analytics Overview — all active links aggregated.
     */
    public function index(Request $request): View
    {
        [$startDate, $endDate, $dateLabel] = $this->parseDateRange($request);

        $links   = Link::where('status', 'active')->orderBy('title')->get();
        $sources = Source::with('link')->orderBy('name')->get();

        $linkFilter   = $request->input('link_id');
        $sourceFilter = $request->input('source_id');

        // Base query builder scoped to filters
        $baseQuery = fn () => ClickEvent::query()
            ->whereBetween('clicked_at', [$startDate->startOfDay()->copy(), $endDate->endOfDay()->copy()])
            ->when($linkFilter, fn ($q) => $q->where('link_id', $linkFilter))
            ->when($sourceFilter === 'direct', fn ($q) => $q->whereNull('source_id'))
            ->when($sourceFilter && $sourceFilter !== 'direct', fn ($q) => $q->where('source_id', $sourceFilter));

        // KPI: Total Visits
        $totalVisits = $baseQuery()->where('event_type', ClickEvent::PAGE_VIEW)->count();

        // KPI: Product Clicks
        $productClicks = $baseQuery()->where('event_type', ClickEvent::PRODUCT_CLICK)->count();

        // KPI: CTR
        $ctr = $totalVisits > 0 ? round(($productClicks / $totalVisits) * 100, 1) : 0;

        // KPI: Unique Visitors (by visitor_id)
        $uniqueVisitors = $baseQuery()
            ->where('event_type', ClickEvent::PAGE_VIEW)
            ->whereNotNull('visitor_id')
            ->distinct('visitor_id')
            ->count('visitor_id');

        // Traffic Trend (daily breakdown)
        $trend = $baseQuery()
            ->selectRaw('DATE(clicked_at) as date, event_type, COUNT(*) as total')
            ->groupBy('date', 'event_type')
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        $trendDates = [];
        $trendVisits = [];
        $trendClicks = [];
        $period = Carbon::parse($startDate)->startOfDay();
        $endPeriod = Carbon::parse($endDate)->endOfDay();
        while ($period->lte($endPeriod)) {
            $dateKey = $period->toDateString();
            $trendDates[] = $period->format('d M');
            $dayData = $trend->get($dateKey, collect());
            $trendVisits[] = $dayData->where('event_type', ClickEvent::PAGE_VIEW)->sum('total');
            $trendClicks[] = $dayData->where('event_type', ClickEvent::PRODUCT_CLICK)->sum('total');
            $period->addDay();
        }

        // Traffic Sources breakdown
        $trafficSources = $baseQuery()
            ->selectRaw('source_id, event_type, COUNT(*) as total')
            ->groupBy('source_id', 'event_type')
            ->with('source')
            ->get()
            ->groupBy('source_id');

        $sourcesData = [];
        foreach ($trafficSources as $srcId => $events) {
            $srcModel = $events->first()->source;
            $views = $events->where('event_type', ClickEvent::PAGE_VIEW)->sum('total');
            $clicks = $events->where('event_type', ClickEvent::PRODUCT_CLICK)->sum('total');
            $sourcesData[] = [
                'name'   => $srcModel ? $srcModel->name : 'Direct / Unknown',
                'visits' => $views,
                'clicks' => $clicks,
                'ctr'    => $views > 0 ? round(($clicks / $views) * 100, 1) : 0,
            ];
        }
        usort($sourcesData, fn ($a, $b) => $b['visits'] - $a['visits']);

        // Top Links
        $topLinks = ClickEvent::query()
            ->selectRaw('link_id, event_type, COUNT(*) as total')
            ->whereBetween('clicked_at', [$startDate->startOfDay()->copy(), $endDate->endOfDay()->copy()])
            ->when($sourceFilter === 'direct', fn ($q) => $q->whereNull('source_id'))
            ->when($sourceFilter && $sourceFilter !== 'direct', fn ($q) => $q->where('source_id', $sourceFilter))
            ->groupBy('link_id', 'event_type')
            ->with('link')
            ->get()
            ->groupBy('link_id')
            ->map(function ($events) {
                $link   = $events->first()->link;
                $views  = $events->where('event_type', ClickEvent::PAGE_VIEW)->sum('total');
                $clicks = $events->where('event_type', ClickEvent::PRODUCT_CLICK)->sum('total');
                return [
                    'link'   => $link,
                    'visits' => $views,
                    'clicks' => $clicks,
                    'ctr'    => $views > 0 ? round(($clicks / $views) * 100, 1) : 0,
                ];
            })
            ->filter(fn ($d) => $d['link'] && $d['link']->status === 'active')
            ->sortByDesc('visits')
            ->values()
            ->take(10);

        // Top Products
        $topProducts = ClickEvent::query()
            ->selectRaw('product_id, COUNT(*) as total')
            ->where('event_type', ClickEvent::PRODUCT_CLICK)
            ->whereBetween('clicked_at', [$startDate->startOfDay()->copy(), $endDate->endOfDay()->copy()])
            ->when($linkFilter, fn ($q) => $q->where('link_id', $linkFilter))
            ->when($sourceFilter === 'direct', fn ($q) => $q->whereNull('source_id'))
            ->when($sourceFilter && $sourceFilter !== 'direct', fn ($q) => $q->where('source_id', $sourceFilter))
            ->whereNotNull('product_id')
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->limit(10)
            ->with('product.page.link')
            ->get();

        // Peak Hours (page views grouped by day of week + hour)
        $peakHours = $this->getPeakHours($baseQuery());

        // Recent Activity
        $recentActivity = $baseQuery()
            ->with(['link', 'source', 'product'])
            ->orderByDesc('clicked_at')
            ->limit(10)
            ->get();

        return view('admin.analytics.index', compact(
            'links', 'sources',
            'linkFilter', 'sourceFilter', 'dateLabel',
            'startDate', 'endDate',
            'totalVisits', 'productClicks', 'ctr', 'uniqueVisitors',
            'trendDates', 'trendVisits', 'trendClicks',
            'sourcesData', 'topLinks', 'topProducts',
            'peakHours', 'recentActivity'
        ));
    }

    /**
     * Link Analytics Detail — scoped to a single link.
     */
    public function show(Request $request, Link $link): View
    {
        [$startDate, $endDate, $dateLabel] = $this->parseDateRange($request);
        $sourceFilter = $request->input('source_id');

        $linkSources = $link->sources()->orderBy('name')->get();

        $baseQuery = fn () => ClickEvent::where('link_id', $link->id)
            ->whereBetween('clicked_at', [$startDate->startOfDay()->copy(), $endDate->endOfDay()->copy()])
            ->when($sourceFilter === 'direct', fn ($q) => $q->whereNull('source_id'))
            ->when($sourceFilter && $sourceFilter !== 'direct', fn ($q) => $q->where('source_id', $sourceFilter));

        $totalVisits    = $baseQuery()->where('event_type', ClickEvent::PAGE_VIEW)->count();
        $productClicks  = $baseQuery()->where('event_type', ClickEvent::PRODUCT_CLICK)->count();
        $ctr            = $totalVisits > 0 ? round(($productClicks / $totalVisits) * 100, 1) : 0;
        $uniqueVisitors = $baseQuery()->where('event_type', ClickEvent::PAGE_VIEW)->whereNotNull('visitor_id')->distinct('visitor_id')->count('visitor_id');

        // Traffic Trend
        $trend = $baseQuery()
            ->selectRaw('DATE(clicked_at) as date, event_type, COUNT(*) as total')
            ->groupBy('date', 'event_type')
            ->orderBy('date')
            ->get()
            ->groupBy('date');

        $trendDates = [];
        $trendVisits = [];
        $trendClicks = [];
        $period = Carbon::parse($startDate)->startOfDay();
        $endPeriod = Carbon::parse($endDate)->endOfDay();
        while ($period->lte($endPeriod)) {
            $dateKey = $period->toDateString();
            $trendDates[] = $period->format('d M');
            $dayData = $trend->get($dateKey, collect());
            $trendVisits[] = $dayData->where('event_type', ClickEvent::PAGE_VIEW)->sum('total');
            $trendClicks[] = $dayData->where('event_type', ClickEvent::PRODUCT_CLICK)->sum('total');
            $period->addDay();
        }

        // Traffic Sources
        $trafficSources = $baseQuery()
            ->selectRaw('source_id, event_type, COUNT(*) as total')
            ->groupBy('source_id', 'event_type')
            ->with('source')
            ->get()
            ->groupBy('source_id');

        $sourcesData = [];
        foreach ($trafficSources as $srcId => $events) {
            $srcModel = $events->first()->source;
            $views = $events->where('event_type', ClickEvent::PAGE_VIEW)->sum('total');
            $clicks = $events->where('event_type', ClickEvent::PRODUCT_CLICK)->sum('total');
            $sourcesData[] = [
                'name'   => $srcModel ? $srcModel->name : 'Direct / Unknown',
                'visits' => $views,
                'clicks' => $clicks,
                'ctr'    => $views > 0 ? round(($clicks / $views) * 100, 1) : 0,
            ];
        }
        usort($sourcesData, fn ($a, $b) => $b['visits'] - $a['visits']);

        // Top Products
        $topProducts = $baseQuery()
            ->selectRaw('product_id, COUNT(*) as total')
            ->where('event_type', ClickEvent::PRODUCT_CLICK)
            ->whereNotNull('product_id')
            ->groupBy('product_id')
            ->orderByDesc('total')
            ->limit(10)
            ->with('product')
            ->get();

        // Peak Hours
        $peakHours = $this->getPeakHours($baseQuery());

        // Recent Activity
        $recentActivity = $baseQuery()
            ->with(['link', 'source', 'product'])
            ->orderByDesc('clicked_at')
            ->limit(10)
            ->get();

        return view('admin.analytics.show', compact(
            'link', 'linkSources', 'sourceFilter', 'dateLabel',
            'startDate', 'endDate',
            'totalVisits', 'productClicks', 'ctr', 'uniqueVisitors',
            'trendDates', 'trendVisits', 'trendClicks',
            'sourcesData', 'topProducts',
            'peakHours', 'recentActivity'
        ));
    }

    /**
     * Parse date range from request.
     * Returns [startDate, endDate, label].
     */
    private function parseDateRange(Request $request): array
    {
        $range = $request->input('range', 'last7');

        switch ($range) {
            case 'today':
                return [now()->startOfDay(), now()->endOfDay(), 'Today'];
            case 'yesterday':
                return [now()->subDay()->startOfDay(), now()->subDay()->endOfDay(), 'Yesterday'];
            case 'last30':
                return [now()->subDays(29)->startOfDay(), now()->endOfDay(), 'Last 30 Days'];
            case 'last90':
                return [now()->subDays(89)->startOfDay(), now()->endOfDay(), 'Last 90 Days'];
            case 'custom':
                $start = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : now()->subDays(6);
                $end   = $request->input('end_date')   ? Carbon::parse($request->input('end_date'))   : now();
                return [$start->startOfDay(), $end->endOfDay(), 'Custom Range'];
            default: // last7
                return [now()->subDays(6)->startOfDay(), now()->endOfDay(), 'Last 7 Days'];
        }
    }

    /**
     * Build a peak hours matrix [day_of_week][hour] => count.
     * Uses page_view events.
     */
    private function getPeakHours($baseQuery): array
    {
        $rows = $baseQuery
            ->where('event_type', ClickEvent::PAGE_VIEW)
            ->selectRaw('DAYOFWEEK(clicked_at) as dow, HOUR(clicked_at) as hour, COUNT(*) as total')
            ->groupBy('dow', 'hour')
            ->get();

        // dow: 1=Sunday … 7=Saturday, reorder to Monday-first
        $matrix = [];
        $days   = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        // DAYOFWEEK: 1=Sun,2=Mon,...,7=Sat
        $dowMap = [2 => 0, 3 => 1, 4 => 2, 5 => 3, 6 => 4, 7 => 5, 1 => 6];
        foreach ($days as $i => $day) {
            $matrix[$day] = array_fill(0, 24, 0);
        }
        foreach ($rows as $row) {
            $dayIdx = $dowMap[$row->dow] ?? 0;
            $dayName = $days[$dayIdx];
            $matrix[$dayName][$row->hour] = (int) $row->total;
        }
        return $matrix;
    }
}
