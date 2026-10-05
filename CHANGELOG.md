# 1.0.0 — 2026-10-04

## 1.0.1 — 2026-10-05

- Add the product logo above the README title at 125 by 125 pixels.
- Clarify current-release licence and availability.
- Plugin functionality and complete licence terms are unchanged.

- First stable release, with the fixed product name Support Desk.
- Conversation image galleries, optional voice messages and optional support-mailbox copies.
- Verified ticket creation, replies, attachments, protected downloads, status changes and feedback on Moodle 4.5 and 5.2.
- Voice messages and additional mailbox copies remain optional and disabled by default.

# 0.6.1-beta — 2026-10-03

- Temporarily hide the visitor setting pending public Magic Link availability/activation; retain code, hints and stored configuration.
- Staff notification order: active department team, otherwise explicitly assigned ticket staff, otherwise support mailbox.
- Mailbox is a fallback only, with no extra copy for assigned teams/staff or fallback for customer-facing replies.
- Default mailbox uses Moodle supportemail, otherwise blank; recommend configuring a fallback in DE/EN settings.
- Replace only the former noreply@example.com placeholder on upgrade; preserve configured mailboxes and explicit blanks.
- Describe fallback email data in Privacy API metadata; no mailbox account or additional access is created.

# 0.6.0-beta — 2026-10-03



- Optional verified-email visitor entry via existing auth_magiclink, explicitly off by default; native login remains available.

- Availability hints and server-side checks for provider enablement, activation and allowed account creation.

- Safe ticket return targets; no anonymous ticket/file access, extra token store or Storefront dependency.

- Multiple Support-role members per department, native notification routing and membership-filtered urgent alerts.

- New tickets/customer replies/department moves notify only the relevant team; staff replies notify the customer.

- Empty teams have no staff fallback; membership does not restrict staff ticket visibility.

- Dedicated membership schema and privacy erasure; preserve existing tickets, departments, roles and configuration.

- Matching DE/EN settings, entry UI and notification strings.



# 0.5.0-beta — 2026-10-03



- Correct fake-block class so native theme block styles apply; compact count pills and singular/plural labels.

- Icon buttons beside management selects, consistent small muted uppercase form labels and a restrained upload picker.

- Semantic status/priority badges with icons, and configurable department colours with contrast-aware text.

- Compact localised dates (German day followed by a full stop); forward icon showing an arrow in a box, without button background and safe clickable ticket cells excluding actions and controls.

- Active overview tab remains a return link from ticket details; custom licence footer removed, source notices retained.

- Paste screenshots into description/reply as removable multipart attachments; ordinary and mixed text paste is preserved.

- Recording uses a normal FA microphone; recording and table action hover states keep readable inherited text/icon colour.

- Short ticket-table column labels in DE/EN.

- Retire Live Viewing including AJAX registration, scripts, privacy metadata and transient presence table.

- Schema upgrade adds department colour while preserving existing tickets, replies, departments and support role.



# 0.4.0-beta — 2026-10-03



- Native theme typography, Moodle tabs/breadcrumbs, Bootstrap tables/forms and accessible core icons.

- Statistics and ticket side panels use generated Moodle fake blocks, with a no-region fallback.

- Tailwind, extra icon fonts and SweetAlert removed from source, build, metadata and runtime.

- Shared AMD files/recorder controls: format detection, preview/removal, microphone and URL cleanup.

- Presence retained with native hidden states and heartbeat lifecycle; image preview and deletion use core dialogs.

- Complete matching DE/EN string sets and restrained notification emails. Colour overrides retired.

- Department editing uses a normal prefilled form; successful POST redirects prevent duplicate resubmission.

- No schema, role, visitor onboarding or Storefront changes.



# 0.3.0-beta — 2026-10-03



Remove the inherited request for the nonexistent AMD module `bootstrap` from the dashboard.

The category action retains its native title tooltip and no longer depends on a particular

Bootstrap version or theme. No schema or role changes.



# 0.2.0-beta — 2026-10-03



CMS browser acceptance exposed SweetAlert selecting its anonymous AMD wrapper instead of

the global dialog API. The local classic-script build now exposes the global API without

registering an anonymous AMD module. Normal versioned upgrade; no schema or role changes.



# 0.1.0-beta — 2026-10-03



Independent GPL v3-or-later customer support fork of Academic Ticket System 3.2.1.



- New `local_supportdesk` component and own routes/tables. New installation; no ATS data migration.

- Automatic **Support** system role with plugin-only permissions; invalid role archetype fixed.

- Ticket-level access for original/reply attachments and Presence/urgent APIs; validated actions/uploads.

- Privacy API aligned with the actual tables; user export, erasure and bulk requests.

- Scoped, locally bundled CSS/icons/dialog assets; no Tailwind CDN at runtime. Tailwind remains build tooling.

- Optional passwordless Moodle sign-in via an independently installed `auth_magiclink`; no hard dependency.



Validation: PHP 8.3 syntax, reproducible 21-asset build, synthetic external-footer browser regression,

34 functional assertions per MySQL/PostgreSQL lane on isolated Moodle 5.2, and three page-render checks

per database. Complete PHPUnit/fresh-install matrix, Moodle 4.5 and real browser upload/reply workflows

remain outstanding. This is a beta, not full production acceptance.

