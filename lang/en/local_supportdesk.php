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
 * English strings for local_supportdesk.
 *
 * @package    local_supportdesk
 * @copyright  2026 learn-ix support@learn-ix.com
 * Modified 2026-10-03 by 108design: Supportdesk consolidation.
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

defined('MOODLE_INTERNAL') || die();
$string['general'] = 'General';
$string['deletion_failed'] = 'The department could not be deleted. Check whether tickets are still assigned to it.';
$string['update_failed'] = 'The department could not be updated.';
$string['closed_tickets'] = 'Closed tickets';
$string['my_tickets'] = 'My tickets';
$string['open_tickets'] = 'Open tickets';
$string['total_tickets'] = 'Total tickets';
$string['reply'] = 'Reply';
$string['submit_reply'] = 'Send reply';
$string['writereply'] = 'Write a reply';

// Modified 2026-10-03 by 108design: complete native UI localisation.
$string['access_denied'] = 'You do not have permission to view this ticket.';
$string['action_needed_hint'] = 'Staff have replied. Please respond.';
$string['actions'] = 'Actions';
$string['add_department'] = 'Departments';
$string['add_new_department'] = 'Add New Department';
$string['add_reply_heading'] = 'Write Your Reply';
$string['add_ticket'] = 'New ticket';
$string['add_to_navbar'] = 'Add to Navigation Bar';
$string['add_to_navbar_desc'] = 'If enabled, a link to the ticket system will be added to the primary navigation menu.';
$string['admin_alert_body'] = '<p>Urgent support ticket from {$a->firstname}: {$a->title}</p><p>Department: {$a->category}</p><p><a href="{$a->url}">View ticket</a></p>';
$string['admin_alert_subject'] = 'Urgent ticket #{$a->id}: {$a->title}';
$string['admin_only_label'] = 'Administrative Controls';
$string['all_rights_reserved'] = 'Original author';
$string['all_tickets'] = 'Support tickets';
$string['all_tickets_stats'] = 'Global Tickets Overview';
$string['allowed_file_types'] = 'Up to 10 files, maximum 5 MB per file';
$string['assign_user'] = 'Assign Support Agent';
$string['assign_user_label'] = 'Assign to Support Agent';
$string['assignable_roles'] = 'Assignable roles';
$string['assignable_roles_desc'] = 'Select the roles that can be assigned to tickets. Users with these roles will appear in the assignee dropdown.';
$string['assigned_to'] = 'Assigned to';
$string['assigned_to_label'] = 'Assigned To';
$string['assigned_to_me'] = 'Assigned to me';
$string['assigned_user'] = 'Assigned To';
$string['attach_files_optional'] = 'Attachments (Optional)';
$string['attached_files'] = 'Attachments';
$string['attachments'] = 'Attachments';
$string['attachments_heading'] = 'Original Attachments';
$string['attention_required'] = 'Attention Required';
$string['audio_preview'] = 'Listen to recording';
$string['awaiting_me_label'] = 'Awaiting My Action';
$string['back_to_home'] = 'Back to Home';
$string['browser_no_audio'] = 'Your browser does not support the audio element.';
$string['cancel'] = 'Cancel';
$string['category'] = 'Category';
$string['category_title'] = 'Department';
$string['categoryinuse'] = 'Move the tickets to another department before deleting this department.';
$string['change_category_label'] = 'Change Department';
$string['change_status_label'] = 'Update Status';
$string['choose_rating'] = 'Choose a rating';
$string['click_to_download'] = 'Click to download';
$string['click_to_record'] = 'Click the microphone to start recording';
$string['click_to_upload'] = 'Click here to choose files from your device';
$string['close'] = 'Close';
$string['closed_label'] = 'Closed';
$string['conversation'] = 'Conversation';
$string['copyright_label'] = 'GNU GPL v3 or later';
$string['create_ticket'] = 'Create Ticket';
$string['created_at'] = 'Date Created';
$string['created_by'] = 'Created By';
$string['creation_failed'] = 'The support department could not be created.';
$string['current_year_label'] = 'Current year';
$string['customer'] = 'Customer';
$string['default_email_placeholder'] = 'noreply@yourmoodlesite.com';
$string['delete'] = 'Delete';
$string['delete_department'] = 'Delete support department';
$string['delete_department_confirm'] = 'Do you want to delete this support department? Departments containing tickets cannot be deleted.';
$string['deleteduser'] = 'Deleted customer';
$string['department'] = 'Department';
$string['department_created'] = 'Support department created.';
$string['department_deleted'] = 'Support department deleted.';
$string['department_updated'] = 'Support department updated.';
$string['description'] = 'Description';
$string['description_optional'] = 'Description (optional)';
$string['description_placeholder'] = 'Please explain the issue in detail...';
$string['disabled'] = 'Supportdesk is disabled.';
$string['drag_drop_hint'] = 'Drag and drop files here or click to upload';
$string['edit_department'] = 'Edit support department';
$string['email_confirm_body'] = '<p>Hello {$a->firstname},</p><p>We received your ticket #{$a->id} regarding {$a->title}.</p><p>Department: {$a->category}<br>Status: {$a->status}<br>Date: {$a->date}</p><p><a href="{$a->url}">View ticket</a></p><p>Your support team at {$a->site}</p>';
$string['email_confirm_body_plain'] = 'Hello {$a->firstname},
We received your ticket #{$a->id} regarding {$a->title}.
Status: {$a->status}
{$a->url}
Your support team at {$a->site}';
$string['email_confirm_subject'] = 'Ticket #{$a->id} received: {$a->title}';
$string['enable'] = 'Enable System';
$string['enable_desc'] = 'If enabled, users can create and view tickets.';
$string['erasedticket'] = 'Erased customer ticket';
$string['error'] = 'Error!';
$string['existing_departments'] = 'Existing support departments';
$string['experience_question'] = 'How satisfied are you with the support?';
$string['feedback_comment_label'] = 'Comment (optional)';
$string['feedback_help'] = 'Your feedback helps us improve support.';
$string['feedback_placeholder'] = 'Additional comment (optional)';
$string['feedback_submitted'] = 'Your feedback';
$string['filesselected'] = '{$a} files selected';
$string['form_instruction'] = 'Describe your issue and choose the appropriate support department.';
$string['happy_to_help_hint'] = 'Your issue has been resolved.';
$string['header_subtitle'] = 'We are here to help with your request.';
$string['id'] = 'ID';
$string['internal_note_placeholder'] = 'Leave a note for colleagues...';
$string['internal_notes_heading'] = 'Internal Team Notes';
$string['ip_address'] = 'IP address';
$string['live_stats_heading'] = 'Ticket overview';
$string['log_assigned'] = 'Ticket assigned to: {$a}';
$string['log_category_changed'] = 'Support department changed to: {$a}';
$string['log_feedback_submitted'] = 'Feedback submitted: {$a} out of 5';
$string['log_internal_note_added'] = 'Internal note added';
$string['log_replied'] = 'Reply added';
$string['log_status_changed'] = '{$a->user} changed the status from {$a->old} to {$a->new}.';
$string['log_status_changed_from_to'] = '{$a->user} changed the status from {$a->old} to {$a->new}.';
$string['messageprovider:admin_urgent_alert'] = 'Urgent support tickets';
$string['messageprovider:ticket_confirmation'] = 'Ticket submission confirmation';
$string['mic_access_denied'] = 'Recording could not be started. Please check your microphone permission.';
$string['my_assignment'] = 'Assigned to';
$string['my_summary_heading'] = 'My ticket overview';
$string['my_tickets_desc'] = 'Your support requests are listed here.';
$string['my_tickets_label'] = 'My tickets';
$string['next'] = 'Next';
$string['no_departments'] = 'No support departments available.';
$string['no_internal_notes'] = 'No internal notes yet.';
$string['no_recent_tickets'] = 'No other tickets.';
$string['no_replies_hint'] = 'You can add a reply below.';
$string['no_replies_message'] = 'There are no replies yet.';
$string['no_search_results'] = 'No matching tickets on this page.';
$string['no_tickets'] = 'No tickets available.';
$string['no_tickets_desc'] = 'You have not created any tickets yet.';
$string['no_tickets_message'] = 'There are no tickets yet.';
$string['no_tickets_title'] = 'No tickets found';
$string['nopermission'] = 'Access denied';
$string['nopermission_desc'] = 'You do not have permission to view this ticket.';
$string['of'] = 'of';
$string['open_label'] = 'Open';
$string['pagination_label'] = 'Ticket pages';
$string['pluginname'] = 'Supportdesk';
$string['previous'] = 'Previous';
$string['primary_color'] = 'Primary Color';
$string['primary_color_desc'] = 'The main color used for buttons, headers, and primary branding.';
$string['priority'] = 'Priority';
$string['priority_high'] = 'High';
$string['priority_low'] = 'Low';
$string['priority_medium'] = 'Medium';
$string['priority_urgent'] = 'Urgent';
$string['privacy:metadata:categories'] = 'Supportdesk categories data.';
$string['privacy:metadata:comments'] = 'Supportdesk comments data.';
$string['privacy:metadata:feedback'] = 'Supportdesk feedback data.';
$string['privacy:metadata:field'] = 'Support ticket data associated with a user.';
$string['privacy:metadata:files'] = 'Ticket and reply attachments.';
$string['privacy:metadata:logs'] = 'Supportdesk logs data.';
$string['privacy:metadata:replies'] = 'Supportdesk replies data.';
$string['privacy:metadata:tickets'] = 'Supportdesk tickets data.';
$string['privacy:metadata:tickets:content'] = 'Ticket content and description.';
$string['privacy:metadata:tickets:created_at'] = 'Time of ticket creation.';
$string['privacy:metadata:tickets:title'] = 'Ticket subject.';
$string['privacy:metadata:tickets:userid'] = 'The ID of the person who created the ticket.';
$string['quick_tip_label'] = 'Tip';
$string['rating_label'] = 'Rating';
$string['ready'] = 'Recording ready';
$string['recent_tickets_heading'] = 'Other tickets from this person';
$string['record_voice_note'] = 'Record a voice message';
$string['recording_finished'] = 'Voice note recorded successfully';
$string['recording_now'] = 'Recording... click stop when finished';
$string['recording_unsupported'] = 'Your browser does not support microphone recording. You can upload an audio file as an attachment.';
$string['remove_file'] = 'Remove';
$string['reopen_ticket_button'] = 'Reopen ticket';
$string['replies_heading'] = 'Conversation';
$string['required_fields'] = 'Fields marked * are required.';
$string['resolved_label'] = 'Resolved';
$string['return_home'] = 'Back to List';
$string['rolecollision'] = 'The role shortname supportdeskagent is already in use. Installation stopped without changing that role.';
$string['rolespecialist'] = 'Support';
$string['rolespecialistdescription'] = 'Handles department support tickets and support workflow.';
$string['save_department'] = 'Save support department';
$string['save_note'] = 'Save note';
$string['search_page_hint'] = 'Search filters the tickets displayed on this page.';
$string['search_placeholder'] = 'Search by ID or subject';
$string['search_user_placeholder'] = 'Search name, email or username';
$string['secondary_color'] = 'Secondary Color';
$string['secondary_color_desc'] = 'Used for gradients, accents, and secondary UI elements.';
$string['select_department_hint'] = '-- Select Department --';
$string['select_priority_hint'] = 'Select priority level';
$string['send_reply_button'] = 'Send reply';
$string['send_ticket'] = 'Send ticket';
$string['sending'] = 'Sending...';
$string['showing'] = 'Showing';
$string['sorry_no_ticket'] = 'No Tickets Found';
$string['start_new_ticket_btn'] = 'Create ticket';
$string['start_recording'] = 'Start voice recording';
$string['start_reply'] = 'You can reply below.';
$string['status'] = 'Ticket Status';
$string['status_admin_reply'] = 'Support replied';
$string['status_adminreply'] = 'Support replied';
$string['status_assigned'] = 'Assigned';
$string['status_closed'] = 'Closed';
$string['status_in_progress'] = 'In Progress';
$string['status_open'] = 'Open';
$string['status_pending'] = 'Pending';
$string['status_resolved'] = 'Resolved';
$string['status_student_reply'] = 'Customer replied';
$string['status_studentreply'] = 'Customer replied';
$string['status_urgent'] = 'Urgent';
$string['stop_before_sending'] = 'Please stop the recording before sending the ticket or reply.';
$string['stop_recording'] = 'Stop voice recording';
$string['student'] = 'Customer';
$string['student_dashboard_tip'] = 'Your requests are available in the ticket overview.';
$string['submit'] = 'Submit';
$string['submit_feedback'] = 'Submit feedback';
$string['success'] = 'Success!';
$string['support_departments'] = 'Support departments';
$string['support_email'] = 'Support Email';
$string['support_email_desc'] = 'Support notification order: department team → explicitly assigned person on the ticket → this fallback address. The address receives no extra copy. Defaults to the support contact configured in Moodle, or blank if none. Configuring a fallback address is recommended.';
$string['support_team'] = 'Support';
$string['supportdesk:addcategory'] = 'Permission to add new categories';
$string['supportdesk:addticket'] = 'Permission to create new tickets (Customer)';
$string['supportdesk:download'] = 'Permission to download ticket attachments';
$string['supportdesk:manageticket'] = 'Permission to manage/assign all tickets (Admin/Staff)';
$string['supportdesk:specialist'] = 'Receive urgent alerts as a specialist';
$string['supportdesk:viewownoverviews'] = 'View own activity dashboard';
$string['supportdesk:viewticket'] = 'Permission to view ticket details';
$string['system_name'] = 'First Department Name';
$string['system_name_desc'] = 'This is the default name for the first department. You can rename it or manage other departments later by clicking on the "Departments" section.';
$string['ticket_department_label'] = 'Department';
$string['ticket_description_label'] = 'Issue Description';
$string['ticket_details_heading'] = 'Ticket details';
$string['ticket_id_label'] = 'Ticket ID';
$string['ticket_information'] = 'Ticket information';
$string['ticket_log'] = 'History';
$string['ticket_management'] = 'Manage ticket';
$string['ticket_not_found'] = 'This ticket was not found.';
$string['ticket_priority_label'] = 'Ticket Priority';
$string['ticket_status_label'] = 'Current Status';
$string['ticket_title'] = 'Subject';
$string['ticket_title_help'] = 'Describe your request with a short subject.';
$string['ticket_title_label'] = 'Ticket Title';
$string['tickets_count'] = 'tickets';
$string['ticketsystem'] = 'Ticket system';
$string['title'] = 'Subject';
$string['title_placeholder'] = 'e.g., I cannot access my purchase...';
$string['to'] = 'to';
$string['tooltip_category_hint'] = 'Select the appropriate support department.';
$string['tooltip_desc_hint'] = 'Describe your issue and any error messages.';
$string['tooltip_priority_hint'] = 'Select the priority.';
$string['tooltip_title_hint'] = 'Enter a short subject.';
$string['tooltip_upload_hint'] = 'Files up to 5 MB or the lower site limit.';
$string['tooltip_voice_hint'] = 'Record your request using the microphone.';
$string['total'] = 'Total';
$string['total_tickets_label'] = 'Total tickets';
$string['unassigned'] = 'Not Assigned';
$string['under_review_label'] = 'In progress';
$string['unknown_user'] = 'Unknown user';
$string['update_category_button'] = 'Update department';
$string['update_status_button'] = 'Save Changes';
$string['urgentnotification'] = 'Urgent support ticket';
$string['user_name_label'] = 'Submitted by';
$string['view'] = 'Preview';
$string['view_profile'] = 'View profile';
$string['view_ticket'] = 'View Ticket';
$string['viewticket'] = 'View Ticket';
$string['visit_my_portfolio'] = 'View the licence';
$string['we_are_working_hint'] = 'We are reviewing your request.';
$string['welcome_message'] = 'Supportdesk';
$string['write_your_reply'] = 'Your reply';

