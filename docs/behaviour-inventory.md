# Behaviour inventory (M0)

Every behaviour the v2 MVP must reproduce, the test that proves it, and what happens to it in v2.

**Baseline:** the old app on `redesign/v2` @ M0 passes **87 / 87 tests**, none skipped, on PHP 8.3 + imagick (MySQL 8.4 strict was verified for the 62 tests that existed at `0484b97`).

**How the tests carry over:** in M1 the suite is copied into the new skeleton and marked skipped. Each MVP phase un-skips the tests for what it builds. **M9 cutover requires zero skipped MVP tests.** Tests are adapted to v2's names and schema (for example `qrx_codes` → `qr_codes`); the behaviour they assert is kept unless the last column says otherwise.

**Last column:**
- **Keep:** same behaviour in v2.
- **Change (approved):** you've already approved this change.
- **Change (proposed):** I'm proposing it; please confirm at the M0 review.

## Auth (M4)

| Behaviour | Tests | v2 |
|---|---|---|
| Register with name, email, phone, country, city, area, password | `Auth/RegistrationTest` (2) | Keep (Fortify) |
| Log in; wrong password rejected; log out | `Auth/AuthenticationTest` (3) | Keep |
| Forgot password → emailed link → reset | `Auth/PasswordResetTest` (4) | Keep |
| Verify email via signed link; bad hash rejected | `Auth/EmailVerificationTest` (3) | Keep. **Change (proposed):** today verification is *not required* to use the dashboard. Requiring it is a one-line switch in Fortify; recommended for a paid product |
| Confirm password for sensitive areas | `Auth/PasswordConfirmationTest` (3) | Keep |
| Update name/email (email change clears verification); change password; delete account with password | `ProfileTest` (5), `Auth/PasswordUpdateTest` (2) | Keep |
| Social login (Google, Facebook) | `SocialLoginTest` (3) | Later phase L1 |

## QR codes (M2, M5)

| Behaviour | Tests | v2 |
|---|---|---|
| Create each of the 7 types and store its content | `QrxCodeCreationTest` (text, email), `QrxCodeTypesAndDownloadTest` (url, phone, SMS, vCard, Wi-Fi) | Keep; stored as `qr_codes.payload` (approved schema) |
| An Email QR creates only its own content (no stray SMS row) | `QrxCodeCreationTest` | Keep (structurally guaranteed by the single table) |
| URL QRs accept only http/https | `QrxCodeInputSafetyTest` (7) | Keep |
| No subscription → sent to plans page; at plan limit → sent to upgrade page | `QrxCodeCreationTest` (2) | Keep; quota read from the Cashier subscription |
| Owner can open, edit and save their QR | `QrxCodeAuthorizationTest` (2) | Keep |
| Users cannot open, edit, save, pause, delete or download others' QRs; guests go to login; unknown ids → 404 | `QrxCodeAuthorizationTest` (7) | Keep |
| Lists show only the user's own QRs; status filters (active / paused / expired) | `QrxCodeListTest` (4) | Keep, as one page with a filter instead of 4 pages |
| Pause / resume | `QrxCodeAuthorizationTest` | Keep |
| Delete removes the QR and its stored image | `QrxCodeAuthorizationTest` | Keep |
| Download SVG / PNG / EPS with the right content type and a safe file name; size clamped to 2000 px; other formats rejected | `QrxCodeAuthorizationTest`, `QrxCodeTypesAndDownloadTest` (2), `QrxCodeInputSafetyTest` (2) | Keep. Round-style EPS gets *better* (see the spike) |
| Builder preview size clamped | `QrxCodeInputSafetyTest` | Keep. **Change (proposed):** the preview renders at error-correction level H (today: L) so it matches the downloaded file |

## Public scan page `/x/{code}` (M6)

