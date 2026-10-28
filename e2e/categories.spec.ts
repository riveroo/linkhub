import { test, expect, openProducts, unique } from './fixtures';

const URL = (slug: string) => `https://shop.example.test/${slug}`;

test.describe('categories & subcategories', () => {
  test('creates a category without subcategories', async ({ page, createStore }) => {
    const store = createStore();
    const name = unique('Aksesoris');

    await openProducts(page, store);
    await page.getByRole('button', { name: '+ Add Category' }).click();
    await page.locator('#cat_name').fill(`  ${name}  `); // surrounding whitespace must be trimmed
    await page.getByRole('button', { name: 'Create Category' }).click();

    await expect(page.getByText('Category created successfully.')).toBeVisible();
    await expect(page.getByRole('button', { name: `Edit category ${name}` })).toBeVisible();
    expect(store.catalog().categories).toEqual([{ name, subcategories: [] }]);
  });

  test('creates a category with several subcategories in one submission', async ({ page, createStore }) => {
    const store = createStore();

    await openProducts(page, store);
    await page.getByRole('button', { name: '+ Add Category' }).click();
    await page.locator('#cat_name').fill('Kalung');

    for (const sub of ['Emas', 'Perak', 'Perunggu']) {
      await page.locator('#new_subcat_input').fill(sub);
      await page.locator('#new_subcat_input').press('Enter');
    }
    await expect(page.getByRole('textbox', { name: 'Subcategory Name' })).toHaveCount(3);

    // An added subcategory can be removed again before saving.
    await page.getByRole('button', { name: 'Remove subcategory' }).nth(2).click();
    await expect(page.getByRole('textbox', { name: 'Subcategory Name' })).toHaveCount(2);
    await page.locator('#new_subcat_input').fill('Perunggu');
    await page.getByRole('button', { name: '+ Add Subcategory' }).click();

    await page.getByRole('button', { name: 'Create Category' }).click();

    await expect(page.getByText('Category created successfully.')).toBeVisible();
    await expect(page.getByText('3 sub')).toBeVisible();
    expect(store.catalog().categories).toEqual([{ name: 'Kalung', subcategories: ['Emas', 'Perak', 'Perunggu'] }]);
  });

  test('editing a category keeps its subcategories and product assignments', async ({ page, createStore }) => {
    const store = createStore({
      categories: [{ name: 'Kalung', subcategories: ['Emas', 'Perak'] }],
      products: [
        { name: 'Kalung Emas Asli', category: 'Kalung', subcategory: 'Emas', url: URL('emas') },
        { name: 'Kalung Perak Asli', category: 'Kalung', subcategory: 'Perak', url: URL('perak') },
      ],
    });

    await openProducts(page, store);
    await page.getByRole('button', { name: 'Edit category Kalung' }).click();

    // Persisted subcategories are loaded into the modal (rows are ordered by id).
    const subInputs = page.getByRole('textbox', { name: 'Subcategory Name' });
    await expect(subInputs).toHaveCount(2);
    await expect(subInputs.nth(0)).toHaveValue('Emas');
    await expect(subInputs.nth(1)).toHaveValue('Perak');

    await page.locator('#edit_cat_name').fill('Kalung Premium'); // rename parent
    await subInputs.nth(0).fill('Emas Murni'); // rename subcategory
    await page.getByRole('button', { name: 'Remove subcategory' }).nth(1).click(); // remove Perak
    await page.locator('#edit_subcat_input').fill('Perunggu'); // add new
    await page.getByRole('button', { name: '+ Add Subcategory' }).click();
    await page.getByRole('button', { name: 'Update Category' }).click();

    await expect(page.getByText('Category updated successfully.')).toBeVisible();

    const snapshot = store.catalog();
    expect(snapshot.categories).toEqual([{ name: 'Kalung Premium', subcategories: ['Emas Murni', 'Perunggu'] }]);
    // Renamed subcategory keeps the same record => product still assigned to it.
    expect(snapshot.products.find((p) => p.name === 'Kalung Emas Asli')).toMatchObject({
      category: 'Kalung Premium',
      subcategory: 'Emas Murni',
    });
    // Removed subcategory => assignment cleared, parent category preserved, product kept.
    expect(snapshot.products.find((p) => p.name === 'Kalung Perak Asli')).toMatchObject({
      category: 'Kalung Premium',
      subcategory: null,
    });

    // Reopening the modal shows what was persisted.
    await page.getByRole('button', { name: 'Edit category Kalung Premium' }).click();
    await expect(page.getByRole('textbox', { name: 'Subcategory Name' })).toHaveCount(2);
    await expect(page.getByRole('textbox', { name: 'Subcategory Name' }).nth(0)).toHaveValue('Emas Murni');
    await expect(page.getByRole('textbox', { name: 'Subcategory Name' }).nth(1)).toHaveValue('Perunggu');
  });

  test('rejects empty and duplicate subcategory names in the form', async ({ page, createStore }) => {
    const store = createStore();
    await openProducts(page, store);
    await page.getByRole('button', { name: '+ Add Category' }).click();
    await page.locator('#cat_name').fill('Pakaian');

    // empty / whitespace-only
    await page.locator('#new_subcat_input').fill('   ');
    await page.getByRole('button', { name: '+ Add Subcategory' }).click();
    await expect(page.getByRole('alert').filter({ hasText: 'Subcategory name cannot be empty.' })).toBeVisible();

    // duplicate, case-insensitive
    await page.locator('#new_subcat_input').fill('Kemeja');
    await page.getByRole('button', { name: '+ Add Subcategory' }).click();
    await page.locator('#new_subcat_input').fill('kemeja');
    await page.getByRole('button', { name: '+ Add Subcategory' }).click();
    await expect(page.getByRole('alert').filter({ hasText: /already been added/ })).toBeVisible();
    await expect(page.getByRole('textbox', { name: 'Subcategory Name' })).toHaveCount(1);

    // Nothing was submitted.
    expect(store.catalog().categories).toEqual([]);
  });

  test('server rejects a duplicate category name (case-insensitive)', async ({ page, createStore }) => {
    const store = createStore({ categories: [{ name: 'Kalung' }] });

    await openProducts(page, store);
    await page.getByRole('button', { name: '+ Add Category' }).click();
    await page.locator('#cat_name').fill('kalung');
    await page.getByRole('button', { name: 'Create Category' }).click();

    await expect(page.getByText('This category name already exists for this page.')).toBeVisible();
    expect(store.catalog().categories).toEqual([{ name: 'Kalung', subcategories: [] }]);
  });
});
