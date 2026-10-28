import { test, expect, openProducts } from './fixtures';
import type { Page } from '@playwright/test';

const URL = (slug: string) => `https://shop.example.test/${slug}`;

/** Store with 2 parent categories, mixed statuses, and one product that bulk actions must never touch. */
const bulkStore = {
  categories: [
    { name: 'Kalung', subcategories: ['Emas', 'Perak'] },
    { name: 'Gelang', subcategories: ['Giok'] },
  ],
  products: [
    { name: 'Prod Alpha', category: 'Kalung', subcategory: 'Emas', url: URL('alpha') },
    { name: 'Prod Beta', category: 'Kalung', subcategory: 'Perak', url: URL('beta') },
    { name: 'Prod Gamma', category: 'Kalung', url: URL('gamma'), status: 'inactive' as const },
    { name: 'Prod Delta', category: 'Gelang', subcategory: 'Giok', url: URL('delta') },
    { name: 'Prod Untouched', category: 'Gelang', url: URL('untouched') },
  ],
};

const rowCheckbox = (page: Page, name: string) => page.getByRole('checkbox', { name: `Select product ${name}`, exact: true });
const headerCheckbox = (page: Page) => page.getByRole('checkbox', { name: 'Select all products on this page' });
const selectedCount = (page: Page, n: number) => page.locator('span.bg-indigo-600').filter({ hasText: `${n}` });
const toolbarButton = (page: Page, name: string) => page.getByRole('button', { name, exact: true });

async function select(page: Page, ...names: string[]) {
  for (const name of names) await rowCheckbox(page, name).check();
}