$string['badge_color'] = 'Badge colour';
$string['badge_color_help'] = 'This colour identifies the support department in lists and tickets.';
$string['invalidbadgecolor'] = 'Choose a valid colour in #RRGGBB format.';
$string['choose_files'] = 'Choose files';
$string['date_format'] = '%d %b %Y, %H:%M';

$string['stats_total_one'] = 'ticket in total';
$string['stats_total_many'] = 'tickets in total';
$string['stats_open_one'] = 'open ticket';
$string['stats_open_many'] = 'open tickets';
$string['stats_closed_one'] = 'closed ticket';
$string['stats_closed_many'] = 'closed tickets';

$string['department_column'] = 'Department';
$string['assignee_column'] = 'Assigned to';

$string['paste_image_hint'] = 'Paste screenshots here with Ctrl+V / ⌘V to attach them.';

// Visitor entry and department notification teams, 2026-10-03.
$string['visitoraccess'] = 'Visitor access through Magic Link';
$string['visitoraccess_desc'] = 'Off by default. Visitors verify their email before creating a ticket or opening their tickets. Requires the enabled, available 108design Magic Link plugin (auth_magiclink) and permitted account creation. Standard Moodle login remains available.';
$string['visitor_missing'] = 'Install 108design Magic Link (auth_magiclink) to make this option available.';
$string['visitor_authdisabled'] = 'Enable Magic Link in Moodle authentication.';
$string['visitor_activationrequired'] = 'Magic Link requires an active activation.';
$string['visitor_newaccountsdisabled'] = 'Allow account creation in Magic Link and Moodle authentication settings.';
$string['visitor_unavailable'] = 'Magic Link is currently unavailable. Visitor access is inactive.';
$string['visitor_heading'] = 'Contact support';
$string['visitor_intro'] = 'Verify your email address with Magic Link. Then create your ticket or open your existing tickets.';
$string['visitor_continue'] = 'Continue with email';
$string['visitor_account_hint'] = 'You do not need a password. If you do not have an account yet, Moodle creates one after email verification using your first and last name.';
$string['visitor_standardlogin'] = 'Use standard Moodle login';
$string['department_team'] = 'Support team';
$string['department_team_help'] = 'Selected active users with the system Support role receive notifications for this area. Without a department team, the explicitly assigned person on the ticket is notified, otherwise the configured support email is used as fallback. Membership does not change access permissions.';
$string['department_team_empty'] = 'No eligible people yet. Assign the Support role at system level to the people you want to select.';
$string['invalidteammember'] = 'The team contains a person without an active system-level Support role.';
$string['notify_new'] = 'New support ticket #{$a->id}: {$a->title}';
$string['notify_reply'] = 'New reply to ticket #{$a->id}: {$a->title}';
$string['notify_moved'] = 'Ticket #{$a->id} moved to your department: {$a->title}';
$string['notify_body'] = 'Ticket #{$a->id}: {$a->title}
Department: {$a->category}

Open ticket: {$a->url}';
$string['messageprovider:department_ticket'] = 'Support team: tickets in assigned departments';
$string['messageprovider:ticket_reply'] = 'Replies to your own support tickets';
$string['privacy:metadata:members'] = 'Assignment of support users to notification teams.';
$string['privacy:metadata:messages'] = 'Ticket notifications through Moodle messaging.';

$string['privacy:metadata:fallback_email'] = 'Ticket number, subject, area and ticket link are sent to the configured support contact when no staff recipient is assigned.';
