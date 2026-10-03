<?php
// This file is part of Moodle - http://moodle.org/
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
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * French strings for local_supportdesk.
 *
 * @package    local_supportdesk
 * @copyright  2026 learn-ix support@learn-ix.com
 * Modified 2026-10-03 by 108design: Support Desk consolidation.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();

$string['supportdesk:addcategory'] = 'Permission d\'ajouter de nouvelles catégories';
$string['supportdesk:addticket'] = 'Permission de créer de nouveaux tickets (Étudiant)';
$string['supportdesk:download'] = 'Permission de télécharger les pièces jointes des tickets';
$string['supportdesk:manageticket'] = 'Permission de gérer/assigner tous les tickets (Administrateur/Personnel)';
$string['supportdesk:specialist'] = 'Recevoir des alertes urgentes en tant que spécialiste';
$string['supportdesk:viewownoverviews'] = 'Consulter son propre tableau de bord d\'activité';
$string['supportdesk:viewticket'] = 'Permission de consulter les détails du ticket';
$string['action_needed_hint'] = 'Le personnel a répondu. Veuillez répondre.';
$string['actions'] = 'Actions';
$string['add_department'] = 'Départements';
$string['add_new_department'] = 'Ajouter un Nouveau Département';
$string['add_reply_heading'] = 'Rédigez Votre Réponse';
$string['add_ticket'] = 'Ajouter un Nouveau Ticket';
$string['add_to_navbar'] = 'Ajouter à la Barre de Navigation';
$string['add_to_navbar_desc'] = 'Si activé, un lien vers le système de tickets sera ajouté au menu de navigation principal.';
$string['admin_alert_body'] = '
<div style="padding: 15px; border-left: 5px solid #ef4444; background-color: #fef2f2;">
    <h3 style="margin-top:0; color: #b91c1c;">🚨 Ticket Urgent Reçu</h3>
    <p><strong>Étudiant :</strong> {$a->firstname}</p>
    <p><strong>Titre :</strong> {$a->title}</p>
    <p><strong>Catégorie :</strong> {$a->category}</p>
    <hr style="border:0; border-top:1px solid #fee2e2; margin: 10px 0;">
    <a href="{$a->url}" style="background-color: #dc2626; color: white; padding: 8px 15px; text-decoration: none; border-radius: 5px; display: inline-block;">
        Voir le Ticket Maintenant
    </a>
