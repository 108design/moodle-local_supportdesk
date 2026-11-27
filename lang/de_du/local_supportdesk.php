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

$string['deletion_failed'] = 'Der Support-Bereich konnte nicht gelöscht werden. Prüfe, ob noch Tickets zugeordnet sind.';
$string['access_denied'] = 'Du darfst dieses Ticket nicht ansehen.';
$string['action_needed_hint'] = 'Der Support hat geantwortet. Bitte antworte auf das Ticket.';
$string['browser_no_audio'] = 'Dein Browser unterstützt die Audiowiedergabe nicht.';
$string['categoryinuse'] = 'Verschiebe die Tickets zunächst in einen anderen Support-Bereich.';
$string['delete_department_confirm'] = 'Möchtest du diesen Support-Bereich löschen? Bereiche mit Tickets können nicht gelöscht werden.';
$string['description_placeholder'] = 'Beschreibe dein Anliegen möglichst genau.';
$string['email_confirm_body'] = '<p>Hallo {$a->firstname},</p><p>Dein Ticket #{$a->id} zu {$a->title} ist eingegangen.</p><p>Support-Bereich: {$a->category}<br>Status: {$a->status}<br>Datum: {$a->date}</p><p><a href="{$a->url}">Ticket ansehen</a></p><p>Dein Support-Team von {$a->site}</p>';
$string['email_confirm_body_plain'] = 'Hallo {$a->firstname},
Dein Ticket #{$a->id} zu {$a->title} ist eingegangen.
Status: {$a->status}
{$a->url}
Dein Support-Team von {$a->site}';
$string['experience_question'] = 'Wie zufrieden bist du mit dem Support?';
$string['feedback_help'] = 'Deine Bewertung hilft uns, den Support zu verbessern.';
$string['feedback_submitted'] = 'Deine Bewertung';
$string['form_instruction'] = 'Beschreibe dein Anliegen und wähle den passenden Support-Bereich.';
$string['happy_to_help_hint'] = 'Dein Anliegen wurde gelöst.';
$string['header_subtitle'] = 'Wir helfen dir bei deinem Anliegen.';
$string['mic_access_denied'] = 'Die Aufnahme konnte nicht gestartet werden. Bitte prüfe die Mikrofonfreigabe.';
$string['my_tickets_desc'] = 'Hier findest du deine Support-Anfragen.';
$string['no_replies_hint'] = 'Du kannst unten eine Antwort hinzufügen.';
$string['no_tickets_desc'] = 'Du hast noch keine Tickets erstellt.';
$string['nopermission_desc'] = 'Du darfst dieses Ticket nicht ansehen.';
$string['recording_unsupported'] = 'Dein Browser unterstützt keine Mikrofonaufnahme. Du kannst eine Audiodatei als Anhang hochladen.';
$string['start_reply'] = 'Du kannst unten antworten.';
$string['stop_before_sending'] = 'Bitte stoppe die Aufnahme, bevor du das Ticket oder die Antwort sendest.';
$string['student_dashboard_tip'] = 'Du findest deine Anfragen in der Ticket-Übersicht.';
$string['ticket_title_help'] = 'Beschreibe dein Anliegen mit einem kurzen Betreff.';
$string['tooltip_category_hint'] = 'Wähle den passenden Support-Bereich.';
$string['tooltip_desc_hint'] = 'Beschreibe dein Anliegen und gegebenenfalls Fehlermeldungen.';
$string['tooltip_priority_hint'] = 'Wähle die Dringlichkeit.';
$string['tooltip_title_hint'] = 'Gib einen kurzen Betreff ein.';
$string['tooltip_voice_hint'] = 'Nimm dein Anliegen mit dem Mikrofon auf.';
$string['we_are_working_hint'] = 'Wir bearbeiten dein Anliegen.';
$string['write_your_reply'] = 'Deine Antwort';
$string['writereply'] = 'Deine Antwort';
$string['visitor_missing'] = 'Installiere das optionale Plugin Magic Link Authentication (auth_magiclink), um den Besucherzugang nach E-Mail-Bestätigung auszuwählen.';
$string['visitor_authdisabled'] = 'Aktiviere Magic Link in der Moodle-Authentifizierung.';
$string['visitor_newaccountsdisabled'] = 'Erlaube die Kontoerstellung in Magic Link und in den Moodle-Authentifizierungseinstellungen.';
$string['visitor_heading'] = 'Dein Anliegen an den Support';
$string['visitor_intro'] = 'Bestätige deine E-Mail-Adresse per Magic Link. Danach kannst du dein Ticket erstellen oder deine bisherigen Tickets aufrufen.';
$string['visitor_account_hint'] = 'Du brauchst kein Passwort. Falls noch kein Konto besteht, erstellt Moodle nach der E-Mail-Bestätigung ein Konto mit deinem Vor- und Nachnamen.';
$string['department_team_help'] = 'Wähle aktive Personen mit der systemweiten Rolle Support. Sie erhalten die Meldungen dieses Bereichs. Ist die Teamtrennung aktiv, können sie auch die Tickets des Bereichs bearbeiten.';
$string['department_team_empty'] = 'Noch keine auswählbaren Personen. Weise den gewünschten Personen zunächst die Rolle Support auf Systemebene zu.';
$string['public_help'] = 'Du kannst dein Anliegen ohne Anmeldung senden. Für den weiteren Verlauf meldest du dich anschließend im selben Browser mit einem bestätigten Moodle-Konto und derselben E-Mail-Adresse an. Es wird kein Konto automatisch angelegt.';
$string['public_name'] = 'Dein Name';
$string['public_email'] = 'Deine E-Mail-Adresse';
$string['public_receipt_help'] = 'Dein Anliegen wurde an den Support übergeben. Antworten des Supports erhältst du per E-Mail. Um den Verlauf im Ticketsystem anzusehen und dort zu antworten, kannst du das Ticket innerhalb von 24 Stunden im selben Browser mit einem bestätigten Konto und derselben E-Mail-Adresse übernehmen.';
$string['public_claim_help'] = 'Ordne das in diesem Browser erstellte Ticket deinem angemeldeten Konto zu. Die Konto-E-Mail muss mit der bei der Erstellung angegebenen Adresse übereinstimmen.';
$string['public_claim_failed'] = 'Das Ticket kann diesem Konto nicht zugeordnet werden. Verwende dasselbe Browserfenster und ein bestätigtes Konto mit der angegebenen E-Mail-Adresse innerhalb von 24 Stunden.';
$string['public_reply_body'] = 'Der Support hat auf dein Ticket #{$a->id} „{$a->title}“ geantwortet:

