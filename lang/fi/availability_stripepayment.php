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
 * Finnish language strings for availability_stripepayment.
 *
 * @package    availability_stripepayment
 * @category   string
 * @copyright  2026 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$string['accounts_email'] = 'Taloushallinnon sähköposti';
$string['accounts_email_desc'] = 'Sähköpostiosoite sisäisten maksuilmoitusten vastaanottamiseen (opiskelijakuitit lähettää Stripe automaattisesti)';
$string['activity_not_found'] = 'Aktiviteettia ei löydy.';
$string['activitypaymentreport'] = 'Näytä maksuraportti';
$string['allcourses'] = 'Kaikki kurssit';
$string['already_paid'] = 'Olet jo maksanut tästä aktiviteetista.';
$string['amount'] = 'Summa';
$string['backtocourse'] = 'Takaisin kurssille';
$string['cancelled'] = 'Peruutettu';
$string['clearfilter'] = 'Tyhjennä suodatin';
$string['completed'] = 'Valmis';
$string['completedpayments'] = 'Suoritetut maksut';
$string['continue_to_activity'] = 'Jatka kohteeseen {$a}';
$string['copytransactionid'] = 'Kopioi koko tapahtumatunnus';
$string['currency'] = 'Valuutta';
$string['description'] = 'Vaatii opiskelijoita suorittamaan maksun Stripen kautta ennen aktiviteettiin pääsyä.';
$string['dot'] = '.';
$string['downloadcsv'] = 'Lataa CSV';
$string['email_accounts_html_footer'] = 'Asiakaskuitti on lähetetty automaattisesti Stripeltä.';
$string['email_accounts_html_heading'] = '&#x2705; Uusi maksu vastaanotettu Stripen kautta.';
$string['email_accounts_html_title'] = 'Uusi maksu vastaanotettu';
$string['email_accounts_intro'] = 'Uusi maksu vastaanotettu Stripen kautta:';
$string['email_accounts_note'] = 'Huom: Asiakaskuitti lähetetty automaattisesti Stripeltä.';
$string['email_accounts_row_activity'] = 'Aktiviteetti';
$string['email_accounts_row_course'] = 'Kurssi';
$string['email_accounts_row_date'] = 'Päivämäärä';
$string['email_accounts_subject'] = 'Uusi maksu - {$a}';
$string['email_student_body'] = 'Maksusi onnistui! Sinulla on nyt pääsy aktiviteettiin.';
$string['email_student_subject'] = 'Maksu vahvistettu - {$a}';
$string['enable'] = 'Ota Stripe-maksut käyttöön';
$string['enable_desc'] = 'Salli aktiviteettien vaatia Stripe-maksuja pääsyä varten';
$string['error_amount_required'] = 'Anna kelvollinen summa, joka on suurempi kuin 0.';
$string['error_itemname_required'] = 'Anna kohteen nimi.';
$string['error_not_configured'] = 'Stripe-maksu ei ole asianmukaisesti määritetty.';
$string['expired'] = 'Vanhentunut';
$string['failed'] = 'Epäonnistui';
$string['filteractive'] = 'Suodatettu:';
$string['id'] = 'ID';
$string['invalid_amount'] = 'Virheellinen maksusumma';
$string['invalid_amount_admin'] = 'Virheellinen maksusumma määritetty. Ota yhteyttä järjestelmänvalvojaan.';
$string['invalid_currency_admin'] = 'Virheellinen valuutta määritetty. Ota yhteyttä järjestelmänvalvojaan.';
$string['itemname'] = 'Kohteen nimi';
$string['itemname_help'] = 'Nimi, joka näytetään käyttäjille maksun aikana';
$string['make_payment'] = 'Tee maksu';
$string['managetransactions'] = 'Hallinnoi Stripe-maksutapahtumia';
$string['no_condition_found'] = 'Stripe-maksuehto puuttuu tästä aktiviteetista. Ota yhteyttä järjestelmänvalvojaan.';
$string['nopayments'] = 'Tälle aktiviteetille ei ole vielä kirjattu maksuja.';
$string['not_paid'] = 'Et ole maksanut pääsystä tähän aktiviteettiin.';
$string['pay_now'] = 'Maksa nyt';
$string['payingstudents'] = 'Maksavat opiskelijat';
$string['payment_cancelled'] = 'Maksu peruutettu';
$string['payment_completed'] = 'Maksu suoritettu';
$string['payment_config_error'] = 'Maksumääritysvirhe. Ota yhteyttä tukeen.';
$string['payment_detail_amount'] = 'Summa';
$string['payment_detail_id'] = 'Maksutunnus';
$string['payment_detail_item'] = 'Kohde';
$string['payment_details'] = 'Maksutiedot';
$string['payment_failed'] = 'Maksu epäonnistui. Yritä uudelleen.';
$string['payment_failed_declined'] = 'Maksu epäonnistui: kortti hylättiin.';
$string['payment_in_progress'] = 'Maksu on jo käynnissä. Odota hetki ja yritä sitten uudelleen.';
$string['payment_not_found'] = 'Maksutietuetta ei löydy.';
$string['payment_required'] = 'Maksu vaaditaan';
$string['payment_required_desc'] = 'Pääsy kohteeseen "{$a->item}" vaatii maksun {$a->amount} {$a->currency}.';
$string['payment_success_notification'] = 'Maksu onnistui! Sinulla on nyt pääsy tähän sisältöön.';
$string['payment_successful'] = 'Maksu onnistui! Ohjataan...';
$string['payment_successful_title'] = 'Maksu onnistui';
$string['paymentreport'] = 'Maksuraportti';
$string['payments'] = 'Maksut';
$string['pending'] = 'Odottaa';
$string['pluginname'] = 'Stripe-maksuehto';
$string['privacy:metadata:payments'] = 'Stripe-maksutietueet tallentavat tietoja käyttäjien suorittamista maksuista kurssitoimintoihin pääsemiseksi.';
$string['privacy:metadata:payments:amount'] = 'Peritty maksusumma.';
$string['privacy:metadata:payments:cmid'] = 'Aktiviteetti (kurssimoduuli), jonka maksu avaa.';
$string['privacy:metadata:payments:courseid'] = 'Kurssi, johon maksu liittyy.';
$string['privacy:metadata:payments:sessionid'] = 'Stripe Checkout -istuntotunnus tapahtumaa varten.';
$string['privacy:metadata:payments:status'] = 'Maksun nykyinen tila (esim. valmis, odottaa).';
$string['privacy:metadata:payments:timecreated'] = 'Maksutietueen luomisaika.';
$string['privacy:metadata:payments:userid'] = 'Maksun suorittaneen käyttäjän tunnus.';
$string['processing'] = 'Käsitellään maksua...';
$string['redirecting_prefix'] = 'Ohjataan automaattisesti';
$string['second'] = 'sekunti';
$string['seconds'] = 'sekuntia';
$string['settings_transactions_admin'] = 'Stripe-maksutapahtumat';
$string['settings_transactions_heading'] = 'Maksutapahtumat';
$string['settings_transactions_link'] = 'Näytä kaikki maksutapahtumat';
$string['status_ok'] = 'ok';
$string['stripe_not_configured'] = 'Stripe ei ole asianmukaisesti määritetty. Ota yhteyttä järjestelmänvalvojaan.';
$string['stripe_publishable_key'] = 'Stripen julkinen avain';
$string['stripe_publishable_key_desc'] = 'Stripen julkinen avain Stripe-hallintapaneelista';
$string['stripe_secret_key'] = 'Stripen salainen avain';
$string['stripe_secret_key_desc'] = 'Stripen salainen avain Stripe-hallintapaneelista';
$string['stripedashboard'] = '⧉ Stripe-hallintapaneeli';
$string['stripelink'] = '🔗 Stripe';
$string['stripepayment:managetransactions'] = 'Hallinnoi Stripe-maksutapahtumia';
$string['student'] = 'Opiskelija';
$string['tablenotexist'] = 'Tietokantataulua {$a} ei ole olemassa';
$string['task_cleanup_pending'] = 'Siivoa vanhentuneet odottavat maksut';
$string['title'] = 'Stripe-maksu';
$string['totalpayments'] = 'Maksut yhteensä';
$string['totalrevenue'] = 'Kokonaistulot';
$string['transactionid'] = 'Tapahtumatunnus';
$string['transactionsreport'] = 'Stripe-maksutapahtumat';
$string['unknownactivity'] = 'Tuntematon aktiviteetti';
$string['viewinstripe'] = 'Näytä Stripe-hallintapaneelissa';
$string['webhook_already_processed'] = 'Jo käsitelty';
$string['webhook_empty_payload'] = 'Tyhjä payload';
$string['webhook_error'] = 'Webhook-virhe';
$string['webhook_invalid_payload'] = 'Virheellinen payload';
$string['webhook_invalid_signature'] = 'Virheellinen allekirjoitus';
$string['webhook_method_not_allowed'] = 'Menetelmä ei ole sallittu';
$string['webhook_missing_signature'] = 'Stripe-allekirjoitus puuttuu';
$string['webhook_payment_not_completed'] = 'Maksu ei suoritettu';
$string['webhook_secret'] = 'Webhook-päätepisteen salaisuus';
$string['webhook_secret_desc'] = 'Webhook-päätepisteen salaisuus Stripeltä';
$string['webhook_secret_not_configured'] = 'Webhook-salaisuutta ei ole määritetty';