| Behaviour | Tests | v2 |
|---|---|---|
| Each of the 7 types shows its content | `ScanPageTest` (7) | Keep |
| Each scan increments the QR's counter | `ScanPageTest` | Keep, done atomically; also records a `scans` row (stores no raw IP, see below) |
| Stored non-http URLs are not turned into links; stored text is escaped | `QrxCodeInputSafetyTest` (3) | Keep |
| Paused QR → "not found" page, scan not counted | `ScanPageTest` | **Change (proposed):** a "temporarily unavailable" page with HTTP 404 (today: a generic "not found" with HTTP 200). Still not counted |
| Reaching the plan's scan limit marks the QR expired | `ScanPageTest` | Keep |
| Unknown code → "not found" page | `ScanPageTest` | **Change (proposed):** HTTP 404 (today 200) |
| Owner without a subscription → **HTTP 500** today (audit H8) | none (it's a crash) | **Change (approved):** friendly "unavailable" page |

## Billing (M7)

| Behaviour | Tests | v2 |
|---|---|---|
| Checkout creates the Stripe customer and redirects to Stripe Checkout | `CheckoutTest` (2) | Keep (Cashier 16) |
| A `transactions` row is written at checkout, before payment | `CheckoutTest` | **Change (approved):** dropped; Cashier and Stripe are the source of truth |
| Subscription valid for created_at + 1 year | none | **Change (approved):** validity comes from the Stripe subscription status |
| Landing page lists plans | `ExampleTest` | Keep. **Change (proposed):** only active plans are listed and purchasable (today inactive ones are too, audit M11) |

## Admin (M8)

| Behaviour | Tests | v2 |
|---|---|---|
| Admins can enter the panel; customers cannot | `AdminPlanTest` | Keep, via `users.is_admin` (approved) |
| Admin creates a plan | `AdminPlanTest` | Keep |
| Admin lists and opens users | `AdminResourcesTest` | Keep |
| Admin lists, opens and pauses any user's QR | `AdminResourcesTest` (2), `QrxCodeAuthorizationTest` (admin policy) | Keep |

## Outside the MVP (kept in the suite, un-skipped in their later phase)

| Behaviour | Tests | Phase |
|---|---|---|
| Landing-page demo: size clamped, rate-limited per IP | `QrxCodeInputSafetyTest` (2) | L4 |
| Contact form | **none.** It has known bugs (blank response, wrong phone rule); it will be specified fresh when built | L4 |

## Scan records and privacy (your note)

The `scans` table stores no raw IP addresses. Each row holds:
- `scanned_at`;
- `device` (mobile / tablet / desktop);
- `referrer_host`;
- `country` (nullable): taken from a trusted proxy header such as Cloudflare's `CF-IPCountry` when present. A GeoIP database can be added later;
- `visitor_hash` (nullable): HMAC-SHA256 of IP + user agent, keyed with a secret that **rotates daily**. It can count unique visitors within a day but can't be reversed or linked across days.

## v2 test mapping (M9 gate)

Every row above is covered by a passing v2 test. 165 tests pass on PHP 8.5 with both SQLite and MySQL 8.4 (strict).

| Area | v2 tests |
|---|---|
| Auth (register, login/logout, reset, verification, confirmation, profile, delete account) | `tests/Feature/Auth/*` (Registration, Authentication, PasswordReset, EmailVerification, PasswordConfirmation, Profile) |
| QR creation for all 8 types, payload validation, URL schemes | `Qr/QrBuilderTest`, `Domain/QrCodeActionsTest`, `Domain/QrTypeValidationTest` |
| Quota / plan limits (free tier replaces "no subscription → plans page") | `Qr/QrBuilderTest` (free limit, paid limit), `Domain/SubscriptionsTest` |
| Ownership: view, edit, pause, delete, download; guests; 404 | `Qr/QrIndexTest`, `Qr/QrShowTest`, `Qr/QrBuilderTest`, `Qr/QrDownloadTest`, `Domain/QrCodePolicyTest` |
| Lists, status filters (incl. derived limit reached), search | `Qr/QrIndexTest` |
| Downloads PNG/SVG/EPS, size clamp, allowlist, file name | `Qr/QrDownloadTest`, `Domain/QrRendererTest` |
| Scan page: all types, counting, paused/limit/unknown (404), unsafe URLs, escaping, vCard | `Public/ScanPageTest`, `Domain/RecordScanTest` |
| Scans store no raw IP | `Domain/RecordScanTest::test_no_raw_ip_address_is_stored_anywhere` |
| Billing: checkout, swap, portal, webhook → plan, active plans only | `Billing/BillingTest`, `Billing/StripeWebhookTest`, `Public/HomePageTest` |
| Admin: access, users, QR codes, plans, make-admin | `Admin/AdminPanelTest` |
| i18n / RTL guardrails | `Ui/LogicalPropertiesTest`, `Ui/HardcodedStringTest`, `Ui/TranslationCoverageTest`, `Ui/LocaleTest`, `Ui/ComponentsTest` |
| Whole journey (LTR + RTL) | `EndToEndJourneyTest` |

Not carried over by design: the 7 `feature_*` tables, the `admins` table and second guard, the `transactions` table, stored PNG files, MP3 type (dropped), JWT/Sanctum API.
