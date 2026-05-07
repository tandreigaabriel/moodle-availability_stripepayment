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
 * Italian language strings for availability_stripepayment.
 *
 * @package    availability_stripepayment
 * @category   string
 * @copyright  2026 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$string['accounts_email'] = 'Email contabilità';
$string['accounts_email_desc'] = 'Indirizzo email per ricevere le notifiche interne di pagamento (le ricevute per gli studenti vengono inviate automaticamente da Stripe)';
$string['activity_not_found'] = 'Attività non trovata.';
$string['activitypaymentreport'] = 'Visualizza il report dei pagamenti';
$string['allcourses'] = 'Tutti i corsi';
$string['already_paid'] = 'Hai già pagato per questa attività.';
$string['amount'] = 'Importo';
$string['backtocourse'] = 'Torna al corso';
$string['cancelled'] = 'Annullato';
$string['clearfilter'] = 'Cancella filtro';
$string['completed'] = 'Completato';
$string['completedpayments'] = 'Pagamenti completati';
$string['continue_to_activity'] = 'Continua a {$a}';
$string['copytransactionid'] = 'Copia ID transazione completo';
$string['currency'] = 'Valuta';
$string['description'] = 'Richiede agli studenti di effettuare un pagamento tramite Stripe prima di accedere all\'attività.';
$string['dot'] = '.';
$string['downloadcsv'] = 'Scarica CSV';
$string['email_accounts_html_footer'] = 'La ricevuta del cliente è stata inviata automaticamente da Stripe.';
$string['email_accounts_html_heading'] = '&#x2705; Nuovo pagamento ricevuto tramite Stripe.';
$string['email_accounts_html_title'] = 'Nuovo pagamento ricevuto';
$string['email_accounts_intro'] = 'Nuovo pagamento ricevuto tramite Stripe:';
$string['email_accounts_note'] = 'Nota: Ricevuta del cliente inviata automaticamente da Stripe.';
$string['email_accounts_row_activity'] = 'Attività';
$string['email_accounts_row_course'] = 'Corso';
$string['email_accounts_row_date'] = 'Data';
$string['email_accounts_subject'] = 'Nuovo pagamento - {$a}';
$string['email_student_body'] = 'Il tuo pagamento è andato a buon fine! Ora hai accesso all\'attività.';
$string['email_student_subject'] = 'Pagamento confermato - {$a}';
$string['enable'] = 'Abilita pagamenti Stripe';
$string['enable_desc'] = 'Consenti alle attività di richiedere pagamenti Stripe per l\'accesso';
$string['error_amount_required'] = 'Inserisci un importo valido maggiore di 0.';
$string['error_itemname_required'] = 'Inserisci un nome per l\'articolo.';
$string['error_not_configured'] = 'Il pagamento Stripe non è configurato correttamente.';
$string['expired'] = 'Scaduto';
$string['failed'] = 'Fallito';
$string['filteractive'] = 'Filtrato per:';
$string['id'] = 'ID';
$string['invalid_amount'] = 'Importo di pagamento non valido';
$string['invalid_amount_admin'] = 'Importo di pagamento non valido configurato. Contatta l\'amministratore.';
$string['invalid_currency_admin'] = 'Valuta non valida configurata. Contatta l\'amministratore.';
$string['itemname'] = 'Nome articolo';
$string['itemname_help'] = 'Il nome mostrato agli utenti durante il pagamento';
$string['make_payment'] = 'Effettua pagamento';
$string['managetransactions'] = 'Gestisci le transazioni di pagamento Stripe';
$string['no_condition_found'] = 'Nessuna condizione di pagamento Stripe trovata per questa attività. Contatta l\'amministratore.';
$string['nopayments'] = 'Nessun pagamento registrato per questa attività.';
$string['not_paid'] = 'Non hai pagato per accedere a questa attività.';
$string['pay_now'] = 'Paga ora';
$string['payingstudents'] = 'Studenti paganti';
$string['payment_cancelled'] = 'Pagamento annullato';
$string['payment_completed'] = 'Pagamento completato';
$string['payment_config_error'] = 'Errore di configurazione del pagamento. Contatta l\'assistenza.';
$string['payment_detail_amount'] = 'Importo';
$string['payment_detail_id'] = 'ID pagamento';
$string['payment_detail_item'] = 'Articolo';
$string['payment_details'] = 'Dettagli pagamento';
$string['payment_failed'] = 'Pagamento fallito. Riprova.';
$string['payment_failed_declined'] = 'Pagamento fallito: carta rifiutata.';
$string['payment_in_progress'] = 'Un pagamento è già in corso. Attendi un momento e riprova.';
$string['payment_not_found'] = 'Record di pagamento non trovato.';
$string['payment_required'] = 'Pagamento richiesto';
$string['payment_required_desc'] = 'L\'accesso a "{$a->item}" richiede un pagamento di {$a->amount} {$a->currency}.';
$string['payment_success_notification'] = 'Pagamento riuscito! Ora hai accesso a questo contenuto.';
$string['payment_successful'] = 'Pagamento riuscito! Reindirizzamento in corso...';
$string['payment_successful_title'] = 'Pagamento riuscito';
$string['paymentreport'] = 'Report pagamenti';
$string['payments'] = 'Pagamenti';
$string['pending'] = 'In attesa';
$string['pluginname'] = 'Restrizione pagamento Stripe';
$string['privacy:metadata:payments'] = 'I record di pagamento Stripe memorizzano dati sui pagamenti effettuati dagli utenti per accedere alle attività del corso.';
$string['privacy:metadata:payments:amount'] = 'L\'importo del pagamento addebitato.';
$string['privacy:metadata:payments:cmid'] = 'L\'attività (modulo del corso) che il pagamento sblocca.';
$string['privacy:metadata:payments:courseid'] = 'Il corso a cui si riferisce il pagamento.';
$string['privacy:metadata:payments:sessionid'] = 'L\'ID sessione Stripe Checkout per la transazione.';
$string['privacy:metadata:payments:status'] = 'Lo stato attuale del pagamento (es. completato, in attesa).';
$string['privacy:metadata:payments:timecreated'] = 'Il momento in cui è stato creato il record di pagamento.';
$string['privacy:metadata:payments:userid'] = 'L\'ID dell\'utente che ha effettuato il pagamento.';
$string['processing'] = 'Elaborazione pagamento...';
$string['redirecting_prefix'] = 'Reindirizzamento automatico tra';
$string['second'] = 'secondo';
$string['seconds'] = 'secondi';
$string['settings_transactions_admin'] = 'Transazioni di pagamento Stripe';
$string['settings_transactions_heading'] = 'Transazioni di pagamento';
$string['settings_transactions_link'] = 'Visualizza tutte le transazioni di pagamento';
$string['status_ok'] = 'ok';
$string['stripe_not_configured'] = 'Stripe non è configurato correttamente. Contatta un amministratore.';
$string['stripe_publishable_key'] = 'Chiave pubblica Stripe';
$string['stripe_publishable_key_desc'] = 'La tua chiave pubblica Stripe dalla dashboard Stripe';
$string['stripe_secret_key'] = 'Chiave segreta Stripe';
$string['stripe_secret_key_desc'] = 'La tua chiave segreta Stripe dalla dashboard Stripe';
$string['stripedashboard'] = '⧉ Dashboard Stripe';
$string['stripelink'] = '🔗 Stripe';
$string['stripepayment:managetransactions'] = 'Gestisci le transazioni di pagamento Stripe';
$string['student'] = 'Studente';
$string['tablenotexist'] = 'La tabella del database {$a} non esiste';
$string['task_cleanup_pending'] = 'Pulizia pagamenti in scadenza';
$string['title'] = 'Pagamento Stripe';
$string['totalpayments'] = 'Pagamenti totali';
$string['totalrevenue'] = 'Ricavi totali';
$string['transactionid'] = 'ID transazione';
$string['transactionsreport'] = 'Transazioni di pagamento Stripe';
$string['unknownactivity'] = 'Attività sconosciuta';
$string['viewinstripe'] = 'Visualizza nella dashboard Stripe';
$string['webhook_already_processed'] = 'Già elaborato';
$string['webhook_empty_payload'] = 'Payload vuoto';
$string['webhook_error'] = 'Errore webhook';
$string['webhook_invalid_payload'] = 'Payload non valido';
$string['webhook_invalid_signature'] = 'Firma non valida';
$string['webhook_method_not_allowed'] = 'Metodo non consentito';
$string['webhook_missing_signature'] = 'Firma Stripe mancante';
$string['webhook_payment_not_completed'] = 'Pagamento non completato';
$string['webhook_secret'] = 'Segreto endpoint webhook';
$string['webhook_secret_desc'] = 'Il segreto dell\'endpoint webhook da Stripe';
$string['webhook_secret_not_configured'] = 'Segreto webhook non configurato';
