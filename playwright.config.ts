import path from 'node:path';
import { defineConfig, devices } from '@playwright/test';
import { AUTH_STATE, BASE_URL, E2E_HOST, E2E_PORT, LARAVEL_ENV, ROOT } from './e2e/support/env';

// Importing ./e2e/support/env already rejects any non-localhost E2E_BASE_URL.

export default defineConfig({
  testDir: './e2e',
  testMatch: '**/*.spec.ts',
  outputDir: 'test-results',
  globalSetup: './e2e/global-setup.ts',

  // One shared SQLite file => run serially. Each test still owns a unique store.
  fullyParallel: false,
  workers: 1,
  retries: 0, // never mask failures with retries
  forbidOnly: !!process.env.CI,
  timeout: 45_000,
  expect: { timeout: 8_000 },

  reporter: [['list'], ['html', { open: 'never', outputFolder: 'playwright-report' }]],

  use: {
    baseURL: BASE_URL,
    storageState: AUTH_STATE,
    trace: 'retain-on-failure',
    screenshot: 'only-on-failure',
    video: 'off',
    actionTimeout: 10_000,
  },

  projects: [{ name: 'chromium', use: { ...devices['Desktop Chrome'] } }],

  // Dedicated local PHP server that is pinned to the isolated E2E environment.
  // reuseExistingServer=false: if something else already listens on the port we
  // fail instead of silently testing an unknown (possibly real) instance.
  webServer: {
    // Same layout as `php artisan serve`: cwd = public/, Laravel's dev router script.
    command: `php -S ${E2E_HOST}:${E2E_PORT} -t . ../vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php`,
    cwd: path.join(ROOT, 'public'),
    url: `${BASE_URL}/login`,
    env: LARAVEL_ENV,
    reuseExistingServer: false,
    timeout: 60_000,
    stdout: 'ignore',
    stderr: 'ignore', // PHP's dev server logs every request to stderr; Laravel errors surface in the page/trace
  },
});
