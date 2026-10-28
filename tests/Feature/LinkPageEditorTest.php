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

class LinkPageEditorTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Link $link;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@admin.com',
        ]);

        $this->link = Link::create([
            'title' => 'My Shop',
            'slug' => 'my-shop',
            'url' => 'https://example.com',
            'status' => 'active',
        ]);
    }

    public function test_guests_cannot_access_page_editor(): void
    {
        $response = $this->get("/admin/links/{$this->link->id}/edit-page");

        $response->assertRedirect('/login');
    }

    public function test_admin_can_access_page_editor_and_auto_initializes_link_page(): void
    {
        $this->assertDatabaseMissing('link_pages', [
            'link_id' => $this->link->id,
        ]);

        $response = $this->actingAs($this->admin)->get("/admin/links/{$this->link->id}/edit-page");

        $response->assertStatus(200);
        $response->assertSee('Atur Halaman');
        $response->assertSee('My Shop');

        $this->assertDatabaseHas('link_pages', [
            'link_id' => $this->link->id,
            'store_name' => 'My Shop',
        ]);
    }

    public function test_admin_can_update_page_profile_and_appearance(): void
    {
        $response = $this->actingAs($this->admin)->put("/admin/links/{$this->link->id}/edit-page", [
            'store_name' => 'Official Store',
            'bio' => 'Best clothing store in town',
            'cover_type' => 'color',
            'cover_color' => '#6366f1',
            'header_alignment' => 'left',
            'product_layout' => 'list',
            'whatsapp_number' => '+6281234567890',
            'source' => 'instagram',
            'sources' => ['instagram', 'tiktok', 'ads'],
            'socials' => [
                'instagram' => 'https://instagram.com/officialstore',
                'tiktok' => 'https://tiktok.com/@officialstore',
            ],
        ]);

        $response->assertRedirect("/admin/links/{$this->link->id}/edit-page");
        $response->assertSessionHas('success', 'Page settings saved successfully.');

        $this->assertDatabaseHas('link_pages', [
            'link_id' => $this->link->id,
            'store_name' => 'Official Store',
            'bio' => 'Best clothing store in town',
            'header_alignment' => 'left',
            'product_layout' => 'list',
            'whatsapp_number' => '+6281234567890',
            'source' => 'instagram',
        ]);

        $page = $this->link->fresh()->page;
        $this->assertEquals(['instagram', 'tiktok', 'ads'], $page->sources);

        $this->assertDatabaseHas('link_socials', [
            'platform' => 'instagram',
            'url' => 'https://instagram.com/officialstore',
        ]);
    }

    public function test_admin_can_manage_categories(): void
    {
        $response = $this->actingAs($this->admin)->post("/admin/links/{$this->link->id}/categories", [
            'name' => 'T-Shirt',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('link_categories', ['name' => 'T-Shirt']);

        $category = LinkCategory::first();

        $response = $this->actingAs($this->admin)->put("/admin/links/{$this->link->id}/categories/{$category->id}", [
            'name' => 'T-Shirts & Tops',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('link_categories', ['name' => 'T-Shirts & Tops']);

        $response = $this->actingAs($this->admin)->delete("/admin/links/{$this->link->id}/categories/{$category->id}");
        $response->assertRedirect();
        $this->assertDatabaseMissing('link_categories', ['id' => $category->id]);
    }

    public function test_admin_can_manage_products(): void
    {
        $page = LinkPage::create([
            'link_id' => $this->link->id,
            'store_name' => 'My Store',
        ]);

        $category = LinkCategory::create([
            'link_page_id' => $page->id,
            'name' => 'Apparel',
        ]);

        $response = $this->actingAs($this->admin)->post("/admin/links/{$this->link->id}/products", [
            'name' => 'Black Hoodie',
            'category_id' => $category->id,
            'product_url' => 'https://shopee.co.id/hoodie',
            'description' => 'Comfortable fleece hoodie',
            'status' => 'active',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('link_products', [
            'name' => 'Black Hoodie',
            'product_url' => 'https://shopee.co.id/hoodie',
            'status' => 'active',
        ]);

        $product = LinkProduct::first();

        $response = $this->actingAs($this->admin)->patch("/admin/links/{$this->link->id}/products/{$product->id}/toggle-status");
        $response->assertRedirect();
        $this->assertDatabaseHas('link_products', [
            'id' => $product->id,
            'status' => 'inactive',
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/links/{$this->link->id}/products/{$product->id}");
        $response->assertRedirect();
        $this->assertDatabaseMissing('link_products', ['id' => $product->id]);
    }

    public function test_admin_can_download_product_import_template(): void
    {
        $response = $this->actingAs($this->admin)->get("/admin/links/{$this->link->id}/products/template");

        $response->assertStatus(200);
        $response->assertHeader('content-type', 'text/csv; charset=UTF-8');
        
        $content = $response->streamedContent();
        $this->assertStringContainsString('Subcategory', $content);
        $this->assertStringContainsString('"Product Name",Category,Subcategory,"Product URL"', $content);
        $this->assertStringContainsString('Pakaian,Kaos,', $content);
    }

    public function test_admin_can_bulk_import_products_with_subcategories(): void
    {
        // Pre-create a category and subcategory
        $page = $this->link->page()->firstOrCreate([
            'store_name' => $this->link->title,
        ]);
        $existingCat = \App\Models\LinkCategory::create([
            'link_page_id' => $page->id,
            'name' => 'Kalung',
        ]);
        $existingSub = \App\Models\LinkSubcategory::create([
            'category_id' => $existingCat->id,
            'name' => 'Emas',
        ]);

        $csvContent = "Product Name,Category,Subcategory,Product URL,Description,Product Image\n";
        // 1. Reuses existing subcategory 'Emas' under 'Kalung'
        $csvContent .= "Kalung Rantai,Kalung,Emas,https://example.com/kalung-emas,Kalung emas murni,\n";
        // 2. Automatically creates new subcategory 'Perak' under 'Kalung'
        $csvContent .= "Kalung Liontin,Kalung,Perak,https://example.com/kalung-perak,Kalung perak 925,\n";
        // 3. New category 'Pakaian' and new subcategory 'Kaos'
        $csvContent .= "Kaos Vintage,Pakaian,Kaos,https://example.com/kaos-vintage,Kaos retro,\n";
        // 4. Product without subcategory
        $csvContent .= "Gelang Tali,Gelang,,https://example.com/gelang,Gelang tali polos,\n";

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('import_with_subcat.csv', $csvContent);

        $response = $this->actingAs($this->admin)->post("/admin/links/{$this->link->id}/products/import", [
            'file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        // Subcategory 'Emas' is reused (count should still be 1 under Kalung)
        $this->assertEquals(1, \App\Models\LinkSubcategory::where('category_id', $existingCat->id)->where('name', 'Emas')->count());

        // Subcategory 'Perak' was created under Kalung
        $subPerak = \App\Models\LinkSubcategory::where('category_id', $existingCat->id)->where('name', 'Perak')->first();
        $this->assertNotNull($subPerak);

        // Category 'Pakaian' and subcategory 'Kaos' were created
        $catPakaian = \App\Models\LinkCategory::where('link_page_id', $page->id)->where('name', 'Pakaian')->first();
        $this->assertNotNull($catPakaian);
        $subKaos = \App\Models\LinkSubcategory::where('category_id', $catPakaian->id)->where('name', 'Kaos')->first();
        $this->assertNotNull($subKaos);

        // Verify products and relationships
        $this->assertDatabaseHas('link_products', [
            'name' => 'Kalung Rantai',
            'category_id' => $existingCat->id,
            'subcategory_id' => $existingSub->id,
        ]);
        $this->assertDatabaseHas('link_products', [
            'name' => 'Kalung Liontin',
            'category_id' => $existingCat->id,
            'subcategory_id' => $subPerak->id,
        ]);
        $this->assertDatabaseHas('link_products', [
            'name' => 'Kaos Vintage',
            'category_id' => $catPakaian->id,
            'subcategory_id' => $subKaos->id,
        ]);
        $this->assertDatabaseHas('link_products', [
            'name' => 'Gelang Tali',
            'subcategory_id' => null,
        ]);
    }

    public function test_admin_can_bulk_import_products_and_auto_create_categories(): void
    {
        $csvContent = "Product Name,Category,Product URL,Description,Product Image\n";
        $csvContent .= "Sepatu Running,Sepatu,https://example.com/sepatu,Sepatu ringan empuk,https://example.com/sepatu.jpg\n";
        $csvContent .= "Jaket Parka,Outerwear,https://example.com/jaket,Jaket anti angin,https://example.com/jaket.jpg\n";

        $file = \Illuminate\Http\UploadedFile::fake()->createWithContent('import.csv', $csvContent);

        $response = $this->actingAs($this->admin)->post("/admin/links/{$this->link->id}/products/import", [
            'file' => $file,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('link_categories', ['name' => 'Sepatu']);
        $this->assertDatabaseHas('link_categories', ['name' => 'Outerwear']);

        $this->assertDatabaseHas('link_products', [
            'name' => 'Sepatu Running',
            'product_url' => 'https://example.com/sepatu',
        ]);
        $this->assertDatabaseHas('link_products', [
            'name' => 'Jaket Parka',
            'product_url' => 'https://example.com/jaket',
        ]);
    }

    public function test_admin_can_create_category_with_multiple_subcategories_and_trims_whitespace(): void
    {
        $response = $this->actingAs($this->admin)->post("/admin/links/{$this->link->id}/categories", [
            'name' => '  Kalung  ',
            'subcategories' => [
                ['name' => ' Emas '],
                ['name' => 'Perak'],
                ['name' => '  Perunggu  '],
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Category created successfully.');

        $category = LinkCategory::where('name', 'Kalung')->firstOrFail();

        $this->assertDatabaseHas('link_subcategories', ['category_id' => $category->id, 'name' => 'Emas']);
        $this->assertDatabaseHas('link_subcategories', ['category_id' => $category->id, 'name' => 'Perak']);
        $this->assertDatabaseHas('link_subcategories', ['category_id' => $category->id, 'name' => 'Perunggu']);
        $this->assertCount(3, $category->subcategories);
    }

    public function test_admin_can_edit_category_name_without_losing_subcategories(): void
    {
        $page = LinkPage::create(['link_id' => $this->link->id, 'store_name' => 'My Shop']);
        $category = LinkCategory::create(['link_page_id' => $page->id, 'name' => 'Kalung']);
        $sub1 = LinkSubcategory::create(['category_id' => $category->id, 'name' => 'Emas']);
        $sub2 = LinkSubcategory::create(['category_id' => $category->id, 'name' => 'Perak']);

        // Update category name only (without sending subcategories array or sync_subcategories)
        $response = $this->actingAs($this->admin)->put("/admin/links/{$this->link->id}/categories/{$category->id}", [
            'name' => 'Kalung Premium',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('link_categories', ['id' => $category->id, 'name' => 'Kalung Premium']);
        $this->assertDatabaseHas('link_subcategories', ['id' => $sub1->id, 'name' => 'Emas']);
        $this->assertDatabaseHas('link_subcategories', ['id' => $sub2->id, 'name' => 'Perak']);

        // Update category name while also passing existing subcategories from modal
        $response2 = $this->actingAs($this->admin)->put("/admin/links/{$this->link->id}/categories/{$category->id}", [
            'name' => 'Perhiasan Kalung',
            'sync_subcategories' => '1',
            'subcategories' => [
                ['id' => $sub1->id, 'name' => 'Emas'],
                ['id' => $sub2->id, 'name' => 'Perak'],
            ],
        ]);

        $response2->assertRedirect();
        $this->assertDatabaseHas('link_categories', ['id' => $category->id, 'name' => 'Perhiasan Kalung']);
        $this->assertDatabaseHas('link_subcategories', ['id' => $sub1->id, 'name' => 'Emas']);
        $this->assertDatabaseHas('link_subcategories', ['id' => $sub2->id, 'name' => 'Perak']);
    }

    public function test_admin_can_add_rename_and_remove_subcategories_preserving_product_references(): void
    {
        $page = LinkPage::create(['link_id' => $this->link->id, 'store_name' => 'My Shop']);
        $category = LinkCategory::create(['link_page_id' => $page->id, 'name' => 'Kalung']);
        $subEmas = LinkSubcategory::create(['category_id' => $category->id, 'name' => 'Emas']);
        $subPerak = LinkSubcategory::create(['category_id' => $category->id, 'name' => 'Perak']);

        $productEmas = LinkProduct::create([
            'link_page_id' => $page->id,
            'category_id' => $category->id,
            'subcategory_id' => $subEmas->id,
            'name' => 'Kalung Emas 24K',
            'product_url' => 'https://example.com/emas',
            'status' => 'active',
        ]);

        $productPerak = LinkProduct::create([
            'link_page_id' => $page->id,
            'category_id' => $category->id,
            'subcategory_id' => $subPerak->id,
            'name' => 'Kalung Perak 925',
            'product_url' => 'https://example.com/perak',
            'status' => 'active',
        ]);

        // Rename Emas -> Emas Murni, remove Perak, add Perunggu
        $response = $this->actingAs($this->admin)->put("/admin/links/{$this->link->id}/categories/{$category->id}", [
            'name' => 'Kalung',
            'sync_subcategories' => '1',
            'subcategories' => [
                ['id' => $subEmas->id, 'name' => 'Emas Murni'],
                ['name' => 'Perunggu'],
            ],
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Category updated successfully.');

        // Emas was renamed in-place so productEmas still points to the same subcategory record
        $this->assertDatabaseHas('link_subcategories', ['id' => $subEmas->id, 'name' => 'Emas Murni']);
        $this->assertEquals($subEmas->id, $productEmas->fresh()->subcategory_id);

        // Perak was removed; productPerak remains in parent category with subcategory_id cleared
        $this->assertDatabaseMissing('link_subcategories', ['id' => $subPerak->id]);
        $this->assertDatabaseHas('link_products', [
            'id' => $productPerak->id,
            'category_id' => $category->id,
            'subcategory_id' => null,
        ]);

        // Perunggu was added
        $this->assertDatabaseHas('link_subcategories', [
            'category_id' => $category->id,
            'name' => 'Perunggu',
        ]);
    }

    public function test_rejects_duplicate_or_empty_subcategory_names(): void
    {
        // Duplicate subcategories (case-insensitive) on create
        $response = $this->actingAs($this->admin)->post("/admin/links/{$this->link->id}/categories", [
            'name' => 'Kalung',
            'subcategories' => [
                ['name' => 'Emas'],
                ['name' => 'emas'],
            ],
        ]);

        $response->assertSessionHasErrors(['subcategories.1']);
        $this->assertDatabaseMissing('link_categories', ['name' => 'Kalung']);

        // Empty / whitespace-only subcategory name on create
        $responseEmpty = $this->actingAs($this->admin)->post("/admin/links/{$this->link->id}/categories", [
            'name' => 'Kalung',
            'subcategories' => [
                ['name' => '   '],
            ],
        ]);

        $responseEmpty->assertSessionHasErrors(['subcategories.0']);
        $this->assertDatabaseMissing('link_categories', ['name' => 'Kalung']);
    }

    public function test_assigning_product_to_category_with_and_without_subcategory(): void
    {
        $page = LinkPage::create(['link_id' => $this->link->id, 'store_name' => 'My Shop']);
        $catKalung = LinkCategory::create(['link_page_id' => $page->id, 'name' => 'Kalung']);
        $subEmas = LinkSubcategory::create(['category_id' => $catKalung->id, 'name' => 'Emas']);

        $catGelang = LinkCategory::create(['link_page_id' => $page->id, 'name' => 'Gelang']);
        $subGelangPerak = LinkSubcategory::create(['category_id' => $catGelang->id, 'name' => 'Perak']);

        // 1. Product without subcategory
        $res1 = $this->actingAs($this->admin)->post("/admin/links/{$this->link->id}/products", [
            'name' => 'Kalung Liontin',
            'category_id' => $catKalung->id,
            'subcategory_id' => '',
            'product_url' => 'https://example.com/kalung-liontin',
            'status' => 'active',
        ]);
        $res1->assertSessionHasNoErrors();
        $this->assertDatabaseHas('link_products', [
            'name' => 'Kalung Liontin',
            'category_id' => $catKalung->id,
            'subcategory_id' => null,
        ]);

        // 2. Product with valid subcategory
        $res2 = $this->actingAs($this->admin)->post("/admin/links/{$this->link->id}/products", [
            'name' => 'Kalung Emas Rantai',
            'category_id' => $catKalung->id,
            'subcategory_id' => $subEmas->id,
            'product_url' => 'https://example.com/kalung-emas',
            'status' => 'active',
        ]);
        $res2->assertSessionHasNoErrors();
        $this->assertDatabaseHas('link_products', [
            'name' => 'Kalung Emas Rantai',
            'category_id' => $catKalung->id,
            'subcategory_id' => $subEmas->id,
        ]);

        // 3. Reject subcategory belonging to a different parent category
        $res3 = $this->actingAs($this->admin)->post("/admin/links/{$this->link->id}/products", [
            'name' => 'Invalid Product',
            'category_id' => $catKalung->id,
            'subcategory_id' => $subGelangPerak->id,
            'product_url' => 'https://example.com/invalid',
            'status' => 'active',
        ]);
        $res3->assertSessionHasErrors(['subcategory_id']);
        $this->assertDatabaseMissing('link_products', ['name' => 'Invalid Product']);

        // 4. Updating product to another parent category rejects mismatched subcategory and allows clearing subcategory
        $product = LinkProduct::where('name', 'Kalung Emas Rantai')->firstOrFail();

        $res4 = $this->actingAs($this->admin)->put("/admin/links/{$this->link->id}/products/{$product->id}", [
            'name' => 'Kalung Emas Rantai',
            'category_id' => $catGelang->id,
            'subcategory_id' => $subEmas->id, // belongs to Kalung, not Gelang
            'product_url' => 'https://example.com/kalung-emas',
            'status' => 'active',
        ]);
        $res4->assertSessionHasErrors(['subcategory_id']);

        $res5 = $this->actingAs($this->admin)->put("/admin/links/{$this->link->id}/products/{$product->id}", [
            'name' => 'Gelang Polos',
            'category_id' => $catGelang->id,
            'subcategory_id' => '',
            'product_url' => 'https://example.com/gelung-polos',
            'status' => 'active',
        ]);
        $res5->assertSessionHasNoErrors();
        $this->assertDatabaseHas('link_products', [
            'id' => $product->id,
            'category_id' => $catGelang->id,
            'subcategory_id' => null,
        ]);
    }

    public function test_guests_cannot_mutate_categories_or_products_and_cross_link_mutations_fail(): void
    {
        $page = LinkPage::create(['link_id' => $this->link->id, 'store_name' => 'My Shop']);
        $category = LinkCategory::create(['link_page_id' => $page->id, 'name' => 'Kalung']);

        $otherLink = Link::create([
            'title' => 'Other Shop',
            'slug' => 'other-shop',
            'status' => 'active',
        ]);
        $otherPage = LinkPage::create(['link_id' => $otherLink->id, 'store_name' => 'Other Shop']);
        $otherCategory = LinkCategory::create(['link_page_id' => $otherPage->id, 'name' => 'Sepatu']);

        // Guest requests redirect to login
        $this->post("/admin/links/{$this->link->id}/categories", ['name' => 'Hack'])->assertRedirect('/login');
        $this->put("/admin/links/{$this->link->id}/categories/{$category->id}", ['name' => 'Hack'])->assertRedirect('/login');
        $this->delete("/admin/links/{$this->link->id}/categories/{$category->id}")->assertRedirect('/login');

        // Cross-link category update/delete returns 404
        $this->actingAs($this->admin)
            ->put("/admin/links/{$this->link->id}/categories/{$otherCategory->id}", ['name' => 'Mismatch'])
            ->assertStatus(404);

        $this->actingAs($this->admin)
            ->delete("/admin/links/{$this->link->id}/categories/{$otherCategory->id}")
            ->assertStatus(404);
    }
}
