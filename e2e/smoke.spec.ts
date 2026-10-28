import { test, expect, openProducts } from './fixtures';
import { php } from './support/db';
import { BASE_URL, E2E_DB } from './support/env';

test.describe('environment isolation', () => {
  test('targets localhost and the dedicated E2E database', async () => {
    expect(new URL(BASE_URL).hostname).toMatch(/^(localhost|127\.0\.0\.1|::1)$/);

    const info = php<{ environment: string; driver: string; database: string; mailer: string }>('info');
    expect(info).toMatchObject({ environment: 'e2e', driver: 'sqlite', mailer: 'array' });
    expect(info.database.endsWith('e2e.sqlite')).toBe(true);
    expect(info.database).toContain(E2E_DB.replace(/\/database\/e2e\.sqlite$/, ''));
  });
});

test.describe('authentication & navigation', () => {
  test.describe('guest', () => {
    test.use({ storageState: { cookies: [], origins: [] } });

    test('login page loads', async ({ page }) => {
      await page.goto('/login');
      await expect(page.getByLabel('Email')).toBeVisible();
      await expect(page.getByRole('button', { name: 'Sign In' })).toBeVisible();
    });

    test('is redirected to /login when opening the admin area', async ({ page, createStore }) => {
      const store = createStore();
      await page.goto('/admin/links');
      await expect(page).toHaveURL(/\/login$/);

      await page.goto(`/admin/links/${store.linkId}/edit-page`);
      await expect(page).toHaveURL(/\/login$/);
    });

    test('rejects invalid credentials', async ({ page }) => {
      await page.goto('/login');
      await page.getByLabel('Email').fill('e2e-admin@example.test');
      await page.getByLabel('Password').fill('definitely-wrong');
      await page.getByRole('button', { name: 'Sign In' }).click();
      await expect(page).toHaveURL(/\/login$/);
      await expect(page.getByLabel('Email')).toBeVisible();
    });
  });

  test('authorized user can open the links management page', async ({ page, createStore }) => {
    const store = createStore();
    await page.goto('/admin/links');
    await expect(page).toHaveURL(/\/admin\/links$/);
    await expect(page.getByText(store.title)).toBeVisible();
  });

  test('authorized user can open the product catalog of a store', async ({ page, createStore }) => {
    const store = createStore({
      categories: [{ name: 'Kaos' }],
      products: [{ name: 'Kaos Smoke', category: 'Kaos', url: 'https://shop.example.test/kaos-smoke' }],
    });
    await openProducts(page, store);
    await expect(page.getByRole('row').filter({ hasText: 'Kaos Smoke' })).toBeVisible();
    await expect(page.getByRole('button', { name: '+ Add Product' })).toBeEnabled();
  });
});

