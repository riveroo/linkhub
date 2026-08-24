<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClickEvent;
use App\Models\Link;
use App\Models\LinkProduct;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class DashboardController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function index(): View
    {
        $todayStart = Carbon::today();
        $todayEnd   = Carbon::today()->endOfDay();

        // 1. Quick Stats
        $activeLinksCount = Link::where('status', 'active')->count();
        
        $productsCount = LinkProduct::whereHas('page.link', function ($q) {
            $q->where('status', 'active');
        })->count();

        $visitsTodayCount = ClickEvent::where('event_type', ClickEvent::PAGE_VIEW)
            ->whereBetween('clicked_at', [$todayStart, $todayEnd])
            ->count();

        $clicksTodayCount = ClickEvent::where('event_type', ClickEvent::PRODUCT_CLICK)
            ->whereBetween('clicked_at', [$todayStart, $todayEnd])
            ->count();

        // 2. My Links (Max 5, Active first, then updated_at desc)
        $myLinks = Link::with('page')
            ->select('links.*')
            ->selectSub(function ($query) use ($todayStart, $todayEnd) {
                $query->from('click_events')
                    ->whereColumn('click_events.link_id', 'links.id')
                    ->where('event_type', ClickEvent::PAGE_VIEW)
                    ->whereBetween('clicked_at', [$todayStart, $todayEnd])
                    ->selectRaw('count(*)');
            }, 'visits_today')
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        // 3. Recent Activity (Max 5 latest click_events)
        $recentActivities = ClickEvent::with(['link', 'source', 'product'])
            ->latest('clicked_at')
            ->take(5)
            ->get();

        // Formatting date in Indonesian
        Carbon::setLocale('id');
        $currentDateFormatted = Carbon::now()->translatedFormat('l, d F Y');
        $userName = Auth::user()?->name ?? 'Admin';

        return view('admin.dashboard', compact(
            'userName',
            'currentDateFormatted',
            'activeLinksCount',
            'productsCount',
            'visitsTodayCount',
            'clicksTodayCount',
            'myLinks',
            'recentActivities'
        ));
    }
}
