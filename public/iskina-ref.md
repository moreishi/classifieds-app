# IskinaPH — Development Reference

> **Stack:** Laravel 13.7, PHP 8.4, Livewire 4, Filament 5, SQLite (dev) / MySQL (prod)  
> **Repo:** `github.com/moreishi/classifieds-app`  
> **Branches:** `master` (stable), `develop` (active)  
> **Local:** `localhost:8200`

---

## Table of Contents

1. [Architecture](#1-architecture)
2. [Models & Relationships](#2-models--relationships)
3. [Routes](#3-routes)
4. [Livewire Components](#4-livewire-components)
5. [Filament Admin Panel](#5-filament-admin-panel)
6. [Authentication & Authorization](#6-authentication--authorization)
7. [Payment System](#7-payment-system)
8. [Notifications](#8-notifications)
9. [Bump / Promotions System](#9-bump--promotions-system)
10. [Credit System](#10-credit-system)
11. [Messages & Conversations](#11-messages--conversations)
12. [Media & Uploads](#12-media--uploads)
13. [Development Commands](#13-development-commands)
14. [Testing](#14-testing)
15. [Deployment](#15-deployment)
16. [Systemd Services](#16-systemd-services)

---

## 1. Architecture

### Stack Decisions

| Layer | Choice | Why |
|-------|--------|-----|
| Framework | Laravel 13.7 | Latest, well-supported |
| UI Layer | Livewire 4 | Server-rendered, no JS framework needed |
| Admin | Filament 5 | Full CRUD + stats dashboard |
| CSS | Tailwind 4 (no Flux) | Plain utility classes, no component library dependency |
| DB (dev) | SQLite | Zero-config local |
| DB (prod) | MySQL/MariaDB | Production ready |
| Queue | Database | Built-in, no Redis requirement |
| Media | Spatie Medialibrary 11 | Flexible multi-image per listing |
| Roles | Spatie Permission 7 | Admin/user roles |
| Auth | Laravel Breeze | Minimal auth scaffolding |
| Payments | PayMongo | GCash via PayMongo API |

### Key Constraints

- **No Flux UI installed** — all markup is plain HTML/Tailwind + Livewire
- **No in-app escrow** — transactions happen off-platform
- **GCash for verification only** (not direct processing; uses PayMongo API)
- **Buy Credits** also uses PayMongo for GCash payments
- **1:1 centavo-to-credit ratio** — ₱1 = 100 credits
- **User pays verification fee** (₱5, covers PayMongo fees)

---

## 2. Models & Relationships

### User (`app/Models/User.php`)
- Implements `MustVerifyEmail` (since Sprint 4)
- Implements `HasRoles` (Spatie)
- **Fields:** first_name, middle_name, last_name, username, email, gcash_number, gcash_verified_at, credit_balance, reputation_tier, last_active_at, notify_new_inquiry, notify_seller_reply
- **Accessors:** `publicName()` (username fallback), `fullName()`, `avatar`, `initials`
- **Relations:** city, listings, offersMade, receivedOffers, conversationsAsBuyer/Seller, favoriteListings, promotions, archivedConversations

### Listing (`app/Models/Listing.php`)
- Implements `HasMedia` (Spatie MediaLibrary)
- Soft deletes
- **Route key:** slug
- **Fields:** title, description, price (cents), status (active|sold), condition, slug, reference_id, featured_until, city_id, category_id
- **Scopes:** search, active, inCity, inCategory, priceBetween, withCondition, favoritedBy
- **Media collections:** `photos` (multiple images)

### Conversation
- **Fields:** listing_id, buyer_id, seller_id, last_message_at, buyer_archived_at, seller_archived_at
- **Methods:** `otherUser()`, scope `notArchivedBy()`

### Message
- **Fields:** conversation_id, sender_id, body, read_at
- **Methods:** `markAsRead()`, scope `unread()`

### ListingPromotion
- **Plans:** 7d (₱50/50 credits), 14d (₱80/80 credits), 30d (₱140/140 credits)
- **Bump extends listing expiry:** `expires_at` moves forward by plan duration

### Other Models
- **Category** — tree via parent_id, has pricingOverrides
- **City** — nested via parent_id, belongs to Region
- **Region** — top-level geographic grouping
- **Offer** — listing_id, buyer_id, seller_id, amount, status, counter_id
- **TransactionReceipt** — generated PDF receipts for completed transactions
- **Review** — listing reviews (rating 1-5, comment)
- **Report** — listing reports (open → handled)
- **CreditTransaction** — morphTo reference (listing, offer, promotion)
- **ListingViewLog** — per-user view tracking
- **CategoryPricingOverride** — city-specific pricing overrides

---

## 3. Routes

### Public Routes
| URI | Name | Component/Controller |
|-----|------|---------------------|
| `/` | home | `Homepage` |
| `/category/{slug}` | category.show | `SearchListings` |
| `/search` | search | `SearchResults` |
| `/listing/{slug}` | listing.show | `ListingDetail` |
| `/robots.txt` | robots | `SitemapController@robots` |
| `/sitemap.xml` | sitemap.index | `SitemapController@index` |
| `/up` | — | health check (200 JSON) |

### Auth + Verified Routes
| URI | Name | Component |
|-----|------|-----------|
| `/conversations` | conversations.index | `ConversationsList` |
| `/conversation/{conversation}` | conversations.show | `ConversationView` |
| `/listings/create` | listings.create | `CreateListing` |
| `/listing/{slug}/edit` | listings.edit | `EditListing` |
| `/my-listings` | listings.my | `MyListings` |
| `/offers` | offers.index | `OffersInbox` |
| `/transactions` | transactions.index | `Transactions` |
| `/favorites` | favorites.index | `FavoriteListings` |
| `/seller/dashboard` | seller.dashboard | `SellerDashboard` |
| `/notifications` | notifications.index | `Notifications` |
| `/listings/trashed` | listings.trashed | `TrashedListings` |
| `/receipt/{receipt}/download` | receipt.download | `ReceiptController@download` |
| `/dashboard` | dashboard | Blade view |

### Auth-only (no verified required)
| URI | Name | Component |
|-----|------|-----------|
| `/buy-credits` | buy-credits | `BuyCredits` |
| `/verify-account` | verify-account | `VerifyAccount` |
| `/settings` | settings | `UserSettings` |

### Webhooks
| URI | Middleware | Controller |
|-----|-----------|------------|
| `POST /webhooks/paymongo` | No auth, no CSRF | `PayMongoWebhookController` |

### Auth Routes (Breeze scaffold)
Standard Breeze routes: login, register, logout, password reset, email verification, password confirmation.

---

## 4. Livewire Components

| Component | Path | Purpose |
|-----------|------|---------|
| `Homepage` | `app/Livewire/Homepage.php` | Hero search + category grid + promoted/latest listings |
| `SearchListings` | `app/Livewire/SearchListings.php` | Category-based listing grid with filters |
| `SearchResults` | `app/Livewire/SearchResults.php` | Free-text search with filters |
| `ListingDetail` | `app/Livewire/ListingDetail.php` | Full listing view, inquiry modal, mark as sold |
| `CreateListing` | `app/Livewire/CreateListing.php` | Multi-step listing form |
| `EditListing` | `app/Livewire/EditListing.php` | Edit existing listing |
| `MyListings` | `app/Livewire/MyListings.php` | User's listing management |
| `TrashedListings` | `app/Livewire/TrashedListings.php` | Soft-deleted listings (restore) |
| `ConversationsList` | `app/Livewire/ConversationsList.php` | Message inbox with archive toggle |
| `ConversationView` | `app/Livewire/ConversationView.php` | Chat view with read receipts, typing indicator |
| `UserSettings` | `app/Livewire/UserSettings.php` | Profile, GCash, credits, notifications, delete account |
| `SellerDashboard` | `app/Livewire/SellerDashboard.php` | Stats cards + listing performance + recent inquiries |
| `BuyCredits` | `app/Livewire/BuyCredits.php` | Credit pack selection + PayMongo payment |
| `VerifyAccount` | `app/Livewire/VerifyAccount.php` | GCash number entry + ₱5 verification flow |
| `BumpListing` | `app/Livewire/BumpListing.php` | Listing promotion (bump) UI |
| `OffersInbox` | `app/Livewire/OffersInbox.php` | Buy/sell offers with counter-offer system |
| `Transactions` | `app/Livewire/Transactions.php` | Transaction receipts + review system |
| `FavoriteListings` | `app/Livewire/FavoriteListings.php` | Saved/favorited listings |
| `ToggleFavorite` | `app/Livewire/ToggleFavorite.php` | Heart toggle button |
| `Notifications` | `app/Livewire/Notifications.php` | Bell dropdown + full notification page |
| `OfferModal` | `app/Livewire/OfferModal.php` | Offer submission modal |
| `LeaveReview` | `app/Livewire/LeaveReview.php` | Review form |

---

## 5. Filament Admin Panel

**Path:** `/admin`  
**Admin user:** `admin@iskina.ph` / `password` (created by RoleSeeder)

### Resources
| Resource | Description |
|----------|-------------|
| Users | Full CRUD, roles, notifications |
| Listings | All listings, status management, featured toggle |
| Categories | Nested category tree |
| Cities | Region-based city hierarchy |
| Regions | Geographic regions |
| Offers | All buy/sell offers |
| Conversations | All messages between users |
| Reviews | Listing reviews |
| Reports | Listing reports (open/handle flow) |
| TransactionReceipts | Completed transaction receipts |
| CreditTransactions | All credit movements |
| ListingPromotions | Promoted listing records |
| ListingViewLogs | View tracking |
| CategoryPricingOverrides | City-specific pricing rules |

### Widgets
- `AdminStats.php` — Dashboard stats (user count, listing count, etc.)

### Navigation Groups
- User Management
- Content
- Marketplace
- Locations

---

## 6. Authentication & Authorization

### Email Verification
- User model implements `Illuminate\Contracts\Auth\MustVerifyEmail`
- Auto-enabled: new users must verify before accessing most pages
- `verified` middleware registered as alias in `bootstrap/app.php`
- Exceptions: settings, verify-account, buy-credits (accessible before verification)

### Roles (Spatie Permission)
- `admin` — full Filament access
- `user` — registered user, can create listings and interact

### Registration
- Fields: username, first_name, middle_name (optional), last_name, email, password
- `name` column auto-set to `trim(first_name . ' ' . last_name)`
- Username must be unique and `alpha_dash` only

---

## 7. Payment System

### Architecture
```bash
app/Services/Payment/
├── PaymentGateway.php       # Interface (charge(), confirmPayment())
├── PayMongoGateway.php      # Production + dev simulation
├── GCashGateway.php         # Throws PaymentException (not supported directly)
└── ChargeResult.php         # DTO
```

- `AppServiceProvider.php` binds `PaymentGateway` interface to `PayMongoGateway`
- The GCash gateway exists as fallback — swap binding to use it

### PayMongo Integration
- **Verification:** Creates Payment Intent via API → User redirected to GCash → Webhook confirms
- **Buy Credits:** Creates Payment Intent with `type: buy_credits` metadata
- **Webhook endpoint:** `POST /webhooks/paymongo` (no CSRF, no auth)
- **Webhook handler:** `PayMongoWebhookController` routes by metadata type
  - `type: verification` → `verification.paid` event → marks `gcash_verified_at`
  - `type: buy_credits` → reads `purchase:{user_id}` cache → deposits credits + creates `top_up` record

### Credit System
- **1:1 ratio:** ₱1 = 100 cents = 100 credits
- **Credit packs:** ₱50 (50cr), ₱100 (100cr), ₱200 (200cr + 20 bonus), ₱500 (500cr + 100 bonus)
- **Transaction types:** `listing_fee`, `referral_bonus`, `top_up`, `listing_bump`
- **Cache:** Pending purchases stored as `purchase:{user_id}` with 2-hour expiry

### Testing
```bash
PAYMONGO_PUBLIC_KEY=pk_test_xxx  # Test keys
PAYMONGO_SECRET_KEY=sk_test_xxx
PAYMONGO_WALLET_ID=wal_xxx
```

---

## 8. Notifications

### Types
| Notification | Trigger | Channel |
|-------------|---------|---------|
| `NewInquiry` | First message to seller | Mail (if allow) |
| `InquiryFollowUp` | Unanswered inquiry after 24h | Mail |
| `SellerReplied` | Seller responds | Mail (if allow) |

### Notification Preferences
- `notify_new_inquiry` — controls `NewInquiry` mail
- `notify_seller_reply` — controls `SellerReplied` mail
- Preferences are checked in `via()` methods

### Queue
- All notifications implement `ShouldQueue`
- Queue driver: `database`
- Systemd service: `iskina-queue.service`

---

## 9. Bump / Promotions System

### Plans
| Duration | Cost (credits) | ₱ Equivalent |
|----------|---------------|--------------|
| 7 days | 50 | ₱50 |
| 14 days | 80 | ₱80 |
| 30 days | 140 | ₱140 |

### Behavior
- Bump marks listing as `featured` (yellow badge on homepage)
- `featured_until` timestamp set on the Listing
- **Auto-extends listing expiry:** `expires_at` moves forward by plan duration
- `ExpirePromotions` command runs every minute to expire `featured_until`

### Model
- `ListingPromotion` — stores listing_id, user_id, plan, amount, expires_at
- `Listing` has `featured_until` and `activePromotion` relationship

---

## 10. Credit System

### Data Model
- `user.credit_balance` — integer, stored in cents
- `CreditTransaction` table — polymorphic reference, records all movements
- Transaction types: `listing_fee`, `referral_bonus`, `top_up`, `listing_bump`

### Buy Credits Flow
1. User selects pack on `/buy-credits`
2. Enters/saves GCash number
3. System creates PayMongo Payment Intent
4. User redirected to GCash checkout
5. Webhook (`payment.paid`) → deposits credits + creates `top_up` record

---

## 11. Messages & Conversations

### Flow
1. Buyer clicks "Message Seller" on listing detail
2. System finds existing conversation or creates one
3. `NewInquiry` notification sent to seller
4. Buyer/seller chat via `ConversationView` component
5. Messages poll every 5s automatically (`wire:poll.5s="refreshMessages"`)

### Features
- Read receipts (double-check icons in sent messages)
- Online status indicator (<5min since last_active_at)
- 24h unanswered inquiry follow-up reminder
- Archive/restore per-user (`buyer_archived_at`, `seller_archived_at`)
- Sender name hidden from opposite party (shown as partner)

---

## 12. Media & Uploads

- Powered by **Spatie MediaLibrary 11**
- `Listing` model: `InteractsWithMedia`
- Collection: `photos` (multiple images)
- Conversions: `thumb` (generated on upload)

### Migration Note
```php
// media migration has a guard against re-running
if (Schema::hasTable('media')) {
    return;
}
```
This prevents crashes when re-running migrations via `php artisan migrate --force` in CI/CD.

---

## 13. Development Commands

```bash
# Fresh database
php artisan migrate:fresh --seed

# Serve locally
php artisan serve --port=8200

# Queue worker
php artisan queue:work

# Scheduler (run manually)
php artisan schedule:run

# Tests (unit + feature)
php artisan test

# Browser tests (Dusk)
php artisan dusk
```

### Scheduled Tasks
| Command | Schedule | Purpose |
|---------|----------|---------|
| `inquiries:remind-unanswered` | Hourly | Nudge sellers with unanswered inquiries > 24h |
| `listings:expire` | Every minute | Clear `expires_at` and set status to sold |
| `promotions:expire` | Every minute | Clear `featured_until` on expired bumps |

### System Routes
- `/up` — health check (returns `{"status":"ok"}`)
- `/robots.txt` — sitemap directive
- `/sitemap.xml` — SEO sitemap (static, categories, listings pagination)

---

## 14. Testing

### Test Suites
| Suite | Count | Command |
|-------|-------|---------|
| Unit + Feature | 146 tests (344 assertions) | `php artisan test` |
| Dusk Browser | 12 tests (25 assertions) | `php artisan dusk` |
| **Total** | **158 tests (369 assertions)** | |

### Test Files
```
tests/
├── Unit/
│   ├── PayMongoGatewayTest.php       (10 tests)
│   ├── VerificationServiceTest.php   (8 tests)
│   ├── BumpListingTest.php           (7 tests)
│   ├── BuyCreditsTest.php            (9 tests)
│   ├── SellerDashboardTest.php       (5 tests)
│   └── UserSettingsTest.php          (varies)
├── Feature/
│   ├── PayMongoWebhookTest.php       (12 tests)
│   └── Auth/RegistrationTest.php     (4 tests)
└── Browser/
    ├── ListingBumpsTest.php          (5 tests)
    ├── RegistrationTest.php          (4 tests)
    ├── SettingsTest.php              (2 tests)
    └── ConversationsTest.php         (1 test)
```

### Dusk Notes
- ChromeDriver version must match Chromium (currently v147)
- Path: `vendor/laravel/dusk/bin/chromedriver-linux` (manually replaced)
- App server expected at `http://localhost:8200`
- Test user: `test@iskina.ph` / `password` (created by `DatabaseMigrations` trait)

---

## 15. Deployment

See `DEPLOY.md` for full production checklist.

### Key Steps
1. Domain + SSL + Nginx/Caddy
2. `composer install --no-dev --optimize-autoloader`
3. `npm ci --production && npm run build`
4. `php artisan migrate --force`
5. `php artisan db:seed --class=RoleSeeder --force`
6. Set `PAYMONGO_PUBLIC_KEY`, `PAYMONGO_SECRET_KEY`, `PAYMONGO_WALLET_ID` (live keys)
7. Set `MAIL_*` to SMTP
8. Configure queue via Supervisor or systemd
9. Add cron: `* * * * * cd /path && php artisan schedule:run >> /dev/null 2>&1`
10. Register webhook: `https://domain.ph/webhooks/paymongo` → event: `payment.paid`

---

## 16. Systemd Services

### Queue Worker
```ini
[Unit]
Description=Iskina Queue Worker
After=network.target

[Service]
Type=simple
WorkingDirectory=/home/kc/projects/classifieds-app
ExecStart=/usr/bin/php artisan queue:work --sleep=3 --tries=3 --max-time=3600
Restart=always
RestartSec=3
Environment=USER=kc

[Install]
WantedBy=default.target
```

### Scheduler
```ini
[Unit]
Description=Iskina Scheduler

[Service]
Type=simple
WorkingDirectory=/home/kc/projects/classifieds-app
ExecStart=/usr/bin/php artisan schedule:work
Restart=always
RestartSec=3
Environment=USER=kc

[Install]
WantedBy=default.target
```

### Commands
```bash
systemctl --user start iskina-queue
systemctl --user start iskina-scheduler
systemctl --user enable iskina-queue
systemctl --user enable iskina-scheduler
journalctl --user -u iskina-queue -f
```

---

## Configuration Summary

```env
APP_NAME=Iskina.ph
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8200

DB_CONNECTION=sqlite

SESSION_DRIVER=database
QUEUE_CONNECTION=database
FILESYSTEM_DISK=public
MAIL_MAILER=log

PAYMONGO_PUBLIC_KEY=pk_test_xxx
PAYMONGO_SECRET_KEY=sk_test_xxx
PAYMONGO_WALLET_ID=wal_xxx
```

---

*Generated: 2026-05-03 | Last updated: Sprint 4 — Email Verification + Mobile Responsiveness*
