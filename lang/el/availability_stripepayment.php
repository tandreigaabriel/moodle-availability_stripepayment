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
 * Greek language strings for availability_stripepayment.
 *
 * @package    availability_stripepayment
 * @category   string
 * @copyright  2026 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$string['accounts_email'] = 'Email λογιστηρίου';
$string['accounts_email_desc'] = 'Διεύθυνση email για λήψη εσωτερικών ειδοποιήσεων πληρωμής (οι αποδείξεις φοιτητών αποστέλλονται αυτόματα από το Stripe)';
$string['activity_not_found'] = 'Η δραστηριότητα δεν βρέθηκε.';
$string['activitypaymentreport'] = 'Προβολή αναφοράς πληρωμών';
$string['allcourses'] = 'Όλα τα μαθήματα';
$string['already_paid'] = 'Έχετε ήδη πληρώσει για αυτή τη δραστηριότητα.';
$string['amount'] = 'Ποσό';
$string['backtocourse'] = 'Επιστροφή στο μάθημα';
$string['cancelled'] = 'Ακυρώθηκε';
$string['clearfilter'] = 'Εκκαθάριση φίλτρου';
$string['completed'] = 'Ολοκληρώθηκε';
$string['completedpayments'] = 'Ολοκληρωμένες πληρωμές';
$string['continue_to_activity'] = 'Συνέχεια στο {$a}';
$string['copytransactionid'] = 'Αντιγραφή πλήρους ID συναλλαγής';
$string['currency'] = 'Νόμισμα';
$string['description'] = 'Απαιτεί από τους φοιτητές να πραγματοποιήσουν πληρωμή μέσω Stripe πριν αποκτήσουν πρόσβαση στη δραστηριότητα.';
$string['dot'] = '.';
$string['downloadcsv'] = 'Λήψη CSV';
$string['email_accounts_html_footer'] = 'Η απόδειξη πελάτη εστάλη αυτόματα από το Stripe.';
$string['email_accounts_html_heading'] = '&#x2705; Νέα πληρωμή ελήφθη μέσω Stripe.';
$string['email_accounts_html_title'] = 'Νέα πληρωμή ελήφθη';
$string['email_accounts_intro'] = 'Νέα πληρωμή ελήφθη μέσω Stripe:';
$string['email_accounts_note'] = 'Σημείωση: Η απόδειξη πελάτη εστάλη αυτόματα από το Stripe.';
$string['email_accounts_row_activity'] = 'Δραστηριότητα';
$string['email_accounts_row_course'] = 'Μάθημα';
$string['email_accounts_row_date'] = 'Ημερομηνία';
$string['email_accounts_subject'] = 'Νέα πληρωμή - {$a}';
$string['email_student_body'] = 'Η πληρωμή σας ήταν επιτυχής! Έχετε πλέον πρόσβαση στη δραστηριότητα.';
$string['email_student_subject'] = 'Επιβεβαίωση πληρωμής - {$a}';
$string['enable'] = 'Ενεργοποίηση πληρωμών Stripe';
$string['enable_desc'] = 'Επιτρέψτε στις δραστηριότητες να απαιτούν πληρωμές Stripe για πρόσβαση';
$string['error_amount_required'] = 'Εισαγάγετε ένα έγκυρο ποσό μεγαλύτερο από 0.';
$string['error_itemname_required'] = 'Εισαγάγετε ένα όνομα αντικειμένου.';
$string['error_not_configured'] = 'Η πληρωμή Stripe δεν έχει ρυθμιστεί σωστά.';
$string['expired'] = 'Έληξε';
$string['failed'] = 'Απέτυχε';
$string['filteractive'] = 'Φιλτράρισμα κατά:';
$string['id'] = 'ID';
$string['invalid_amount'] = 'Μη έγκυρο ποσό πληρωμής';
$string['invalid_amount_admin'] = 'Ρυθμίστηκε μη έγκυρο ποσό πληρωμής. Επικοινωνήστε με τον διαχειριστή.';
$string['invalid_currency_admin'] = 'Ρυθμίστηκε μη έγκυρο νόμισμα. Επικοινωνήστε με τον διαχειριστή.';
$string['itemname'] = 'Όνομα αντικειμένου';
$string['itemname_help'] = 'Το όνομα που εμφανίζεται στους χρήστες κατά την πληρωμή';
$string['make_payment'] = 'Πραγματοποίηση πληρωμής';
$string['managetransactions'] = 'Διαχείριση συναλλαγών πληρωμής Stripe';
$string['no_condition_found'] = 'Δεν βρέθηκε συνθήκη πληρωμής Stripe για αυτή τη δραστηριότητα. Επικοινωνήστε με τον διαχειριστή.';
$string['nopayments'] = 'Δεν έχουν καταγραφεί πληρωμές για αυτή τη δραστηριότητα.';
$string['not_paid'] = 'Δεν έχετε πληρώσει για πρόσβαση σε αυτή τη δραστηριότητα.';
$string['pay_now'] = 'Πληρώστε τώρα';
$string['payingstudents'] = 'Φοιτητές που πλήρωσαν';
$string['payment_cancelled'] = 'Η πληρωμή ακυρώθηκε';
$string['payment_completed'] = 'Η πληρωμή ολοκληρώθηκε';
$string['payment_config_error'] = 'Σφάλμα διαμόρφωσης πληρωμής. Επικοινωνήστε με την υποστήριξη.';
$string['payment_detail_amount'] = 'Ποσό';
$string['payment_detail_id'] = 'ID πληρωμής';
$string['payment_detail_item'] = 'Αντικείμενο';
$string['payment_details'] = 'Λεπτομέρειες πληρωμής';
$string['payment_failed'] = 'Η πληρωμή απέτυχε. Παρακαλώ δοκιμάστε ξανά.';
$string['payment_failed_declined'] = 'Η πληρωμή απέτυχε: η κάρτα απορρίφθηκε.';
$string['payment_in_progress'] = 'Μια πληρωμή βρίσκεται ήδη σε εξέλιξη. Περιμένετε λίγο και δοκιμάστε ξανά.';
$string['payment_not_found'] = 'Η εγγραφή πληρωμής δεν βρέθηκε.';
$string['payment_required'] = 'Απαιτείται πληρωμή';
$string['payment_required_desc'] = 'Η πρόσβαση στο "{$a->item}" απαιτεί πληρωμή {$a->amount} {$a->currency}.';
$string['payment_success_notification'] = 'Η πληρωμή ήταν επιτυχής! Έχετε πλέον πρόσβαση σε αυτό το περιεχόμενο.';
$string['payment_successful'] = 'Η πληρωμή ήταν επιτυχής! Ανακατεύθυνση...';
$string['payment_successful_title'] = 'Επιτυχής πληρωμή';
$string['paymentreport'] = 'Αναφορά πληρωμών';
$string['payments'] = 'Πληρωμές';
$string['pending'] = 'Εκκρεμεί';
$string['pluginname'] = 'Περιορισμός πληρωμής Stripe';
$string['privacy:metadata:payments'] = 'Τα αρχεία πληρωμής Stripe αποθηκεύουν δεδομένα σχετικά με πληρωμές που πραγματοποιήθηκαν από χρήστες για πρόσβαση σε δραστηριότητες μαθήματος.';
$string['privacy:metadata:payments:amount'] = 'Το ποσό πληρωμής που χρεώθηκε.';
$string['privacy:metadata:payments:cmid'] = 'Η δραστηριότητα (ενότητα μαθήματος) που ξεκλειδώνει η πληρωμή.';
$string['privacy:metadata:payments:courseid'] = 'Το μάθημα στο οποίο αναφέρεται η πληρωμή.';
$string['privacy:metadata:payments:sessionid'] = 'Το ID συνεδρίας Stripe Checkout για τη συναλλαγή.';
$string['privacy:metadata:payments:status'] = 'Η τρέχουσα κατάσταση της πληρωμής (π.χ. ολοκληρώθηκε, εκκρεμεί).';
$string['privacy:metadata:payments:timecreated'] = 'Η ώρα δημιουργίας της εγγραφής πληρωμής.';
$string['privacy:metadata:payments:userid'] = 'Το ID του χρήστη που πραγματοποίησε την πληρωμή.';
$string['processing'] = 'Επεξεργασία πληρωμής...';
$string['redirecting_prefix'] = 'Αυτόματη ανακατεύθυνση σε';
$string['second'] = 'δευτερόλεπτο';
$string['seconds'] = 'δευτερόλεπτα';
$string['settings_transactions_admin'] = 'Συναλλαγές πληρωμής Stripe';
$string['settings_transactions_heading'] = 'Συναλλαγές πληρωμής';
$string['settings_transactions_link'] = 'Προβολή όλων των συναλλαγών πληρωμής';
$string['status_ok'] = 'ok';
$string['stripe_not_configured'] = 'Το Stripe δεν έχει ρυθμιστεί σωστά. Επικοινωνήστε με έναν διαχειριστή.';
$string['stripe_publishable_key'] = 'Δημοσιεύσιμο κλειδί Stripe';
$string['stripe_publishable_key_desc'] = 'Το δημοσιεύσιμο κλειδί Stripe από τον πίνακα ελέγχου Stripe';
$string['stripe_secret_key'] = 'Μυστικό κλειδί Stripe';
$string['stripe_secret_key_desc'] = 'Το μυστικό κλειδί Stripe από τον πίνακα ελέγχου Stripe';
$string['stripedashboard'] = '⧉ Πίνακας ελέγχου Stripe';
$string['stripelink'] = '🔗 Stripe';
$string['stripepayment:managetransactions'] = 'Διαχείριση συναλλαγών πληρωμής Stripe';
$string['student'] = 'Φοιτητής';
$string['tablenotexist'] = 'Ο πίνακας βάσης δεδομένων {$a} δεν υπάρχει';
$string['task_cleanup_pending'] = 'Εκκαθάριση ληξιπρόθεσμων εκκρεμών πληρωμών';
$string['title'] = 'Πληρωμή Stripe';
$string['totalpayments'] = 'Συνολικές πληρωμές';
$string['totalrevenue'] = 'Συνολικά έσοδα';
$string['transactionid'] = 'ID συναλλαγής';
$string['transactionsreport'] = 'Συναλλαγές πληρωμής Stripe';
$string['unknownactivity'] = 'Άγνωστη δραστηριότητα';
$string['viewinstripe'] = 'Προβολή στον πίνακα ελέγχου Stripe';
$string['webhook_already_processed'] = 'Ήδη επεξεργάστηκε';
$string['webhook_empty_payload'] = 'Κενό payload';
$string['webhook_error'] = 'Σφάλμα webhook';
$string['webhook_invalid_payload'] = 'Μη έγκυρο payload';
$string['webhook_invalid_signature'] = 'Μη έγκυρη υπογραφή';
$string['webhook_method_not_allowed'] = 'Η μέθοδος δεν επιτρέπεται';
$string['webhook_missing_signature'] = 'Λείπει η υπογραφή Stripe';
$string['webhook_payment_not_completed'] = 'Η πληρωμή δεν ολοκληρώθηκε';
$string['webhook_secret'] = 'Μυστικό endpoint webhook';
$string['webhook_secret_desc'] = 'Το μυστικό του endpoint webhook από το Stripe';
$string['webhook_secret_not_configured'] = 'Το μυστικό webhook δεν έχει ρυθμιστεί';