</div>
';
$string['admin_alert_subject'] = '🚨 URGENT : Nouveau Ticket #{$a->id} - {$a->title}';
$string['admin_only_label'] = 'Contrôles Administratifs';
$string['all_rights_reserved'] = 'Tous droits réservés à';
$string['all_tickets'] = 'Tous les Tickets Académiques';
$string['all_tickets_stats'] = 'Vue d\'Ensemble Globale des Tickets';
$string['allowed_file_types'] = 'PDF, JPG, PNG (Max 5 Mo)';
$string['assign_user'] = 'Assigner un Spécialiste';
$string['assign_user_label'] = 'Assigner à un Spécialiste';
$string['assignable_roles'] = 'Rôles assignables';
$string['assignable_roles_desc'] = 'Sélectionnez les rôles qui peuvent être assignés aux tickets. Les utilisateurs ayant ces rôles apparaîtront dans la liste déroulante d\'assignation.';
$string['assigned_to'] = 'Spécialiste Assigné';
$string['assigned_to_label'] = 'Assigné À';
$string['assigned_user'] = 'Assigné À';
$string['attach_files_optional'] = 'Pièces Jointes (Facultatif)';
$string['attached_files'] = 'Fichiers joints :';
$string['attachments'] = 'Pièces Jointes';
$string['attachments_heading'] = 'Pièces Jointes Originales';
$string['attention_required'] = 'Attention Requise';
$string['awaiting_me_label'] = 'En Attente de Mon Action';
$string['back_to_home'] = 'Retour à l\'Accueil';
$string['browser_no_audio'] = 'Votre navigateur ne supporte pas l\'élément audio.';
$string['cancel'] = 'Annuler';
$string['category'] = 'Catégorie';
$string['category_title'] = 'Département';
$string['change_category_label'] = 'Changer de Département';
$string['change_status_label'] = 'Mettre à Jour le Statut';
$string['click_to_download'] = 'Cliquez pour télécharger';
$string['click_to_record'] = 'Cliquez sur le microphone pour commencer l\'enregistrement';
$string['click_to_upload'] = 'Cliquez ici pour choisir des fichiers depuis votre appareil';
$string['close'] = 'Fermer';
$string['closed_label'] = 'Fermés';
$string['copyright_label'] = 'GNU GPL v3 or later';
$string['create_ticket'] = 'Créer un Ticket';
$string['created_at'] = 'Date de Création';
$string['created_by'] = 'Créé Par';
$string['creation_failed'] = 'La création de la catégorie a échoué. Veuillez réessayer.';
$string['current_year_label'] = 'Année en Cours';
$string['default_email_placeholder'] = 'noreply@votresitemoodle.com';
$string['department'] = 'Département';
$string['department_created'] = 'Département Créé avec Succès';
$string['department_deleted'] = 'Département Supprimé avec Succès';
$string['department_updated'] = 'Département Mis à Jour avec Succès';
$string['description'] = 'Description';
$string['description_placeholder'] = 'Veuillez expliquer le problème en détail...';
$string['drag_drop_hint'] = 'Glissez-déposez des fichiers ici ou cliquez pour télécharger';
$string['email_confirm_body'] = '
<div style="background-color: #f3f4f6; padding: 40px 0; font-family: \'Segoe UI\', Tahoma, Geneva, Verdana, sans-serif; color: #374151;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.05); border: 1px solid #e5e7eb;">
        <div style="background: linear-gradient(135deg, #4f46e5 0%, #3b82f6 100%); padding: 32px; text-align: center;">
            <h1 style="color: #ffffff; margin: 0; font-size: 24px; font-weight: 800; letter-spacing: -0.5px;">Ticket Reçu !</h1>
            <p style="color: #e0e7ff; margin: 8px 0 0 0; font-size: 15px; font-weight: 500;">Nous examinons votre demande</p>
        </div>
        <div style="padding: 40px 30px;">
            <p style="font-size: 16px; margin-bottom: 24px; color: #111827;">Bonjour <strong>{$a->firstname}</strong>,</p>
            <p style="line-height: 1.6; color: #4b5563; margin-bottom: 30px;">
                Merci de nous avoir contactés. Un nouveau ticket de support a été ouvert avec succès. Notre équipe de support examinera les détails et vous répondra dans les plus brefs délais.
            </p>
            <div style="background-color: #f9fafb; border: 1px solid #e5e7eb; border-radius: 12px; padding: 24px; margin-bottom: 32px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <tr>
                        <td style="padding: 8px 0; color: #6b7280; font-size: 12px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">ID du Ticket</td>
                        <td style="padding: 8px 0; color: #111827; font-weight: 700; text-align: right; font-family: monospace; font-size: 14px;">#{$a->id}</td>
                    </tr>
                    <tr style="border-top: 1px dashed #e5e7eb;">
                        <td style="padding: 12px 0 8px; color: #6b7280; font-size: 12px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Sujet</td>
                        <td style="padding: 12px 0 8px; color: #111827; text-align: right; font-weight: 600;">{$a->title}</td>
                    </tr>
                    <tr style="border-top: 1px dashed #e5e7eb;">
                        <td style="padding: 12px 0 8px; color: #6b7280; font-size: 12px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Catégorie</td>
                        <td style="padding: 12px 0 8px; color: #111827; text-align: right;">{$a->category}</td>
                    </tr>
                    <tr style="border-top: 1px dashed #e5e7eb;">
                        <td style="padding: 12px 0 8px; color: #6b7280; font-size: 12px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Date</td>
                        <td style="padding: 12px 0 8px; color: #111827; text-align: right;">{$a->date}</td>
                    </tr>
                    <tr style="border-top: 1px dashed #e5e7eb;">
                        <td style="padding: 12px 0 0; color: #6b7280; font-size: 12px; text-transform: uppercase; font-weight: 700; letter-spacing: 0.5px;">Statut</td>
                        <td style="padding: 12px 0 0; text-align: right;">
                            <span style="background-color: #dbeafe; color: #1e40af; padding: 4px 12px; border-radius: 9999px; font-size: 11px; font-weight: 800; text-transform: uppercase;">{$a->status}</span>
                        </td>
                    </tr>
                </table>
            </div>
            <div style="text-align: center;">
                <a href="{$a->url}" style="display: inline-block; background: linear-gradient(to right, #4f46e5, #3b82f6); color: #ffffff; text-decoration: none; padding: 14px 36px; border-radius: 10px; font-weight: 700; font-size: 16px; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.4); transition: all 0.3s ease;">
                    Voir les Détails du Ticket
                </a>
            </div>
            <p style="text-align: center; font-size: 12px; color: #9ca3af; margin-top: 24px;">
                Vous pouvez également copier ce lien : <br>
                <a href="{$a->url}" style="color: #6b7280; text-decoration: none;">{$a->url}</a>
            </p>
        </div>
        <div style="background-color: #f9fafb; padding: 24px; text-align: center; border-top: 1px solid #e5e7eb;">
            <p style="color: #9ca3af; font-size: 12px; margin: 0 0 8px;">
                Ceci est un message automatique. Veuillez ne pas répondre directement à cet e-mail.
            </p>
            <p style="color: #d1d5db; font-size: 11px; margin: 0; font-weight: 600; text-transform: uppercase;">
                Propulsé par le Système de Support de {$a->site}
            </p>
        </div>
    </div>
