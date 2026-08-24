<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Link;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LinkController extends Controller
{
    /**
     * Display a listing of links with summary counts, search, and status filters.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search');
        $status = $request->query('status');

        $totalLinksCount = Link::count();
        $activeLinksCount = Link::where('status', 'active')->count();
        $inactiveLinksCount = Link::where('status', 'inactive')->count();
        $totalClicksCount = (int) Link::sum('clicks');

        $links = Link::search($search)
            ->filterStatus($status)
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('admin.links.index', compact(
            'links',
            'search',
            'status',
            'totalLinksCount',
            'activeLinksCount',
            'inactiveLinksCount',
            'totalClicksCount'
        ));
    }

    /**
     * Store a newly created link in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        // Pre-normalize slug if submitted
        if ($request->has('slug')) {
            $request->merge([
                'slug' => Str::slug($request->input('slug'))
            ]);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:2048'],
            'slug' => ['required', 'string', 'max:255', 'alpha_dash:ascii', 'unique:links,slug'],
        ], [
            'slug.unique' => 'This slug is already in use.',
            'url.url' => 'Please enter a valid URL (e.g. https://example.com).',
        ]);

        $validated['status'] = 'active';

        Link::create($validated);

        return redirect()->route('admin.links.index')
            ->with('success', 'Link created successfully.');
    }

    /**
     * Update the specified link in storage.
     */
    public function update(Request $request, Link $link): RedirectResponse
    {
        // Pre-normalize slug if submitted
        if ($request->has('slug')) {
            $request->merge([
                'slug' => Str::slug($request->input('slug'))
            ]);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'url' => ['nullable', 'url', 'max:2048'],
            'slug' => [
                'required',
                'string',
                'max:255',
                'alpha_dash:ascii',
                Rule::unique('links', 'slug')->ignore($link->id)
            ],
        ], [
            'slug.unique' => 'This slug is already in use.',
            'url.url' => 'Please enter a valid URL (e.g. https://example.com).',
        ]);

        $link->update($validated);

        return redirect()->route('admin.links.index')
            ->with('success', 'Link updated successfully.');
    }

    /**
     * Remove the specified link from storage.
     */
    public function destroy(Link $link): RedirectResponse
    {
        $link->delete();

        return redirect()->route('admin.links.index')
            ->with('success', 'Link deleted successfully.');
    }

    /**
     * Toggle the active/inactive status of the link.
     */
    public function toggleStatus(Link $link): RedirectResponse
    {
        $newStatus = $link->status === 'active' ? 'inactive' : 'active';
        $link->update(['status' => $newStatus]);

        $message = $newStatus === 'active' ? 'Link enabled successfully.' : 'Link disabled successfully.';

        return redirect()->route('admin.links.index')
            ->with('success', $message);
    }
}
