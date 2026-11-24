<?php
/**
 * This file is part of Moodle - https://moodle.org/
 *
 * Moodle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * Moodle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Moodle. If not, see <https://www.gnu.org/licenses/>.
 *
 * File documentation for lang/de/local_supportdesk.php.
 *
 * @package   local_supportdesk
 * @copyright 2026 Andreas Giesen, 108design
 * @license   https://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
// SPDX-License-Identifier: GPL-3.0-or-later
// Copyright 2026 Andreas Giesen, 108design.
defined('MOODLE_INTERNAL') || die();

$string['general'] = 'Allgemein';
$string['deletion_failed'] = 'Der Support-Bereich konnte nicht gelöscht werden. Prüfen Sie, ob noch Tickets zugeordnet sind.';
$string['update_failed'] = 'Der Support-Bereich konnte nicht aktualisiert werden.';
$string['access_denied'] = 'Sie dürfen dieses Ticket nicht ansehen.';
$string['action_needed_hint'] = 'Der Support hat geantwortet. Bitte antworten Sie auf das Ticket.';
$string['actions'] = 'Aktionen';
$string['add_department'] = 'Support-Bereiche';
$string['add_new_department'] = 'Neuer Support-Bereich';
$string['add_reply_heading'] = 'Antwort schreiben';
$string['add_ticket'] = 'Neues Ticket';
$string['add_to_navbar'] = 'In der Navigation anzeigen';
$string['add_to_navbar_desc'] = 'Zeigt den Support Desk in der Moodle-Hauptnavigation an.';
$string['admin_alert_body'] = '<p>Dringendes Support-Ticket von {$a->firstname}: {$a->title}</p><p>Support-Bereich: {$a->category}</p><p><a href="{$a->url}">Ticket ansehen</a></p>';
$string['admin_alert_subject'] = 'Dringendes Ticket #{$a->id}: {$a->title}';
$string['admin_only_label'] = 'Support-Verwaltung';
$string['all_rights_reserved'] = 'Ursprünglicher Autor';
$string['all_tickets'] = 'Support-Tickets';
$string['all_tickets_stats'] = 'Ticket-Übersicht';
$string['allowed_file_types'] = 'Bis zu 10 Dateien, jeweils maximal 5 MB';
$string['assign_user'] = 'Ticket zuweisen';
$string['assign_user_label'] = 'Zuständige Person zuweisen';
$string['assignable_roles'] = 'Zuweisbare Support-Rollen';
$string['assignable_roles_desc'] = 'Systemrollen, deren Mitglieder mit Support-Berechtigung Tickets erhalten können.';
$string['assigned_to'] = 'Zuständige Person';
$string['assigned_to_label'] = 'Zugewiesen an';
$string['assigned_to_me'] = 'Mir zugewiesen';
$string['assigned_user'] = 'Zuständige Person';
$string['attach_files_optional'] = 'Anhänge (optional)';
$string['attached_files'] = 'Anhänge';
$string['attachments'] = 'Anhänge';
$string['attachments_heading'] = 'Ticket-Anhänge';
$string['attention_required'] = 'Bearbeitung erforderlich';
$string['audio_preview'] = 'Aufnahme anhören';
$string['awaiting_me_label'] = 'Wartet auf meine Antwort';
$string['back_to_home'] = 'Zur Übersicht';
$string['browser_no_audio'] = 'Ihr Browser unterstützt die Audiowiedergabe nicht.';
$string['cancel'] = 'Abbrechen';
$string['category'] = 'Bereich';
$string['category_title'] = 'Support-Bereich';
$string['categoryinuse'] = 'Verschieben Sie die Tickets zunächst in einen anderen Support-Bereich.';
$string['change_category_label'] = 'Support-Bereich ändern';
$string['change_status_label'] = 'Status ändern';
$string['choose_rating'] = 'Bewertung auswählen';
$string['click_to_download'] = 'Herunterladen';
$string['click_to_record'] = 'Zum Aufnehmen auf das Mikrofon klicken';
$string['click_to_upload'] = 'Dateien auswählen';
$string['close'] = 'Schließen';
$string['closed_label'] = 'Geschlossen';
$string['closed_tickets'] = 'Geschlossene Tickets';
$string['conversation'] = 'Gespräch';
$string['copyright_label'] = 'GNU GPL v3 oder später';
$string['create_ticket'] = 'Ticket erstellen';
$string['created_at'] = 'Erstellt am';
$string['created_by'] = 'Erstellt von';
$string['creation_failed'] = 'Der Support-Bereich konnte nicht erstellt werden.';
$string['current_year_label'] = 'Aktuelles Jahr';
$string['customer'] = 'Kundin/Kunde';
$string['default_email_placeholder'] = 'noreply@Ihr-moodle.de';
$string['delete'] = 'Löschen';
$string['delete_department'] = 'Support-Bereich löschen';
$string['delete_department_confirm'] = 'Möchten Sie diesen Support-Bereich löschen? Bereiche mit Tickets können nicht gelöscht werden.';
$string['deleteduser'] = 'Gelöschtes Kundenkonto';
$string['department'] = 'Support-Bereich';
$string['department_created'] = 'Support-Bereich erstellt.';
$string['department_deleted'] = 'Support-Bereich gelöscht.';
$string['department_updated'] = 'Support-Bereich aktualisiert.';
$string['description'] = 'Beschreibung';
$string['description_optional'] = 'Beschreibung (optional)';
$string['description_placeholder'] = 'Beschreiben Sie Ihr Anliegen möglichst genau.';
$string['disabled'] = 'Der Support Desk ist deaktiviert.';
$string['drag_drop_hint'] = 'Dateien hier ablegen oder zum Auswählen anklicken';
$string['edit_department'] = 'Support-Bereich bearbeiten';
$string['email_confirm_body'] = '<p>Hallo {$a->firstname},</p><p>Ihr Ticket #{$a->id} zu {$a->title} ist eingegangen.</p><p>Support-Bereich: {$a->category}<br>Status: {$a->status}<br>Datum: {$a->date}</p><p><a href="{$a->url}">Ticket ansehen</a></p><p>Ihr Support-Team von {$a->site}</p>';
$string['email_confirm_body_plain'] = 'Hallo {$a->firstname},
Ihr Ticket #{$a->id} zu {$a->title} ist eingegangen.
Status: {$a->status}
{$a->url}
Ihr Support-Team von {$a->site}';
$string['email_confirm_subject'] = 'Ticket #{$a->id} eingegangen: {$a->title}';
$string['enable'] = 'Support Desk aktivieren';
$string['enable_desc'] = 'Aktiviert Ticket-Seiten und Support Desk-Funktionen.';
$string['erasedticket'] = 'Gelöschtes Kundenticket';
$string['error'] = 'Fehler';
$string['existing_departments'] = 'Vorhandene Support-Bereiche';
$string['experience_question'] = 'Wie zufrieden sind Sie mit dem Support?';
$string['feedback_comment_label'] = 'Kommentar (optional)';
$string['feedback_help'] = 'Ihre Bewertung hilft uns, den Support zu verbessern.';
$string['feedback_placeholder'] = 'Zusätzlicher Kommentar (optional)';
$string['feedback_submitted'] = 'Ihre Bewertung';
$string['filesselected'] = '{$a} Dateien ausgewählt';
$string['form_instruction'] = 'Beschreiben Sie Ihr Anliegen und wählen Sie den passenden Support-Bereich.';
$string['happy_to_help_hint'] = 'Ihr Anliegen wurde gelöst.';
$string['header_subtitle'] = 'Wir helfen Ihnen bei Ihrem Anliegen.';
$string['id'] = 'ID';
$string['internal_note_placeholder'] = 'Notiz für das Support-Team';
$string['internal_notes_heading'] = 'Interne Team-Notizen';
$string['ip_address'] = 'IP-Adresse';
$string['live_stats_heading'] = 'Ticket-Übersicht';
$string['log_assigned'] = 'Ticket zugewiesen an: {$a}';
$string['log_category_changed'] = 'Support-Bereich geändert in: {$a}';
$string['log_feedback_submitted'] = 'Bewertung abgegeben: {$a} von 5';
$string['log_internal_note_added'] = 'Interne Notiz hinzugefügt';
$string['log_replied'] = 'Antwort hinzugefügt';
$string['log_status_changed'] = '{$a->user} hat den Status von {$a->old} auf {$a->new} geändert.';
$string['log_status_changed_from_to'] = '{$a->user} hat den Status von {$a->old} auf {$a->new} geändert.';
$string['messageprovider:admin_urgent_alert'] = 'Dringende Support-Tickets';
$string['messageprovider:ticket_confirmation'] = 'Bestätigung der Ticketerstellung';
$string['mic_access_denied'] = 'Die Aufnahme konnte nicht gestartet werden. Bitte prüfen Sie die Mikrofonfreigabe.';
$string['my_assignment'] = 'Zuständige Person';
$string['my_summary_heading'] = 'Meine Ticket-Übersicht';
$string['my_tickets'] = 'Meine Tickets';
$string['my_tickets_desc'] = 'Hier finden Sie Ihre Support-Anfragen.';
$string['my_tickets_label'] = 'Meine Tickets';
$string['next'] = 'Weiter';
$string['no_departments'] = 'Keine Support-Bereiche vorhanden.';
$string['no_internal_notes'] = 'Noch keine internen Notizen.';
$string['no_recent_tickets'] = 'Keine weiteren Tickets.';
$string['no_replies_hint'] = 'Sie können unten eine Antwort hinzufügen.';
$string['no_replies_message'] = 'Es gibt noch keine Antworten.';
$string['no_search_results'] = 'Keine passenden Tickets auf dieser Seite.';
$string['no_tickets'] = 'Keine Tickets vorhanden.';
$string['no_tickets_desc'] = 'Sie haben noch keine Tickets erstellt.';
$string['no_tickets_message'] = 'Es gibt noch keine Tickets.';
$string['no_tickets_title'] = 'Keine Tickets gefunden';
$string['nopermission'] = 'Zugriff verweigert';
$string['nopermission_desc'] = 'Sie dürfen dieses Ticket nicht ansehen.';
$string['of'] = 'von';
$string['open_label'] = 'Offen';
$string['open_tickets'] = 'Offene Tickets';
$string['pagination_label'] = 'Ticketseiten';
$string['pluginname'] = 'Support Desk';
$string['previous'] = 'Zurück';
$string['primary_color'] = 'Primärfarbe';
$string['primary_color_desc'] = 'Primärfarbe im Support Desk.';
$string['priority'] = 'Priorität';
$string['priority_high'] = 'Hoch';
$string['priority_low'] = 'Niedrig';
$string['priority_medium'] = 'Normal';
$string['priority_urgent'] = 'Dringend';
$string['privacy:metadata:categories'] = 'Support-Bereiche und die erstellende Person.';
$string['privacy:metadata:comments'] = 'Interne Notizen.';
$string['privacy:metadata:feedback'] = 'Ticket-Bewertungen.';
$string['privacy:metadata:field'] = 'Mit einer Person verknüpfte Support-Daten.';
$string['privacy:metadata:files'] = 'Dateianhänge von Tickets und Antworten.';
$string['privacy:metadata:logs'] = 'Ticket-Verlauf.';
$string['privacy:metadata:replies'] = 'Antworten auf Tickets.';
$string['privacy:metadata:tickets'] = 'Tickets und ihre Zuordnung zu Kundinnen und Kunden sowie Support-Mitarbeitenden.';
$string['privacy:metadata:tickets:content'] = 'Inhalt und Beschreibung des Tickets.';
$string['privacy:metadata:tickets:created_at'] = 'Zeitpunkt der Ticketerstellung.';
$string['privacy:metadata:tickets:title'] = 'Betreff des Tickets.';
$string['privacy:metadata:tickets:userid'] = 'Kennung der Person, die das Ticket erstellt hat.';
$string['quick_tip_label'] = 'Hinweis';
$string['rating_label'] = 'Bewertung';
$string['ready'] = 'Aufnahme bereit';
$string['recent_tickets_heading'] = 'Weitere Tickets dieser Person';
$string['record_voice_note'] = 'Sprachnachricht aufnehmen';
$string['recording_finished'] = 'Aufnahme abgeschlossen';
$string['recording_now'] = 'Aufnahme läuft …';
$string['recording_unsupported'] = 'Ihr Browser unterstützt keine Mikrofonaufnahme. Sie können eine Audiodatei als Anhang hochladen.';
$string['remove_file'] = 'Entfernen';
$string['reopen_ticket_button'] = 'Ticket wieder öffnen';
$string['replies_heading'] = 'Gespräch';
$string['reply'] = 'Antwort';
$string['required_fields'] = 'Mit * markierte Felder sind erforderlich.';
$string['resolved_label'] = 'Gelöst';
$string['return_home'] = 'Zur Übersicht';
$string['rolecollision'] = 'Der Rollen-Kurzname supportdeskagent wird bereits verwendet. Die Installation hat diese Rolle nicht verändert.';
$string['rolespecialist'] = 'Support';
$string['rolespecialistdescription'] = 'Bearbeitet Support-Tickets, Antworten, Zuweisungen und Anhänge auf Systemebene.';
$string['save_department'] = 'Support-Bereich speichern';
$string['save_note'] = 'Notiz speichern';
$string['search_page_hint'] = 'Die Suche filtert die Tickets auf dieser Seite.';
$string['search_placeholder'] = 'ID oder Betreff suchen';
$string['search_user_placeholder'] = 'Name, E-Mail oder Nutzername suchen';
$string['secondary_color'] = 'Sekundärfarbe';
$string['secondary_color_desc'] = 'Sekundärfarbe im Support Desk.';
$string['select_department_hint'] = 'Support-Bereich auswählen';
$string['select_priority_hint'] = 'Priorität auswählen';
$string['send_reply_button'] = 'Antwort senden';
$string['send_ticket'] = 'Ticket senden';
$string['sending'] = 'Wird gesendet …';
$string['showing'] = 'Angezeigt';
$string['sorry_no_ticket'] = 'Dieses Ticket wurde nicht gefunden:';
$string['start_new_ticket_btn'] = 'Ticket erstellen';
$string['start_recording'] = 'Aufnahme starten';
$string['start_reply'] = 'Sie können unten antworten.';
$string['status'] = 'Status';
$string['status_admin_reply'] = 'Support hat geantwortet';
$string['status_adminreply'] = 'Support hat geantwortet';
$string['status_assigned'] = 'Zugewiesen';
$string['status_closed'] = 'Geschlossen';
$string['status_in_progress'] = 'In Bearbeitung';
$string['status_open'] = 'Offen';
$string['status_pending'] = 'Ausstehend';
$string['status_resolved'] = 'Gelöst';
$string['status_student_reply'] = 'Kundin/Kunde hat geantwortet';
$string['status_studentreply'] = 'Kundin/Kunde hat geantwortet';
$string['status_urgent'] = 'Dringend';
$string['stop_before_sending'] = 'Bitte stoppen Sie die Aufnahme, bevor Sie das Ticket oder die Antwort senden.';
$string['stop_recording'] = 'Aufnahme stoppen';
$string['student'] = 'Kunde';
$string['student_dashboard_tip'] = 'Sie finden Ihre Anfragen in der Ticket-Übersicht.';
$string['submit'] = 'Ticket erstellen';
$string['submit_feedback'] = 'Bewertung senden';
$string['submit_reply'] = 'Antwort senden';
$string['success'] = 'Erfolgreich';
$string['support_departments'] = 'Support-Bereiche';
$string['support_email'] = 'Support-E-Mail';
$string['support_email_desc'] = 'Empfängerfolge für Support-Benachrichtigungen: Bereichsteam → ausdrücklich zuständige Person im Ticket → diese Fallback-Adresse. Ohne aktivierte Zusatzkopie wird die Adresse ausschließlich als Fallback verwendet. Vorgabe ist der in Moodle eingestellte Supportkontakt, sonst leer. Eine Fallback-Adresse zu hinterlegen wird empfohlen.';
$string['support_team'] = 'Support';
$string['supportdesk:addcategory'] = 'Support-Bereiche verwalten';
$string['supportdesk:addticket'] = 'Support-Tickets erstellen';
$string['supportdesk:download'] = 'Berechtigte Ticket-Anhänge herunterladen';
$string['supportdesk:manageticket'] = 'Alle Support-Tickets verwalten und zuweisen';
$string['supportdesk:specialist'] = 'Support-Tickets bearbeiten und dringende Meldungen erhalten';
$string['supportdesk:viewownoverviews'] = 'Eigene Ticket-Übersicht ansehen';
$string['supportdesk:viewticket'] = 'Berechtigte Tickets ansehen';
$string['system_name'] = 'Name des Supportdesks';
$string['system_name_desc'] = 'Angezeigter Name des Ticket-Systems.';
$string['ticket_department_label'] = 'Support-Bereich';
$string['ticket_description_label'] = 'Anliegen';
$string['ticket_details_heading'] = 'Ticketdetails';
$string['ticket_id_label'] = 'Ticketnummer';
$string['ticket_information'] = 'Ticketinformationen';
$string['ticket_log'] = 'Verlauf';
$string['ticket_management'] = 'Ticket verwalten';
$string['ticket_not_found'] = 'Dieses Ticket wurde nicht gefunden.';
$string['ticket_priority_label'] = 'Priorität';
$string['ticket_status_label'] = 'Aktueller Status';
$string['ticket_title'] = 'Betreff';
$string['ticket_title_help'] = 'Beschreiben Sie Ihr Anliegen mit einem kurzen Betreff.';
$string['ticket_title_label'] = 'Betreff';
$string['tickets_count'] = 'Tickets';
$string['ticketsystem'] = 'Ticketsystem';
$string['title'] = 'Betreff';
$string['title_placeholder'] = 'Worum geht es?';
$string['to'] = 'bis';
$string['tooltip_category_hint'] = 'Wählen Sie den passenden Support-Bereich.';
$string['tooltip_desc_hint'] = 'Beschreiben Sie Ihr Anliegen und gegebenenfalls Fehlermeldungen.';
$string['tooltip_priority_hint'] = 'Wählen Sie die Dringlichkeit.';
$string['tooltip_title_hint'] = 'Geben Sie einen kurzen Betreff ein.';
$string['tooltip_upload_hint'] = 'Dateien bis 5 MB beziehungsweise dem niedrigeren Website-Limit.';
$string['tooltip_voice_hint'] = 'Nehmen Sie Ihr Anliegen mit dem Mikrofon auf.';
$string['total'] = 'Insgesamt';
$string['total_tickets'] = 'Tickets insgesamt';
$string['total_tickets_label'] = 'Tickets insgesamt';
$string['unassigned'] = 'Nicht zugewiesen';
$string['under_review_label'] = 'In Bearbeitung';
$string['unknown_user'] = 'Unbekannte Person';
$string['update_category_button'] = 'Support-Bereich ändern';
$string['update_status_button'] = 'Änderungen speichern';
$string['urgentnotification'] = 'Dringendes Support-Ticket';
$string['user_name_label'] = 'Erstellt von';
$string['view'] = 'Vorschau';
$string['view_profile'] = 'Profil ansehen';
$string['view_ticket'] = 'Ticket ansehen';
$string['viewticket'] = 'Ticket ansehen';
$string['visit_my_portfolio'] = 'Lizenz ansehen';
$string['we_are_working_hint'] = 'Wir bearbeiten Ihr Anliegen.';
$string['welcome_message'] = 'Support Desk';
$string['write_your_reply'] = 'Ihre Antwort';
$string['writereply'] = 'Ihre Antwort';
$string['badge_color'] = 'Badge-Farbe';
$string['badge_color_help'] = 'Diese Farbe kennzeichnet den Support-Bereich in Listen und Tickets.';
$string['invalidbadgecolor'] = 'Bitte eine gültige Farbe im Format #RRGGBB wählen.';
$string['choose_files'] = 'Dateien auswählen';
$string['date_format'] = '%d. %b %Y, %H:%M';
$string['stats_total_one'] = 'Ticket insgesamt';
$string['stats_total_many'] = 'Tickets insgesamt';
$string['stats_open_one'] = 'offenes Ticket';
$string['stats_open_many'] = 'offene Tickets';
$string['stats_closed_one'] = 'geschlossenes Ticket';
$string['stats_closed_many'] = 'geschlossene Tickets';
$string['department_column'] = 'Bereich';
$string['assignee_column'] = 'Zuständig';
$string['paste_image_hint'] = 'Screenshots hier mit Strg+V / ⌘V als Anhang einfügen.';
$string['visitoraccess'] = 'Besucherzugang per Magic Link';
$string['visitoraccess_desc'] = 'Standardmäßig aus. Besucher/innen können ihre E-Mail bestätigen und danach ein Ticket erstellen oder ihre Tickets öffnen. Erfordert das aktivierte, nutzbare Plugin 108design Magic Link (auth_magiclink) und erlaubte Kontoerstellung. Bestehende Moodle-Anmeldung bleibt verfügbar.';
$string['visitor_missing'] = 'Installieren Sie das optionale Plugin Magic Link Authentication (auth_magiclink), um den Besucherzugang nach E-Mail-Bestätigung auszuwählen.';
$string['visitor_authdisabled'] = 'Aktivieren Sie Magic Link in der Moodle-Authentifizierung.';
$string['visitor_activationrequired'] = 'Magic Link benötigt eine aktive Freischaltung.';
$string['visitor_newaccountsdisabled'] = 'Erlauben Sie die Kontoerstellung in Magic Link und in den Moodle-Authentifizierungseinstellungen.';
$string['visitor_unavailable'] = 'Magic Link ist derzeit nicht nutzbar. Der Besucherzugang ist inaktiv.';
$string['visitor_heading'] = 'Ihr Anliegen an den Support';
$string['visitor_intro'] = 'Bestätigen Sie Ihre E-Mail-Adresse per Magic Link. Danach können Sie Ihr Ticket erstellen oder Ihre bisherigen Tickets aufrufen.';
$string['visitor_continue'] = 'Mit E-Mail fortfahren';
$string['visitor_account_hint'] = 'Sie brauchen kein Passwort. Falls noch kein Konto besteht, erstellt Moodle nach der E-Mail-Bestätigung ein Konto mit Ihrem Vor- und Nachnamen.';
$string['visitor_standardlogin'] = 'Mit bestehendem Moodle-Zugang anmelden';
$string['department_team'] = 'Support-Team';
$string['department_team_help'] = 'Wählen Sie aktive Personen mit der systemweiten Rolle Support. Sie erhalten die Meldungen dieses Bereichs. Ist die Teamtrennung aktiv, können sie auch die Tickets des Bereichs bearbeiten.';
$string['department_team_empty'] = 'Noch keine auswählbaren Personen. Weisen Sie den gewünschten Personen zunächst die Rolle Support auf Systemebene zu.';
$string['invalidteammember'] = 'Das Team enthält eine Person ohne aktive systemweite Support-Rolle.';
$string['notify_new'] = 'Neues Support-Ticket #{$a->id}: {$a->title}';
$string['notify_reply'] = 'Neue Antwort zu Ticket #{$a->id}: {$a->title}';
$string['notify_moved'] = 'Ticket #{$a->id} dem Bereich zugewiesen: {$a->title}';
$string['notify_body'] = 'Ticket #{$a->id}: {$a->title}
Bereich: {$a->category}

Ticket öffnen: {$a->url}';
$string['messageprovider:department_ticket'] = 'Support-Team: Tickets im zugeordneten Bereich';
$string['messageprovider:ticket_reply'] = 'Antworten auf eigene Support-Tickets';
$string['privacy:metadata:members'] = 'Zuordnung von Support-Nutzer/innen zu Benachrichtigungsteams.';
$string['privacy:metadata:messages'] = 'Ticketbezogene Benachrichtigungen über Moodle-Nachrichten.';
$string['privacy:metadata:fallback_email'] = 'Ticketnummer, Betreff, Bereich und Ticket-Link werden bei fehlender Mitarbeiterzuordnung oder aktivierter Zusatzkopie an die konfigurierte Support-Kontaktadresse gesendet.';
$string['image_gallery'] = 'Ticketbilder';
$string['image_previous'] = 'Vorheriges Bild';
$string['image_next'] = 'Nächstes Bild';
$string['image_zoom'] = 'Bild vergrößern';
$string['image_error'] = 'Das Bild konnte nicht geladen werden.';
$string['image_thumbnail'] = 'Bild {$a} anzeigen';
$string['allowvoicenotes'] = 'Sprachnachrichten erlauben';
$string['allowvoicenotes_desc'] = 'Erlaubt Mikrofonaufnahmen in neuen Tickets und Antworten. Standardmäßig aus. Vorhandene Sprachnachrichten bleiben abspielbar; normale Dateianhänge sind weiterhin möglich.';
$string['voicenotes_disabled'] = 'Sprachnachrichten sind derzeit deaktiviert.';
$string['copyallsupportemail'] = 'Alle Tickets zusätzlich an diese Adresse schicken';
$string['copyallsupportemail_desc'] = 'Standardmäßig aus. Wenn aktiviert, erhält die oben eingestellte Supportadresse zusätzlich alle Ticketbenachrichtigungen (neue Tickets, Antworten und Bereichswechsel), auch wenn ein Bereichsteam oder eine zuständige Person zugeordnet ist. Ohne gültige Adresse wird keine Kopie versendet. Die Adresse erhält dadurch keinen Zugriff auf Tickets. Eine Zusatzkopie entfällt, wenn dieselbe Adresse für dieses Ereignis bereits als Moodle-Mail-Empfänger vorgesehen ist. Reine Moodle-Benachrichtigungen ohne E-Mail unterdrücken die Kopie nicht.';
$string['publiccreate'] = 'Ticketerstellung ohne Anmeldung erlauben';
$string['publiccreate_desc'] = 'Explizit opt-in, standardmäßig aus. Aktiviert /local/supportdesk/public.php nur mit gültig konfiguriertem Turnstile. Besucher/innen können ein Ticket erstellen; Verlauf und Anhänge erfordern die Moodle-Anmeldung. Maximal fünf Einsendungen je IP in zehn Minuten.';
$string['turnstilesitekey'] = 'Turnstile: Website-Schlüssel';
$string['turnstilesitekey_desc'] = 'Öffentlicher Cloudflare-Turnstile-Website-Schlüssel für diese Moodle-Domain. Das Eintragen aktiviert die Gast-Option nicht.';
$string['turnstilesecret'] = 'Turnstile: geheimer Schlüssel';
$string['turnstilesecret_desc'] = 'Wird ausschließlich auf dem Server zur Prüfung verwendet. Ohne beide Schlüssel bleibt die öffentliche Ticketerstellung gesperrt.';
$string['public_heading'] = 'Ticket ohne Anmeldung erstellen';
$string['public_help'] = 'Sie können Ihr Anliegen ohne Anmeldung senden. Für den weiteren Verlauf melden Sie sich anschließend im selben Browser mit einem bestätigten Moodle-Konto und derselben E-Mail-Adresse an. Es wird kein Konto automatisch angelegt.';
$string['public_name'] = 'Ihr Name';
$string['public_email'] = 'Ihre E-Mail-Adresse';
$string['public_login'] = 'Mit bestehendem Moodle-Zugang anmelden';
$string['public_origin'] = 'Ohne Anmeldung eingereicht';
$string['public_unverified'] = 'Kontaktangaben sind noch nicht einem angemeldeten Konto zugeordnet. Die eingegebene E-Mail-Adresse ist nicht bestätigt.';
$string['public_received'] = 'Ticket wurde angelegt';
$string['public_receipt_help'] = 'Ihr Anliegen wurde an den Support übergeben. Antworten des Supports erhalten Sie per E-Mail. Um den Verlauf im Ticketsystem anzusehen und dort zu antworten, können Sie das Ticket innerhalb von 24 Stunden im selben Browser mit einem bestätigten Konto und derselben E-Mail-Adresse übernehmen.';
$string['public_continue'] = 'Anmelden und Ticket übernehmen';
$string['public_claim'] = 'Ticket meinem Konto zuordnen';
$string['public_claim_help'] = 'Ordnen Sie das in diesem Browser erstellte Ticket Ihrem angemeldeten Konto zu. Die Konto-E-Mail muss mit der bei der Erstellung angegebenen Adresse übereinstimmen.';
$string['public_claim_failed'] = 'Das Ticket kann diesem Konto nicht zugeordnet werden. Verwenden Sie dasselbe Browserfenster und ein bestätigtes Konto mit der angegebenen E-Mail-Adresse innerhalb von 24 Stunden.';
$string['public_claim_retry'] = 'Zuordnung erneut prüfen';
$string['public_spam_protection'] = 'Spam-Schutz durch Cloudflare Turnstile';
$string['public_reply_body'] = 'Der Support hat auf Ihr Ticket #{$a->id} „{$a->title}“ geantwortet:

{$a->reply}

Den Verlauf und etwaige Anhänge können Sie nach Anmeldung und Zuordnung zu Ihrem Konto im Ticketsystem öffnen:
{$a->url}

Verwenden Sie dafür innerhalb von 24 Stunden nach der Ticketerstellung denselben Browser und ein bestätigtes Konto mit dieser E-Mail-Adresse.

Antworten auf diese E-Mail werden derzeit nicht automatisch dem Ticket hinzugefügt.';
$string['public_invalid'] = 'Bitte prüfen Sie Ihre Angaben und Anhänge. Name, gültige E-Mail-Adresse, Betreff, Bereich und Anliegen sind erforderlich.';
$string['public_rate_limit'] = 'Für diese Verbindung wurden bereits mehrere Tickets eingereicht. Bitte versuchen Sie es in zehn Minuten erneut oder melden Sie sich an.';
$string['public_verification_failed'] = 'Der Spam-Schutz konnte die Anfrage nicht bestätigen. Bitte versuchen Sie es erneut. Es wurde kein Ticket angelegt.';
$string['public_unavailable'] = 'Die Ticketerstellung ohne Anmeldung ist derzeit nicht verfügbar. Bitte melden Sie sich an.';
$string['visitormode'] = 'Zugang zum Support Desk';
$string['visitormode_login'] = 'Nur nach Anmeldung';
$string['visitormode_anonymous'] = 'Gastticket ohne Anmeldung (Turnstile)';
$string['visitormode_magiclink'] = 'Anmeldung per Magic Link';
$string['visitormode_desc'] = 'Standardmäßig ist eine Anmeldung erforderlich. Alternativ können Sie Gasttickets mit Turnstile oder die Anmeldung über das optionale Plugin Magic Link Authentication (auth_magiclink) wählen. Ist Magic Link installiert, erscheint es direkt in dieser Auswahl. Aktivieren Sie es in der Moodle-Authentifizierung und erlauben Sie die Kontoerstellung in Magic Link und Moodle, um den Besucherzugang zu nutzen. Gasttickets benötigen beide Turnstile-Schlüssel.';
$string['public_entry_help'] = 'Sie können Ihr Anliegen ohne Anmeldung senden. Antworten des Supports erhalten Sie per E-Mail; für den Verlauf im Ticketsystem ist eine Anmeldung erforderlich.';
$string['visitor_login_help'] = 'Sie haben bereits einen Moodle-Zugang? Verwenden Sie Ihre gewohnte Anmeldung.';
$string['public_turnstile_notice'] = 'Dieses Formular verwendet Cloudflare Turnstile zum Spam-Schutz. Dabei werden technische Browserdaten an Cloudflare übertragen; Ihre Ticketinhalte werden nicht zur Prüfung gesendet.';
$string['privacy:metadata:contacts'] = 'Kontaktangaben öffentlicher Tickets und spätere Zuordnung zum angemeldeten Konto.';
$string['privacy:metadata:turnstile'] = 'Cloudflare verarbeitet die Prüfantwort, den Website-Hostnamen und technische Browserdaten für den Spam-Schutz der öffentlichen Ticketerstellung. Ticketinhalte werden nicht gesendet.';
$string['adminhelp'] = 'Hilfe zur Verwaltung';
$string['adminhelpmore'] = 'Mehr dazu im Hilfepanel';
$string['adminhelptopics'] = 'Hilfethemen';
$string['adminhelp_overview'] = 'Support-Zugang einrichten';
$string['adminhelp_overview_article'] = '<p>Wählen Sie unter Zugang zum Support Desk die gewünschte Zugangsart und prüfen Sie sie angemeldet und abgemeldet. Standardmäßig ist eine Anmeldung erforderlich. Gasttickets benötigen beide Turnstile-Schlüssel.</p><p>Magic Link Authentication (auth_magiclink) ist ein optionales Plugin für Anmeldung und Kontoanlage nach E-Mail-Bestätigung. Nach der Installation erscheint Anmeldung per Magic Link unter Zugang zum Support Desk. Aktivieren Sie Magic Link in der Moodle-Authentifizierung und erlauben Sie die Kontoerstellung in Magic Link und Moodle. Die Seite zeigt fehlende Voraussetzungen. Ist eine gespeicherte Zugangsart nicht nutzbar, greift die normale Anmeldung.</p><p>Die Marketplace-Ausgabe von Magic Link benötigt keine Aktivierung. Eine ältere Ausgabe kann ihre eigene Aktivierung voraussetzen. Magic Link erhält vorhandene Anmeldeverfahren und beachtet seine Kontobeschränkungen sowie Moodles MFA-Regeln. Ältere Gasttickets werden nicht allein anhand der E-Mail-Adresse übernommen.</p><p>Sprachaufnahmen sind optional und benötigen die Mikrofonfreigabe des Browsers. Team- und Postfachbenachrichtigungen erklärt das Hilfethema Team und Benachrichtigungen.</p>';
$string['adminhelp_delivery'] = 'Team und Benachrichtigungen';
$string['adminhelp_delivery_article'] = '<p>Ordnen Sie die Support-Mitarbeitenden unter „Support-Bereiche“ ihren Bereichen zu. Aktivieren Sie anschließend „Tickets nach Support-Team einschränken“. Website-Administrator/innen sehen alle Tickets und können sie zwischen Bereichen verschieben oder bereichsübergreifend zuweisen. In einem Bereich ohne Team bearbeiten Website-Administrator/innen und persönlich zugewiesene Support-Mitarbeitende die Tickets.</p><p>Neue Tickets und Antworten der anfragenden Person meldet Support Desk zuerst dem Bereichsteam, sonst der zuständigen Person oder dem Supportpostfach. Wenn Sie E-Mail-Kopien für alle Tickets aktivieren, bekommt das konfigurierte Postfach auch Meldungen aus anderen Bereichen. Antworten Sie im Ticket, damit Ihre Antwort im Verlauf erscheint.</p>';
$string['subjectissuerequired'] = 'Geben Sie einen Betreff mit höchstens 255 Zeichen ein und beschreiben Sie Ihr Anliegen.';
$string['replyrequired'] = 'Geben Sie vor dem Senden eine Antwort ein.';
$string['invalidassignee'] = 'Wählen Sie eine verfügbare Person aus dem Support-Team.';
$string['invalidfeedback'] = 'Wählen Sie eine Bewertung von 1 bis 5.';
$string['invalidstatus'] = 'Wählen Sie einen gültigen Ticketstatus.';
$string['invalidpriority'] = 'Wählen Sie eine gültige Priorität.';
$string['invalidattachmentcount'] = 'Wählen Sie höchstens 10 gültige Anhänge.';
$string['invalidattachment'] = 'Der Anhang konnte nicht hochgeladen werden. Prüfen Sie die Datei und versuchen Sie es erneut. Jeder Anhang muss innerhalb von 5 MB und der Uploadgrenze der Website liegen.';
$string['systemactor'] = 'System';
$string['website'] = 'Website';
$string['adminhelp_tickets'] = 'Tickets erstellen und verfolgen';
$string['adminhelp_tickets_help'] = 'Beschreiben Sie Ihr Anliegen, wählen Sie einen Supportbereich und eine Priorität und fügen Sie hilfreiche Dateien hinzu. Öffnen Sie Ihr Ticket, um Antworten zu lesen oder das Gespräch fortzusetzen.';
$string['adminhelp_tickets_article'] = '<p>Wählen Sie einen Supportbereich, geben Sie einen kurzen Betreff ein und beschreiben Sie Ihr Anliegen. Die Priorität gibt die Dringlichkeit an. Sie können bis zu zehn Dateien anhängen, jeweils innerhalb von 5 MB und der Uploadgrenze der Website. Eingefügte Screenshots werden beim Absenden hochgeladen.</p><p>Sprachaufnahmen sind verfügbar, wenn die Administration sie aktiviert hat und Ihr Browser sie erlaubt. Prüfen Sie eine Aufnahme vor dem Absenden. Öffnen Sie ein Ticket aus der Übersicht, um Antworten zu lesen und zu senden. Geschlossene Tickets können Sie einmal bewerten.</p>';
$string['adminhelp_handling'] = 'Support-Team: Tickets bearbeiten';
$string['adminhelp_handling_help'] = 'In der Ticketverwaltung können Sie den Status und die zuständige Person ändern. Interne Notizen sehen nur Personen, die das Ticket bearbeiten dürfen.';
$string['adminhelp_handling_article'] = '<p>Öffnen Sie ein Ticket, um den Status oder die zuständige Person zu ändern. Nutzen Sie interne Notizen für die Zusammenarbeit im Support und das Antwortfeld für Nachrichten an die anfragende Person. Der Verlauf hält Änderungen fest. Unter „Weitere Tickets“ stehen nur Anfragen, die Sie öffnen dürfen.</p><p>Bei aktiver Teamtrennung können Sie Tickets Ihrer Bereiche und persönlich zugewiesene Tickets bearbeiten. Eigene Anfragen außerhalb Ihrer Bereiche bleiben in der Kundenansicht zugänglich. Website-Administrator/innen verwalten die Teams, verschieben Tickets und können sie außerhalb des zuständigen Teams zuweisen.</p><p>Das Bereichsteam erhält die Benachrichtigungen. Berechtigte Teammitglieder mit Spezialistenhinweisen sehen außerdem Meldungen zu kürzlich erstellten offenen Tickets mit hoher oder dringender Priorität.</p>';
$string['adminhelp_overview_help'] = 'Wählen Sie den Supportzugang und prüfen Sie die Voraussetzungen des gewählten Modus.';
$string['adminhelp_delivery_help'] = 'Richten Sie Support-Teams und das Postfach ein. Mit der Teamtrennung beschränken Sie die Sicht von Support-Mitarbeitenden auf ihre Bereiche, persönliche Zuweisungen und eigene Anfragen.';
$string['tickethelp'] = 'Support-Desk-Hilfe';
$string['invalidaccessmode'] = 'Wählen Sie eine verfügbare Zugangsart für Support Desk.';
$string['team_tickets'] = 'Teamtickets und eigene Tickets';
$string['teamvisibility'] = 'Tickets nach Support-Team einschränken';
$string['teamvisibility_desc'] = 'Support-Mitarbeitende sehen Tickets ihrer Bereiche, persönlich zugewiesene und selbst erstellte Tickets. Website-Administrator/innen sehen alle Tickets.';
