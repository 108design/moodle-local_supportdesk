<p align="center">
  <img src="https://raw.githubusercontent.com/108design/moodle-local_supportdesk/main/docs/branding/logo.svg" alt="Support Desk logo" width="125" height="125">
</p>

# Support Desk for Moodle

Manage support requests inside Moodle. Users create tickets, follow their status
and reply to the support team. Staff organise tickets by support area, assign them
to colleagues, and manage the conversation in one place.

## Screenshots

<details>
<summary>View screenshots (6)</summary>

Click a preview to open the full-size screenshot.

<table>
<tr>
<td align="center" width="50%" valign="middle">
<a href="https://raw.githubusercontent.com/108design/moodle-local_supportdesk/main/docs/screenshots/sd-new-no-login.jpg"><img src="https://raw.githubusercontent.com/108design/moodle-local_supportdesk/main/docs/screenshots/sd-new-no-login.jpg" width="115" height="160" alt="Create a guest ticket without signing in"></a><br>
<sub>Create a guest ticket without signing in</sub>
</td>
<td align="center" width="50%" valign="middle">
<a href="https://raw.githubusercontent.com/108design/moodle-local_supportdesk/main/docs/screenshots/sd-new-logged-in.jpg"><img src="https://raw.githubusercontent.com/108design/moodle-local_supportdesk/main/docs/screenshots/sd-new-logged-in.jpg" width="156" height="160" alt="Create a ticket with files or a voice message"></a><br>
<sub>Create a ticket with files or a voice message</sub>
</td>
</tr>
<tr>
<td align="center" width="50%" valign="middle">
<a href="https://raw.githubusercontent.com/108design/moodle-local_supportdesk/main/docs/screenshots/support-desk-tickets.jpg"><img src="https://raw.githubusercontent.com/108design/moodle-local_supportdesk/main/docs/screenshots/support-desk-tickets.jpg" width="300" height="91" alt="Ticket overview and status"></a><br>
<sub>Ticket overview and status</sub>
</td>
<td align="center" width="50%" valign="middle">
<a href="https://raw.githubusercontent.com/108design/moodle-local_supportdesk/main/docs/screenshots/support-desk-ticket01.jpg"><img src="https://raw.githubusercontent.com/108design/moodle-local_supportdesk/main/docs/screenshots/support-desk-ticket01.jpg" width="300" height="147" alt="Ticket details, assignment and conversation"></a><br>
<sub>Ticket details, assignment and conversation</sub>
</td>
</tr>
<tr>
<td align="center" width="50%" valign="middle">
<a href="https://raw.githubusercontent.com/108design/moodle-local_supportdesk/main/docs/screenshots/support-desk-ticket02.jpg"><img src="https://raw.githubusercontent.com/108design/moodle-local_supportdesk/main/docs/screenshots/support-desk-ticket02.jpg" width="300" height="135" alt="Reply to a ticket and add attachments"></a><br>
<sub>Reply to a ticket and add attachments</sub>
</td>
<td align="center" width="50%" valign="middle">
<a href="https://raw.githubusercontent.com/108design/moodle-local_supportdesk/main/docs/screenshots/support-desk-departments.jpg"><img src="https://raw.githubusercontent.com/108design/moodle-local_supportdesk/main/docs/screenshots/support-desk-departments.jpg" width="289" height="160" alt="Configure support departments and teams"></a><br>
<sub>Configure support departments and teams</sub>
</td>
</tr>
</table>

</details>

## Features

- Private tickets with status, priority, assignment and conversation history.
- Support areas with configurable teams and coloured badges.
- File attachments, pasted screenshots and optional audio recording.
- Email and Moodle notifications for customers and support staff.
- Ticket statistics and filters for finding requests that need attention.
- An interface that follows the installed Moodle theme.

Support Desk requires Moodle 4.5–5.2.

## Installation and staff access

1. Install the plugin as `local/supportdesk` below Moodle's plugin directory.
2. Complete installation through **Site administration → Notifications**.
3. Assign the **Support** system role to your support staff under
   **Site administration → Users → Permissions → Assign system roles**.
4. Configure the plugin settings and create the required support areas.

The Support role permits ticket management, assignment, replies and attachment
access. It does not grant general Moodle administration rights. Support-area
administration requires its separate management permission. The plugin settings
let you select which support roles are available for ticket assignment.

Support Desk is installed separately from Academic Ticket System; existing tickets
from that plugin are not imported during installation.

## Creating and following tickets

