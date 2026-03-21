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
 * Stripe webhook endpoint — processes checkout.session.completed events.
 *
 * @package    availability_stripepayment
 * @copyright  2025 Andrei Toma
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

// MUST be defined before config.php loads.
define('NO_MOODLE_COOKIES', true);
define('NO_DEBUG_DISPLAY', true);
define('NO_UPGRADE_CHECK', true);

require_once('../../../config.php');
require_once(__DIR__ . '/vendor/autoload.php');
require_once(__DIR__ . '/lib.php');

// Webhook is server-to-server, no Moodle user context needed.
global $USER, $DB;
$USER = new stdClass();
$USER->id = 0;

// Only POST requests allowed
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit(get_string('webhook_method_not_allowed', 'availability_stripepayment'));
}

// Read raw payload
$payload = file_get_contents('php://input');
$sigheader = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

if (!$payload) {
    http_response_code(400);
    exit(get_string('webhook_empty_payload', 'availability_stripepayment'));
}

if (!$sigheader) {
    http_response_code(400);
    exit(get_string('webhook_missing_signature', 'availability_stripepayment'));
}

// Plugin config
$config = get_config('availability_stripepayment');

if (empty($config->webhook_secret)) {
    debugging('[availability_stripepayment] Webhook secret not configured', DEBUG_DEVELOPER);
    http_response_code(500);
    exit(get_string('webhook_secret_not_configured', 'availability_stripepayment'));
}

// Verify Stripe signature
try {

    $event = \Stripe\Webhook::constructEvent(
        $payload,
        $sigheader,
        $config->webhook_secret
    );

} catch (\UnexpectedValueException $e) {

    http_response_code(400);
    exit(get_string('webhook_invalid_payload', 'availability_stripepayment'));

} catch (\Stripe\Exception\SignatureVerificationException $e) {

    http_response_code(400);
    exit(get_string('webhook_invalid_signature', 'availability_stripepayment'));

} catch (Exception $e) {

    debugging('[availability_stripepayment] Webhook exception: ' . $e->getMessage(), DEBUG_DEVELOPER);
    http_response_code(400);
    exit(get_string('webhook_error', 'availability_stripepayment'));
}


// ------------------------------------------------------
// Process Stripe events
// ------------------------------------------------------

try {

    if ($event->type === 'checkout.session.completed') {

        $session = $event->data->object;

        // Ensure payment actually succeeded
        if ($session->payment_status !== 'paid') {

            http_response_code(200);
            echo json_encode(['status' => 'ignored', 'reason' => get_string('webhook_payment_not_completed', 'availability_stripepayment')]);
            exit;
        }

        // Find payment record
        $payment = $DB->get_record('availability_stripepayment_payments', [
            'stripe_session_id' => $session->id
        ]);

        if (!$payment) {

            debugging(
                '[availability_stripepayment] Payment record not found for session: ' . $session->id,
                DEBUG_DEVELOPER
            );

            http_response_code(200);
            echo json_encode([
                'status' => get_string('status_ok', 'availability_stripepayment'),
                'note' => get_string('payment_not_found', 'availability_stripepayment')
            ]);
            exit;
        }

        // Prevent duplicate processing
        if ($payment->status === 'completed') {

            http_response_code(200);
            echo json_encode([
                'status' => get_string('status_ok', 'availability_stripepayment'),
                'note' => get_string('webhook_already_processed', 'availability_stripepayment')
            ]);
            exit;
        }
        // Update payment status
        $transaction = $DB->start_delegated_transaction();

        $payment->status = 'completed';
        $payment->timemodified = time();

        $DB->update_record('availability_stripepayment_payments', $payment);

        $transaction->allow_commit();


        // Clear course module cache so activity unlocks immediately
        try {

            $cache = \cache::make('core', 'coursemodinfo');
            $cache->delete($payment->courseid);

        } catch (Exception $e) {

            debugging(
                '[availability_stripepayment] Cache clear error: ' . $e->getMessage(),
                DEBUG_DEVELOPER
            );
        }


        // Send notifications if implemented
        if (function_exists('availability_stripepayment_send_payment_notifications')) {

            availability_stripepayment_send_payment_notifications($payment, $session);
        }
    }

} catch (Exception $e) {

    debugging(
        '[availability_stripepayment] Processing error: ' . $e->getMessage(),
        DEBUG_DEVELOPER
    );
}


// ------------------------------------------------------
// Always return 200 so Stripe does not retry
// ------------------------------------------------------

http_response_code(200);
echo json_encode([
    'status' => get_string('status_ok', 'availability_stripepayment'),
    'note' => get_string('webhook_already_processed', 'availability_stripepayment')
]);
exit;
