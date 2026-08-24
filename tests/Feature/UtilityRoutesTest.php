<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UtilityRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_run_migration_route_executes_successfully(): void
    {
        $response = $this->get('/run-migration');

        $response->assertStatus(200);
        $response->assertSee('Migration Output');
    }

    public function test_run_seeder_route_executes_successfully(): void
    {
        $response = $this->get('/run-seeder');

        $response->assertStatus(200);
        $response->assertSee('Seeder Output');
    }

    public function test_clear_all_route_executes_successfully(): void
    {
        $response = $this->get('/clear-all');

        $response->assertStatus(200);
        $response->assertSee('Clear All Output');
    }
}
