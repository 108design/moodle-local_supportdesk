# Supportdesk for Moodle

A standalone customer support desk forked from learn-ix/Boghdady Academic Ticket System 3.2.1.
Consolidated release: **0.6.1-beta**. Intended for Moodle 4.5–5.2; verified environments are recorded in the release notes.

## Installation

Install this directory as `local/supportdesk` and run Moodle's normal plugin installation.
The component is `local_supportdesk`. This is a new installation, not an in-place update or data migration from Academic Ticket System.

Installation creates a **Support** role (shortname `supportdeskagent`) with ticket management, assignment, reply,
view and attachment permissions at system context. Assign this role to staff in Site administration → Users → Permissions.
It grants no general Moodle administration permission. Department administration remains a separate manager permission.
Settings select assignable support roles; only system-role holders with support permissions appear as assignees.

Authenticated customers can create and view their own tickets. The same ticket ownership/staff checks protect both
original and reply attachments. Disabling Supportdesk disables its entry points and APIs.

## Styling and assets

The installed Moodle theme owns Bootstrap, typography, colours, icons and dialog styling.
There is no Tailwind, additional Bootstrap bundle, icon font or SweetAlert dependency.
The small readable `styles/style.css` is scoped to Supportdesk and contains scoped layout, upload and semantic badge rules.
Statistics and ticket side panels are generated with Moodle's fake-block API, with no block plugin or
editable HTML-block instance. If the theme has no block region, these panels render below the main content.
Ticket status, description, conversation and reply remain in the main area.

Build named Moodle AMD modules with `npm ci --ignore-scripts` and `npm run build`; verify with `npm run check`.
`npm test` exercises recorder APIs and safe ticket-row navigation without requesting a microphone.
Shared files/audio controls support create and reply forms. Recording uses a browser-supported audio MIME type,
releases microphone tracks on stop/navigation and retains the same multipart backend fields.
DE and EN language files have matching keys; no fixed English UI copy is required.

Department administration includes a colour picker for badges. Six-digit hex colours are validated;
black or white text is selected for readable contrast. Priorities and states use fixed semantic colours/icons.
Dates follow the user timezone and locale with abbreviated month, no weekday. Count pills use singular/plural labels.
Ticket rows navigate only from noninteractive cells; actions, checkboxes and future bulk controls are excluded.
Live Viewing and its transient presence table/API are removed. No ticket content or role data is removed.
Author credits and the GPL text remain in source/distribution; the custom visible footer notice is removed.

## Optional visitor access and department teams

Visitor ticket entry is **off by default**, including when `auth_magiclink` is already installed. The administrator setting is temporarily hidden until 108design Magic Link is publicly available and activatable.
The integration, setting implementation, translated availability hints and stored option remain in the code.
For its later release, restore the showvisitoraccesssetting flag in settings.php; the option still requires an installed,
enabled and usable Magic Link provider and allowed new-account creation.
Disabling/removing the provider also disables the visitor route at runtime.

Unauthenticated visitors enter through `/local/supportdesk/entry.php`, verify their email using Magic Link and then
return to the ticket form or the requested ticket. Existing accounts keep their authentication method; new visitors
receive a Moodle account through Magic Link without a password. There is no anonymous ticket or attachment access,
second token system, bundled authentication plugin or Storefront integration. Standard login remains available.

Department administration lets administrators select multiple active users with the system **Support** role.
Teams receive native Moodle notifications for new tickets, customer replies and tickets moved into their department.
Without an active department team, the explicitly assigned active person on the ticket is notified. If neither
exists, the configured support mailbox receives an email, with no extra copy when staff are assigned. The mailbox
defaults to Moodle's configured support contact, otherwise blank; configuring one is recommended. It creates no
user account and grants no ticket access. Staff replies still notify only the owner.
Urgent-ticket UI alerts continue to follow department memberships. Revoked roles, suspended/deleted users and the triggering actor
are excluded. Staff replies notify the ticket owner. Normal Moodle notification preferences still apply.

Membership controls notification routing only. Support staff can still manage tickets in other departments;
customers can only access their own tickets. Existing departments start with empty teams, and no users are assigned
roles automatically. Configure teams explicitly under Supportdesk > Support areas > Edit.

Screenshots pasted into the description or reply field are added to the same attachment list as selected/dropped files. They are only uploaded when the form is submitted; no SideNotes dependency or immediate upload.

## Licence and provenance

GNU GPL version 3 or later; see [LICENSE.md](LICENSE.md).
Original Academic Ticket System: © 2025–2026 learn-ix, support@learn-ix.com; UI credits: Boghdady.
Upstream: https://github.com/abdelrhman2049/moodle-local_academic_ticket_system
Fork maintainer: Andreas Giesen <andreas@108design.com> (108design).
Supportdesk is maintained and versioned independently of Academic Ticket System.

Modified 2026-10-03 by Andreas Giesen / 108design: independent component name, installation/role fixes,
ticket-level attachment authorisation, actual-schema privacy export/erasure, validated actions and native Moodle UI.
Distributed modifications remain under GPL v3 or later. No upstream endorsement is implied.

This program comes without warranty; see the GPL for the applicable terms. Third-party assets retain their own licences.

