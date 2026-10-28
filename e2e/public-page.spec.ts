import { test, expect } from './fixtures';
import type { Page } from '@playwright/test';

const URL = (slug: string) => `https://shop.example.test/${slug}`;

const publicStore = {
  categories: [
    { name: 'Kalung', subcategories: ['Emas', 'Perak', 'Perunggu'] }, // Perunggu has no products
    { name: 'Pakaian' }, // no subcategories
  ],
  products: [
    { name: 'Kalung Emas', category: 'Kalung', subcategory: 'Emas', url: URL('kalung-emas') },
    { name: 'Kalung Perak', category: 'Kalung', subcategory: 'Perak', url: URL('kalung-perak') },
    { name: 'Kalung Polos', category: 'Kalung', url: URL('kalung-polos') }, // no subcategory
    { name: 'Kalung Rahasia', category: 'Kalung', subcategory: 'Emas', url: URL('rahasia'), status: 'inactive' as const },
    { name: 'Kaos Putih', category: 'Pakaian', url: URL('kaos-putih') },
  ],
};

const card = (page: Page, name: string) => page.getByRole('heading', { name, exact: true });
const pill = (page: Page, name: RegExp | string) => page.getByRole('button', { name });
const subFilter = (page: Page) => page.getByRole('group', { name: /Filter subkategori/ });

