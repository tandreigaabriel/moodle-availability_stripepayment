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
 * Spanish language strings for availability_stripepayment.
 *
 * @package    availability_stripepayment
 * @category   string
 * @copyright  2026 Andrei Toma <https://www.tagwebdesign.co.uk>
 * @license    https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
defined('MOODLE_INTERNAL') || die();

$string['accounts_email'] = 'Correo de contabilidad';
$string['accounts_email_desc'] = 'Dirección de correo electrónico para recibir notificaciones internas de pago (los recibos de los estudiantes los envía Stripe automáticamente)';
$string['activity_not_found'] = 'Actividad no encontrada.';
$string['activitypaymentreport'] = 'Ver informe de pagos';
$string['allcourses'] = 'Todos los cursos';
$string['already_paid'] = 'Ya has pagado por esta actividad.';
$string['amount'] = 'Importe';
$string['backtocourse'] = 'Volver al curso';
$string['cancelled'] = 'Cancelado';
$string['clearfilter'] = 'Limpiar filtro';
$string['completed'] = 'Completado';
$string['completedpayments'] = 'Pagos completados';
$string['continue_to_activity'] = 'Continuar a {$a}';
$string['copytransactionid'] = 'Copiar ID de transacción completo';
$string['currency'] = 'Moneda';
$string['description'] = 'Requiere que los estudiantes realicen un pago a través de Stripe antes de acceder a la actividad.';
$string['dot'] = '.';
$string['downloadcsv'] = 'Descargar CSV';
$string['email_accounts_html_footer'] = 'El recibo del cliente ha sido enviado automáticamente por Stripe.';
$string['email_accounts_html_heading'] = '&#x2705; Nuevo pago recibido a través de Stripe.';
$string['email_accounts_html_title'] = 'Nuevo pago recibido';
$string['email_accounts_intro'] = 'Nuevo pago recibido a través de Stripe:';
$string['email_accounts_note'] = 'Nota: Recibo del cliente enviado automáticamente por Stripe.';
$string['email_accounts_row_activity'] = 'Actividad';
$string['email_accounts_row_course'] = 'Curso';
$string['email_accounts_row_date'] = 'Fecha';
$string['email_accounts_subject'] = 'Nuevo pago - {$a}';
$string['email_student_body'] = '¡Tu pago se ha realizado correctamente! Ahora tienes acceso a la actividad.';
$string['email_student_subject'] = 'Pago confirmado - {$a}';
$string['enable'] = 'Activar pagos con Stripe';
$string['enable_desc'] = 'Permite que las actividades requieran pagos de Stripe para el acceso';
$string['error_amount_required'] = 'Por favor, introduce un importe válido mayor que 0.';
$string['error_itemname_required'] = 'Por favor, introduce un nombre para el artículo.';
$string['error_not_configured'] = 'El pago de Stripe no está correctamente configurado.';
$string['expired'] = 'Caducado';
$string['failed'] = 'Fallido';
$string['filteractive'] = 'Filtrado por:';
$string['id'] = 'ID';
$string['invalid_amount'] = 'Importe de pago no válido';
$string['invalid_amount_admin'] = 'Importe de pago no válido configurado. Contacta al administrador.';
$string['invalid_currency_admin'] = 'Moneda no válida configurada. Contacta al administrador.';
$string['itemname'] = 'Nombre del artículo';
$string['itemname_help'] = 'El nombre mostrado a los usuarios durante el pago';
$string['make_payment'] = 'Realizar pago';
$string['managetransactions'] = 'Gestionar transacciones de pago de Stripe';
$string['no_condition_found'] = 'No se encontró ninguna condición de pago de Stripe para esta actividad. Contacta al administrador.';
$string['nopayments'] = 'No se han registrado pagos para esta actividad todavía.';
$string['not_paid'] = 'No has pagado el acceso a esta actividad.';
$string['pay_now'] = 'Pagar ahora';
$string['payingstudents'] = 'Estudiantes que pagan';
$string['payment_cancelled'] = 'Pago cancelado';
$string['payment_completed'] = 'Pago completado';
$string['payment_config_error'] = 'Error de configuración de pago. Por favor, contacta al soporte.';
$string['payment_detail_amount'] = 'Importe';
$string['payment_detail_id'] = 'ID de pago';
$string['payment_detail_item'] = 'Artículo';
$string['payment_details'] = 'Detalles del pago';
$string['payment_failed'] = 'Pago fallido. Por favor, inténtalo de nuevo.';
$string['payment_failed_declined'] = 'Pago fallido: tarjeta rechazada.';
$string['payment_in_progress'] = 'Ya hay un pago en curso. Por favor, espera un momento e inténtalo de nuevo.';
$string['payment_not_found'] = 'Registro de pago no encontrado.';
$string['payment_required'] = 'Pago requerido';
$string['payment_required_desc'] = 'El acceso a "{$a->item}" requiere un pago de {$a->amount} {$a->currency}.';
$string['payment_success_notification'] = '¡Pago exitoso! Ahora tienes acceso a este contenido.';
$string['payment_successful'] = '¡Pago exitoso! Redirigiendo...';
$string['payment_successful_title'] = 'Pago exitoso';
$string['paymentreport'] = 'Informe de pagos';
$string['payments'] = 'Pagos';
$string['pending'] = 'Pendiente';
$string['pluginname'] = 'Restricción de pago con Stripe';
$string['privacy:metadata:payments'] = 'Los registros de pago de Stripe almacenan datos sobre los pagos realizados por los usuarios para acceder a las actividades del curso.';
$string['privacy:metadata:payments:amount'] = 'El importe del pago cobrado.';
$string['privacy:metadata:payments:cmid'] = 'La actividad (módulo del curso) que desbloquea el pago.';
$string['privacy:metadata:payments:courseid'] = 'El curso al que se refiere el pago.';
$string['privacy:metadata:payments:sessionid'] = 'El ID de sesión de Stripe Checkout para la transacción.';
$string['privacy:metadata:payments:status'] = 'El estado actual del pago (p. ej. completado, pendiente).';
$string['privacy:metadata:payments:timecreated'] = 'La hora en que se creó el registro de pago.';
$string['privacy:metadata:payments:userid'] = 'El ID del usuario que realizó el pago.';
$string['processing'] = 'Procesando pago...';
$string['redirecting_prefix'] = 'Redirigiendo automáticamente en';
$string['second'] = 'segundo';
$string['seconds'] = 'segundos';
$string['settings_transactions_admin'] = 'Transacciones de pago de Stripe';
$string['settings_transactions_heading'] = 'Transacciones de pago';
$string['settings_transactions_link'] = 'Ver todas las transacciones de pago';
$string['status_ok'] = 'ok';
$string['stripe_not_configured'] = 'Stripe no está correctamente configurado. Por favor, contacta a un administrador.';
$string['stripe_publishable_key'] = 'Clave publicable de Stripe';
$string['stripe_publishable_key_desc'] = 'Tu clave publicable de Stripe desde el panel de Stripe';
$string['stripe_secret_key'] = 'Clave secreta de Stripe';
$string['stripe_secret_key_desc'] = 'Tu clave secreta de Stripe desde el panel de Stripe';
$string['stripedashboard'] = '⧉ Panel de Stripe';
$string['stripelink'] = '🔗 Stripe';
$string['stripepayment:managetransactions'] = 'Gestionar transacciones de pago de Stripe';
$string['student'] = 'Estudiante';
$string['tablenotexist'] = 'La tabla de base de datos {$a} no existe';
$string['task_cleanup_pending'] = 'Limpiar pagos pendientes caducados';
$string['title'] = 'Pago con Stripe';
$string['totalpayments'] = 'Total de pagos';
$string['totalrevenue'] = 'Ingresos totales';
$string['transactionid'] = 'ID de transacción';
$string['transactionsreport'] = 'Transacciones de pago de Stripe';
$string['unknownactivity'] = 'Actividad desconocida';
$string['viewinstripe'] = 'Ver en el panel de Stripe';
$string['webhook_already_processed'] = 'Ya procesado';
$string['webhook_empty_payload'] = 'Payload vacío';
$string['webhook_error'] = 'Error de webhook';
$string['webhook_invalid_payload'] = 'Payload no válido';
$string['webhook_invalid_signature'] = 'Firma no válida';
$string['webhook_method_not_allowed'] = 'Método no permitido';
$string['webhook_missing_signature'] = 'Firma de Stripe ausente';
$string['webhook_payment_not_completed'] = 'Pago no completado';
$string['webhook_secret'] = 'Secreto del endpoint de webhook';
$string['webhook_secret_desc'] = 'El secreto del endpoint de webhook de Stripe';
$string['webhook_secret_not_configured'] = 'Secreto de webhook no configurado';
