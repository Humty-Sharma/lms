# Multi-Tenant URL Shortener (Laravel)

A secure, production-quality, multi-tenant URL Shortener built with Laravel 11 and MySQL, implementing strict company data isolation, role-based access control, cryptographic invitation flows, and public link redirection.

---

## 1. Project Overview

This application serves multiple tenant organizations from a single application instance and a single shared database. It is engineered with strict least-privilege principles, backend policy-enforced permissions, query-level multi-tenant scopes, and cryptographically hashed invitations.

### Key Roles and Permissions Matrix

| Capability | SuperAdmin (Platform) | Admin (Company) | Member (Company) |
|---|:---:|:---:|:---:|
| **Company Association** | None (Platform-wide) | Exactly 1 Company | Exactly 1 Company |
| **Login / Logout** | Yes | Yes | Yes |
| **Platform Dashboard** | Yes | No | No |
| **View Companies** | Yes (All) | No | No |
| **Invite New Tenant (Admin + Co)** | Yes | No | No |
| **Invite Users to Own Company** | No | Yes (Admin or Member) | No |
| **Invite Users to Other Company**| No | **DENIED** (Server-enforced) | No |
| **Create Short URLs** | **DENIED** | Yes | Yes |
| **Short URL Visibility Scope** | **All** companies & users | **Own company only** | **Own created URLs only** |
| **Public URL Resolution (`/s/{code}`)** | Yes (No auth needed) | Yes (No auth needed) | Yes (No auth needed) |

---

## 2. Requirements & Environment

- **PHP:** `^8.2` (Verified on PHP 8.2.0)
- **Framework:** `Laravel 11.x`
- **Database:** MySQL 8.x / MariaDB (Database name: `2026_short_url`) or SQLite for test runners
- **Composer:** `^2.x`

---

## 3. Installation & Setup

### Step 1: Clone or Open Directory
```bash
cd d:/wampp64/www/interviews/shorturl
```

### Step 2: Install Composer Dependencies
```bash
composer install
```

### Step 3: Configure Environment Variables
Copy `.env.example` to `.env` if not already present:
```bash
cp .env.example .env
php artisan key:generate
```

Configure your `.env` database settings:
```ini
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=2026_short_url
DB_USERNAME=root
DB_PASSWORD=

# Initial SuperAdmin Credentials for Seeder
SUPERADMIN_NAME="Super Administrator"
SUPERADMIN_EMAIL="superadmin@example.com"
SUPERADMIN_PASSWORD="Password123!"
```

### Step 4: Run Database Migrations and SuperAdmin Seeder
Execute fresh migrations and run the raw SQL SuperAdmin seeder:
```bash
php artisan migrate:fresh --seed
```

---

## 4. SuperAdmin Database Seeder Details

As explicitly required by the technical assessment:
- **Raw SQL execution:** Uses `DB::statement('INSERT INTO users (...) VALUES (?, ?, ...)')` with full query parameterization.
- **Secure Hashing:** The password hash is generated via `Hash::make()` prior to query execution.
- **Idempotency:** Safe to run repeatedly; detects existing accounts by email and updates timestamps without unexpectedly wiping an existing password.
- **Configurable Credentials:** Controlled via `SUPERADMIN_EMAIL` and `SUPERADMIN_PASSWORD` in `.env`.

Default initial credentials seeded:
- **Email:** `superadmin@example.com`
- **Password:** `Password123!`

---

## 5. Running the Application

Start the Laravel local development server:
```bash
php artisan serve
```
Access the application in your browser at:
[http://localhost:8000](http://localhost:8000)

---

## 6. Running Automated Tests

Run the full PHPUnit automated feature and unit test suite:
```bash
php artisan test
```

### Verified Test Matrix (29 Tests, 99 Assertions):
- **Authentication:** Guest dashboard protections, login credential validation, session fixation protection, and CSRF-protected logout.
- **URL Creation:** Admin and Member creation, SuperAdmin restriction (HTTP 403), URL validation (HTTP/HTTPS scheme check), prevention of company ID or creator ID spoofing, and unique code generation.
- **URL Visibility & Tenancy Isolation:** SuperAdmin cross-tenant visibility, Admin company-scoped visibility, Member author-only visibility, and verified that pagination never leaks cross-tenant records.
- **Company Invitations:** SuperAdmin new company & admin creation, Admin in-tenant invitations, prevention of Admin cross-company invitations, rejection of Member invite attempts, prevention of privilege escalation (SuperAdmin invites blocked for tenants), and rejection of expired/reused invitations.
- **Public Redirects:** Resolves valid codes to original destination without login, returns 404 for unknown codes, prevents parameter-based destination overriding, and ensures no company metadata or internal IDs leak in redirect headers.

---

## 7. Invitation Workflow & Local Testing

1. **SuperAdmin Flow:**
   - Log in as `superadmin@example.com`.
   - Go to **Invitations** (`/invitations`).
   - Enter a **New Company Name** (e.g. `Acme Corp`) and the **Admin Email** (`admin@acme.com`).
   - Upon submission, the company is created and an invitation token is generated.
   - The UI immediately presents the **invitation acceptance link** (with a 1-click copy button).
2. **Acceptance Flow:**
   - Open the acceptance URL (`/invitations/accept/{token}`).
   - The invitee enters their name and chooses a password.
   - The token is verified against the SHA-256 hash stored in the database.
   - Once submitted, the user account is created, the invitation is marked as accepted (`accepted_at`), and the user is logged into their company dashboard.
3. **Admin Flow:**
   - The newly accepted Admin can now log in and navigate to `/invitations` to invite other **Admins** or **Members** strictly into their own company.

---

## 8. Architectural & Authorization Decisions

1. **Shared Database, Multi-Tenant Isolation:**
   - Multi-tenancy is modeled with a single database schema. Every tenant-specific table (`users`, `short_urls`, `invitations`) includes an indexed foreign key `company_id`.
   - Foreign key constraints use `restrictOnDelete` so that parent records cannot be dropped while leaving orphaned URLs or audit trails.
2. **Server-Enforced Scoping:**
   - Visibility is enforced inside Eloquent query scopes via `ShortUrl::scopeVisibleTo(Builder $query, User $user)`.
   - Creation requests (`ShortUrlController::store`) derive `company_id` and `user_id` strictly from `$request->user()`. User-supplied input for IDs is never accepted.
3. **Cryptographic Token Storage:**
   - Invitation tokens are generated using random alphanumeric strings. Only a SHA-256 digest (`token_hash`) is persisted. Plain text tokens are never stored in the database.
4. **Clean, Idiomatic Laravel:**
   - Built with dedicated Form Requests (`StoreShortUrlRequest`, `StoreInvitationRequest`, `AcceptInvitationRequest`), Laravel Policies (`ShortUrlPolicy`, `InvitationPolicy`), and Role Middleware (`EnsureUserRole`).
   - Zero unnecessary repositories or artificial abstraction layers.
