# LinkHub

A simple and modern personal online link sharing application built with Laravel 12, Blade, Tailwind CSS, Vite, and Alpine.js.

---

## Requirements

* **PHP**: 8.2 / 8.3+
* **Composer**: 2.x
* **Node.js**: 18+ & NPM
* **Database**: MySQL (recommended for production) or SQLite (development)

---

## Installation

1. **Clone the repository / open project directory**:
   ```bash
   cd fastlink
   ```

2. **Install PHP dependencies**:
   ```bash
   composer install
   ```

3. **Install Frontend dependencies**:
   ```bash
   npm install
   ```

---

## Environment Setup

1. **Copy `.env` file**:
   ```bash
   cp .env.example .env
   ```

2. **Generate Application Key**:
   ```bash
   php artisan key:generate
   ```

3. **Configure Database**:
   Update your `.env` file with your MySQL database details:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=linkhub
   DB_USERNAME=root
   DB_PASSWORD=
   ```

---

## Database Setup

Run database migrations and seed the initial admin account:

```bash
php artisan migrate --seed
```

Or for a fresh database reset:

```bash
php artisan migrate:fresh --seed
```

---

## Default Admin Account

Initial seeder creates an administrative account with hashed password:

* **Email**: `admin@admin.com`
* **Password**: `admin123`

---

## Run Development Server

Start the Laravel backend server:

```bash
php artisan serve
```

In a separate terminal tab, start the Vite development server for live asset compilation:

```bash
npm run dev
```

The application will be accessible at `http://localhost:8000`.

---

## Available Routes

| Method | URI | Action / Description | Middleware |
| :--- | :--- | :--- | :--- |
| `GET` | `/` | Landing Page | `web` |
| `GET` | `/login` | Login Form | `guest` |
| `POST` | `/login` | Authenticate User Credentials | `guest` |
| `POST` | `/logout` | Terminate Admin Session | `auth` |
| `GET` | `/admin` | Admin Dashboard | `auth` |

---

## Testing

Run the automated test suite with PHPUnit / Artisan:

```bash
php artisan test
```
# linkhub
