<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Link;
use App\Models\LinkCategory;
use App\Models\LinkPage;
use App\Models\LinkProduct;
use App\Models\LinkSocial;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LinkPageController extends Controller
{
    /**
     * Display the page editor for a specific link.
     */
    public function edit(Link $link): View
    {
        // Auto-initialize page configuration if not present
        $page = $link->page()->firstOrCreate([], [
            'store_name' => $link->title,
            'header_alignment' => 'center',
            'product_layout' => 'grid',
            'cover_type' => 'color',
            'cover_color' => '#4f46e5',
        ]);

        $page->load(['socials', 'categories', 'products.category']);

        $socialsMap = $page->socials->pluck('url', 'platform')->toArray();

        return view('admin.links.edit-page', compact('link', 'page', 'socialsMap'));
    }

    /**
     * Update page profile, appearance, contact, and social settings.
     */
    public function update(Request $request, Link $link): RedirectResponse
    {
        $page = $link->page()->firstOrCreate([], [
            'store_name' => $link->title,
            'header_alignment' => 'center',
            'product_layout' => 'grid',
            'cover_type' => 'color',
            'cover_color' => '#4f46e5',
        ]);

        $validated = $request->validate([
            'store_name' => ['required', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:500'],
            'profile_photo' => ['nullable', 'string', 'max:2048'],
            'cover_type' => ['required', Rule::in(['color', 'image'])],
            'cover_color' => ['nullable', 'string', 'max:50'],
            'cover_image' => ['nullable', 'string', 'max:2048'],
            'header_alignment' => ['required', Rule::in(['center', 'left'])],
            'product_layout' => ['required', Rule::in(['grid', 'list'])],
            'whatsapp_number' => ['nullable', 'string', 'max:30'],
            'source' => ['nullable', 'string', 'max:100'],
            'socials' => ['nullable', 'array'],
            'socials.*' => ['nullable', 'url', 'max:2048'],
            'profile_photo_file' => ['nullable', 'image', 'max:2048'],
            'cover_image_file' => ['nullable', 'image', 'max:4096'],
        ]);

        // Handle profile photo upload if provided
        if ($request->hasFile('profile_photo_file')) {
            $path = $request->file('profile_photo_file')->store('profiles', 'public');
            $validated['profile_photo'] = Storage::url($path);
        }

        // Handle cover image upload if provided
        if ($request->hasFile('cover_image_file')) {
            $path = $request->file('cover_image_file')->store('covers', 'public');
            $validated['cover_image'] = Storage::url($path);
        }

        // Clean whatsapp number (digits and optional leading +)
        if (!empty($validated['whatsapp_number'])) {
            $validated['whatsapp_number'] = preg_replace('/[^\d+]/', '', $validated['whatsapp_number']);
        }

        $page->update([
            'store_name' => $validated['store_name'],
            'bio' => $validated['bio'] ?? null,
            'profile_photo' => $validated['profile_photo'] ?? $page->profile_photo,
            'cover_type' => $validated['cover_type'],
            'cover_color' => $validated['cover_color'] ?? '#4f46e5',
            'cover_image' => $validated['cover_image'] ?? $page->cover_image,
            'header_alignment' => $validated['header_alignment'],
            'product_layout' => $validated['product_layout'],
            'whatsapp_number' => $validated['whatsapp_number'] ?? null,
            'source' => $validated['source'] ?? null,
        ]);

        // Update social links
        $allowedPlatforms = ['facebook', 'instagram', 'tiktok', 'x', 'threads'];
        $submittedSocials = $request->input('socials', []);

        foreach ($allowedPlatforms as $platform) {
            $url = $submittedSocials[$platform] ?? null;

            if (!empty($url)) {
                LinkSocial::updateOrCreate(
                    ['link_page_id' => $page->id, 'platform' => $platform],
                    ['url' => $url]
                );
            } else {
                LinkSocial::where('link_page_id', $page->id)
                    ->where('platform', $platform)
                    ->delete();
            }
        }

        return redirect()->route('admin.links.edit-page', $link)
            ->with('success', 'Page settings saved successfully.');
    }

    /**
     * Store a new product category.
     */
    public function storeCategory(Request $request, Link $link): RedirectResponse
    {
        $page = $link->page()->firstOrCreate([], [
            'store_name' => $link->title,
            'header_alignment' => 'center',
            'product_layout' => 'grid',
            'cover_type' => 'color',
            'cover_color' => '#4f46e5',
        ]);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('link_categories', 'name')->where('link_page_id', $page->id),
            ],
        ], [
            'name.unique' => 'This category name already exists for this page.',
        ]);

        LinkCategory::create([
            'link_page_id' => $page->id,
            'name' => $validated['name'],
        ]);

        return redirect()->route('admin.links.edit-page', ['link' => $link, 'tab' => 'products'])
            ->with('success', 'Category created successfully.');
    }

    /**
     * Update an existing product category.
     */
    public function updateCategory(Request $request, Link $link, LinkCategory $category): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('link_categories', 'name')
                    ->where('link_page_id', $link->page->id)
                    ->ignore($category->id),
            ],
        ]);

        $category->update(['name' => $validated['name']]);

        return redirect()->route('admin.links.edit-page', ['link' => $link, 'tab' => 'products'])
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Delete a category.
     */
    public function destroyCategory(Link $link, LinkCategory $category): RedirectResponse
    {
        $category->delete();

        return redirect()->route('admin.links.edit-page', ['link' => $link, 'tab' => 'products'])
            ->with('success', 'Category deleted successfully.');
    }

    /**
     * Store a new product.
     */
    public function storeProduct(Request $request, Link $link): RedirectResponse
    {
        $page = $link->page()->firstOrCreate([], [
            'store_name' => $link->title,
            'header_alignment' => 'center',
            'product_layout' => 'grid',
            'cover_type' => 'color',
            'cover_color' => '#4f46e5',
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => [
                'required',
                Rule::exists('link_categories', 'id')->where('link_page_id', $page->id),
            ],
            'product_url' => ['required', 'url', 'max:2048'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'image_file' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('products', 'public');
            $validated['image_url'] = Storage::url($path);
        }

        $maxOrder = LinkProduct::where('link_page_id', $page->id)->max('display_order') ?? 0;

        LinkProduct::create([
            'link_page_id' => $page->id,
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'image_url' => $validated['image_url'] ?? null,
            'product_url' => $validated['product_url'],
            'status' => $validated['status'],
            'display_order' => $maxOrder + 1,
        ]);

        return redirect()->route('admin.links.edit-page', ['link' => $link, 'tab' => 'products'])
            ->with('success', 'Product added successfully.');
    }

    /**
     * Update an existing product.
     */
    public function updateProduct(Request $request, Link $link, LinkProduct $product): RedirectResponse
    {
        $page = $link->page()->firstOrCreate([], [
            'store_name' => $link->title,
            'header_alignment' => 'center',
            'product_layout' => 'grid',
            'cover_type' => 'color',
            'cover_color' => '#4f46e5',
        ]);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => [
                'required',
                Rule::exists('link_categories', 'id')->where('link_page_id', $page->id),
            ],
            'product_url' => ['required', 'url', 'max:2048'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'image_file' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('products', 'public');
            $validated['image_url'] = Storage::url($path);
        }

        $product->update([
            'category_id' => $validated['category_id'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'image_url' => $validated['image_url'] ?? $product->image_url,
            'product_url' => $validated['product_url'],
            'status' => $validated['status'],
        ]);

        return redirect()->route('admin.links.edit-page', ['link' => $link, 'tab' => 'products'])
            ->with('success', 'Product updated successfully.');
    }

    /**
     * Delete a product.
     */
    public function destroyProduct(Link $link, LinkProduct $product): RedirectResponse
    {
        $product->delete();

        return redirect()->route('admin.links.edit-page', ['link' => $link, 'tab' => 'products'])
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Toggle product active/inactive status.
     */
    public function toggleProductStatus(Link $link, LinkProduct $product): RedirectResponse
    {
        $newStatus = $product->status === 'active' ? 'inactive' : 'active';
        $product->update(['status' => $newStatus]);

        return redirect()->route('admin.links.edit-page', ['link' => $link, 'tab' => 'products'])
            ->with('success', 'Product status updated.');
    }

    /**
     * Batch update product display ordering.
     */
    public function updateProductOrder(Request $request, Link $link): RedirectResponse
    {
        $orders = $request->input('orders', []); // e.g. [product_id => order]

        foreach ($orders as $productId => $order) {
            LinkProduct::where('id', $productId)
                ->where('link_page_id', $link->page->id)
                ->update(['display_order' => (int) $order]);
        }

        return redirect()->route('admin.links.edit-page', ['link' => $link, 'tab' => 'products'])
            ->with('success', 'Product ordering updated.');
    }

    /**
     * Bulk import products from Excel or CSV file.
     */
    public function importProducts(Request $request, Link $link): RedirectResponse
    {
        $page = $link->page()->firstOrCreate([], [
            'store_name' => $link->title,
            'header_alignment' => 'center',
            'product_layout' => 'grid',
            'cover_type' => 'color',
            'cover_color' => '#4f46e5',
        ]);

        $request->validate([
            'file' => ['required', 'file', 'max:5120'],
        ]);

        $file = $request->file('file');
        $extension = strtolower($file->getClientOriginalExtension());

        $rows = [];

        if (in_array($extension, ['csv', 'txt'])) {
            if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
                while (($data = fgetcsv($handle, 2000, ',')) !== false) {
                    $rows[] = $data;
                }
                fclose($handle);
            }
        } else {
            try {
                $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($file->getRealPath());
                $sheet = $spreadsheet->getActiveSheet();
                $rows = $sheet->toArray();
            } catch (\Throwable $e) {
                return redirect()->route('admin.links.edit-page', ['link' => $link, 'tab' => 'products'])
                    ->withErrors(['file' => 'Could not parse Excel spreadsheet file: ' . $e->getMessage()]);
            }
        }

        if (empty($rows) || count($rows) < 2) {
            return redirect()->route('admin.links.edit-page', ['link' => $link, 'tab' => 'products'])
                ->withErrors(['file' => 'Uploaded spreadsheet file is empty or missing data rows.']);
        }

        // Map Header Columns
        $rawHeaders = array_map('strtolower', array_map('trim', array_map('strval', $rows[0])));

        $nameIndex = $this->findHeaderIndex($rawHeaders, ['product_name', 'product name', 'name', 'nama_produk', 'nama produk', 'nama']);
        $catIndex = $this->findHeaderIndex($rawHeaders, ['category', 'category_name', 'kategori', 'nama_kategori', 'nama kategori']);
        $urlIndex = $this->findHeaderIndex($rawHeaders, ['product_url', 'product url', 'url', 'link', 'link_produk', 'link produk']);
        $descIndex = $this->findHeaderIndex($rawHeaders, ['description', 'deskripsi', 'detail']);
        $imgIndex = $this->findHeaderIndex($rawHeaders, ['product_image', 'product image', 'image', 'image_url', 'image url', 'gambar', 'foto', 'gambar_produk']);

        if ($nameIndex === false || $catIndex === false || $urlIndex === false) {
            return redirect()->route('admin.links.edit-page', ['link' => $link, 'tab' => 'products'])
                ->withErrors(['file' => 'Spreadsheet header is missing required columns: Product Name, Category, Product URL.']);
        }

        $importedCount = 0;
        $createdCategoriesCount = 0;
        $maxOrder = LinkProduct::where('link_page_id', $page->id)->max('display_order') ?? 0;

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            $productName = isset($row[$nameIndex]) ? trim((string) $row[$nameIndex]) : '';
            $categoryName = isset($row[$catIndex]) ? trim((string) $row[$catIndex]) : '';
            $productUrl = isset($row[$urlIndex]) ? trim((string) $row[$urlIndex]) : '';
            $description = ($descIndex !== false && isset($row[$descIndex])) ? trim((string) $row[$descIndex]) : null;
            $imageUrl = ($imgIndex !== false && isset($row[$imgIndex])) ? trim((string) $row[$imgIndex]) : null;

            if (empty($productName) || empty($categoryName) || empty($productUrl)) {
                continue; // Skip invalid rows
            }

            // Find or create category
            $category = LinkCategory::where('link_page_id', $page->id)
                ->where('name', $categoryName)
                ->first();

            if (!$category) {
                $category = LinkCategory::create([
                    'link_page_id' => $page->id,
                    'name' => $categoryName,
                ]);
                $createdCategoriesCount++;
            }

            $maxOrder++;

            LinkProduct::create([
                'link_page_id' => $page->id,
                'category_id' => $category->id,
                'name' => $productName,
                'description' => !empty($description) ? $description : null,
                'image_url' => !empty($imageUrl) ? $imageUrl : null,
                'product_url' => $productUrl,
                'status' => 'active',
                'display_order' => $maxOrder,
            ]);

            $importedCount++;
        }

        $msg = "Imported {$importedCount} products successfully.";
        if ($createdCategoriesCount > 0) {
            $msg .= " Created {$createdCategoriesCount} new categories.";
        }

        return redirect()->route('admin.links.edit-page', ['link' => $link, 'tab' => 'products'])
            ->with('success', $msg);
    }

    /**
     * Download sample Excel/CSV template for product import.
     */
    public function downloadProductTemplate(Link $link)
    {
        $csvHeader = ['Product Name', 'Category', 'Product URL', 'Description', 'Product Image'];
        $sampleRows = [
            ['Kaos Hitam Basic', 'Kaos', 'https://shopee.co.id/kaos-hitam', 'Kaos polos combed 30s premium', 'https://example.com/kaos.jpg'],
            ['Hoodie Oversize', 'Hoodie', 'https://shopee.co.id/hoodie-oversize', 'Hoodie hangat fleece katun', 'https://example.com/hoodie.jpg'],
            ['Topi Snapback', 'Aksesoris', 'https://tokopedia.com/topi-snapback', 'Topi kasual bordir rapi', 'https://example.com/topi.jpg'],
        ];

        $callback = function () use ($csvHeader, $sampleRows) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $csvHeader);

            foreach ($sampleRows as $row) {
                fputcsv($file, $row);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, 'product_import_template.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }

    private function findHeaderIndex(array $headers, array $candidates): int|false
    {
        foreach ($candidates as $candidate) {
            $index = array_search($candidate, $headers);
            if ($index !== false) {
                return $index;
            }
        }

        return false;
    }
}
