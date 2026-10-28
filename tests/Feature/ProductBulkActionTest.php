<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\LinkCategory;
use App\Models\LinkPage;
use App\Models\LinkProduct;
use App\Models\LinkSubcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProductBulkActionTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Link $link;
    private LinkPage $page;
    private LinkCategory $catApparel;
    private LinkCategory $catJewelry;
    private LinkSubcategory $subHoodie;
    private LinkSubcategory $subTShirt;
    private LinkSubcategory $subGold;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@admin.com',
        ]);

        $this->link = Link::create([
            'title' => 'Test Store',
            'slug' => 'test-store',
            'status' => 'active',
        ]);

        $this->page = LinkPage::create([
            'link_id' => $this->link->id,
            'store_name' => 'Test Store',
        ]);

        $this->catApparel = LinkCategory::create([
            'link_page_id' => $this->page->id,
            'name' => 'Apparel',
        ]);

        $this->subHoodie = LinkSubcategory::create([
            'category_id' => $this->catApparel->id,
            'name' => 'Hoodie',
        ]);

        $this->subTShirt = LinkSubcategory::create([
            'category_id' => $this->catApparel->id,
            'name' => 'T-Shirt',
        ]);

        $this->catJewelry = LinkCategory::create([
            'link_page_id' => $this->page->id,
            'name' => 'Jewelry',
        ]);

        $this->subGold = LinkSubcategory::create([
            'category_id' => $this->catJewelry->id,
            'name' => 'Gold',
        ]);
    }

    private function createProduct(string $name, LinkCategory $category, ?LinkSubcategory $subcategory = null, string $status = 'active'): LinkProduct
    {
        return LinkProduct::create([
            'link_page_id' => $this->page->id,
            'category_id' => $category->id,
            'subcategory_id' => $subcategory?->id,
            'name' => $name,
            'product_url' => 'https://example.com/' . strtolower(str_replace(' ', '-', $name)),
            'status' => $status,
        ]);
    }

    public function test_guests_cannot_execute_bulk_actions(): void
    {
        $p1 = $this->createProduct('P1', $this->catApparel);

        $response = $this->post("/admin/links/{$this->link->id}/products/bulk-action", [
            'action' => 'activate',
            'product_ids' => [$p1->id],
        ]);

        $response->assertRedirect('/login');
    }

    public function test_bulk_update_category_updates_parent_and_clears_incompatible_subcategories(): void
    {
        $p1 = $this->createProduct('Hoodie Black', $this->catApparel, $this->subHoodie);
        $p2 = $this->createProduct('Shirt White', $this->catApparel, $this->subTShirt);

        $response = $this->actingAs($this->admin)->post("/admin/links/{$this->link->id}/products/bulk-action", [
            'action' => 'update_category',
            'product_ids' => [$p1->id, $p2->id],
            'category_id' => $this->catJewelry->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Successfully updated category for 2 product(s).');

        $this->assertDatabaseHas('link_products', [
            'id' => $p1->id,
            'category_id' => $this->catJewelry->id,
            'subcategory_id' => null, // cleared
        ]);

        $this->assertDatabaseHas('link_products', [
            'id' => $p2->id,
            'category_id' => $this->catJewelry->id,
            'subcategory_id' => null, // cleared
        ]);
    }

    public function test_bulk_update_subcategory_succeeds_when_all_products_share_same_parent_category(): void
    {
        $p1 = $this->createProduct('Hoodie A', $this->catApparel, null);
        $p2 = $this->createProduct('Hoodie B', $this->catApparel, $this->subTShirt);

        $response = $this->actingAs($this->admin)->post("/admin/links/{$this->link->id}/products/bulk-action", [
            'action' => 'update_subcategory',
            'product_ids' => [$p1->id, $p2->id],
            'subcategory_id' => $this->subHoodie->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Successfully updated subcategory for 2 product(s).');

        $this->assertEquals($this->subHoodie->id, $p1->fresh()->subcategory_id);
        $this->assertEquals($this->subHoodie->id, $p2->fresh()->subcategory_id);
    }

    public function test_bulk_update_subcategory_allows_clearing_subcategory(): void
    {
        $p1 = $this->createProduct('Hoodie A', $this->catApparel, $this->subHoodie);
        $p2 = $this->createProduct('Hoodie B', $this->catApparel, $this->subTShirt);

        $response = $this->actingAs($this->admin)->post("/admin/links/{$this->link->id}/products/bulk-action", [
            'action' => 'update_subcategory',
            'product_ids' => [$p1->id, $p2->id],
            'subcategory_id' => '', // clearing
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Successfully updated subcategory for 2 product(s).');

        $this->assertNull($p1->fresh()->subcategory_id);
        $this->assertNull($p2->fresh()->subcategory_id);
        $this->assertEquals($this->catApparel->id, $p1->fresh()->category_id);
    }

    public function test_bulk_update_subcategory_rejects_products_with_different_parent_categories(): void
    {
        $p1 = $this->createProduct('Hoodie A', $this->catApparel, $this->subHoodie);
        $p2 = $this->createProduct('Ring Gold', $this->catJewelry, $this->subGold);

        $response = $this->actingAs($this->admin)->post("/admin/links/{$this->link->id}/products/bulk-action", [
            'action' => 'update_subcategory',
            'product_ids' => [$p1->id, $p2->id],
            'subcategory_id' => $this->subHoodie->id,
        ]);

        $response->assertSessionHasErrors(['subcategory_id']);
        $this->assertEquals($this->subHoodie->id, $p1->fresh()->subcategory_id);
        $this->assertEquals($this->subGold->id, $p2->fresh()->subcategory_id);
    }

    public function test_bulk_update_subcategory_rejects_subcategory_belonging_to_another_category(): void
    {
        $p1 = $this->createProduct('Hoodie A', $this->catApparel, null);

        $response = $this->actingAs($this->admin)->post("/admin/links/{$this->link->id}/products/bulk-action", [
            'action' => 'update_subcategory',
            'product_ids' => [$p1->id],
            'subcategory_id' => $this->subGold->id, // belongs to Jewelry, not Apparel
        ]);

        $response->assertSessionHasErrors(['subcategory_id']);
        $this->assertNull($p1->fresh()->subcategory_id);
    }

    public function test_bulk_status_activate_and_deactivate(): void
    {
        $p1 = $this->createProduct('P1', $this->catApparel, null, 'inactive');
        $p2 = $this->createProduct('P2', $this->catApparel, null, 'active');

        // Bulk activate
        $resActivate = $this->actingAs($this->admin)->post("/admin/links/{$this->link->id}/products/bulk-action", [
            'action' => 'activate',
            'product_ids' => [$p1->id, $p2->id],
        ]);

        $resActivate->assertRedirect();
        $resActivate->assertSessionHas('success', 'Successfully activated 2 product(s).');
        $this->assertEquals('active', $p1->fresh()->status);
        $this->assertEquals('active', $p2->fresh()->status);

        // Bulk deactivate
        $resDeactivate = $this->actingAs($this->admin)->post("/admin/links/{$this->link->id}/products/bulk-action", [
            'action' => 'deactivate',
            'product_ids' => [$p1->id, $p2->id],
        ]);

        $resDeactivate->assertRedirect();
        $resDeactivate->assertSessionHas('success', 'Successfully deactivated 2 product(s).');
        $this->assertEquals('inactive', $p1->fresh()->status);
        $this->assertEquals('inactive', $p2->fresh()->status);
    }

    public function test_bulk_delete_deletes_only_selected_products(): void
    {
        $p1 = $this->createProduct('P1', $this->catApparel);
        $p2 = $this->createProduct('P2', $this->catApparel);
        $p3 = $this->createProduct('P3', $this->catApparel);

        $response = $this->actingAs($this->admin)->post("/admin/links/{$this->link->id}/products/bulk-action", [
            'action' => 'delete',
            'product_ids' => [$p1->id, $p2->id],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Successfully deleted 2 product(s).');

        $this->assertDatabaseMissing('link_products', ['id' => $p1->id]);
        $this->assertDatabaseMissing('link_products', ['id' => $p2->id]);
        $this->assertDatabaseHas('link_products', ['id' => $p3->id]);
    }

    public function test_rejects_products_belonging_to_other_links_or_non_existent(): void
    {
        $otherLink = Link::create(['title' => 'Other', 'slug' => 'other', 'status' => 'active']);
        $otherPage = LinkPage::create(['link_id' => $otherLink->id, 'store_name' => 'Other Store']);
        $otherCat = LinkCategory::create(['link_page_id' => $otherPage->id, 'name' => 'Other Cat']);
        $otherProduct = LinkProduct::create([
            'link_page_id' => $otherPage->id,
            'category_id' => $otherCat->id,
            'name' => 'Secret Product',
            'product_url' => 'https://example.com/secret',
            'status' => 'active',
        ]);

        $myProduct = $this->createProduct('My Product', $this->catApparel);

        // Attempting to delete both my product and foreign product in single request
        $response = $this->actingAs($this->admin)->post("/admin/links/{$this->link->id}/products/bulk-action", [
            'action' => 'delete',
            'product_ids' => [$myProduct->id, $otherProduct->id],
        ]);

        // Must reject and rollback atomically
        $response->assertSessionHasErrors(['product_ids']);
        $this->assertDatabaseHas('link_products', ['id' => $myProduct->id]);
        $this->assertDatabaseHas('link_products', ['id' => $otherProduct->id]);
    }

    public function test_requires_at_least_one_product_id(): void
    {
        $response = $this->actingAs($this->admin)->post("/admin/links/{$this->link->id}/products/bulk-action", [
            'action' => 'activate',
            'product_ids' => [],
        ]);

        $response->assertSessionHasErrors(['product_ids']);
    }

    public function test_edit_page_renders_bulk_selection_checkboxes_and_toolbar(): void
    {
        $p1 = $this->createProduct('P1', $this->catApparel);

        $response = $this->actingAs($this->admin)->get("/admin/links/{$this->link->id}/edit-page?tab=products");

        $response->assertStatus(200);
        $response->assertSee('Select all products on this page');
        $response->assertSee('Select product P1');
        $response->assertSee('Edit Category');
        $response->assertSee('Edit Subcategory');
        $response->assertSee('selectedProductIds.length', false);
        $response->assertSee('openBulkCategoryModal()', false);
        $response->assertSee('openBulkSubcategoryModal()', false);
        $response->assertSee('openBulkStatusModal', false);
        $response->assertSee('openBulkDeleteModal()', false);
    }
}

