<?php

namespace Tests\Feature;

use App\Models\ClickEvent;
use App\Models\Link;
use App\Models\LinkCategory;
use App\Models\LinkPage;
use App\Models\LinkProduct;
use App\Models\LinkSocial;
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
}