{$a->reply}

Den Verlauf und etwaige Anhänge kannst du nach Anmeldung und Zuordnung zu deinem Konto im Ticketsystem öffnen:
{$a->url}

Verwende dafür innerhalb von 24 Stunden nach der Ticketerstellung denselben Browser und ein bestätigtes Konto mit dieser E-Mail-Adresse.

Antworten auf diese E-Mail werden derzeit nicht automatisch dem Ticket hinzugefügt.';
$string['public_invalid'] = 'Bitte prüfe deine Angaben und Anhänge. Name, gültige E-Mail-Adresse, Betreff, Bereich und Anliegen sind erforderlich.';
$string['public_rate_limit'] = 'Für diese Verbindung wurden bereits mehrere Tickets eingereicht. Bitte versuche es in zehn Minuten erneut oder melde dich an.';
$string['public_verification_failed'] = 'Der Spam-Schutz konnte die Anfrage nicht bestätigen. Bitte versuche es erneut. Es wurde kein Ticket angelegt.';
$string['public_unavailable'] = 'Die Ticketerstellung ohne Anmeldung ist derzeit nicht verfügbar. Bitte melde dich an.';
$string['visitormode_desc'] = 'Standardmäßig ist eine Anmeldung erforderlich. Alternativ kannst du Gasttickets mit Turnstile oder die Anmeldung über das optionale Plugin Magic Link Authentication (auth_magiclink) wählen. Ist Magic Link installiert, erscheint es direkt in dieser Auswahl. Aktiviere es in der Moodle-Authentifizierung und erlaube die Kontoerstellung in Magic Link und Moodle, um den Besucherzugang zu nutzen. Gasttickets benötigen beide Turnstile-Schlüssel.';
$string['public_entry_help'] = 'Du kannst dein Anliegen ohne Anmeldung senden. Antworten des Supports erhältst du per E-Mail; für den Verlauf im Ticketsystem ist eine Anmeldung erforderlich.';
$string['visitor_login_help'] = 'Du hast bereits einen Moodle-Zugang? Verwende deine gewohnte Anmeldung.';
$string['public_turnstile_notice'] = 'Dieses Formular verwendet Cloudflare Turnstile zum Spam-Schutz. Dabei werden technische Browserdaten an Cloudflare übertragen; deine Ticketinhalte werden nicht zur Prüfung gesendet.';
$string['adminhelp_overview_article'] = '<p>Wähle unter Zugang zum Support Desk die gewünschte Zugangsart und prüfe sie angemeldet und abgemeldet. Standardmäßig ist eine Anmeldung erforderlich. Gasttickets benötigen beide Turnstile-Schlüssel.</p><p>Magic Link Authentication (auth_magiclink) ist ein optionales Plugin für Anmeldung und Kontoanlage nach E-Mail-Bestätigung. Nach der Installation erscheint Anmeldung per Magic Link unter Zugang zum Support Desk. Aktiviere Magic Link in der Moodle-Authentifizierung und erlaube die Kontoerstellung in Magic Link und Moodle. Die Seite zeigt fehlende Voraussetzungen. Ist eine gespeicherte Zugangsart nicht nutzbar, greift die normale Anmeldung.</p><p>Die Marketplace-Ausgabe von Magic Link benötigt keine Aktivierung. Eine ältere Ausgabe kann ihre eigene Aktivierung voraussetzen. Magic Link erhält vorhandene Anmeldeverfahren und beachtet seine Kontobeschränkungen sowie Moodles MFA-Regeln. Ältere Gasttickets werden nicht allein anhand der E-Mail-Adresse übernommen.</p><p>Sprachaufnahmen sind optional und benötigen die Mikrofonfreigabe des Browsers. Team- und Postfachbenachrichtigungen erklärt das Hilfethema Team und Benachrichtigungen.</p>';
$string['adminhelp_delivery_article'] = '<p>Ordne die Support-Mitarbeitenden unter „Support-Bereiche“ ihren Bereichen zu. Aktiviere anschließend „Tickets nach Support-Team einschränken“. Website-Administrator/innen sehen alle Tickets und können sie zwischen Bereichen verschieben oder bereichsübergreifend zuweisen. In einem Bereich ohne Team bearbeiten Website-Administrator/innen und persönlich zugewiesene Support-Mitarbeitende die Tickets.</p><p>Neue Tickets und Antworten der anfragenden Person meldet Support Desk zuerst dem Bereichsteam, sonst der zuständigen Person oder dem Supportpostfach. Wenn du E-Mail-Kopien für alle Tickets aktivierst, bekommt das konfigurierte Postfach auch Meldungen aus anderen Bereichen. Antworte im Ticket, damit deine Antwort im Verlauf erscheint.</p>';
$string['subjectissuerequired'] = 'Gib einen Betreff mit höchstens 255 Zeichen ein und beschreibe dein Anliegen.';
$string['replyrequired'] = 'Gib vor dem Senden eine Antwort ein.';
$string['invalidassignee'] = 'Wähle eine verfügbare Person aus dem Support-Team.';
$string['invalidfeedback'] = 'Wähle eine Bewertung von 1 bis 5.';
$string['invalidstatus'] = 'Wähle einen gültigen Ticketstatus.';
$string['invalidpriority'] = 'Wähle eine gültige Priorität.';
$string['invalidattachmentcount'] = 'Wähle höchstens 10 gültige Anhänge.';
$string['invalidattachment'] = 'Der Anhang konnte nicht hochgeladen werden. Prüfe die Datei und versuche es erneut. Jeder Anhang muss innerhalb von 5 MB und der Uploadgrenze der Website liegen.';
$string['adminhelp_tickets_help'] = 'Beschreibe dein Anliegen, wähle einen Supportbereich und eine Priorität und füge hilfreiche Dateien hinzu. Öffne dein Ticket, um Antworten zu lesen oder das Gespräch fortzusetzen.';
$string['adminhelp_tickets_article'] = '<p>Wähle einen Supportbereich, gib einen kurzen Betreff ein und beschreibe dein Anliegen. Die Priorität gibt die Dringlichkeit an. Du kannst bis zu zehn Dateien anhängen, jeweils innerhalb von 5 MB und der Uploadgrenze der Website. Eingefügte Screenshots werden beim Absenden hochgeladen.</p><p>Sprachaufnahmen sind verfügbar, wenn die Administration sie aktiviert hat und dein Browser sie erlaubt. Prüfe eine Aufnahme vor dem Absenden. Öffne ein Ticket aus der Übersicht, um Antworten zu lesen und zu senden. Geschlossene Tickets kannst du einmal bewerten.</p>';
$string['adminhelp_handling_help'] = 'In der Ticketverwaltung kannst du den Status und die zuständige Person ändern. Interne Notizen sehen nur Personen, die das Ticket bearbeiten dürfen.';
$string['adminhelp_handling_article'] = '<p>Öffne ein Ticket, um den Status oder die zuständige Person zu ändern. Nutze interne Notizen für die Zusammenarbeit im Support und das Antwortfeld für Nachrichten an die anfragende Person. Der Verlauf hält Änderungen fest. Unter „Weitere Tickets“ stehen nur Anfragen, die du öffnen darfst.</p><p>Bei aktiver Teamtrennung kannst du Tickets deiner Bereiche und persönlich zugewiesene Tickets bearbeiten. Eigene Anfragen außerhalb deiner Bereiche bleiben in der Kundenansicht zugänglich. Website-Administrator/innen verwalten die Teams, verschieben Tickets und können sie außerhalb des zuständigen Teams zuweisen.</p><p>Das Bereichsteam erhält die Benachrichtigungen. Berechtigte Teammitglieder mit Spezialistenhinweisen sehen außerdem Meldungen zu kürzlich erstellten offenen Tickets mit hoher oder dringender Priorität.</p>';
$string['adminhelp_overview_help'] = 'Wähle den Supportzugang und prüfe die Voraussetzungen des gewählten Modus.';
$string['adminhelp_delivery_help'] = 'Richte Support-Teams und das Postfach ein. Mit der Teamtrennung beschränkst du die Sicht von Support-Mitarbeitenden auf ihre Bereiche, persönliche Zuweisungen und eigene Anfragen.';
$string['invalidaccessmode'] = 'Wähle eine verfügbare Zugangsart für Support Desk.';
