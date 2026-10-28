import { test, expect, openProducts } from './fixtures';

const URL = (slug: string) => `https://shop.example.test/${slug}`;

const catalog = {
  categories: [
    { name: 'Kalung', subcategories: ['Emas', 'Perak'] },
    { name: 'Pakaian', subcategories: ['Kemeja'] },
    { name: 'Topi' }, // no subcategories
  ],
};

test.describe('product management', () => {
  test('creates a product directly under a category (no subcategory)', async ({ page, createStore }) => {
    const store = createStore(catalog);

    await openProducts(page, store);
    await page.getByRole('button', { name: '+ Add Product' }).click();
    await page.locator('#prod_name').fill('Topi Snapback');
    await page.locator('#prod_cat').selectOption({ label: 'Topi' });
    // Category without subcategories => no empty subcategory selector.
    await expect(page.locator('#prod_subcat')).toBeHidden();
    await page.locator('#prod_url').fill(URL('topi'));
    await page.getByRole('button', { name: 'Add Product', exact: true }).click();

    await expect(page.getByText('Product added successfully.')).toBeVisible();
    expect(store.catalog().products).toEqual([
      expect.objectContaining({ name: 'Topi Snapback', url: URL('topi'), status: 'active', category: 'Topi', subcategory: null }),
    ]);
  });

  test('subcategory is optional and only lists the chosen category’s subcategories', async ({ page, createStore }) => {
    const store = createStore(catalog);

    await openProducts(page, store);
    await page.getByRole('button', { name: '+ Add Product' }).click();
    await page.locator('#prod_name').fill('Kalung Emas 24K');
    await page.locator('#prod_cat').selectOption({ label: 'Kalung' });

    const sub = page.locator('#prod_subcat');
    await expect(sub).toBeVisible();
    await expect(sub.locator('option')).toHaveText(['None (No Subcategory)', 'Emas', 'Perak']);

    await sub.selectOption({ label: 'Emas' });
    // Switching parent category resets the subcategory choice and swaps the option list.
    await page.locator('#prod_cat').selectOption({ label: 'Pakaian' });
    await expect(sub.locator('option')).toHaveText(['None (No Subcategory)', 'Kemeja']);
    await expect(sub).toHaveValue('');

    await page.locator('#prod_cat').selectOption({ label: 'Kalung' });
    await sub.selectOption({ label: 'Emas' });
    await page.locator('#prod_url').fill(URL('kalung-emas'));
    await page.getByRole('button', { name: 'Add Product', exact: true }).click();

    await expect(page.getByText('Product added successfully.')).toBeVisible();
    expect(store.catalog().products).toEqual([
      expect.objectContaining({ name: 'Kalung Emas 24K', category: 'Kalung', subcategory: 'Emas' }),
    ]);
    const row = page.getByRole('row').filter({ hasText: 'Kalung Emas 24K' });
    await expect(row.getByText('Emas', { exact: true })).toBeVisible();
  });

  test('edits a product; URL and category assignment persist', async ({ page, createStore }) => {
    const store = createStore({
      ...catalog,
      products: [{ name: 'Kalung Lama', category: 'Kalung', subcategory: 'Emas', url: URL('lama') }],
    });

    await openProducts(page, store);
    await page.getByRole('button', { name: 'Edit product Kalung Lama' }).click();
    await page.locator('#edit_prod_name').fill('Kemeja Baru');
    await page.locator('#edit_prod_cat').selectOption({ label: 'Pakaian' });
    await page.locator('#edit_prod_url').fill(URL('kemeja-baru'));
    await page.getByRole('button', { name: 'Update Product' }).click();

    await expect(page.getByText('Product updated successfully.')).toBeVisible();

    // Moving to another parent clears the (now invalid) subcategory instead of keeping a stale one.
    expect(store.catalog().products).toEqual([
      expect.objectContaining({ name: 'Kemeja Baru', url: URL('kemeja-baru'), category: 'Pakaian', subcategory: null, status: 'active' }),
    ]);

    // The persisted values are what the edit form loads next time.
    await page.getByRole('button', { name: 'Edit product Kemeja Baru' }).click();
    await expect(page.locator('#edit_prod_url')).toHaveValue(URL('kemeja-baru'));
    await expect(page.locator('#edit_prod_cat')).toHaveValue(String(store.categoryIds['Pakaian']));
  });

  test('activating / deactivating a product controls public visibility', async ({ page, createStore }) => {
    const store = createStore({
      categories: [{ name: 'Topi' }],
      products: [
        { name: 'Topi Toggle', category: 'Topi', url: URL('toggle') },
        { name: 'Topi Penjaga', category: 'Topi', url: URL('penjaga') }, // keeps the catalog section rendered
      ],
    });
    const row = page.getByRole('row').filter({ hasText: 'Topi Toggle' });

    await page.goto(`/${store.slug}`);
    await expect(page.getByRole('heading', { name: 'Topi Toggle', exact: true })).toBeVisible();

    await openProducts(page, store);
    await row.getByRole('button', { name: 'Toggle status of product Topi Toggle' }).click();
    await expect(page.getByText('Product status updated.')).toBeVisible();
    await expect(row.getByText('Inactive')).toBeVisible();
    expect(store.catalog().products.find((p) => p.name === 'Topi Toggle')?.status).toBe('inactive');

    await page.goto(`/${store.slug}`);
    await expect(page.getByRole('heading', { name: 'Topi Penjaga', exact: true })).toBeVisible();
    await expect(page.getByRole('heading', { name: 'Topi Toggle', exact: true })).toHaveCount(0);

    await openProducts(page, store);
    await row.getByRole('button', { name: 'Toggle status of product Topi Toggle' }).click();
    await expect(row.getByText('Active', { exact: true })).toBeVisible();

    await page.goto(`/${store.slug}`);
    await expect(page.getByRole('heading', { name: 'Topi Toggle', exact: true })).toBeVisible();
  });
});

