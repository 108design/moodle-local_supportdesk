# Support Desk for Moodle

Manage support requests inside Moodle. Users create tickets, follow their status
and reply to the support team. Staff organise tickets by support area, assign them
to colleagues, and manage the conversation in one place.

## Features

- Private tickets with status, priority, assignment and conversation history.
- Support areas with configurable teams and coloured badges.
- File attachments, pasted screenshots and optional audio recording.
- Email and Moodle notifications for customers and support staff.
- Ticket statistics and filters for finding requests that need attention.
- An interface that follows the installed Moodle theme.

The current release is **0.7.0-beta**, targeting Moodle 4.5–5.2. Try the ticket,
attachment and notification workflows on a test site before production use.

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
area and description, and add any files that help explain the request. Screenshots
pasted into the description or reply field join the attachment list. Files are
uploaded when you submit the form; you can remove them before submitting.

Where supported by the browser, the audio control can record a message after you
grant microphone permission. Review the recording before submitting it.

Users can view and reply to their own tickets. Support staff can manage tickets
across support areas. The same access rules apply to ticket and reply attachments.

## Support teams and notifications

Under **Support Desk → Support areas → Edit**, select the active Support-role users
who belong to each team. New tickets, customer replies and tickets moved to a
support area notify its active team.

If no active team is configured, notifications go to the ticket's assigned active
staff member. If neither is available, they go to the configured support mailbox.
Configure a fallback mailbox in the plugin settings so unassigned requests still
reach someone. This email address does not itself grant access to tickets.

Staff replies notify the ticket owner. Moodle notification preferences apply;
users do not receive notifications for their own actions. Team membership controls
notification routing, while the Support role controls ticket access.

## Site settings and display

Administrators can enable or disable Support Desk, select assignable support roles,
and configure the support mailbox. Disabling the plugin prevents access to its
ticket pages and services.

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

GNU General Public License version 3 or later. See [LICENSE.md](LICENSE.md) for the full terms.
Third-party assets retain their own licences. The bundled PhotoSwipe 5.4.4 viewer
is MIT licensed; see [its licence](thirdparty/photoswipe/LICENSE).

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

This program comes without warranty; see the GPL for the applicable terms.
