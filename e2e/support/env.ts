import path from 'node:path';
import { fileURLToPath } from 'node:url';

/**
 * Single source of truth for the isolated E2E environment.
 *
 * These values are injected as REAL process environment variables into the PHP
 * server (and every PHP helper). Real env vars take precedence over `.env`, so
 * the development/production database settings in `.env` are never used.
 */

export const ROOT = path.resolve(path.dirname(fileURLToPath(import.meta.url)), '../..');

const LOCAL_HOSTS = new Set(['localhost', '127.0.0.1', '[::1]', '::1']);
const DEFAULT_BASE_URL = 'http://127.0.0.1:8011';

/**
 * The only supported override is a different *local* port. Anything that is not
 * a localhost URL is rejected loudly - never silently accepted.
 */
function resolveBaseUrl(): URL {
  const raw = process.env.E2E_BASE_URL?.trim();
  if (!raw) return new URL(DEFAULT_BASE_URL);

  let url: URL;
  try {
    url = new URL(raw);
  } catch {
    throw new Error(`E2E SAFETY GUARD: E2E_BASE_URL is not a valid URL: "${raw}"`);
  }
  if (!LOCAL_HOSTS.has(url.hostname)) {
    throw new Error(
      `E2E SAFETY GUARD: refusing E2E_BASE_URL "${raw}". ` +
        `E2E tests may only target localhost (${[...LOCAL_HOSTS].join(', ')}). ` +
        `Deployed/production hosts are never allowed.`,
    );
  }
  if (!url.port) {
    throw new Error('E2E SAFETY GUARD: E2E_BASE_URL must include an explicit port (e.g. http://127.0.0.1:8011).');
  }
  return url;
}

const baseUrl = resolveBaseUrl();

export const BASE_URL = baseUrl.origin;
export const E2E_HOST = baseUrl.hostname === '[::1]' ? '::1' : baseUrl.hostname;
export const E2E_PORT = baseUrl.port;

/** Dedicated SQLite file. Never the dev (MySQL `linkhub`) or production database. */
export const E2E_DB = path.join(ROOT, 'database', 'e2e.sqlite');

export const E2E_ADMIN = {
  email: 'e2e-admin@example.test',
  // Exists only inside the throw-away E2E database, so it is not a secret.
  password: 'e2e-only-password-123',
};

export const AUTH_STATE = path.join(ROOT, 'e2e', '.auth', 'admin.json');

/** Environment handed to the PHP server and PHP helpers. Overrides `.env`. */
export const LARAVEL_ENV: Record<string, string> = {
  APP_ENV: 'e2e',
  APP_DEBUG: 'true',
  APP_URL: BASE_URL,
  // Database: isolated sqlite file; blank out every remote-DB setting.
  DB_CONNECTION: 'sqlite',
  DB_DATABASE: E2E_DB,
  DB_URL: '',
  DB_HOST: '',
  DB_PORT: '',
  DB_USERNAME: '',
  DB_PASSWORD: '',
  // No shared/remote infrastructure, no external side effects.
  SESSION_DRIVER: 'file',
  CACHE_STORE: 'file',
  QUEUE_CONNECTION: 'sync',
  MAIL_MAILER: 'array',
  BROADCAST_CONNECTION: 'null',
  FILESYSTEM_DISK: 'local',
  BCRYPT_ROUNDS: '4',
  E2E_EXPECTED_DB: E2E_DB,
};

