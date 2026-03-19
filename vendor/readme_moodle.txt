Library:    stripe-php
Source:     https://github.com/stripe/stripe-php
Version:    10.x (installed via composer require stripe/stripe-php:^10)
License:    MIT

Purpose:
Used by availability_stripepayment to create Stripe Checkout sessions,
retrieve session status, and verify webhook signatures.

Installation / Rebuild:
1. Ensure Composer is installed: https://getcomposer.org/
2. From the plugin root directory, run:
       composer require stripe/stripe-php:^10
3. Commit the resulting vendor/ directory (no composer install needed at deploy time).

The plugin ZIP must include the vendor/ directory pre-built so that
the plugin works out of the box after installation on any Moodle server.
