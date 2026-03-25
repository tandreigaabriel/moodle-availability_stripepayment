# Stripe Payment Availability Condition for Moodle

Restrict access to any Moodle activity or resource behind a Stripe payment. Students pay once and gain immediate access. Teachers and admins always bypass the restriction.

---

## Features

- Restrict any activity (assignment, quiz, file, page, etc.) behind a one-time payment
- Supports all major currencies including zero-decimal currencies (JPY, KRW, etc.)
- Stripe Checkout hosted page — no card data touches your server
- Webhook-driven access grant — access is unlocked the moment payment completes
- Payment button rendered inline on the course page — no JavaScript DOM injection
- Branded HTML email notifications sent to your accounts team and site admins
- Admin dashboard with transaction history, per-currency revenue summary, and direct Stripe links
- Per-activity payment report for teachers — no site-admin access required
- Duplicate session prevention — a second click within 2 minutes is blocked server-side
- Stale pending payments automatically cleaned up daily by a scheduled task
- CSV export of all transactions
- Teachers and admins always bypass the payment restriction

---

## Requirements

- Moodle 4.5 or later
- PHP 8.1 or later
- A [Stripe](https://stripe.com) account (free to create)
- HTTPS on your Moodle site (required by Stripe)

---

## Installation

### 1. Download the plugin

Download the latest release ZIP from the [Moodle plugins directory](https://moodle.org/plugins/availability_stripepayment) or from the [GitHub releases page](https://github.com/tandreigaabriel/moodle-availability_stripepayment/releases).

> The ZIP already includes the Stripe PHP SDK bundled inside `vendor/`. No Composer required on your server.

### 2. Install in Moodle

**Option A — Via the Moodle admin interface (recommended)**

1. Log in as admin
2. Go to **Site Administration → Plugins → Install plugins**
3. Upload the ZIP file
4. Click **Install plugin from the ZIP file**
5. Follow the on-screen prompts and click **Upgrade Moodle database now**

**Option B — Manual installation**

1. Unzip the plugin
2. Copy the `stripepayment` folder to `[moodleroot]/availability/condition/stripepayment/`
3. Log in as admin
4. Go to **Site Administration → Notifications**
5. Click **Upgrade Moodle database now**

---

## Configuration

### 1. Get your Stripe API keys

1. Log in to the [Stripe Dashboard](https://dashboard.stripe.com)
2. Go to **Developers → API keys**
3. Copy your **Publishable key** and **Secret key**
   - Use **test mode** keys while testing, switch to **live** keys when ready

### 2. Set up a Stripe Webhook

1. In the Stripe Dashboard go to **Developers → Webhooks**
2. Click **Add endpoint**
3. Set the endpoint URL to:
   ```
   https://yourmoodlesite.com/availability/condition/stripepayment/webhook.php
   ```
4. Under **Events to listen to**, select:
   - `checkout.session.completed`
5. Click **Add endpoint**
6. Click **Reveal** next to **Signing secret** and copy it

### 3. Configure the plugin in Moodle

1. Go to **Site Administration → Plugins → Availability restrictions → Stripe Payment**
2. Fill in:
   | Setting | Value |
   |---|---|
   | Enable Stripe payments | Checked |
   | Stripe publishable key | `pk_test_...` or `pk_live_...` |
   | Stripe secret key | `sk_test_...` or `sk_live_...` |
   | Webhook endpoint secret | `whsec_...` |
   | Accounts email | Email to receive payment notifications |

---

## Adding a Payment Restriction to an Activity

1. Go to your course and turn editing on
2. Click **Edit** next to any activity → **Edit settings**
3. Scroll down to **Restrict access**
4. Click **Add restriction** → **Stripe payment**
5. Fill in:
   - **Amount** — price as you would write it on a price tag (e.g. `9.99` = £9.99, `35` = £35.00, `100` = £100.00)
   - **Currency** — e.g. `GBP`, `USD`, `EUR`
   - **Item name** — what the student sees on the Stripe checkout page
6. Save the activity

Students who have not paid will see a payment button inline on the course page. After paying they are redirected back and access is granted automatically.

---

## Payment Flow

```
Student clicks Pay
       ↓
payment.php checks for duplicate session (blocks if one exists < 2 min ago)
       ↓
payment.php creates a Stripe Checkout session
       ↓
Student is redirected to Stripe's hosted checkout page
       ↓
Student enters card details on Stripe
       ↓
Student is redirected to success.php
       ↓
success.php verifies payment status with Stripe API
       ↓
If paid → marks payment "completed" + sends email notifications
       ↓
(In parallel) Stripe sends checkout.session.completed to webhook.php
       ↓
webhook.php marks payment "completed" if not already done
       ↓
Student is auto-redirected to the activity
       ↓
condition.php detects completed payment → access granted
```

> **Note:** Email notifications are sent by `success.php` immediately after the student returns from Stripe. The webhook acts as a reliable fallback to complete the payment record if `success.php` is skipped for any reason.

---

## Transaction Reports

### Site-wide report (admins)

Go to **Site Administration → Reports → Stripe Payment Transactions** to view:

- All payments across all courses with student name, activity, amount, status, and date
- Summary cards: total payments, completed, pending, and one revenue card per currency (e.g. 10.00 GBP, 20.00 USD)
- Direct links to each transaction in the Stripe Dashboard
- Filter by course and by status
- CSV export

### Per-activity report (teachers)

Teachers do not need site-admin access to see who has paid for their own activities. The report is accessible two ways:

- Via the **View payment report** button shown next to any activity with a Stripe restriction on the course page
- Via the course **Reports** menu in the course navigation (visible to teachers and admins)

The report shows:

- Completed payments, pending count, and total revenue per currency for that activity
- A table of all paying students with name, email, amount, currency, status, and date

---

## Testing

Use Stripe's test card numbers while your plugin is in test mode:

| Card number           | Result             |
| --------------------- | ------------------ |
| `4242 4242 4242 4242` | Payment succeeds   |
| `4000 0000 0000 0002` | Card declined      |
| `4000 0025 0000 3155` | Requires 3D Secure |

Use any future expiry date, any 3-digit CVC, and any postcode.

### Testing email notifications

The plugin sends an internal notification email to your accounts team after every completed payment. To test this:

1. Make sure the **Accounts email** field is filled in under **Site Administration → Plugins → Availability restrictions → Stripe Payment**
   - If left blank, the plugin falls back to `accounts@yourmoodledomain.com`
2. Complete a test payment using a Stripe test card (see table above)
3. Check the inbox of the configured accounts email address — you should receive a branded HTML email containing:
   - Student name and email address
   - Activity name and course name
   - Amount and currency
   - Stripe session ID
   - Payment date and time

> **Note:** Moodle must be configured to send outgoing email. Verify this under **Site Administration → Server → Email → Outgoing mail configuration**. You can use the **Send a test message** button on that page to confirm delivery before testing payments.

> **Note:** The accounts email address must belong to a registered Moodle user. If no Moodle user account matches the address, the notification is sent to the site admin instead.

---

## Troubleshooting

**Access not granted after payment**

- Check that your webhook URL is correctly configured in Stripe
- Verify the webhook secret matches what is in the plugin settings
- Check your server's PHP error log for `[availability_stripepayment]` entries
- Ensure your Moodle site is accessible from the internet (webhooks cannot reach localhost)

**"Stripe is not configured" error**

- Make sure the secret key is saved in the plugin settings

**Test payments work but live payments don't**

- Switch from test keys (`pk_test_`, `sk_test_`) to live keys (`pk_live_`, `sk_live_`) in the plugin settings
- Create a separate live webhook endpoint in the Stripe Dashboard and update the webhook secret

---

## Frequently Asked Questions

**Does the student get a receipt?**
Yes. Stripe automatically sends a payment receipt to the student's email address. No extra configuration needed.

**What happens if a student pays but doesn't get access?**
The webhook may not have been received. Check the webhook logs in the Stripe Dashboard under **Developers → Webhooks** and use the **Resend** button.

**Can a student pay for the same activity twice?**
No. The plugin checks for an existing completed payment before creating a new Stripe session. Additionally, if a student clicks Pay and then clicks again within 2 minutes, the second request is blocked server-side with a warning message.

**Is refund handled automatically?**
Not currently. If you issue a refund via Stripe, you must manually remove the payment record from the database or the student will retain access.

**What happens to abandoned checkouts?**
Stripe checkout sessions expire after 24 hours. The plugin's daily scheduled task automatically marks any pending payment records older than 48 hours as `expired`, keeping the transaction table clean.

---

## Third-party libraries

This plugin bundles the Stripe PHP SDK:

- Library: Stripe PHP SDK
- Version: 13.18.0
- License: MIT License
- Source: https://github.com/stripe/stripe-php

The library is included in the `vendor/` directory.

---

## Support & Donation

Developed and maintained by **Andrei Toma** at [tagwebdesign.co.uk](https://www.tagwebdesign.co.uk).

- GitHub repository: [tandreigaabriel/moodle-availability_stripepayment](https://github.com/tandreigaabriel/moodle-availability_stripepayment)
- Bug reports & feature requests: [open an issue on GitHub](https://github.com/tandreigaabriel/moodle-availability_stripepayment/issues)

If this plugin saves you time, please consider supporting its development via the website.

---

## License

This plugin is free software: you can redistribute it and/or modify it under the terms of the [GNU General Public License](https://www.gnu.org/licenses/gpl-3.0.html) as published by the Free Software Foundation, either version 3 of the License, or any later version.

Copyright &copy; 2025 Andrei Toma — [tagwebdesign.co.uk](https://www.tagwebdesign.co.uk)

---

## Changelog

### 1.3.6

- Fixed `classes/hook/output/before_http_headers.php` incorrectly extending the final core hook class `\core\hook\output\before_http_headers` — caused a PHP fatal error on every request, silently preventing redirects (e.g. the Save button on `admin/upgradesettings.php` did nothing)
- Removed the now-redundant hook callback registration from `db/hooks.php` — the callback was intentionally empty as Moodle loads plugin CSS automatically
- Fixed CSV export in the site-wide transactions report downloading Bootstrap HTML summary card markup alongside data rows — added an `is_downloading()` early-return guard to the `start_output()` override in `classes/transactions_table.php`
- Fixed Moodle debugging warning "name fields missing from user object" on the per-activity report — added `firstnamephonetic`, `lastnamephonetic`, `middlename`, and `alternatename` to the SQL query in `activity_report.php` so `fullname()` has all required fields
- Fixed PHPCS violations: renamed `$transactions_url` → `$transactionsurl` in `settings.php`; corrected Allman-style opening braces to K&R style across all methods in `classes/condition.php`; removed trailing whitespace, fixed overlong line, and removed blank line before `catch` in `payment.php`
- Replaced all hard-coded English strings in `lib.php` (`send_payment_notifications`) and `transactions.php` with `get_string()` calls; added corresponding entries to `lang/en/availability_stripepayment.php`

### 1.3.5

- Migrated availability condition form editor from legacy YUI to an AMD ES6 module (`amd/src/form.js`) — `frontend.php` now overrides `include_javascript()` and loads via `js_call_amd()`, removing the YUI dependency from the teacher-facing form UI
- Moved success-page countdown redirect from inline PHP `echo html_writer::script()` to a dedicated AMD module (`amd/src/success.js`) to comply with Moodle JS coding standards
- Added GPL licence headers to all four YUI build/source files (retained for reference; no longer loaded at runtime)
- Added explanatory comments to `webhook.php` clarifying that (a) `NO_MOODLE_COOKIES` etc. are Moodle core bootstrap constants and are exempt from the Frankenstyle prefix rule, and (b) the `$DB->update_record()` call is protected by Stripe cryptographic signature verification, not a Moodle capability check
- Fixed non-ASCII box-drawing characters in `classes/transactions_table.php` comments (I18N002)

### 1.3.4

- Added `require_capability('moodle/course:view', ...)` to `payment.php` and `success.php` — only enrolled users with course access can initiate or confirm a payment
- Moved `$PAGE->set_context()` in `success.php` to immediately after the course module is resolved, before any output or capability check
- Removed incorrect `$USER` override in `webhook.php` — `NO_MOODLE_COOKIES` already prevents session loading for the server-to-server webhook request
- Fixed incomplete GPL licence header in `classes/privacy/provider.php`
- Added GitHub Actions CI pipeline (`.github/workflows/ci.yml`) using `moodlehq/moodle-plugin-ci` — runs PHP lint, code checker, PHPDoc, Mustache lint, Grunt AMD build check, and PHPUnit across PHP 8.1/8.2/8.3 and Moodle 4.5 stable + main

### 1.3.3

- Fixed accounts team and admin email notifications not being sent — `success.php` now sends notifications immediately when the student returns from Stripe, resolving a race condition where the webhook saw the payment already completed and skipped the notification step
- Replaced plain-text HTML email bodies with branded Bootstrap-style HTML templates featuring a header band, styled details table, and action buttons

### 1.3.2

- Fixed Moodle plugin review issues
- Implemented Privacy API provider
- Fixed language strings
- Improved CSS namespacing
- Added third-party library documentation

### 1.3.1

- Fixed currency formatting edge cases for zero-decimal currencies in payment and report pages
- Fixed currency code normalisation — values are now consistently stored and displayed in uppercase

### 1.3.0

- Added support for additional currencies including all Stripe-supported zero-decimal currencies (BIF, CLP, DJF, GNF, JPY, KMF, KRW, MGA, PYG, RWF, UGX, VND, VUV, XAF, XOF, XPF)
- Added Stripe test mode detection — transaction links in the admin report automatically point to the correct Stripe test or live dashboard URL based on the session ID prefix (`cs_test_` vs `cs_`)

### 1.2.0

- Revenue summary now groups by currency — one card per currency (e.g. 10.00 GBP, 20.00 USD) instead of a hardcoded single total
- Transaction report accessible from the course Reports navigation menu for teachers and admins
- Bootstrap 5 styling throughout all report pages
- Activity name now correctly resolved in the admin transactions table
- Status filter added to the site-wide transactions report

### 1.1.0

- Per-activity payment report for teachers (accessible from the course page)
- Duplicate session prevention — server-side block if a pending payment exists within the last 2 minutes
- Scheduled task to expire stale pending payments older than 48 hours
- Payment button now rendered via Mustache template — no JavaScript DOM injection

### 1.0.0

- Initial release
- Stripe Checkout integration
- Webhook-based payment verification
- Admin transaction dashboard with CSV export
- Multi-currency support including zero-decimal currencies
