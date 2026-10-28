<?php

namespace Tests\Feature;

use App\Models\Link;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LinkManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'email' => 'admin@admin.com',
        ]);
    }

    public function test_guests_cannot_access_links_index(): void
    {
        $response = $this->get('/admin/links');

        $response->assertRedirect('/login');
    }

    public function test_admin_can_view_links_index(): void
    {
        Link::factory()->create([
            'title' => 'GitHub',
            'slug' => 'github',
            'url' => 'https://github.com/example',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/links');

        $response->assertStatus(200);
        $response->assertSee('Manage your shared links.');
        $response->assertSee('GitHub');
        $response->assertSee('github');
        $response->assertSee('Total Links');
        // Short link column is a link opening in a new tab
        $response->assertSee('target="_blank"', false);
        $response->assertSee('rel="noopener noreferrer"', false);
        $response->assertSee(url('/github'), false);
    }

    public function test_admin_can_create_link_with_valid_data(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/links', [
            'title' => 'My Portfolio',
            'url' => 'https://example.com',
            'slug' => 'my-portfolio',
        ]);

        $response->assertRedirect('/admin/links');
        $response->assertSessionHas('success', 'Link created successfully.');

        $this->assertDatabaseHas('links', [
            'title' => 'My Portfolio',
            'url' => 'https://example.com',
            'slug' => 'my-portfolio',
            'status' => 'active',
        ]);
    }

    public function test_create_link_requires_title_and_slug(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/links', [
            'title' => '',
            'slug' => '',
        ]);

        $response->assertSessionHasErrors(['title', 'slug']);
    }

    public function test_slug_must_be_unique(): void
    {
        Link::create([
            'title' => 'Existing Link',
            'slug' => 'duplicate-slug',
            'url' => 'https://example.com',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->post('/admin/links', [
            'title' => 'New Link',
            'url' => 'https://newexample.com',
            'slug' => 'duplicate-slug',
        ]);

        $response->assertSessionHasErrors(['slug']);
    }

    public function test_url_must_be_valid_format(): void
    {
        $response = $this->actingAs($this->admin)->post('/admin/links', [
            'title' => 'Invalid Link',
            'url' => 'not-a-valid-url',
            'slug' => 'invalid-link',
        ]);

        $response->assertSessionHasErrors(['url']);
    }

    public function test_admin_can_search_links(): void
    {
        Link::create([
            'title' => 'GitHub Repository',
            'slug' => 'github',
            'url' => 'https://github.com',
            'status' => 'active',
        ]);

        Link::create([
            'title' => 'Personal Blog',
            'slug' => 'blog',
            'url' => 'https://blog.example.com',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/links?search=github');

        $response->assertStatus(200);
        $response->assertSee('GitHub Repository');
        $response->assertDontSee('Personal Blog');
    }

    public function test_admin_can_filter_links_by_status(): void
    {
        Link::create([
            'title' => 'Active Target Link',
            'slug' => 'active-target-link',
            'url' => 'https://active.com',
            'status' => 'active',
        ]);

        Link::create([
            'title' => 'Disabled Target Link',
            'slug' => 'disabled-target-link',
            'url' => 'https://inactive.com',
            'status' => 'inactive',
        ]);

        $response = $this->actingAs($this->admin)->get('/admin/links?status=active');

        $response->assertStatus(200);
        $response->assertSee('Active Target Link');
        $response->assertDontSee('Disabled Target Link');
    }

    public function test_admin_can_update_link(): void
    {
        $link = Link::create([
            'title' => 'Old Title',
            'slug' => 'old-slug',
            'url' => 'https://old.com',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->put("/admin/links/{$link->id}", [
            'title' => 'Updated Title',
            'slug' => 'updated-slug',
            'url' => 'https://updated.com',
        ]);

        $response->assertRedirect('/admin/links');
        $response->assertSessionHas('success', 'Link updated successfully.');

        $this->assertDatabaseHas('links', [
            'id' => $link->id,
            'title' => 'Updated Title',
            'slug' => 'updated-slug',
            'url' => 'https://updated.com',
        ]);
    }

    public function test_admin_can_toggle_link_status(): void
    {
        $link = Link::create([
            'title' => 'Toggle Link',
            'slug' => 'toggle-link',
            'url' => 'https://toggle.com',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->patch("/admin/links/{$link->id}/toggle-status");

        $response->assertRedirect('/admin/links');
        $this->assertDatabaseHas('links', [
            'id' => $link->id,
            'status' => 'inactive',
        ]);

        $response = $this->actingAs($this->admin)->patch("/admin/links/{$link->id}/toggle-status");
        $this->assertDatabaseHas('links', [
            'id' => $link->id,
            'status' => 'active',
        ]);
    }

    public function test_admin_can_delete_link(): void
    {
        $link = Link::create([
            'title' => 'Delete Me',
            'slug' => 'delete-me',
            'url' => 'https://delete.com',
            'status' => 'active',
        ]);

        $response = $this->actingAs($this->admin)->delete("/admin/links/{$link->id}");

        $response->assertRedirect('/admin/links');
        $response->assertSessionHas('success', 'Link deleted successfully.');

        $this->assertDatabaseMissing('links', [
            'id' => $link->id,
        ]);
    }
}
