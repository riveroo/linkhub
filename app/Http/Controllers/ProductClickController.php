<?php

namespace App\Http\Controllers;

use App\Models\ClickEvent;
use App\Models\Link;
use App\Models\LinkProduct;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class ProductClickController extends Controller
{
    /**
     * Track a product click and redirect to the product URL.
     *
     * Security: Validates product belongs to the given link before recording or redirecting.
     * Prevents open redirect attacks — redirect URL always comes from database, never from client.
     */
    public function track(Request $request, Link $link, LinkProduct $product): RedirectResponse
    {
        // Validate product belongs to this link's page (prevent open redirect)
        if (!$link->page || $product->link_page_id !== $link->page->id) {
            abort(404);
        }

        // Only track clicks on active links and products
        if ($link->status !== 'active' || $product->status !== 'active') {
            return redirect($product->product_url);
        }

        // Resolve source from session (set when user first visited the public page)
        $sourceId = session("link_{$link->id}_source_id");

        // Generate or retrieve visitor ID from session
        $visitorId = session('visitor_id');
        if (!$visitorId) {
            $visitorId = substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(24))), 0, 32);
            session(['visitor_id' => $visitorId]);
        }

        // Record product click event
        ClickEvent::create([
            'link_id'    => $link->id,
            'source_id'  => $sourceId ?: null,
            'product_id' => $product->id,
            'event_type' => ClickEvent::PRODUCT_CLICK,
            'visitor_id' => $visitorId,
            'clicked_at' => now(),
        ]);

        // Redirect to actual product URL (from database — never from client input)
        return redirect($product->product_url);
    }
}