</div>';
$string['email_confirm_body_plain'] = '
Bonjour {$a->firstname},
Nous avons reçu votre ticket #{$a->id} concernant "{$a->title}".
Statut Actuel : {$a->status}
Vous pouvez suivre votre ticket ici :
{$a->url}
Cordialement,
L\'équipe de support de {$a->site}
';
$string['email_confirm_subject'] = '✔ [Ticket #{$a->id}] Nous avons bien reçu votre demande : {$a->title}';
$string['enable'] = 'Activer le Système';
$string['enable_desc'] = 'Si activé, les utilisateurs peuvent créer et consulter des tickets.';
$string['error'] = 'Erreur !';
$string['experience_question'] = 'Comment s\'est passée votre expérience ?';
$string['feedback_help'] = 'Vos retours nous aident à améliorer notre service.';
$string['feedback_placeholder'] = 'Des commentaires supplémentaires ? (Facultatif)';
$string['feedback_submitted'] = 'Commentaire soumis';
$string['filesselected'] = '{$a} fichiers sélectionnés';
$string['form_instruction'] = 'Nous sommes heureux de vous aider ; veuillez remplir le formulaire ci-dessous.';
$string['happy_to_help_hint'] = 'Votre problème a été résolu avec succès.';
$string['header_subtitle'] = 'Nous sommes là pour vous aider aujourd\'hui 🌟';
$string['id'] = 'ID';
$string['internal_note_placeholder'] = 'Laissez une note pour vos collègues...';
$string['internal_notes_heading'] = 'Notes Internes de l\'Équipe';
$string['ip_address'] = 'Adresse IP';
$string['live_stats_heading'] = 'Tableau de Bord des Statistiques en Direct';
$string['log_assigned'] = 'Ticket assigné à un spécialiste.';
$string['log_category_changed'] = '{$a->user} a déplacé le ticket de {$a->old} à {$a->new}.';
$string['log_feedback_submitted'] = 'Commentaire soumis avec la note : {$a} étoiles';
$string['log_internal_note_added'] = 'Une note interne a été ajoutée';
$string['log_replied'] = '{$a} a ajouté une nouvelle réponse.';
$string['log_status_changed'] = '{$a->user} a changé le statut de {$a->old} à {$a->new}.';
$string['log_status_changed_from_to'] = '{$a->user} a changé le statut de "{$a->old}" à "{$a->new}"';
$string['messageprovider:admin_urgent_alert'] = 'Alerte administrateur : Notification de ticket urgent';
$string['messageprovider:ticket_confirmation'] = 'Confirmation de soumission du ticket';
$string['mic_access_denied'] = 'Accès au microphone refusé.';
$string['my_summary_heading'] = 'Mon Résumé d\'Activité';
$string['my_tickets_desc'] = 'Suivez et gérez toutes vos demandes de support';
$string['my_tickets_label'] = 'Mes Tickets Soumis';
$string['next'] = 'Suivant';
$string['no_internal_notes'] = 'Aucune note interne pour l\'instant.';
$string['no_recent_tickets'] = 'Aucun ticket récent.';
$string['no_replies_hint'] = 'Soyez le premier à ajouter une réponse ou une question.';
$string['no_replies_message'] = 'Aucune réponse trouvée pour ce ticket pour l\'instant.';
$string['no_tickets_desc'] = 'Vous n\'avez pas encore créé de ticket.';
$string['no_tickets_message'] = 'Vous n\'avez encore soumis aucun ticket de support.
Besoin d\'aide ? Créez un nouveau ticket et notre équipe prendra en charge votre demande.';
$string['no_tickets_title'] = 'Aucun ticket trouvé';
$string['nopermission'] = 'Accès Refusé';
$string['nopermission_desc'] = 'Désolé, vous n\'avez pas la permission de consulter ce ticket.';
$string['of'] = 'sur';
$string['open_label'] = 'Ouverts';
$string['pluginname'] = 'Support Desk';
$string['previous'] = 'Précédent';
$string['primary_color'] = 'Couleur Principale';
$string['primary_color_desc'] = 'La couleur principale utilisée pour les boutons, les en-têtes et l\'identité visuelle primaire.';
$string['priority'] = 'Priorité';
$string['priority_high'] = 'Élevée';
$string['priority_low'] = 'Faible';
$string['priority_medium'] = 'Moyenne';
$string['priority_urgent'] = 'Urgent';
$string['privacy:metadata:tickets'] = 'Stockage des tickets de support créés par les utilisateurs.';
$string['privacy:metadata:tickets:content'] = 'Le contenu complet et la description du ticket.';
$string['privacy:metadata:tickets:created_at'] = 'L\'horodatage de l\'ouverture du ticket.';
$string['privacy:metadata:tickets:title'] = 'Le titre du ticket.';
$string['privacy:metadata:tickets:userid'] = 'L\'identifiant de l\'utilisateur qui a créé le ticket.';
$string['quick_tip_label'] = 'Conseil Rapide';
$string['ready'] = 'Prêt';
$string['recent_tickets_heading'] = 'Derniers Tickets des Étudiants';
$string['record_voice_note'] = 'Note Vocale (Accessibilité)';
$string['recording_finished'] = 'Note vocale enregistrée avec succès';
$string['recording_now'] = 'Enregistrement en cours... cliquez sur arrêter lorsque vous avez terminé';
$string['remove_file'] = 'Supprimer le fichier';
$string['reopen_ticket_button'] = 'Rouvrir le Ticket';
$string['replies_heading'] = 'Historique de la Conversation';
$string['resolved_label'] = 'Résolus';
$string['return_home'] = 'Retour à la Liste';
$string['rolespecialist'] = 'Spécialiste du support';
$string['rolespecialistdescription'] = 'Gère les tickets académiques du département et le flux de travail d\'assistance.';
$string['search_placeholder'] = 'Rechercher par ID ou Sujet...';
$string['search_user_placeholder'] = 'Rechercher un nom, e-mail ou nom d\'utilisateur...';
$string['secondary_color'] = 'Couleur Secondaire';
$string['secondary_color_desc'] = 'Utilisée pour les dégradés, les accents et les éléments secondaires de l\'interface.';
$string['select_department_hint'] = '-- Sélectionner un Département --';
$string['select_priority_hint'] = 'Sélectionnez le niveau de priorité';
$string['send_reply_button'] = 'Envoyer la Réponse et les Fichiers';
$string['send_ticket'] = 'Envoyer le Ticket';
$string['sending'] = 'Envoi en cours...';
$string['showing'] = 'Affichage';
$string['sorry_no_ticket'] = 'Aucun Ticket Trouvé';
$string['start_new_ticket_btn'] = 'Créer Votre Premier Ticket';
$string['start_recording'] = 'Démarrer l\'enregistrement vocal';
$string['start_reply'] = 'Démarrez la conversation ci-dessous !';
$string['status'] = 'Statut du Ticket';
$string['status_admin_reply'] = 'Réponse de l\'Administrateur';
$string['status_adminreply'] = 'Réponse de l\'Administrateur';
$string['status_assigned'] = 'Assigné à un Spécialiste';
$string['status_closed'] = 'Fermé';
$string['status_in_progress'] = 'En Cours';
$string['status_open'] = 'Ouvert';
$string['status_pending'] = 'En Attente';
$string['status_resolved'] = 'Résolu';
$string['status_student_reply'] = 'Réponse de l\'Étudiant';
$string['status_studentreply'] = 'Réponse de l\'Étudiant';
$string['status_urgent'] = 'Urgent';
$string['stop_recording'] = 'Arrêter l\'enregistrement vocal';
$string['student'] = 'Étudiant';
$string['student_dashboard_tip'] = 'Pour garantir le support le plus rapide, veuillez répondre dans les 12 heures. Les tickets sans activité pendant 12 heures sont automatiquement fermés.';
$string['submit'] = 'Soumettre';
$string['submit_feedback'] = 'Soumettre le commentaire';
$string['success'] = 'Succès !';
$string['support_email'] = 'E-mail de Support';
$string['support_email_desc'] = 'L\'adresse e-mail affichée aux utilisateurs pour un contact direct ou des notifications.';
$string['support_team'] = 'Équipe de support';
$string['system_name'] = 'Nom du Premier Département';
$string['system_name_desc'] = 'Il s\'agit du nom par défaut du premier département. Vous pouvez le renommer ou gérer d\'autres départements ultérieurement en cliquant sur la section « Départements ».';
$string['ticket_department_label'] = 'Département';
$string['ticket_description_label'] = 'Description du Problème';
$string['ticket_details_heading'] = 'Détails du Ticket';
$string['ticket_id_label'] = 'ID de Référence du Ticket';
$string['ticket_log'] = 'Chronologie des Activités';
$string['ticket_priority_label'] = 'Priorité du Ticket';
$string['ticket_status_label'] = 'Statut Actuel';
$string['ticket_title'] = 'Titre du Ticket';
$string['ticket_title_help'] = 'Saisissez un titre bref et descriptif pour votre demande de support.';
$string['ticket_title_label'] = 'Titre du Ticket';
$string['tickets_count'] = 'tickets';
$string['ticketsystem'] = 'Système de Tickets';
$string['title'] = 'Titre du Sujet';
$string['title_placeholder'] = 'Ex. : Je ne peux pas accéder au cours...';
$string['to'] = 'à';
$string['tooltip_category_hint'] = 'Sélectionnez le département qui gère ce problème';
$string['tooltip_desc_hint'] = 'Fournissez tous les détails (étapes pour reproduire, erreurs, etc.)';
$string['tooltip_priority_hint'] = 'Choisissez le degré d\'urgence de votre demande';
$string['tooltip_title_hint'] = 'Donnez à votre ticket un nom clair et concis';
$string['tooltip_upload_hint'] = 'Téléchargez des captures d\'écran ou des journaux (Maximum 5 Mo)';
$string['tooltip_voice_hint'] = 'Idéal pour les problèmes complexes - Maximum 2 minutes';
$string['total'] = 'Total';
$string['total_tickets_label'] = 'Total des Tickets';
$string['unassigned'] = 'Non Assigné';
$string['under_review_label'] = 'En Cours d\'Examen';
$string['update_category_button'] = 'Mettre à Jour le Département';
$string['update_status_button'] = 'Enregistrer les Modifications';
$string['user_name_label'] = 'Soumis Par';
$string['view'] = 'Voir les Détails';
$string['view_profile'] = 'Voir le profil';
$string['view_ticket'] = 'Voir le Ticket';
$string['viewticket'] = 'Voir le Ticket';
$string['visit_my_portfolio'] = 'Visitez mon portfolio';
$string['we_are_working_hint'] = 'Nous examinons actuellement votre demande.';
$string['welcome_message'] = 'Bienvenue dans le Système de Support Académique';
$string['write_your_reply'] = 'Rédigez votre réponse....';
