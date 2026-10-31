# Multi-Tenant URL Shortener (Laravel 11)

[![Laravel](https://img.shields.io/badge/Laravel-11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)
[![Tests](https://img.shields.io/badge/Tests-34%20Passed-success?style=for-the-badge)](https://phpunit.de)
[![License](https://img.shields.io/badge/License-MIT-blue?style=for-the-badge)](LICENSE)

A production-grade, multi-tenant URL Shortener built with **Laravel 11**, **MySQL**, and **Bootstrap 5**. Designed with a single-database multi-tenancy model featuring strict company-level data isolation, role-based access control (RBAC), secure hashed invitation workflows, and public link resolution.

---

## 🚀 Key Features & Permissions Matrix

| Capability | SuperAdmin (Platform) | Admin (Company) | Member (Company) |
|---|:---:|:---:|:---:|
| **Tenant Scope** | Global (Platform-wide) | Own Company | Own Company |
| **Login / Logout** | ✅ | ✅ | ✅ |
| **Platform Console** | ✅ | ❌ | ❌ |
| **Inspect Tenant Companies** | ✅ (Full drilldown) | ❌ | ❌ |
| **Onboard New Tenant (Admin + Co)** | ✅ | ❌ | ❌ |
| **Invite Users to Own Company** | ❌ | ✅ (Admin / Member) | ❌ |
| **Invite Users to Other Company** | ❌ | 🚫 **Denied** | ❌ |
| **Create Short URLs** | 🚫 **Denied** | ✅ | ✅ |
| **Short URL Visibility** | **All companies** | **Own company only** | **Self-created only** |
| **Public URL Redirection (`/s/{code}`)** | ✅ (Public) | ✅ (Public) | ✅ (Public) |
| **Profile & Password Settings** | ✅ | ✅ | ✅ |

---

## 🛠️ Technology Stack & Requirements

- **PHP:** `^8.2` (Verified on PHP 8.2)
- **Framework:** `Laravel 11.x`
- **Database:** MySQL 8.0+ / MariaDB (or SQLite for automated test runners)
- **Frontend:** Laravel Blade, Bootstrap 5.3, Bootstrap Icons, Plus Jakarta Sans & JetBrains Mono typography
- **Testing:** PHPUnit (34 Feature & Unit Tests, 110 Assertions)

---

## 📦 Installation & Setup

### 1. Clone Repository
```bash
git clone https://github.com/Humty-Sharma/lms.git
cd lms
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Environment Configuration
Copy `.env.example` to `.env`:
```bash
cp .env.example .env
php artisan key:generate
```

Configure your database connection in `.env`:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=2026_short_url
DB_USERNAME=root
DB_PASSWORD=

# Initial SuperAdmin Credentials
SUPERADMIN_NAME="Super Administrator"
SUPERADMIN_EMAIL="superadmin@example.com"
SUPERADMIN_PASSWORD="Password123!"
```

### 4. Run Migrations & Seeder
Execute fresh migrations and seed the initial SuperAdmin account:
```bash
php artisan migrate:fresh --seed
```

---

## ⚡ Initial SuperAdmin Account

The SuperAdmin account is initialized via a secure, parameterized raw SQL statement in `SuperAdminSeeder`:
- **Login URL:** `http://localhost:8000/login`
- **Email:** `superadmin@example.com`
- **Password:** `Password123!`

> **Tip:** The login page includes a **1-click Autofill** button for instant evaluation without manual typing.

---

## 🖥️ Running the Application

Start the local development server:
```bash
php artisan serve
```
Visit **[http://localhost:8000](http://localhost:8000)** in your browser.

---

## 🧪 Running Automated Tests

Run the complete test suite:
```bash
php artisan test
```

### Verified Test Results:
```
PASS  Tests\Unit\ExampleTest
PASS  Tests\Feature\AuthenticationTest (5 tests)
PASS  Tests\Feature\ExampleTest (1 test)
PASS  Tests\Feature\InvitationTest (8 tests)
PASS  Tests\Feature\ProfileTest (5 tests)
PASS  Tests\Feature\PublicRedirectTest (4 tests)
PASS  Tests\Feature\ShortUrlCreationTest (6 tests)
PASS  Tests\Feature\ShortUrlVisibilityTest (4 tests)

Tests:    34 passed (110 assertions)
Duration: 1.90s
```

---

## 🏢 Workflows

### 1. SuperAdmin Onboarding Workflow
1. Log in as `superadmin@example.com`.
2. Navigate to **Invitations** (`/invitations`).
3. Enter the **New Company Name** (e.g. `Acme Corp`) and the **Admin Email** (`admin@acme.com`).
4. An invitation token is generated with a 1-click copy link.
5. In the **Companies** directory (`/companies`), click **View Details** on any company to inspect its team members, active short links, and invitation history.

### 2. Tenant Invitation Acceptance
1. Open the invitation acceptance link: `/invitations/accept/{token}`.
2. Enter your full name and password (minimum 8 characters).
3. The system validates the SHA-256 token hash, activates the account, marks the invitation as accepted, and logs the user into their scoped dashboard.

### 3. URL Shortening & Public Redirect
1. Admins and Members can enter destination URLs (must start with `http://` or `https://`).
2. A unique 6-character short code is generated with retry protection against database collisions.
3. Accessing `http://localhost:8000/s/{code}` returns a **302 Redirect** directly to the original destination without requiring login.

---

## 🏛️ Architectural & Security Decisions

1. **Strict Multi-Tenant Scoping:**
   - Multi-tenant isolation is enforced inside the Eloquent query scope `ShortUrl::scopeVisibleTo(Builder $query, User $user)`.
   - Records are filtered at the database level before loading into memory.
2. **Server-Enforced Ownership:**
   - URL creation strictly uses `$request->user()->company_id` and `$request->user()->id`. Request parameter spoofing of company or user IDs is completely ignored.
3. **Cryptographic Token Storage:**
   - Only a SHA-256 hash (`token_hash`) of invitation tokens is stored in the database. Tokens expire after 7 days and cannot be reused once accepted or revoked.
4. **Foreign Key Integrity:**
   - Relationships on `short_urls` and `invitations` use `restrictOnDelete()` to prevent accidental cascaded deletions from corrupting audit trails.
5. **Session Fixation & CSRF Protection:**
   - Session IDs are regenerated upon authentication (`$request->session()->regenerate()`), and CSRF tokens are refreshed on logout.
