<?php

namespace App\Http\Controllers;

use App\Models\Link;
use Illuminate\Http\Response;
use Illuminate\View\View;

class PublicLinkController extends Controller
{
    /**
     * Display the public link page for a given slug.
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

        // Increment click counter
        $link->increment('clicks');

        $link->load([
            'page.socials',
            'page.categories',
            'page.products' => function ($query) {
                $query->where('status', 'active')
                    ->orderBy('display_order', 'asc')
                    ->orderBy('created_at', 'asc')
                    ->with('category');
            },
        ]);

        $page = $link->page;
        $products = $page ? $page->products : collect();
        $categories = $page ? $page->categories : collect();
        $socials = $page ? $page->socials->pluck('url', 'platform')->toArray() : [];

        // Normalize WhatsApp number to international format (e.g., 0812... -> 62812...)
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
