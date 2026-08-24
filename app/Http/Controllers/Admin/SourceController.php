<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Link;
use App\Models\Source;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SourceController extends Controller
{
    /**
     * Display all sources for a link.
     */
    public function index(Link $link): View
    {
        $sources = $link->sources()->withCount('clickEvents')->get();

        return view('admin.links.sources', compact('link', 'sources'));
    }

    /**
     * Store a new source.
     */
    public function store(Request $request, Link $link): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9][a-z0-9_-]*$/',
                Rule::unique('sources')->where('link_id', $link->id),
            ],
        ], [
            'code.regex' => 'Code harus dimulai dengan huruf kecil atau angka, dan hanya boleh mengandung huruf kecil, angka, tanda hubung (-), dan garis bawah (_).',
            'code.unique' => 'Code sudah digunakan oleh source lain pada link ini.',
        ]);

        $link->sources()->create($validated);

        return redirect()->route('admin.links.sources.index', $link)
            ->with('success', 'Source berhasil ditambahkan.');
    }

    /**
     * Update an existing source.
     */
    public function update(Request $request, Link $link, Source $source): RedirectResponse
    {
        // Ensure source belongs to this link
        abort_if($source->link_id !== $link->id, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => [
                'required',
                'string',
                'max:100',
                'regex:/^[a-z0-9][a-z0-9_-]*$/',
                Rule::unique('sources')->where('link_id', $link->id)->ignore($source->id),
            ],
        ], [
            'code.regex' => 'Code harus dimulai dengan huruf kecil atau angka, dan hanya boleh mengandung huruf kecil, angka, tanda hubung (-), dan garis bawah (_).',
            'code.unique' => 'Code sudah digunakan oleh source lain pada link ini.',
        ]);

        $source->update($validated);

        return redirect()->route('admin.links.sources.index', $link)
            ->with('success', 'Source berhasil diperbarui.');
    }

    /**
     * Delete a source.
     */
    public function destroy(Link $link, Source $source): RedirectResponse
    {
        abort_if($source->link_id !== $link->id, 403);

        $source->delete();

        return redirect()->route('admin.links.sources.index', $link)
            ->with('success', 'Source berhasil dihapus.');
    }
}
