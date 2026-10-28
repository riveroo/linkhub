<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Link;
use App\Models\LinkCategory;
use App\Models\LinkPage;
use App\Models\LinkProduct;
use App\Models\LinkSocial;
use App\Models\LinkSubcategory;
use App\Services\ProductBulkActionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
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

        $page->load(['socials', 'categories.subcategories', 'products.category', 'products.subcategory']);

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
            'sources' => ['nullable', 'array'],
            'sources.*' => ['nullable', 'string', 'max:100'],
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

        // Clean sources array (remove empty or duplicate values)
        $sources = [];
        if (!empty($validated['sources'])) {
            $sources = array_values(array_unique(array_filter(array_map('trim', $validated['sources']))));
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
            'source' => $validated['source'] ?? ($sources[0] ?? null),
            'sources' => $sources,
        ]);

        // Update social links
        $allowedPlatforms = ['facebook', 'instagram', 'tiktok', 'x', 'threads', 'shopee', 'tokopedia'];
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

        $rawName = trim((string) $request->input('name', ''));
        $rawSubcategories = $request->input('subcategories', []);
        if (!is_array($rawSubcategories)) {
            $rawSubcategories = [];
        }

        $request->merge(['name' => $rawName]);

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) use ($page) {
                    $exists = LinkCategory::where('link_page_id', $page->id)
                        ->whereRaw('LOWER(name) = ?', [mb_strtolower($value)])
                        ->exists();
                    if ($exists) {
                        $fail('This category name already exists for this page.');
                    }
                },
            ],
            'subcategories' => ['nullable', 'array'],
        ]);

        $normalizedSubcategories = [];
        $seenLower = [];

        $validator->after(function ($validator) use ($rawSubcategories, &$normalizedSubcategories, &$seenLower) {
            foreach ($rawSubcategories as $index => $item) {
                $subName = is_array($item) ? ($item['name'] ?? '') : (is_scalar($item) ? (string) $item : '');
                $trimmed = trim($subName);

                if ($trimmed === '') {
                    $validator->errors()->add("subcategories.{$index}", 'Subcategory name cannot be empty.');
                    continue;
                }

                if (mb_strlen($trimmed) > 100) {
                    $validator->errors()->add("subcategories.{$index}", 'Subcategory name must not exceed 100 characters.');
                    continue;
                }

                $lower = mb_strtolower($trimmed);
                if (isset($seenLower[$lower])) {
                    $validator->errors()->add("subcategories.{$index}", "Duplicate subcategory name \"{$trimmed}\" within the same category.");
                    continue;
                }

                $seenLower[$lower] = true;
                $normalizedSubcategories[] = $trimmed;
            }
        });

        $validated = $validator->validate();

        DB::transaction(function () use ($page, $validated, $normalizedSubcategories) {
            $category = LinkCategory::create([
                'link_page_id' => $page->id,
                'name' => $validated['name'],
            ]);

            foreach ($normalizedSubcategories as $subName) {
                LinkSubcategory::create([
                    'category_id' => $category->id,
                    'name' => $subName,
                ]);
            }
        });

        return redirect()->route('admin.links.edit-page', ['link' => $link, 'tab' => 'products'])
            ->with('success', 'Category created successfully.');
    }

    /**
     * Update an existing product category.
     */
    public function updateCategory(Request $request, Link $link, LinkCategory $category): RedirectResponse
    {
        if (!$link->page || $category->link_page_id !== $link->page->id) {
            abort(404);
        }

        $rawName = trim((string) $request->input('name', ''));
        $hasSubcategoriesInput = $request->has('subcategories') || $request->boolean('sync_subcategories');
        $rawSubcategories = $request->input('subcategories', []);
        if (!is_array($rawSubcategories)) {
            $rawSubcategories = [];
        }

        $request->merge(['name' => $rawName]);

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'string',
                'max:100',
                function ($attribute, $value, $fail) use ($link, $category) {
                    $exists = LinkCategory::where('link_page_id', $link->page->id)
                        ->where('id', '!=', $category->id)
                        ->whereRaw('LOWER(name) = ?', [mb_strtolower($value)])
                        ->exists();
                    if ($exists) {
                        $fail('This category name already exists for this page.');
                    }
                },
            ],
            'subcategories' => ['nullable', 'array'],
        ]);

        $normalizedSubcategories = [];
        $seenLower = [];
        $existingSubcategories = $category->subcategories()->pluck('id')->all();

        $validator->after(function ($validator) use ($rawSubcategories, &$normalizedSubcategories, &$seenLower, $existingSubcategories) {
            foreach ($rawSubcategories as $index => $item) {
                $subId = is_array($item) && !empty($item['id']) ? (int) $item['id'] : null;
                $subName = is_array($item) ? ($item['name'] ?? '') : (is_scalar($item) ? (string) $item : '');
                $trimmed = trim((string) $subName);

                if ($subId !== null && !in_array($subId, $existingSubcategories, true)) {
                    $validator->errors()->add("subcategories.{$index}", 'Invalid subcategory reference for this category.');
                    continue;
                }

                if ($trimmed === '') {
                    $validator->errors()->add("subcategories.{$index}", 'Subcategory name cannot be empty.');
                    continue;
                }

                if (mb_strlen($trimmed) > 100) {
                    $validator->errors()->add("subcategories.{$index}", 'Subcategory name must not exceed 100 characters.');
                    continue;
                }

                $lower = mb_strtolower($trimmed);
                if (isset($seenLower[$lower])) {
                    $validator->errors()->add("subcategories.{$index}", "Duplicate subcategory name \"{$trimmed}\" within the same category.");
                    continue;
                }

                $seenLower[$lower] = true;
                $normalizedSubcategories[] = [
                    'id' => $subId,
                    'name' => $trimmed,
                ];
            }
        });

        $validated = $validator->validate();

        DB::transaction(function () use ($category, $validated, $hasSubcategoriesInput, $normalizedSubcategories) {
            $category->update(['name' => $validated['name']]);

            if ($hasSubcategoriesInput) {
                $existingById = $category->subcategories()->get()->keyBy('id');
                $keptIds = [];

                // Match un-ID'd inputs to existing subcategories with the same name if not already matched by ID
                $submittedIds = array_filter(array_column($normalizedSubcategories, 'id'));

                foreach ($normalizedSubcategories as &$item) {
                    if ($item['id'] === null) {
                        $match = $existingById->first(function ($sub) use ($item, $submittedIds, $keptIds) {
                            return !in_array($sub->id, $submittedIds, true)
                                && !in_array($sub->id, $keptIds, true)
                                && mb_strtolower($sub->name) === mb_strtolower($item['name']);
                        });
                        if ($match) {
                            $item['id'] = $match->id;
                        }
                    }
                    if ($item['id'] !== null) {
                        $keptIds[] = $item['id'];
                    }
                }
                unset($item);

                // Delete removed subcategories first (products referencing them will have subcategory_id set to null)
                $toDeleteIds = array_diff($existingById->keys()->all(), $keptIds);
                if (!empty($toDeleteIds)) {
                    LinkProduct::where('category_id', $category->id)
                        ->whereIn('subcategory_id', $toDeleteIds)
                        ->update(['subcategory_id' => null]);

                    LinkSubcategory::where('category_id', $category->id)
                        ->whereIn('id', $toDeleteIds)
                        ->delete();
                }

                // Temporary rename pass if needed to avoid unique index collision when swapping names
                foreach ($normalizedSubcategories as $idx => $item) {
                    if ($item['id'] !== null && isset($existingById[$item['id']])) {
                        $sub = $existingById[$item['id']];
                        if ($sub->name !== $item['name']) {
                            $sub->update(['name' => "__tmp_{$sub->id}_{$idx}__"]);
                        }
                    }
                }

                // Apply updates and creations
                foreach ($normalizedSubcategories as $item) {
                    if ($item['id'] !== null && isset($existingById[$item['id']])) {
                        $existingById[$item['id']]->update(['name' => $item['name']]);
                    } else {
                        LinkSubcategory::create([
                            'category_id' => $category->id,
                            'name' => $item['name'],
                        ]);
                    }
                }
            }
        });

        return redirect()->route('admin.links.edit-page', ['link' => $link, 'tab' => 'products'])
            ->with('success', 'Category updated successfully.');
    }

    /**
     * Delete a category.
     */
    public function destroyCategory(Link $link, LinkCategory $category): RedirectResponse
    {
        if (!$link->page || $category->link_page_id !== $link->page->id) {
            abort(404);
        }

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

        if ($request->input('subcategory_id') === '') {
            $request->merge(['subcategory_id' => null]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => [
                'required',
                Rule::exists('link_categories', 'id')->where('link_page_id', $page->id),
            ],
            'subcategory_id' => [
                'nullable',
                Rule::exists('link_subcategories', 'id')->where('category_id', $request->input('category_id')),
            ],
            'product_url' => ['required', 'url', 'max:2048'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'image_file' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            'subcategory_id.exists' => 'The selected subcategory does not belong to the selected category.',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('products', 'public');
            $validated['image_url'] = Storage::url($path);
        }

        $maxOrder = LinkProduct::where('link_page_id', $page->id)->max('display_order') ?? 0;

        LinkProduct::create([
            'link_page_id' => $page->id,
            'category_id' => $validated['category_id'],
            'subcategory_id' => $validated['subcategory_id'] ?? null,
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

        if ($product->link_page_id !== $page->id) {
            abort(404);
        }

        if ($request->input('subcategory_id') === '') {
            $request->merge(['subcategory_id' => null]);
        }

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'category_id' => [
                'required',
                Rule::exists('link_categories', 'id')->where('link_page_id', $page->id),
            ],
            'subcategory_id' => [
                'nullable',
                Rule::exists('link_subcategories', 'id')->where('category_id', $request->input('category_id')),
            ],
            'product_url' => ['required', 'url', 'max:2048'],
            'description' => ['nullable', 'string', 'max:1000'],
            'image_url' => ['nullable', 'string', 'max:2048'],
            'image_file' => ['nullable', 'image', 'max:2048'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
        ], [
            'subcategory_id.exists' => 'The selected subcategory does not belong to the selected category.',
        ]);

        if ($request->hasFile('image_file')) {
            $path = $request->file('image_file')->store('products', 'public');
            $validated['image_url'] = Storage::url($path);
        }

        $product->update([
            'category_id' => $validated['category_id'],
            'subcategory_id' => $validated['subcategory_id'] ?? null,
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
        if (!$link->page || $product->link_page_id !== $link->page->id) {
            abort(404);
        }

        $product->delete();

        return redirect()->route('admin.links.edit-page', ['link' => $link, 'tab' => 'products'])
            ->with('success', 'Product deleted successfully.');
    }

    /**
     * Toggle product active/inactive status.
     */
    public function toggleProductStatus(Link $link, LinkProduct $product): RedirectResponse
    {
        if (!$link->page || $product->link_page_id !== $link->page->id) {
            abort(404);
        }

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
        $subcatIndex = $this->findHeaderIndex($rawHeaders, ['subcategory', 'sub_category', 'sub category', 'subkategori', 'sub_kategori', 'sub kategori', 'nama_subkategori']);
        $urlIndex = $this->findHeaderIndex($rawHeaders, ['product_url', 'product url', 'url', 'link', 'link_produk', 'link produk']);
        $descIndex = $this->findHeaderIndex($rawHeaders, ['description', 'deskripsi', 'detail']);
        $imgIndex = $this->findHeaderIndex($rawHeaders, ['product_image', 'product image', 'image', 'image_url', 'image url', 'gambar', 'foto', 'gambar_produk']);

        if ($nameIndex === false || $catIndex === false || $urlIndex === false) {
            return redirect()->route('admin.links.edit-page', ['link' => $link, 'tab' => 'products'])
                ->withErrors(['file' => 'Spreadsheet header is missing required columns: Product Name, Category, Product URL.']);
        }

        $importedCount = 0;
        $createdCategoriesCount = 0;
        $createdSubcategoriesCount = 0;
        $maxOrder = LinkProduct::where('link_page_id', $page->id)->max('display_order') ?? 0;

        for ($i = 1; $i < count($rows); $i++) {
            $row = $rows[$i];

            $productName = isset($row[$nameIndex]) ? trim((string) $row[$nameIndex]) : '';
            $categoryName = isset($row[$catIndex]) ? trim((string) $row[$catIndex]) : '';
            $subcategoryName = ($subcatIndex !== false && isset($row[$subcatIndex])) ? trim((string) $row[$subcatIndex]) : '';
            $productUrl = isset($row[$urlIndex]) ? trim((string) $row[$urlIndex]) : '';
            $description = ($descIndex !== false && isset($row[$descIndex])) ? trim((string) $row[$descIndex]) : null;
            $imageUrl = ($imgIndex !== false && isset($row[$imgIndex])) ? trim((string) $row[$imgIndex]) : null;

            if (empty($productName) || empty($categoryName) || empty($productUrl)) {
                continue; // Skip invalid rows
            }

            // Find or create category (case-insensitive check)
            $category = LinkCategory::where('link_page_id', $page->id)
                ->whereRaw('LOWER(name) = ?', [mb_strtolower($categoryName)])
                ->first();

            if (!$category) {
                $category = LinkCategory::create([
                    'link_page_id' => $page->id,
                    'name' => $categoryName,
                ]);
                $createdCategoriesCount++;
            }

            // Find or create subcategory under this category if provided
            $subcategoryId = null;
            if ($subcategoryName !== '') {
                $subcategory = LinkSubcategory::where('category_id', $category->id)
                    ->whereRaw('LOWER(name) = ?', [mb_strtolower($subcategoryName)])
                    ->first();

                if (!$subcategory) {
                    $subcategory = LinkSubcategory::create([
                        'category_id' => $category->id,
                        'name' => $subcategoryName,
                    ]);
                    $createdSubcategoriesCount++;
                }

                $subcategoryId = $subcategory->id;
            }

            $maxOrder++;

            LinkProduct::create([
                'link_page_id' => $page->id,
                'category_id' => $category->id,
                'subcategory_id' => $subcategoryId,
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
        if ($createdSubcategoriesCount > 0) {
            $msg .= " Created {$createdSubcategoriesCount} new subcategories.";
        }

        return redirect()->route('admin.links.edit-page', ['link' => $link, 'tab' => 'products'])
            ->with('success', $msg);
    }

    /**
     * Download sample Excel/CSV template for product import.
     */
    public function downloadProductTemplate(Link $link)
    {
        $csvHeader = ['Product Name', 'Category', 'Subcategory', 'Product URL', 'Description', 'Product Image'];
        $sampleRows = [
            ['Kaos Hitam Basic', 'Pakaian', 'Kaos', 'https://shopee.co.id/kaos-hitam', 'Kaos polos combed 30s premium', 'https://example.com/kaos.jpg'],
            ['Hoodie Oversize', 'Pakaian', 'Hoodie', 'https://shopee.co.id/hoodie-oversize', 'Hoodie hangat fleece katun', 'https://example.com/hoodie.jpg'],
            ['Kalung Emas 24K', 'Kalung', 'Emas', 'https://tokopedia.com/kalung-emas', 'Kalung emas asli berkilau', 'https://example.com/kalung.jpg'],
            ['Topi Snapback Polos', 'Aksesoris', '', 'https://tokopedia.com/topi-snapback', 'Topi kasual bordir rapi tanpa subkategori', 'https://example.com/topi.jpg'],
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

    /**
     * Execute bulk action on selected products (category, subcategory, status, delete).
     */
    public function bulkAction(Request $request, Link $link, ProductBulkActionService $bulkService): RedirectResponse
    {
        $validated = $request->validate([
            'action' => ['required', 'string', Rule::in(['update_category', 'update_subcategory', 'activate', 'deactivate', 'delete'])],
            'product_ids' => ['required', 'array', 'min:1'],
            'product_ids.*' => ['required', 'integer'],
            'category_id' => ['required_if:action,update_category', 'nullable', 'integer'],
            'subcategory_id' => ['nullable'], // can be empty string or integer
        ]);

        $productIds = $validated['product_ids'];
        $action = $validated['action'];

        switch ($action) {
            case 'update_category':
                $count = $bulkService->updateCategory($link, $productIds, (int) $validated['category_id']);
                $message = "Successfully updated category for {$count} product(s).";
                break;

            case 'update_subcategory':
                $subId = !empty($validated['subcategory_id']) ? (int) $validated['subcategory_id'] : null;
                $count = $bulkService->updateSubcategory($link, $productIds, $subId);
                $message = "Successfully updated subcategory for {$count} product(s).";
                break;

            case 'activate':
                $count = $bulkService->updateStatus($link, $productIds, 'active');
                $message = "Successfully activated {$count} product(s).";
                break;

            case 'deactivate':
                $count = $bulkService->updateStatus($link, $productIds, 'inactive');
                $message = "Successfully deactivated {$count} product(s).";
                break;

            case 'delete':
                $count = $bulkService->deleteProducts($link, $productIds);
                $message = "Successfully deleted {$count} product(s).";
                break;

            default:
                abort(400, 'Unsupported bulk action.');
        }

        return redirect()->route('admin.links.edit-page', ['link' => $link, 'tab' => 'products'])
            ->with('success', $message);
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