Sign in to Moodle and open Support Desk. Create a ticket with a subject, support
area and issue, choose a priority from the badge radio buttons, and add any files
that help explain the request. Screenshots
pasted into the description or reply field join the attachment list. Files are
uploaded when you submit the form; you can remove them before submitting.

Where supported by the browser, the audio control can record a message after you
grant microphone permission. Review the recording before submitting it.

Users can view and reply to their own tickets. Support staff can manage tickets
across support areas. The same access rules apply to ticket and reply attachments.

## Tickets without login

This feature is an explicit opt-in and is **disabled by default**. In the plugin
settings, configure a Cloudflare Turnstile site key and secret for your Moodle
hostname, then select **Guest ticket without sign-in (Turnstile)** under **Support Desk access**. Keys alone do not
activate it. Share `/local/supportdesk/entry.php` as the support entry.
This flow does not create Moodle accounts.

Visitors provide their name, email, area, subject, issue and priority, with optional file
attachments and pasted screenshots. Turnstile is checked on the server before
saving. Up to five public tickets per IP can be submitted in ten minutes; users
behind the same network share this limit. Logged-in ticket creation is unaffected.
Technical browser data is processed by Cloudflare; ticket content is not sent
for verification. Failed or unavailable verification creates no ticket.

Contacts are initially unverified. A submitted email never links an existing
account automatically. Staff replies are emailed to the submitted contact address,
including the public reply text. This does not verify the address or grant access. Staff
see the contact details and unverified state. Staff/team/fallback notifications
continue normally. Ticket history and attachment downloads require Moodle login.

After submission, use **Sign in and claim ticket** within 24 hours in the same
browser. Sign in through an available Moodle login method with a confirmed
account using the submitted email address, then confirm association. Both the
browser submission proof and matching account email are required. A new account,
if needed, must be created through the site's normal process. Switching browsers,
clearing the session or losing the receipt removes this self-service path.

## Support teams and notifications

Under **Support Desk → Support areas → Edit**, select the active Support-role users
who belong to each team. New tickets, customer replies and tickets moved to a
support area notify its active team.

If no active team is configured, notifications go to the ticket's assigned active
staff member. If neither is available, they go to the configured support mailbox.
Configure a fallback mailbox in the plugin settings so unassigned requests still
reach someone. Guest reply emails use this address as Reply-To when configured.
Replies to those emails are not currently imported into tickets; continue in the
ticket system after login and association. This email address does not itself grant access to tickets. The optional **Send all tickets to this address as well** setting is disabled by default. Enable it to copy every ticket notification, including staff replies, to the mailbox while retaining normal recipient routing. An empty or invalid address receives no copy. The additional copy is omitted if the same address already receives the native Moodle email for that event, including permitted notification-email overrides. In-app-only notifications do not suppress the copy.

Staff replies notify the ticket owner. Moodle notification preferences apply;
users do not receive notifications for their own actions. Team membership controls
notification routing, while the Support role controls ticket access.

## Site settings and display

Administrators can enable or disable Support Desk, select assignable support roles,
configure the support mailbox, and enable voice-message recording (disabled by default). Disabling the plugin prevents access to its
ticket pages and services.

**Support Desk access** defaults to sign-in required. Administrators may enable
guest tickets with Turnstile as an alternative. Standard Moodle sign-in remains
available in both modes.

Entry, submission and confirmation panels use the active theme's login layout.
When Frontpage provides the configured, published login page, these panels reuse
its actual panel design, backgrounds and navigation/footer settings.

Image attachments appear as thumbnails. Open an image to browse all ticket images,
including reply attachments, in a keyboard-accessible lightbox with a thumbnail strip.

Support-area colours can be chosen in their administration form. Ticket dates
follow each user's timezone and language. Information panels use the theme's block
region, or appear below the main content when the theme provides no block region.

## Maintainer and origin

Support Desk is an independently maintained derivative of
[Academic Ticket System](https://github.com/abdelrhman2049/moodle-local_academic_ticket_system).
Original work: © 2025–2026 learn-ix <support@learn-ix.com>; UI credits: Boghdady.
Maintained by Andreas Giesen <andreas@108design.com> (108design).
Original authorship and copyright notices are retained.

## License

**Available free of charge under the terms of the applicable license.**

GNU General Public License version 3 or later. See [LICENSE.md](LICENSE.md) for the full terms.
Third-party assets retain their own licences. The bundled PhotoSwipe 5.4.4 viewer
is MIT licensed; see [its licence](thirdparty/photoswipe/LICENSE).

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

This program comes without warranty; see the GPL for the applicable terms.
