<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\LinkCategory;
use App\Models\LinkPage;
use App\Models\LinkProduct;
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
}
