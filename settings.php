<?php
// This file is part of Moodle - https://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <https://www.gnu.org/licenses/>.

/**
 * Admin settings for the Stripe availability condition.
 *
 * @package    availability_stripepayment
 * @copyright  2025 Andrei Toma
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

if ($hassiteconfig && isset($settings)) {

    // Enable plugin
    $settings->add(new admin_setting_configcheckbox(
        'availability_stripepayment/enabled',
        get_string('enable', 'availability_stripepayment'),
        get_string('enable_desc', 'availability_stripepayment'),
        0
    ));

    // Stripe publishable key
    $settings->add(new admin_setting_configtext(
        'availability_stripepayment/stripe_publishable_key',
        get_string('stripe_publishable_key', 'availability_stripepayment'),
        get_string('stripe_publishable_key_desc', 'availability_stripepayment'),
        '',
        PARAM_TEXT
    ));

    // Stripe secret key
    $settings->add(new admin_setting_configtext(
        'availability_stripepayment/stripe_secret_key',
        get_string('stripe_secret_key', 'availability_stripepayment'),
        get_string('stripe_secret_key_desc', 'availability_stripepayment'),
        '',
        PARAM_TEXT
    ));

    // Webhook secret
    $settings->add(new admin_setting_configtext(
        'availability_stripepayment/webhook_secret',
        get_string('webhook_secret', 'availability_stripepayment'),
        get_string('webhook_secret_desc', 'availability_stripepayment'),
        '',
        PARAM_TEXT
    ));

    // Accounts email
    $settings->add(new admin_setting_configtext(
        'availability_stripepayment/accounts_email',
        get_string('accounts_email', 'availability_stripepayment'),
        get_string('accounts_email_desc', 'availability_stripepayment'),
        '',
        PARAM_EMAIL
    ));

    // Transactions page link (SAFE - no admin tree injection)
    $transactions_url = new moodle_url('/availability/condition/stripepayment/transactions.php');

    $settings->add(new admin_setting_description(
        'availability_stripepayment/transactions_link',
        get_string('settings_transactions_heading', 'availability_stripepayment'),
        html_writer::link(
            $transactions_url,
            get_string('settings_transactions_link', 'availability_stripepayment'),
            ['class' => 'btn btn-primary']
        )
    ));
}
