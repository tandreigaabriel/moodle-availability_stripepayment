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
 * Swedish language strings for availability_stripepayment.
 *
 * @package    availability_stripepayment
 * @category   string
 * @copyright  2026 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$string['accounts_email'] = 'Bokförings-e-post';
$string['accounts_email_desc'] = 'E-postadress för att ta emot interna betalningsaviseringar (studentkvitton skickas automatiskt av Stripe)';
$string['activity_not_found'] = 'Aktiviteten hittades inte.';
$string['activitypaymentreport'] = 'Visa betalningsrapport';
$string['allcourses'] = 'Alla kurser';
$string['already_paid'] = 'Du har redan betalat för den här aktiviteten.';
$string['amount'] = 'Belopp';
$string['backtocourse'] = 'Tillbaka till kursen';
$string['cancelled'] = 'Avbruten';
$string['clearfilter'] = 'Rensa filter';
$string['completed'] = 'Slutförd';
$string['completedpayments'] = 'Slutförda betalningar';
$string['continue_to_activity'] = 'Fortsätt till {$a}';
$string['copytransactionid'] = 'Kopiera fullständigt transaktions-ID';
$string['currency'] = 'Valuta';
$string['description'] = 'Kräver att studenter gör en betalning via Stripe innan de får åtkomst till aktiviteten.';
$string['dot'] = '.';
$string['downloadcsv'] = 'Ladda ner CSV';
$string['email_accounts_html_footer'] = 'Kundkvitto har skickats automatiskt av Stripe.';
$string['email_accounts_html_heading'] = '&#x2705; Ny betalning mottagen via Stripe.';
$string['email_accounts_html_title'] = 'Ny betalning mottagen';
$string['email_accounts_intro'] = 'Ny betalning mottagen via Stripe:';
$string['email_accounts_note'] = 'Obs: Kundkvitto skickat automatiskt av Stripe.';
$string['email_accounts_row_activity'] = 'Aktivitet';
$string['email_accounts_row_course'] = 'Kurs';
$string['email_accounts_row_date'] = 'Datum';
$string['email_accounts_subject'] = 'Ny betalning - {$a}';
$string['email_student_body'] = 'Din betalning lyckades! Du har nu tillgång till aktiviteten.';
$string['email_student_subject'] = 'Betalning bekräftad - {$a}';
$string['enable'] = 'Aktivera Stripe-betalningar';
$string['enable_desc'] = 'Tillåt aktiviteter att kräva Stripe-betalningar för åtkomst';
$string['error_amount_required'] = 'Ange ett giltigt belopp större än 0.';
$string['error_itemname_required'] = 'Ange ett artikelnamn.';
$string['error_not_configured'] = 'Stripe-betalning är inte korrekt konfigurerad.';
$string['expired'] = 'Utgången';
$string['failed'] = 'Misslyckad';
$string['filteractive'] = 'Filtrerat efter:';
$string['id'] = 'ID';
$string['invalid_amount'] = 'Ogiltigt betalningsbelopp';
$string['invalid_amount_admin'] = 'Ogiltigt betalningsbelopp konfigurerat. Kontakta administratören.';
$string['invalid_currency_admin'] = 'Ogiltig valuta konfigurerad. Kontakta administratören.';
$string['itemname'] = 'Artikelnamn';
$string['itemname_help'] = 'Namnet som visas för användare under betalning';
$string['make_payment'] = 'Gör betalning';
$string['managetransactions'] = 'Hantera Stripe-betalningstransaktioner';
$string['no_condition_found'] = 'Inget Stripe-betalningsvillkor hittades för den här aktiviteten. Kontakta administratören.';
$string['nopayments'] = 'Inga betalningar registrerade för den här aktiviteten ännu.';
$string['not_paid'] = 'Du har inte betalat för åtkomst till den här aktiviteten.';
$string['pay_now'] = 'Betala nu';
$string['payingstudents'] = 'Betalande studenter';
$string['payment_cancelled'] = 'Betalning avbruten';
$string['payment_completed'] = 'Betalning slutförd';
$string['payment_config_error'] = 'Betalningskonfigurationsfel. Kontakta supporten.';
$string['payment_detail_amount'] = 'Belopp';
$string['payment_detail_id'] = 'Betalnings-ID';
$string['payment_detail_item'] = 'Artikel';
$string['payment_details'] = 'Betalningsdetaljer';
$string['payment_failed'] = 'Betalning misslyckades. Försök igen.';
$string['payment_failed_declined'] = 'Betalning misslyckades: kortet avvisades.';
$string['payment_in_progress'] = 'En betalning pågår redan. Vänta en stund och försök sedan igen.';
$string['payment_not_found'] = 'Betalningspost hittades inte.';
$string['payment_required'] = 'Betalning krävs';
$string['payment_required_desc'] = 'Åtkomst till "{$a->item}" kräver en betalning på {$a->amount} {$a->currency}.';
$string['payment_success_notification'] = 'Betalning lyckades! Du har nu tillgång till det här innehållet.';
$string['payment_successful'] = 'Betalning lyckades! Omdirigerar...';
$string['payment_successful_title'] = 'Betalning lyckades';
$string['paymentreport'] = 'Betalningsrapport';
$string['payments'] = 'Betalningar';
$string['pending'] = 'Väntar';
$string['pluginname'] = 'Stripe-betalningsrestriktion';
$string['privacy:metadata:payments'] = 'Stripe-betalningsposter lagrar data om betalningar gjorda av användare för att komma åt kursaktiviteter.';
$string['privacy:metadata:payments:amount'] = 'Det betalda beloppet.';
$string['privacy:metadata:payments:cmid'] = 'Aktiviteten (kursmodulen) som betalningen låser upp.';
$string['privacy:metadata:payments:courseid'] = 'Kursen som betalningen avser.';
$string['privacy:metadata:payments:sessionid'] = 'Stripe Checkout-sessions-ID för transaktionen.';
$string['privacy:metadata:payments:status'] = 'Betalningens aktuella status (t.ex. slutförd, väntar).';
$string['privacy:metadata:payments:timecreated'] = 'Tidpunkten då betalningsposten skapades.';
$string['privacy:metadata:payments:userid'] = 'ID för användaren som genomförde betalningen.';
$string['processing'] = 'Bearbetar betalning...';
$string['redirecting_prefix'] = 'Omdirigerar automatiskt om';
$string['second'] = 'sekund';
$string['seconds'] = 'sekunder';
$string['settings_transactions_admin'] = 'Stripe-betalningstransaktioner';
$string['settings_transactions_heading'] = 'Betalningstransaktioner';
$string['settings_transactions_link'] = 'Visa alla betalningstransaktioner';
$string['status_ok'] = 'ok';
$string['stripe_not_configured'] = 'Stripe är inte korrekt konfigurerad. Kontakta en administratör.';
$string['stripe_publishable_key'] = 'Stripe publiceringsnyckel';
$string['stripe_publishable_key_desc'] = 'Din Stripe publiceringsnyckel från Stripe-instrumentpanelen';
$string['stripe_secret_key'] = 'Stripe hemlig nyckel';
$string['stripe_secret_key_desc'] = 'Din Stripe hemliga nyckel från Stripe-instrumentpanelen';
$string['stripedashboard'] = '⧉ Stripe instrumentpanel';
$string['stripelink'] = '🔗 Stripe';
$string['stripepayment:managetransactions'] = 'Hantera Stripe-betalningstransaktioner';
$string['student'] = 'Student';
$string['tablenotexist'] = 'Databastabellen {$a} existerar inte';
$string['task_cleanup_pending'] = 'Rensa utgångna väntande betalningar';
$string['title'] = 'Stripe-betalning';
$string['totalpayments'] = 'Totala betalningar';
$string['totalrevenue'] = 'Total intäkt';
$string['transactionid'] = 'Transaktions-ID';
$string['transactionsreport'] = 'Stripe-betalningstransaktioner';
$string['unknownactivity'] = 'Okänd aktivitet';
$string['viewinstripe'] = 'Visa i Stripe instrumentpanel';
$string['webhook_already_processed'] = 'Redan bearbetad';
$string['webhook_empty_payload'] = 'Tom payload';
$string['webhook_error'] = 'Webhook-fel';
$string['webhook_invalid_payload'] = 'Ogiltig payload';
$string['webhook_invalid_signature'] = 'Ogiltig signatur';
$string['webhook_method_not_allowed'] = 'Metod inte tillåten';
$string['webhook_missing_signature'] = 'Stripe-signatur saknas';
$string['webhook_payment_not_completed'] = 'Betalning inte slutförd';
$string['webhook_secret'] = 'Webhook endpoint-hemlighet';
$string['webhook_secret_desc'] = 'Webhook endpoint-hemligheten från Stripe';
$string['webhook_secret_not_configured'] = 'Webhook-hemlighet inte konfigurerad';