test.describe('public landing page', () => {
  test.use({ storageState: { cookies: [], origins: [] } }); // visitors are not logged in

  test('loads the public profile with only active products', async ({ page, createStore }) => {
    const store = createStore(publicStore);
    await page.goto(`/${store.slug}`);

    await expect(page.getByRole('heading', { level: 1, name: store.title })).toBeVisible();
    for (const name of ['Kalung Emas', 'Kalung Perak', 'Kalung Polos', 'Kaos Putih']) {
      await expect(card(page, name)).toBeVisible();
    }
    await expect(card(page, 'Kalung Rahasia')).toHaveCount(0); // inactive => not public
    // No subcategory container is rendered until it is applicable.
    await expect(subFilter(page)).toHaveCount(0);
  });

  test('category and subcategory filtering follow the persisted relationships', async ({ page, createStore }) => {
    const store = createStore(publicStore);
    await page.goto(`/${store.slug}`);

    // Parent category with subcategories => contextual second row appears.
    await pill(page, /^Kalung \(3\)$/).click();
    const group = page.getByRole('group', { name: 'Filter subkategori Kalung' });
    await expect(group).toBeVisible();
    await expect(card(page, 'Kaos Putih')).toBeHidden();
    for (const name of ['Kalung Emas', 'Kalung Perak', 'Kalung Polos']) await expect(card(page, name)).toBeVisible();
    await expect(pill(page, 'Semua Kalung (3)')).toHaveAttribute('aria-pressed', 'true');

    // A specific subcategory excludes siblings and products without a subcategory.
    await pill(page, 'Emas (1)').click();
    await expect(card(page, 'Kalung Emas')).toBeVisible();
    await expect(card(page, 'Kalung Perak')).toBeHidden();
    await expect(card(page, 'Kalung Polos')).toBeHidden();

    // "All" within the parent shows everything in it, including the no-subcategory product.
    await pill(page, 'Semua Kalung (3)').click();
    for (const name of ['Kalung Emas', 'Kalung Perak', 'Kalung Polos']) await expect(card(page, name)).toBeVisible();

    // Switching parent resets the subcategory and removes the container for a category without subcategories.
    await pill(page, 'Perak (1)').click();
    await expect(card(page, 'Kalung Emas')).toBeHidden();
    await pill(page, /^Pakaian \(1\)$/).click();
    await expect(subFilter(page)).toHaveCount(0);
    await expect(card(page, 'Kaos Putih')).toBeVisible();
    await expect(card(page, 'Kalung Emas')).toBeHidden();

    // Returning to the first parent starts from "All" again (selection was reset).
    await pill(page, /^Kalung \(3\)$/).click();
    await expect(pill(page, 'Semua Kalung (3)')).toHaveAttribute('aria-pressed', 'true');
    await expect(pill(page, 'Perak (1)')).toHaveAttribute('aria-pressed', 'false');
    await expect(card(page, 'Kalung Emas')).toBeVisible();

    // Global "All" hides the subcategory row and shows every active product.
    await pill(page, /^Semua \(4\)$/).click();
    await expect(subFilter(page)).toHaveCount(0);
    for (const name of ['Kalung Emas', 'Kalung Perak', 'Kalung Polos', 'Kaos Putih']) await expect(card(page, name)).toBeVisible();
  });

  test('shows an empty state when no product matches, and can reset', async ({ page, createStore }) => {
    const store = createStore(publicStore);
    await page.goto(`/${store.slug}`);

    await pill(page, /^Kalung \(3\)$/).click();
    await pill(page, 'Perunggu (0)').click();

    await expect(page.getByText('Tidak ada produk yang sesuai dengan filter ini.')).toBeVisible();
    for (const name of ['Kalung Emas', 'Kalung Perak', 'Kalung Polos', 'Kaos Putih']) await expect(card(page, name)).toBeHidden();

    await page.getByRole('button', { name: 'Reset Filter' }).click();
    await expect(page.getByText('Tidak ada produk yang sesuai dengan filter ini.')).toBeHidden();
    await expect(card(page, 'Kalung Emas')).toBeVisible();
  });

  test('filters products by searching product name, case-insensitively, with clear button', async ({ page, createStore }) => {
    const store = createStore(publicStore);
    await page.goto(`/${store.slug}`);

    const searchInput = page.getByRole('textbox', { name: 'Cari nama produk' });
    await expect(searchInput).toBeVisible();

    // Search for "emas" (lowercase) -> shows only "Kalung Emas"
    await searchInput.fill('emas');
    await expect(card(page, 'Kalung Emas')).toBeVisible();
    await expect(card(page, 'Kalung Perak')).toBeHidden();
    await expect(card(page, 'Kalung Polos')).toBeHidden();
    await expect(card(page, 'Kaos Putih')).toBeHidden();

    // Clear search using the clear button
    const clearButton = page.getByRole('button', { name: 'Hapus pencarian' });
    await expect(clearButton).toBeVisible();
    await clearButton.click();

    await expect(searchInput).toHaveValue('');
    for (const name of ['Kalung Emas', 'Kalung Perak', 'Kalung Polos', 'Kaos Putih']) {
      await expect(card(page, name)).toBeVisible();
    }

    // Search for non-existent product -> empty state appears
    await searchInput.fill('Barang Tidak Ada');
    await expect(page.getByText('Tidak ada produk yang sesuai dengan filter ini.')).toBeVisible();
    await page.getByRole('button', { name: 'Reset Filter' }).click();
    await expect(page.getByText('Tidak ada produk yang sesuai dengan filter ini.')).toBeHidden();
    await expect(card(page, 'Kalung Emas')).toBeVisible();
  });

  test('product buttons keep going through the tracking endpoint to the stored destination', async ({ page, createStore }) => {
    const store = createStore(publicStore);
    await page.goto(`/${store.slug}`);

    const link = page.locator(`a[href$="/link/${store.linkId}/product/${store.productIds['Kalung Emas']}/click"]`);
    await expect(link).toBeVisible();
    await expect(link).toContainText('Lihat Produk');

    // Do not follow the redirect (it points to an external host): assert where it would go.
    const response = await page.request.get((await link.getAttribute('href'))!, { maxRedirects: 0 });
    expect(response.status()).toBe(302);
    expect(response.headers()['location']).toBe(URL('kalung-emas'));

    // The raw destination URL is not exposed in the page HTML.
    expect(await page.content()).not.toContain(URL('kalung-emas'));
  });
});

