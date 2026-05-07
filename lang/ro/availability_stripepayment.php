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
 * Romanian language strings for availability_stripepayment.
 *
 * @package    availability_stripepayment
 * @category   string
 * @copyright  2026 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$string['accounts_email'] = 'Email contabilitate';
$string['accounts_email_desc'] = 'Adresa de email pentru a primi notificări interne de plată (chitanțele studenților sunt trimise automat de Stripe)';
$string['activity_not_found'] = 'Activitate negăsită.';
$string['activitypaymentreport'] = 'Vizualizare raport plăți';
$string['allcourses'] = 'Toate cursurile';
$string['already_paid'] = 'Ați plătit deja pentru această activitate.';
$string['amount'] = 'Sumă';
$string['backtocourse'] = 'Înapoi la curs';
$string['cancelled'] = 'Anulat';
$string['clearfilter'] = 'Șterge filtrul';
$string['completed'] = 'Finalizat';
$string['completedpayments'] = 'Plăți finalizate';
$string['continue_to_activity'] = 'Continuați la {$a}';
$string['copytransactionid'] = 'Copiați ID-ul complet al tranzacției';
$string['currency'] = 'Monedă';
$string['description'] = 'Solicitați studenților să efectueze o plată prin Stripe înainte de a accesa activitatea.';
$string['dot'] = '.';
$string['downloadcsv'] = 'Descărcați CSV';
$string['email_accounts_html_footer'] = 'Chitanța clientului a fost trimisă automat de Stripe.';
$string['email_accounts_html_heading'] = '&#x2705; Plată nouă primită prin Stripe.';
$string['email_accounts_html_title'] = 'Plată nouă primită';
$string['email_accounts_intro'] = 'Plată nouă primită prin Stripe:';
$string['email_accounts_note'] = 'Notă: Chitanța clientului a fost trimisă automat de Stripe.';
$string['email_accounts_row_activity'] = 'Activitate';
$string['email_accounts_row_course'] = 'Curs';
$string['email_accounts_row_date'] = 'Data';
$string['email_accounts_subject'] = 'Plată nouă - {$a}';
$string['email_student_body'] = 'Plata dumneavoastră a fost efectuată cu succes! Acum aveți acces la activitate.';
$string['email_student_subject'] = 'Plată confirmată - {$a}';
$string['enable'] = 'Activați plățile Stripe';
$string['enable_desc'] = 'Permiteți activităților să necesite plăți Stripe pentru acces';
$string['error_amount_required'] = 'Introduceți o sumă validă mai mare decât 0.';
$string['error_itemname_required'] = 'Introduceți un nume pentru articol.';
$string['error_not_configured'] = 'Plata Stripe nu este configurată corect.';
$string['expired'] = 'Expirat';
$string['failed'] = 'Eșuat';
$string['filteractive'] = 'Filtrat după:';
$string['id'] = 'ID';
$string['invalid_amount'] = 'Sumă de plată invalidă';
$string['invalid_amount_admin'] = 'Sumă de plată invalidă configurată. Contactați administratorul.';
$string['invalid_currency_admin'] = 'Monedă invalidă configurată. Contactați administratorul.';
$string['itemname'] = 'Numele articolului';
$string['itemname_help'] = 'Numele afișat utilizatorilor în timpul plății';
$string['make_payment'] = 'Efectuați plata';
$string['managetransactions'] = 'Gestionați tranzacțiile de plată Stripe';
$string['no_condition_found'] = 'Nu a fost găsită nicio condiție de plată Stripe pentru această activitate. Contactați administratorul.';
$string['nopayments'] = 'Nicio plată înregistrată pentru această activitate.';
$string['not_paid'] = 'Nu ați plătit pentru accesul la această activitate.';
$string['pay_now'] = 'Plătiți acum';
$string['payingstudents'] = 'Studenți plătitori';
$string['payment_cancelled'] = 'Plată anulată';
$string['payment_completed'] = 'Plată finalizată';
$string['payment_config_error'] = 'Eroare de configurare a plății. Contactați asistența.';
$string['payment_detail_amount'] = 'Sumă';
$string['payment_detail_id'] = 'ID plată';
$string['payment_detail_item'] = 'Articol';
$string['payment_details'] = 'Detalii plată';
$string['payment_failed'] = 'Plată eșuată. Vă rugăm să încercați din nou.';
$string['payment_failed_declined'] = 'Plată eșuată: card refuzat.';
$string['payment_in_progress'] = 'O plată este deja în curs. Așteptați un moment și încercați din nou.';
$string['payment_not_found'] = 'Înregistrare plată negăsită.';
$string['payment_required'] = 'Plată necesară';
$string['payment_required_desc'] = 'Accesul la "{$a->item}" necesită o plată de {$a->amount} {$a->currency}.';
$string['payment_success_notification'] = 'Plată reușită! Acum aveți acces la acest conținut.';
$string['payment_successful'] = 'Plată reușită! Redirecționare...';
$string['payment_successful_title'] = 'Plată reușită';
$string['paymentreport'] = 'Raport plăți';
$string['payments'] = 'Plăți';
$string['pending'] = 'În așteptare';
$string['pluginname'] = 'Restricție plată Stripe';
$string['privacy:metadata:payments'] = 'Înregistrările de plată Stripe stochează date despre plățile efectuate de utilizatori pentru a accesa activitățile cursului.';
$string['privacy:metadata:payments:amount'] = 'Suma plătită.';
$string['privacy:metadata:payments:cmid'] = 'Activitatea (modulul cursului) pe care plata o deblochează.';
$string['privacy:metadata:payments:courseid'] = 'Cursul la care se referă plata.';
$string['privacy:metadata:payments:sessionid'] = 'ID-ul sesiunii Stripe Checkout pentru tranzacție.';
$string['privacy:metadata:payments:status'] = 'Starea actuală a plății (ex. finalizat, în așteptare).';
$string['privacy:metadata:payments:timecreated'] = 'Momentul în care a fost creată înregistrarea plății.';
$string['privacy:metadata:payments:userid'] = 'ID-ul utilizatorului care a efectuat plata.';
$string['processing'] = 'Se procesează plata...';
$string['redirecting_prefix'] = 'Redirecționare automată în';
$string['second'] = 'secundă';
$string['seconds'] = 'secunde';
$string['settings_transactions_admin'] = 'Tranzacții de plată Stripe';
$string['settings_transactions_heading'] = 'Tranzacții de plată';
$string['settings_transactions_link'] = 'Vizualizați toate tranzacțiile de plată';
$string['status_ok'] = 'ok';
$string['stripe_not_configured'] = 'Stripe nu este configurat corect. Contactați un administrator.';
$string['stripe_publishable_key'] = 'Cheie publicabilă Stripe';
$string['stripe_publishable_key_desc'] = 'Cheia publicabilă Stripe din tabloul de bord Stripe';
$string['stripe_secret_key'] = 'Cheie secretă Stripe';
$string['stripe_secret_key_desc'] = 'Cheia secretă Stripe din tabloul de bord Stripe';
$string['stripedashboard'] = '⧉ Tablou de bord Stripe';
$string['stripelink'] = '🔗 Stripe';
$string['stripepayment:managetransactions'] = 'Gestionați tranzacțiile de plată Stripe';
$string['student'] = 'Student';
$string['tablenotexist'] = 'Tabelul de baze de date {$a} nu există';
$string['task_cleanup_pending'] = 'Curățați plățile în așteptare expirate';
$string['title'] = 'Plată Stripe';
$string['totalpayments'] = 'Total plăți';
$string['totalrevenue'] = 'Venituri totale';
$string['transactionid'] = 'ID tranzacție';
$string['transactionsreport'] = 'Tranzacții de plată Stripe';
$string['unknownactivity'] = 'Activitate necunoscută';
$string['viewinstripe'] = 'Vizualizați în tabloul de bord Stripe';
$string['webhook_already_processed'] = 'Deja procesat';
$string['webhook_empty_payload'] = 'Payload gol';
$string['webhook_error'] = 'Eroare webhook';
$string['webhook_invalid_payload'] = 'Payload invalid';
$string['webhook_invalid_signature'] = 'Semnătură invalidă';
$string['webhook_method_not_allowed'] = 'Metodă nepermisă';
$string['webhook_missing_signature'] = 'Semnătură Stripe lipsă';
$string['webhook_payment_not_completed'] = 'Plată nefinalizată';
$string['webhook_secret'] = 'Secret endpoint webhook';
$string['webhook_secret_desc'] = 'Secretul endpoint-ului webhook de la Stripe';
$string['webhook_secret_not_configured'] = 'Secretul webhook nu este configurat';
