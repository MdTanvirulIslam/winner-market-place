# Winner Marketplace

Single-vendor digital-products store for Winner Devs applications (news portals, POS,
inventory, HRM, …). Customers browse, pay with SSLCommerz (bKash / Nagad / Rocket / cards)
or arrange manual payment, and receive their **license and downloads automatically** through
the companion **License Manager** application.

## How the pieces fit

```
Customer ──► Marketplace ──► SSLCommerz (hosted checkout + server-side validation)
                 │
                 └──► License Manager  POST /api/licenses  (Bearer token)
                          └──► emails the customer their license key + one-time credentials link
Customer ──► My Downloads (signed, expiring links; all versions, forever)
```

- The **product slug here must exactly match** the product slug in the License Manager —
  it is the join key used when provisioning licenses.
- Payment truth comes **only** from the SSLCommerz validation API. Redirects and IPN posts
  are hints; amount, currency, and transaction ID are re-checked server-side.
- If the License Manager is unreachable after a payment, the order stays `paid` with a
  provisioning-failed flag and an idempotent **Retry Provisioning** button — money is never
  in an ambiguous state, and retries never create duplicate licenses.

## Local setup

```bash
composer install
cp .env.example .env && php artisan key:generate
# set DB_* in .env (MySQL), then:
php artisan migrate --seed        # seeds the initial super admin
php artisan storage:link
npm install && npm run build
php artisan serve
```

Log in at `/admin` with the seeded super admin (see `database/seeders/DatabaseSeeder.php`)
and **change the password immediately**.

## Environment variables

| Variable | Purpose |
|---|---|
| `APP_URL` | Must be the real public URL in production — used for signed download links, payment callbacks, and emails. |
| `LICENSE_MANAGER_URL` | Base URL of the License Manager (no trailing slash). |
| `LICENSE_MANAGER_TOKEN` | Bearer token for `POST /api/licenses`; must equal `ADMIN_API_TOKEN` in the License Manager's `.env`. Generate: `php -r "echo bin2hex(random_bytes(32));"` |
| `SSLCZ_STORE_ID` / `SSLCZ_STORE_PASSWORD` | SSLCommerz merchant credentials. |
| `SSLCZ_SANDBOX` | `true` for the sandbox gateway, `false` for live. |
| `MAIL_*` | Real SMTP in production — order and contact emails are sent synchronously. |
| `MYSQLDUMP_PATH` | Full path to `mysqldump` if not on PATH (XAMPP: `D:/xampp/mysql/bin/mysqldump.exe`). |

Secrets live **only** in `.env` — never commit it. The admin Settings page shows
configured/not-configured status without revealing values.

## Backups

`php artisan backup:run` dumps the database and zips all release files into
`storage/app/private/backups`, keeping the newest 7 of each. It is scheduled daily at 03:30;
the scheduler needs a cron entry (see checklist). Download the backups off-server regularly —
a backup on the same disk is only half a backup.

## Deployment (GitHub Actions → cPanel)

`.github/workflows/deploy.yml` zips the repo on push to `main` and extracts it into the
cPanel directory. Because of that:

- `vendor/` and `public/build/` are **committed** so the server needs neither Composer nor Node.
  Run `npm run build` and commit before pushing UI changes.
- `.env` is **not** in the repo (and must never be). Create it once on the server with
  production values; deploys won't touch it.
- The domain's document root must point at the app's `public/` folder.

## Go-live checklist

1. **Domain + HTTPS** — point the document root at `public/`, install the SSL certificate,
   set `APP_URL=https://...` and `APP_ENV=production`, `APP_DEBUG=false`.
2. **Server `.env`** — copy `.env.example`, fill production values, `php artisan key:generate`.
   Set `SESSION_SECURE_COOKIE=true`.
3. **License Manager** — deploy it, set its `ADMIN_API_TOKEN` (same value as
   `LICENSE_MANAGER_TOKEN` here), real SMTP, and correct `APP_URL` there too.
   Cross-check every product slug matches.
4. **SSLCommerz** — sandbox first: `SSLCZ_SANDBOX=true` + sandbox credentials, run a full
   test purchase. Then live credentials, `SSLCZ_SANDBOX=false`, and make **one real small
   purchase and refund it** to verify the whole loop including the refund flow.
5. **Mail** — real SMTP on both apps; send a test order and confirm both emails arrive
   (order email from the marketplace, credentials email from the License Manager).
6. **Cron** — cPanel cron job: `* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1`
   (drives the nightly backups).
7. **Caches** — after each deploy: `php artisan config:cache && php artisan route:cache && php artisan view:cache && php artisan migrate --force`.
8. **Accounts** — change the seeded super admin password; create staff accounts as needed.
9. **Content** — Settings: store name, support email, payment instructions. Review the
   About/Terms/Privacy/Refund pages. Submit `/sitemap.xml` to Google Search Console.

## Tests

```bash
php artisan test
```

Covers role access, catalog CRUD + uploads, checkout, the full order/provisioning flow
(success, failure, idempotent retry), download authorization, payment validation
(forged redirects, amount tampering, IPN races), static pages, invoices, and backups.
