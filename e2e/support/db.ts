import { execFileSync } from 'node:child_process';
import path from 'node:path';
import { LARAVEL_ENV, ROOT } from './env';

const RUNNER = path.join(ROOT, 'e2e', 'support', 'php', 'run.php');

/** Run one guarded action against the E2E database and parse its JSON result. */
export function php<T = unknown>(action: string, spec: unknown = {}): T {
  let stdout: string;
  try {
    stdout = execFileSync('php', [RUNNER, action], {
      cwd: ROOT,
      encoding: 'utf8',
      env: { ...process.env, ...LARAVEL_ENV, E2E_SPEC: JSON.stringify(spec) },
      stdio: ['ignore', 'pipe', 'pipe'],
    });
  } catch (error) {
    const e = error as { stderr?: string; stdout?: string; message: string };
    throw new Error(`PHP helper "${action}" failed:\n${e.stderr || e.stdout || e.message}`);
  }
  const match = stdout.match(/@@([\s\S]*)@@/);
  if (!match) throw new Error(`PHP helper "${action}" returned no result. Output:\n${stdout}`);
  return JSON.parse(match[1]) as T;
}

export type SeedSpec = {
  title: string;
  slug: string;
  layout?: 'grid' | 'list';
  categories?: { name: string; subcategories?: string[] }[];
  products?: { name: string; category: string; subcategory?: string; status?: 'active' | 'inactive'; url: string }[];
};

export type SeededStore = {
  linkId: number;
  pageId: number;
  slug: string;
  categoryIds: Record<string, number>;
  subcategoryIds: Record<string, number>;
  productIds: Record<string, number>;
};

export type CatalogSnapshot = {
  categories: { name: string; subcategories: string[] }[];
  products: {
    name: string;
    url: string;
    status: 'active' | 'inactive';
    category: string | null;
    subcategory: string | null;
    display_order: number;
  }[];
};

export const seedStore = (spec: SeedSpec) => php<SeededStore>('seed', spec);
export const catalogOf = (linkId: number) => php<CatalogSnapshot>('catalog', { linkId });
export const cleanupStore = (linkId: number) => php('cleanup', { linkId });