test.describe('bulk actions', () => {
  test('selection: individual, select-all, indeterminate header, count and clear', async ({ page, createStore }) => {
    const store = createStore(bulkStore);
    await openProducts(page, store);

    // Nothing selected => no toolbar.
    await expect(toolbarButton(page, 'Deselect All')).toHaveCount(0);
    await expect(headerCheckbox(page)).not.toBeChecked();

    await select(page, 'Prod Alpha');
    await expect(selectedCount(page, 1)).toBeVisible();
    await expect(headerCheckbox(page)).toHaveJSProperty('indeterminate', true);

    await select(page, 'Prod Beta');
    await expect(selectedCount(page, 2)).toBeVisible();

    await rowCheckbox(page, 'Prod Beta').uncheck();
    await expect(selectedCount(page, 1)).toBeVisible();

    // Select all (current page = all 5 rows here).
    await headerCheckbox(page).check();
    await expect(selectedCount(page, 5)).toBeVisible();
    await expect(headerCheckbox(page)).toBeChecked();
    await expect(headerCheckbox(page)).toHaveJSProperty('indeterminate', false);
    for (const p of bulkStore.products) await expect(rowCheckbox(page, p.name)).toBeChecked();

    // One row less => back to indeterminate.
    await rowCheckbox(page, 'Prod Untouched').uncheck();
    await expect(selectedCount(page, 4)).toBeVisible();
    await expect(headerCheckbox(page)).toHaveJSProperty('indeterminate', true);

    // Clear selection hides the toolbar and unchecks everything.
    await toolbarButton(page, 'Deselect All').click();
    await expect(toolbarButton(page, 'Deselect All')).toHaveCount(0);
    await expect(headerCheckbox(page)).not.toBeChecked();
    await expect(headerCheckbox(page)).toHaveJSProperty('indeterminate', false);
    await expect(rowCheckbox(page, 'Prod Alpha')).not.toBeChecked();
  });

  test('changes the category of selected products and clears incompatible subcategories', async ({ page, createStore }) => {
    const store = createStore(bulkStore);
    const before = store.catalog().products;

    await openProducts(page, store);
    await select(page, 'Prod Alpha', 'Prod Beta');
    await toolbarButton(page, 'Edit Category').click();
    await expect(page.getByText(/Change the parent category for\s*2\s*selected product/)).toBeVisible();
    await page.locator('#bulk_category_id').selectOption({ label: 'Gelang' });
    await page.getByRole('button', { name: 'Apply Category' }).click();

    await expect(page.getByText('Successfully updated category for 2 product(s).')).toBeVisible();

    const after = store.catalog().products;
    for (const name of ['Prod Alpha', 'Prod Beta']) {
      expect(after.find((p) => p.name === name)).toMatchObject({ category: 'Gelang', subcategory: null });
    }
    // Unrelated products and unrelated fields are unchanged.
    for (const name of ['Prod Gamma', 'Prod Delta', 'Prod Untouched']) {
      expect(after.find((p) => p.name === name)).toEqual(before.find((p) => p.name === name));
    }
    for (const name of ['Prod Alpha', 'Prod Beta']) {
      const b = before.find((p) => p.name === name)!;
      const a = after.find((p) => p.name === name)!;
      expect({ url: a.url, status: a.status, order: a.display_order }).toEqual({ url: b.url, status: b.status, order: b.display_order });
    }
    // Selection is gone after the page reloads.
    await expect(toolbarButton(page, 'Deselect All')).toHaveCount(0);
  });

  test('changes and clears the subcategory when all selected products share a parent', async ({ page, createStore }) => {
    const store = createStore(bulkStore);
    const before = store.catalog().products;

    await openProducts(page, store);
    await select(page, 'Prod Alpha', 'Prod Beta', 'Prod Gamma'); // all Kalung
    await expect(toolbarButton(page, 'Edit Subcategory')).toBeEnabled();
    await toolbarButton(page, 'Edit Subcategory').click();

    // Only the shared parent's subcategories (+ explicit "None") are offered.
    await expect(page.locator('#bulk_subcategory_id option')).toHaveText(['None (Clear Subcategory)', 'Emas', 'Perak']);
    await page.locator('#bulk_subcategory_id').selectOption({ label: 'Perak' });
    await page.getByRole('button', { name: 'Apply Subcategory' }).click();
    await expect(page.getByText('Successfully updated subcategory for 3 product(s).')).toBeVisible();

    let products = store.catalog().products;
    for (const name of ['Prod Alpha', 'Prod Beta', 'Prod Gamma']) {
      expect(products.find((p) => p.name === name)).toMatchObject({ category: 'Kalung', subcategory: 'Perak' });
    }
    expect(products.find((p) => p.name === 'Prod Delta')).toEqual(before.find((p) => p.name === 'Prod Delta'));
    expect(products.find((p) => p.name === 'Prod Gamma')?.status).toBe('inactive'); // status untouched

    // Clearing keeps the parent category.
    await select(page, 'Prod Alpha', 'Prod Beta');
    await toolbarButton(page, 'Edit Subcategory').click();
    await page.locator('#bulk_subcategory_id').selectOption({ label: 'None (Clear Subcategory)' });
    await page.getByRole('button', { name: 'Apply Subcategory' }).click();
    await expect(page.getByText('Successfully updated subcategory for 2 product(s).')).toBeVisible();

    products = store.catalog().products;
    for (const name of ['Prod Alpha', 'Prod Beta']) {
      expect(products.find((p) => p.name === name)).toMatchObject({ category: 'Kalung', subcategory: null });
    }
    expect(products.find((p) => p.name === 'Prod Gamma')).toMatchObject({ subcategory: 'Perak' });
  });

  test('blocks subcategory changes when selected products have different parent categories', async ({ page, createStore }) => {
    const store = createStore(bulkStore);
    const before = store.catalog().products;

    const bulkPosts: string[] = [];
    page.on('request', (r) => {
      if (r.method() === 'POST' && r.url().includes('/products/bulk-action')) bulkPosts.push(r.url());
    });

    await openProducts(page, store);
    await select(page, 'Prod Alpha', 'Prod Delta'); // Kalung + Gelang

    const button = toolbarButton(page, 'Edit Subcategory');
    await expect(button).toBeDisabled();
    await expect(button).toHaveAttribute('title', /same parent category/);

    await button.click({ force: true }); // a disabled button must not open anything
    await expect(page.locator('#bulk_subcategory_id')).toBeHidden();
    expect(bulkPosts).toHaveLength(0);
    expect(store.catalog().products).toEqual(before);
  });

  test('activates and deactivates selected products with explicit actions', async ({ page, createStore }) => {
    const store = createStore(bulkStore);
    const before = store.catalog().products;
    const statusOf = (name: string) => store.catalog().products.find((p) => p.name === name)?.status;

    await openProducts(page, store);

    // All active => only Deactivate is offered.
    await select(page, 'Prod Alpha', 'Prod Beta');
    await expect(toolbarButton(page, 'Deactivate')).toBeVisible();
    await expect(toolbarButton(page, 'Activate')).toHaveCount(0);

    // Mixed (active + inactive) => both explicit actions, never a toggle.
    await select(page, 'Prod Gamma');
    await expect(toolbarButton(page, 'Deactivate')).toBeVisible();
    await expect(toolbarButton(page, 'Activate')).toBeVisible();

    await toolbarButton(page, 'Deactivate').click();
    await expect(page.getByText(/Are you sure you want to\s*deactivate\s*3\s*selected product/)).toBeVisible();
    await page.getByRole('button', { name: 'Confirm Deactivate' }).click();
    await expect(page.getByText('Successfully deactivated 3 product(s).')).toBeVisible();
    for (const name of ['Prod Alpha', 'Prod Beta', 'Prod Gamma']) expect(statusOf(name)).toBe('inactive');
    expect(statusOf('Prod Delta')).toBe('active'); // outside the selection

    // All inactive => only Activate is offered.
    await select(page, 'Prod Alpha', 'Prod Gamma');
    await expect(toolbarButton(page, 'Activate')).toBeVisible();
    await expect(toolbarButton(page, 'Deactivate')).toHaveCount(0);
    await toolbarButton(page, 'Activate').click();
    await page.getByRole('button', { name: 'Confirm Activate' }).click();
    await expect(page.getByText('Successfully activated 2 product(s).')).toBeVisible();
    expect(statusOf('Prod Alpha')).toBe('active');
    expect(statusOf('Prod Gamma')).toBe('active');
    expect(statusOf('Prod Beta')).toBe('inactive');

    // Repeating the same action is idempotent and reports a consistent result.
    await select(page, 'Prod Alpha');
    await toolbarButton(page, 'Deactivate').click();
    await page.getByRole('button', { name: 'Confirm Deactivate' }).click();
    await expect(page.getByText('Successfully deactivated 1 product(s).')).toBeVisible();
    await select(page, 'Prod Alpha');
    await toolbarButton(page, 'Activate').click();
    await page.getByRole('button', { name: 'Confirm Activate' }).click();
    await expect(page.getByText('Successfully activated 1 product(s).')).toBeVisible();

    // Only status was ever modified.
    const after = store.catalog().products;
    for (const p of after) {
      const b = before.find((x) => x.name === p.name)!;
      expect({ ...p, status: b.status }).toEqual(b);
    }
  });

  test('deletes selected products only after explicit confirmation', async ({ page, createStore }) => {
    const store = createStore(bulkStore);
    const names = () => store.catalog().products.map((p) => p.name);

    await openProducts(page, store);
    await select(page, 'Prod Alpha', 'Prod Beta');
    await toolbarButton(page, 'Delete').click();

    const heading = page.getByRole('heading', { name: 'Delete Selected Products?' });
    await expect(heading).toBeVisible();
    await expect(page.getByText(/Are you sure you want to delete\s*2\s*selected product/)).toBeVisible();

    // Cancelling must not delete anything and keeps the selection.
    await page.getByRole('button', { name: 'Cancel' }).click();
    await expect(heading).toBeHidden();
    expect(names()).toHaveLength(5);
    await expect(selectedCount(page, 2)).toBeVisible();

    // Confirming deletes exactly the selected products.
    await toolbarButton(page, 'Delete').click();
    await page.getByRole('button', { name: 'Yes, Delete Products' }).click();
    await expect(page.getByText('Successfully deleted 2 product(s).')).toBeVisible();

    expect(names()).toEqual(['Prod Gamma', 'Prod Delta', 'Prod Untouched']);
    await expect(page.getByRole('row').filter({ hasText: 'Prod Alpha' })).toHaveCount(0);
    await expect(page.getByRole('row').filter({ hasText: 'Prod Gamma' })).toBeVisible();
    // Selection cleaned up; categories and subcategories untouched.
    await expect(toolbarButton(page, 'Deselect All')).toHaveCount(0);
    expect(store.catalog().categories).toEqual([
      { name: 'Kalung', subcategories: ['Emas', 'Perak'] },
      { name: 'Gelang', subcategories: ['Giok'] },
    ]);
  });
});

