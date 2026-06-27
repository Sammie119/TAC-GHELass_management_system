# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Commands

```bash
# Install dependencies
composer install
npm install

# Start development
php artisan serve          # Laravel dev server (http://localhost:8000)
npm run dev                # Vite asset watcher

# Build assets for production
npm run build

# Database
php artisan migrate
php artisan migrate:fresh --seed   # Reset and seed (creates roles via RoleSeeder)
php artisan db:seed --class=RoleSeeder

# Code formatting
./vendor/bin/pint          # Laravel Pint (PHP formatter)

# Tests
php artisan test
php artisan test --filter=TestClassName
./vendor/bin/phpunit tests/Feature/SomeTest.php
```

## Architecture

### Application Type
Church attendance and finance management system built on **Laravel 10 / PHP 8.1**. Uses Blade templates with **inline CSS styles** (not Tailwind utility classes in views, despite Tailwind being installed — the admin views use hand-written `style=""` attributes throughout). Alpine.js is available but rarely used; most interactivity is vanilla JS inside `<script>` blocks at the bottom of Blade files.

### Portals / Route Groups
There are four distinct front-ends sharing one Laravel app:

| Prefix | Guard | Purpose |
|--------|-------|---------|
| `/admin/*` | `auth` middleware + Spatie role | Staff admin panel |
| `/portal/*` | Custom OTP session (`portal_member_id`) | Member self-service |
| `/checkin/{token}` | None (public) | QR-code kiosk check-in |
| `/give` | None (public) | Guest online giving (Paystack) |

### Roles (Spatie Permission)
Five roles: `admin`, `usher`, `membership`, `finance`, `member`. Routes inside `/admin` are guarded with `RoleMiddleware::using('admin|finance')` etc. The `member` role is used in the portal, not the admin panel.

### Key Models & Relationships
- **Member** — core entity; auto-generates `member_id_card` (`EL-00001`) and `qr_code` UUID on creation; soft-deletes.
- **IncomeRecord** — stores `amount` (original currency) + `exchange_rate` + `amount_ghs` (GHS equivalent). Soft-deletes. Linked to optional Member, Event, and User (`recorded_by`).
- **ExpenseRecord** — mirrors IncomeRecord structure; soft-deletes.
- **Event** — has a `qr_token` for kiosk check-in; status: `active` / `closed`.
- **Attendance / CheckinLog** — attendance is per-member-per-event; CheckinLog tracks individual scan actions.
- **Pledge / PledgePayment** — pledge commitments and their payment instalments.
- **OnlinePayment** — Paystack gateway records; confirmed via `FinanceController::confirmPayment` which creates an `IncomeRecord`.
- **NewSoul / SoulFollowup** — new convert tracking with follow-up notes.
- **CellGroup** — many-to-many with Member via `cell_group_members` pivot.
- **AbsenteeFlag** — flagged absentees with follow-up status.

### Finance Module
`config/finance.php` is the single source of truth for `income_categories`, `expense_categories`, `payment_methods`, and `currencies`. Income/expense categories and departments in `config/departments.php` are editable via the Settings UI (which overwrites the config files directly).

The `storeBulkIncome` method uses `firstOrCreate` with all fields as the match key (problematic — see known issue). `storeIncome` and `storeSundayTithes` use `updateOrCreate` with a narrower match key (member + category + date + currency).

### Services
- **MnotifyService** / **SMSOnlineGhService** — SMS providers (Ghana); configured via `services.mnotify` / `services.smsonline` in config.
- **NotificationService** — wraps SMS services; sends birthday/absentee SMS.
- **PaymentService** — Paystack transaction verification.
- **AttendanceService** — shared logic for check-in processing.

### Exports / Imports
All Excel work uses **Maatwebsite/Excel**. Export classes live in `app/Exports/`. The `FinanceExport` uses multiple sheets (`FinanceIncomeSheet`, `FinanceExpenseSheet`). PDF exports use **barryvdh/laravel-dompdf**; PDF views are under `resources/views/admin/*/pdf/`. Member bulk import reads an Excel template and creates Members; income bulk import reads column positions directly (no header mapping).

### Third-Party Env Variables
```
PAYSTACK_SECRET_KEY / PAYSTACK_PUBLIC_KEY / PAYSTACK_PAYMENT_URL / PAYSTACK_CALL_BACK_URL
MNOTIFY_API_KEY / MNOTIFY_SENDER_ID / MNOTIFY_BASE_URL
SMS_API_KEY / SMS_SENDER_ID / SMS_HOST
```
