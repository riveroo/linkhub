<?php

namespace App\Http\Controllers;

use App\Models\ClickEvent;
use App\Models\Link;
use App\Models\Source;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PublicLinkController extends Controller
{
    /**
     * Display the public link page for a given slug and record a page view event.
     */
    public function show(string $slug): View|Response
    {
        $link = Link::where('slug', $slug)->first();

        if (!$link) {
            abort(404, "Page Not Found. The link you're looking for doesn't exist.");
        }

        if ($link->status !== 'active') {
            return response()->view('public.unavailable', [
                'link' => $link,
            ], 403);
        }

        // --- Source resolution ---
        // Look up source by ?src= query param, scoped to this link.
        // Unknown codes are silently ignored (source_id = NULL = Direct traffic).
        $sourceId = null;
        $srcCode = request()->query('src');

        if ($srcCode) {
            $source = Source::where('link_id', $link->id)
                ->where('code', $srcCode)
                ->first();
            $sourceId = $source?->id;
        }

        // Persist source in session so product clicks on this page retain the same source
        session(["link_{$link->id}_source_id" => $sourceId]);

        // --- Visitor identifier (privacy-friendly, no IP stored) ---
        $visitorId = session('visitor_id');
        if (!$visitorId) {
            $visitorId = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(24))), 0, 32);
            session(['visitor_id' => $visitorId]);
        }

        // --- Record page view event ---
        ClickEvent::create([
            'link_id'    => $link->id,
            'source_id'  => $sourceId,
            'product_id' => null,
            'event_type' => ClickEvent::PAGE_VIEW,
            'visitor_id' => $visitorId,
            'clicked_at' => now(),
        ]);

        $link->load([
            'page.socials',
            'page.categories.subcategories',
            'page.products' => function ($query) {
                $query->where('status', 'active')
                    ->orderBy('display_order', 'asc')
                    ->orderBy('created_at', 'asc')
                    ->with(['category', 'subcategory']);
            },
        ]);

        $page = $link->page;
        $products = $page ? $page->products : collect();
        $categories = $page ? $page->categories : collect();
        $socials = $page ? $page->socials->pluck('url', 'platform')->toArray() : [];

        // Normalize WhatsApp number to international format (e.g., 0812... → 62812...)
        $normalizedWa = null;
        if ($page && !empty($page->whatsapp_number)) {
            $digits = preg_replace('/[^\d]/', '', $page->whatsapp_number);
            if (str_starts_with($digits, '0')) {
                $digits = '62' . substr($digits, 1);
            }
            $normalizedWa = $digits;
        }

        return view('public.link', compact('link', 'page', 'products', 'categories', 'socials', 'normalizedWa'));
    }
}
