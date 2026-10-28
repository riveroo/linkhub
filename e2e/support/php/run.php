<?php

/**
 * E2E-only helper. Boots the Laravel app and executes ONE action against the
 * dedicated E2E SQLite database. It refuses to run unless the *resolved* app
 * configuration proves the target is the isolated E2E database.
 *
 * Usage:  php e2e/support/php/run.php <action>     (spec passed via E2E_SPEC env, JSON)
 * Output: a single JSON document wrapped in @@ ... @@ markers.
 */

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

$root = dirname(__DIR__, 3);
chdir($root);

require $root . '/vendor/autoload.php';
$app = require $root . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

function guard_fail(string $message): never
{
    fwrite(STDERR, "E2E SAFETY GUARD FAILED: {$message}\n");
    exit(3);
}

// ---------------------------------------------------------------- guards ----
$expected = getenv('E2E_EXPECTED_DB');
if (!$expected) {
    guard_fail('E2E_EXPECTED_DB is not set.');
}
if (basename($expected) !== 'e2e.sqlite') {
    guard_fail("Expected database file must be named e2e.sqlite, got: {$expected}");
}
if (app()->environment() !== 'e2e') {
    guard_fail('APP_ENV resolved to "' . app()->environment() . '", expected "e2e".');
}
if (config('database.default') !== 'sqlite') {
    guard_fail('Default DB connection resolved to "' . config('database.default') . '", expected "sqlite".');
}
$configured = (string) config('database.connections.sqlite.database');
if (!is_file($configured) || realpath($configured) !== realpath($expected)) {
    guard_fail("Configured sqlite database ({$configured}) is not the E2E database ({$expected}).");
}
$connection = DB::connection();
if ($connection->getDriverName() !== 'sqlite' || realpath($connection->getDatabaseName()) !== realpath($expected)) {
    guard_fail('Live DB connection does not point to the E2E sqlite database.');
}
if (config('mail.default') !== 'array') {
    guard_fail('Mailer must be "array" during E2E runs (no real emails).');
}

// --------------------------------------------------------------- actions ----
$action = $argv[1] ?? '';
$spec = json_decode(getenv('E2E_SPEC') ?: '{}', true) ?: [];

function out(mixed $data): void
{
    echo '@@' . json_encode($data) . '@@';
}

function require_e2e_slug(string $slug): void
{
    if (!str_starts_with($slug, 'e2e-')) {
        guard_fail("Refusing to touch a store whose slug does not start with 'e2e-': {$slug}");
    }
}

switch ($action) {
    case 'info':
        out([
            'environment' => app()->environment(),
            'connection' => $connection->getName(),
            'driver' => $connection->getDriverName(),
            'database' => realpath($connection->getDatabaseName()),
            'mailer' => config('mail.default'),
        ]);
        break;

    case 'migrate':
        Artisan::call('migrate', ['--force' => true]);
        out(['output' => trim(Artisan::output())]);
        break;

    case 'user':
        $user = App\Models\User::updateOrCreate(
            ['email' => $spec['email']],
            ['name' => 'E2E Admin', 'password' => $spec['password']] // 'hashed' cast hashes it
        );
        out(['id' => $user->id]);
        break;

    case 'seed':
        require_e2e_slug($spec['slug']);
        $result = DB::transaction(function () use ($spec) {
            $link = App\Models\Link::create([
                'title' => $spec['title'],
                'slug' => $spec['slug'],
                'status' => 'active',
            ]);
            $page = App\Models\LinkPage::create([
                'link_id' => $link->id,
                'store_name' => $spec['title'],
                'cover_type' => 'color',
                'cover_color' => '#4f46e5',
                'header_alignment' => 'center',
                'product_layout' => $spec['layout'] ?? 'grid',
            ]);

            $categoryIds = [];
            $subcategoryIds = [];
            foreach ($spec['categories'] ?? [] as $cat) {
                $category = App\Models\LinkCategory::create(['link_page_id' => $page->id, 'name' => $cat['name']]);
                $categoryIds[$cat['name']] = $category->id;
                foreach ($cat['subcategories'] ?? [] as $subName) {
                    $sub = App\Models\LinkSubcategory::create(['category_id' => $category->id, 'name' => $subName]);
                    $subcategoryIds[$cat['name'] . '/' . $subName] = $sub->id;
                }
            }

            $productIds = [];
            $order = 0;
            foreach ($spec['products'] ?? [] as $p) {
                $product = App\Models\LinkProduct::create([
                    'link_page_id' => $page->id,
                    'category_id' => $categoryIds[$p['category']],
                    'subcategory_id' => isset($p['subcategory']) ? $subcategoryIds[$p['category'] . '/' . $p['subcategory']] : null,
                    'name' => $p['name'],
                    'product_url' => $p['url'],
                    'status' => $p['status'] ?? 'active',
                    'display_order' => ++$order,
                ]);
                $productIds[$p['name']] = $product->id;
            }

            return [
                'linkId' => $link->id,
                'pageId' => $page->id,
                'slug' => $link->slug,
                'categoryIds' => $categoryIds,
                'subcategoryIds' => $subcategoryIds,
                'productIds' => $productIds,
            ];
        });
        out($result);
        break;

    case 'catalog':
        // Read-only snapshot of one store's catalog, for DB-level assertions.
        $link = App\Models\Link::findOrFail($spec['linkId']);
        require_e2e_slug($link->slug);
        $page = $link->page;
        $categories = App\Models\LinkCategory::with('subcategories')
            ->where('link_page_id', $page->id)->orderBy('id')->get()
            ->map(fn ($c) => ['name' => $c->name, 'subcategories' => $c->subcategories->pluck('name')->all()]);
        $products = App\Models\LinkProduct::with(['category', 'subcategory'])
            ->where('link_page_id', $page->id)->orderBy('id')->get()
            ->map(fn ($p) => [
                'name' => $p->name,
                'url' => $p->product_url,
                'status' => $p->status,
                'category' => $p->category?->name,
                'subcategory' => $p->subcategory?->name,
                'display_order' => $p->display_order,
            ]);
        out(['categories' => $categories, 'products' => $products]);
        break;

    case 'cleanup':
        // Removes ONLY the one store created by the test (identified by id AND e2e- slug).
        $link = App\Models\Link::find($spec['linkId']);
        if (!$link) {
            out(['deleted' => false]);
            break;
        }
        require_e2e_slug($link->slug);
        DB::transaction(function () use ($link) {
            $page = $link->page;
            if ($page) {
                $categoryIds = App\Models\LinkCategory::where('link_page_id', $page->id)->pluck('id');
                App\Models\LinkProduct::where('link_page_id', $page->id)->delete();
                App\Models\LinkSubcategory::whereIn('category_id', $categoryIds)->delete();
                App\Models\LinkCategory::where('link_page_id', $page->id)->delete();
                App\Models\LinkSocial::where('link_page_id', $page->id)->delete();
                $page->delete();
            }
            App\Models\ClickEvent::where('link_id', $link->id)->delete();
            $link->delete();
        });
        out(['deleted' => true]);
        break;

    default:
        guard_fail("Unknown action: {$action}");
}

