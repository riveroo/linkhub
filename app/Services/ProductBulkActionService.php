<?php

namespace App\Services;

use App\Models\Link;
use App\Models\LinkCategory;
use App\Models\LinkProduct;
use App\Models\LinkSubcategory;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProductBulkActionService
{
    /**
     * Authorize and validate a collection of product IDs belonging strictly to a specific link's page.
     *
     * @param Link $link
     * @param array $productIds
     * @return Collection<int, LinkProduct>
     * @throws ValidationException
     */
    public function validateAndResolveProducts(Link $link, array $productIds): Collection
    {
        $page = $link->page;
        if (!$page) {
            throw ValidationException::withMessages([
                'product_ids' => ['No active store page found for this link.'],
            ]);
        }

        // Clean & deduplicate IDs while preserving numeric integers
        $cleanIds = array_values(array_unique(array_filter(array_map(function ($id) {
            return is_numeric($id) ? (int) $id : null;
        }, $productIds))));

        if (empty($cleanIds)) {
            throw ValidationException::withMessages([
                'product_ids' => ['Please select at least one product.'],
            ]);
        }

        // Resolve products belonging to this link page
        $products = LinkProduct::where('link_page_id', $page->id)
            ->whereIn('id', $cleanIds)
            ->with(['category', 'subcategory'])
            ->get();

        // If any requested product ID does not belong to this link's page or is missing, reject
        if ($products->count() !== count($cleanIds)) {
            $foundIds = $products->pluck('id')->all();
            $missingOrUnauthorized = array_diff($cleanIds, $foundIds);

            throw ValidationException::withMessages([
                'product_ids' => ['One or more selected products are invalid, unavailable, or unauthorized: #' . implode(', #', $missingOrUnauthorized)],
            ]);
        }

        return $products;
    }

    /**
     * Bulk update parent category for the selected products.
     *
     * @param Link $link
     * @param array $productIds
     * @param int $categoryId
     * @return int Number of updated products
     * @throws ValidationException
     */
    public function updateCategory(Link $link, array $productIds, int $categoryId): int
    {
        $products = $this->validateAndResolveProducts($link, $productIds);

        // Validate destination category belongs to this link's page
        $category = LinkCategory::where('link_page_id', $link->page->id)
            ->where('id', $categoryId)
            ->first();

        if (!$category) {
            throw ValidationException::withMessages([
                'category_id' => ['The selected destination category does not exist or is unauthorized.'],
            ]);
        }

        return DB::transaction(function () use ($products, $category) {
            $updatedCount = 0;

            foreach ($products as $product) {
                $attributes = [
                    'category_id' => $category->id,
                ];

                // If the product was in another category, clear its subcategory assignment
                // Subcategory assignment is not automatically assigned
                if ($product->category_id !== $category->id) {
                    $attributes['subcategory_id'] = null;
                }

                $product->update($attributes);
                $updatedCount++;
            }

            return $updatedCount;
        });
    }

    /**
     * Bulk update subcategory for selected products.
     * All selected products MUST share the same parent category.
     *
     * @param Link $link
     * @param array $productIds
     * @param int|null $subcategoryId
     * @return int Number of updated products
     * @throws ValidationException
     */
    public function updateSubcategory(Link $link, array $productIds, ?int $subcategoryId): int
    {
        $products = $this->validateAndResolveProducts($link, $productIds);

        // Check rule: All selected products must share the exact same parent category
        $distinctCategoryIds = $products->pluck('category_id')->unique();
        if ($distinctCategoryIds->count() > 1) {
            throw ValidationException::withMessages([
                'subcategory_id' => ['All selected products must belong to the same parent category to update their subcategory in bulk.'],
            ]);
        }

        $sharedCategoryId = $distinctCategoryIds->first();

        // If subcategoryId is provided, validate it belongs to this shared parent category
        if ($subcategoryId !== null) {
            $subcategory = LinkSubcategory::where('category_id', $sharedCategoryId)
                ->where('id', $subcategoryId)
                ->first();

            if (!$subcategory) {
                throw ValidationException::withMessages([
                    'subcategory_id' => ['The selected subcategory does not belong to the shared parent category.'],
                ]);
            }
        }

        return DB::transaction(function () use ($products, $subcategoryId) {
            $updatedCount = 0;

            foreach ($products as $product) {
                $product->update([
                    'subcategory_id' => $subcategoryId, // null clears the subcategory assignment
                ]);
                $updatedCount++;
            }

            return $updatedCount;
        });
    }

    /**
     * Bulk update status (active / inactive) for selected products.
     *
     * @param Link $link
     * @param array $productIds
     * @param string $status ('active' or 'inactive')
     * @return int Number of updated products
     * @throws ValidationException
     */
    public function updateStatus(Link $link, array $productIds, string $status): int
    {
        if (!in_array($status, ['active', 'inactive'], true)) {
            throw ValidationException::withMessages([
                'status' => ['Invalid status value provided.'],
            ]);
        }

        $products = $this->validateAndResolveProducts($link, $productIds);

        return DB::transaction(function () use ($products, $status) {
            $updatedCount = 0;

            foreach ($products as $product) {
                $product->update(['status' => $status]);
                $updatedCount++;
            }

            return $updatedCount;
        });
    }

    /**
     * Bulk delete selected products.
     *
     * @param Link $link
     * @param array $productIds
     * @return int Number of deleted products
     * @throws ValidationException
     */
    public function deleteProducts(Link $link, array $productIds): int
    {
        $products = $this->validateAndResolveProducts($link, $productIds);

        return DB::transaction(function () use ($products) {
            $deletedCount = 0;

            foreach ($products as $product) {
                $product->delete();
                $deletedCount++;
            }

            return $deletedCount;
        });
    }
}

