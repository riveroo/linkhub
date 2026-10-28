<?php

namespace Tests\Feature;

use App\Models\ClickEvent;
use App\Models\Link;
use App\Models\LinkCategory;
use App\Models\LinkPage;
use App\Models\LinkProduct;
use App\Models\LinkSocial;
use App\Models\LinkSubcategory;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicLinkPageTest extends TestCase
{
    use RefreshDatabase;

    private Link $link;
    private LinkPage $page;

    protected function setUp(): void
    {
        parent::setUp();

        $this->link = Link::create([
            'title' => 'Toko Berly',
            'slug' => 'tokoberly',
            'status' => 'active',
        ]);

        $this->page = LinkPage::create([
            'link_id' => $this->link->id,
            'store_name' => 'Toko Berly Official',
            'bio' => 'Toko baju anak dan dewasa premium',
            'cover_type' => 'color',
            'cover_color' => '#4f46e5',
            'header_alignment' => 'center',
            'product_layout' => 'grid',
            'whatsapp_number' => '081234567890',
        ]);

        LinkSocial::create([
            'link_page_id' => $this->page->id,
            'platform' => 'instagram',
            'url' => 'https://instagram.com/tokoberly',
        ]);

        $category = LinkCategory::create([
            'link_page_id' => $this->page->id,
            'name' => 'Kaos',
        ]);

        LinkProduct::create([
            'link_page_id' => $this->page->id,
            'category_id' => $category->id,
            'name' => 'Kaos Oversize Hitam',
            'description' => 'Bahan cotton combed 24s',
            'product_url' => 'https://shopee.co.id/kaos-oversize',
            'status' => 'active',
            'display_order' => 1,
        ]);

        LinkProduct::create([
            'link_page_id' => $this->page->id,
            'category_id' => $category->id,
            'name' => 'Kaos Polo Putih (Inactive)',
            'description' => 'Produk habis',
            'product_url' => 'https://shopee.co.id/kaos-polo',
            'status' => 'inactive',
            'display_order' => 2,
        ]);
    }

    public function test_guest_can_access_active_public_link_page(): void
    {
        $response = $this->get('/tokoberly');

        $response->assertStatus(200);
        $response->assertSee('Toko Berly Official');
        $response->assertSee('Toko baju anak dan dewasa premium');
        $response->assertSee('Hubungi via WhatsApp');
        $response->assertSee('https://wa.me/6281234567890', false);
        $response->assertSee('Instagram');
        $response->assertSee('Kaos Oversize Hitam');
        // Product URLs are now behind a tracking endpoint — the raw product URL
        // should NOT appear in the HTML; instead the tracking route endpoint is rendered.
        $response->assertDontSee('https://shopee.co.id/kaos-oversize');
        $response->assertSee('/link/', false); // tracking route prefix
    }

    public function test_inactive_products_are_not_rendered_on_public_page(): void
    {
        $response = $this->get('/tokoberly');

        $response->assertStatus(200);
        $response->assertSee('Kaos Oversize Hitam');
        $response->assertDontSee('Kaos Polo Putih (Inactive)');
    }

    public function test_non_existent_slug_returns_404(): void
    {
        $response = $this->get('/non-existent-store-slug');

        $response->assertStatus(404);
    }

    public function test_inactive_link_shows_unavailable_view(): void
    {
        $this->link->update(['status' => 'inactive']);

        $response = $this->get('/tokoberly');

        $response->assertStatus(403);
        $response->assertSee('Link Unavailable');
        $response->assertSee('This link is currently inactive or temporarily unavailable.');
    }

    public function test_public_link_without_whatsapp_does_not_show_whatsapp_button(): void
    {
        $this->page->update(['whatsapp_number' => null]);

        $response = $this->get('/tokoberly');

        $response->assertStatus(200);
        $response->assertDontSee('Hubungi via WhatsApp');
    }

    public function test_list_product_layout_renders(): void
    {
        $this->page->update(['product_layout' => 'list']);

        $response = $this->get('/tokoberly');

        $response->assertStatus(200);
        $response->assertSee('Kaos Oversize Hitam');
    }

    public function test_category_filter_pills_are_rendered_on_public_page(): void
    {
        $response = $this->get('/tokoberly');

        $response->assertStatus(200);
        $response->assertSee('Semua (1)');
        $response->assertSee('Kaos (1)');
    }

    public function test_visiting_public_link_creates_click_events(): void
    {
        $this->assertDatabaseCount('click_events', 0);

        $this->get('/tokoberly');
        $this->assertDatabaseHas('click_events', [
            'link_id'    => $this->link->id,
            'event_type' => ClickEvent::PAGE_VIEW,
        ]);
        $this->assertDatabaseCount('click_events', 1);

        $this->get('/tokoberly');
        $this->assertDatabaseCount('click_events', 2);
    }

    public function test_slug_route_does_not_shadow_system_routes(): void
    {
        $loginResponse = $this->get('/login');
        $loginResponse->assertStatus(200);

        $admin = User::factory()->create();
        $adminResponse = $this->actingAs($admin)->get('/admin/links');
        $adminResponse->assertStatus(200);
    }

    public function test_hierarchical_category_and_subcategory_filters_render_correctly_on_public_page(): void
    {
        // Create a category WITH subcategories (Kalung -> Emas, Perak, Perunggu)
        $catKalung = LinkCategory::create([
            'link_page_id' => $this->page->id,
            'name' => 'Kalung',
        ]);

        $subEmas = LinkSubcategory::create(['category_id' => $catKalung->id, 'name' => 'Emas']);
        $subPerak = LinkSubcategory::create(['category_id' => $catKalung->id, 'name' => 'Perak']);
        $subPerunggu = LinkSubcategory::create(['category_id' => $catKalung->id, 'name' => 'Perunggu']);

        // Create products under Kalung: one in Emas, one directly in Kalung (no subcategory)
        $prodEmas = LinkProduct::create([
            'link_page_id' => $this->page->id,
            'category_id' => $catKalung->id,
            'subcategory_id' => $subEmas->id,
            'name' => 'Kalung Emas Asli',
            'product_url' => 'https://shopee.co.id/kalung-emas',
            'status' => 'active',
            'display_order' => 3,
        ]);

        $prodNoSub = LinkProduct::create([
            'link_page_id' => $this->page->id,
            'category_id' => $catKalung->id,
            'subcategory_id' => null,
            'name' => 'Kalung Polos Standar',
            'product_url' => 'https://shopee.co.id/kalung-polos',
            'status' => 'active',
            'display_order' => 4,
        ]);

        // Existing category 'Kaos' from setUp has 0 subcategories and 1 active product
        $catKaos = LinkCategory::where('link_page_id', $this->page->id)->where('name', 'Kaos')->firstOrFail();

        $response = $this->get('/tokoberly');
        $response->assertStatus(200);

        // Parent category filter pills
        $response->assertSee('Semua (3)');
        $response->assertSee('Kaos (1)');
        $response->assertSee('Kalung (2)');

        // Subcategory contextual filter exists for Kalung, with "Semua Kalung (2)" and each subcategory
        $response->assertSee("data-subcategory-filter-for=\"{$catKalung->id}\"", false);
        $response->assertSee('Semua Kalung (2)');
        $response->assertSee('Emas (1)');
        $response->assertSee('Perak (0)');
        $response->assertSee('Perunggu (0)');

        // Category without subcategories ('Kaos') does NOT render a subcategory filter container
        $response->assertDontSee("data-subcategory-filter-for=\"{$catKaos->id}\"", false);

        // Product filtering bindings include both category_id and subcategory_id (or null)
        $response->assertSee("matchesFilter({$catKalung->id}, {$subEmas->id})", false);
        $response->assertSee("matchesFilter({$catKalung->id}, null)", false);
        $response->assertSee("matchesFilter({$catKaos->id}, null)", false);

        // Switching parent category resets selectedSubcategory to 'all'
        $response->assertSee("selectCategory(categoryId)", false);
        $response->assertSee("this.selectedSubcategory = 'all'", false);
    }

    public function test_product_name_search_input_renders_on_public_page(): void
    {
        $response = $this->get('/tokoberly');
        $response->assertStatus(200);

        // Search input with model and placeholder
        $response->assertSee('x-model="searchQuery"', false);
        $response->assertSee('placeholder="Cari nama produk..."', false);
        $response->assertSee('aria-label="Cari nama produk"', false);
        $response->assertSee('matchesSearch(', false);
    }
}
