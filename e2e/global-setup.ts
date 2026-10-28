import fs from 'node:fs';
import path from 'node:path';
import { chromium, type FullConfig } from '@playwright/test';
import { php } from './support/db';
import { AUTH_STATE, BASE_URL, E2E_ADMIN, E2E_DB, ROOT } from './support/env';

/**
 * Runs once, after the isolated PHP server is up and BEFORE any test.
 * Every mutating step is preceded by the PHP-side guard in support/php/run.php,
 * which inspects the resolved Laravel configuration (not just env vars).
 */
export default async function globalSetup(_config: FullConfig) {
  // 1. Static preconditions -------------------------------------------------
  if (new URL(BASE_URL).hostname.match(/^(localhost|127\.0\.0\.1|::1)$/) === null) {
    throw new Error(`E2E SAFETY GUARD: ${BASE_URL} is not a local address.`);
  }
  if (path.basename(E2E_DB) !== 'e2e.sqlite' || path.dirname(E2E_DB) !== path.join(ROOT, 'database')) {
    throw new Error(`E2E SAFETY GUARD: unexpected E2E database path: ${E2E_DB}`);
  }
  if (fs.existsSync(path.join(ROOT, 'public', 'hot'))) {
    throw new Error('public/hot exists (Vite dev server marker). Stop `npm run dev` before running E2E tests.');
  }
  if (!fs.existsSync(path.join(ROOT, 'public', 'build', 'manifest.json'))) {
    throw new Error('Frontend assets are not built. Run `npm run build` first.');
  }

  // 2. Create the (empty) dedicated database file, then verify the target ----
  if (!fs.existsSync(E2E_DB)) fs.writeFileSync(E2E_DB, '');
  const info = php<{ environment: string; driver: string; database: string; mailer: string }>('info');
  console.log(`[e2e] isolated target verified -> env=${info.environment} driver=${info.driver} db=${info.database} mailer=${info.mailer}`);

  // 3. Migrate ONLY the verified E2E database and create the test account ----
  php('migrate');
  php('user', E2E_ADMIN);

  // 4. Log in through the real UI. This also proves the *running server* uses
  //    the E2E database: the account exists nowhere else.
  fs.mkdirSync(path.dirname(AUTH_STATE), { recursive: true });
  const browser = await chromium.launch();
  try {
    const context = await browser.newContext({ baseURL: BASE_URL });
    const page = await context.newPage();
    await page.goto('/login');
    await page.getByLabel('Email').fill(E2E_ADMIN.email);
    await page.getByLabel('Password').fill(E2E_ADMIN.password);
    await page.getByRole('button', { name: /sign in|login|masuk/i }).click();
    try {
      await page.waitForURL('**/admin', { timeout: 15_000 });
    } catch {
      throw new Error(
        'E2E login failed. The running server does not recognise the E2E account, so it is probably NOT using ' +
          'the isolated E2E database (is another server already bound to the port?). Aborting before any test runs.',
      );
    }
    await context.storageState({ path: AUTH_STATE });
  } finally {
    await browser.close();
  }
}

