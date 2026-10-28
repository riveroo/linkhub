import { test as base, expect, type Page } from '@playwright/test';
import { catalogOf, cleanupStore, seedStore, type CatalogSnapshot, type SeedSpec, type SeededStore } from './support/db';

export type Store = SeededStore & {
  title: string;
  /** Re-reads the persisted catalog from the E2E database. */
  catalog: () => CatalogSnapshot;
};

type Fixtures = {
  /**
   * Creates an isolated store (unique e2e-* slug) for the test and removes
   * exactly that store - and nothing else - when the test finishes.
   */
  createStore: (spec?: Partial<Omit<SeedSpec, 'slug' | 'title'>>) => Store;
};

export const test = base.extend<Fixtures>({
  createStore: async ({}, use, testInfo) => {
    const created: number[] = [];

    await use((spec = {}) => {
      const uid = `${Date.now().toString(36)}${Math.random().toString(36).slice(2, 6)}`;
      const title = `E2E Store ${uid}`;
      const seeded = seedStore({ ...spec, title, slug: `e2e-${uid}` });
      created.push(seeded.linkId);
      return { ...seeded, title, catalog: () => catalogOf(seeded.linkId) };
    });

    for (const linkId of created) cleanupStore(linkId);
    void testInfo;
  },
});

export { expect };

/** Unique, collision-free names for records created through the UI. */
export const unique = (label: string) => `${label} ${Math.random().toString(36).slice(2, 7)}`;

export const productsTab = (store: Pick<SeededStore, 'linkId'>) => `/admin/links/${store.linkId}/edit-page?tab=products`;

/** Go to the editor's Products tab and wait until the Alpine component is ready. */
export async function openProducts(page: Page, store: Pick<SeededStore, 'linkId'>) {
  await page.goto(productsTab(store));
  await expect(page.getByRole('heading', { name: 'Products Catalog' })).toBeVisible();
}

