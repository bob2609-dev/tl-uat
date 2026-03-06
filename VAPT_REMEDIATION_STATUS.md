# VAPT Remediation Status

This document tracks remediation progress for prioritized items.

## Legend
- Status: Pending | In Progress | Done | Verified
- Risk: High | Medium

## Summary Checklist
- [x] Stored XSS (Inventory module) — Status: Verified — Risk: High
- [x] Reflected XSS (link entry points: linkto.php, ltx.php) — Status: Verified — Risk: High
- [x] File download/header hardening (direct_image.php) — Status: Verified — Risk: Medium
- [x] Reflected XSS (debug_direct.php, redmine_direct.php) — Status: Done (awaiting verification) — Risk: High
- [x] API input/header hardening (redmine_status_api.php) — Status: Done (awaiting verification) — Risk: Medium
- [x] Time-based SQLi (Search) — Status: Done (awaiting verification) — Risk: High
- [x] Role modification via DevTools — Status: Done (awaiting verification) — Risk: High
- [x] Predictable machineOwner validation — Status: Done (awaiting verification) — Risk: High
- [x] Exposed config/dev/info files review — Status: Done (awaiting verification) — Risk: Medium
- [ ] Improve error/stack exposure — Status: Pending — Risk: Medium
 - [x] Login flow validation (cookies + CSRF) — Status: Done — Risk: Medium

## Item Details

## Implementation Guide: What to change (code) and on the server

The following items are already implemented in this codebase. Use this as a guide to port or verify in other environments.

### A) Fix login loop: make session cookie set reliably
- __Files__: `lib/functions/common.php`, `login.php`
- __Changes__:
  - `lib/functions/common.php` → function `doSessionStart($setPaths=false)`:
    - Use PHP 7.2–compatible cookie setup. For PHP < 7.3, set flags via `ini_set()` and call legacy `session_set_cookie_params($lifetime, $path, $domain, $secure, $httponly)`.
    - Derive `secure` from `config_get('force_https')` if set; otherwise detect HTTPS including proxy headers `HTTP_X_FORWARDED_PROTO`/`X_FORWARDED_PROTO`.
    - Call `session_start()` immediately when `session_status() == PHP_SESSION_NONE` to emit `Set-Cookie` on the first response.
  - `login.php`:
    - Start session as early as possible: after including `common.php`, call `doSessionStart(true)` before any output or redirects. This ensures the login POST response includes `Set-Cookie`.

### B) CSRF initialization order and usage
- __Files__: `lib/functions/csrf.php`, `lib/execute/bugs_view.php`, `lib/execute/ajax_redmine_status.php`
- __Changes__:
  - Ensure include order on entry points: include `lib/functions/common.php` first, then `lib/functions/csrf.php`.
  - Initialize once per request: call `doSessionStart(false)` then `csrfguard_start()` in the entry script (removed any auto-exec in `lib/functions/csrf.php`).
  - Generate tokens via `csrfguard_generate_token('form-name')` and validate on POST via `csrfguard_validate_token($token_name, $token_value)` for forms and AJAX.

### C) Harden cookie flags
- __Files__: `lib/functions/common.php`
- __Changes__:
  - Set `HttpOnly` and `SameSite=Lax` (or `ini_set('session.cookie_samesite', 'Lax')` on PHP 7.2).
  - Set `Secure` when running over HTTPS (see A).

### D) Restrict diagnostic and sensitive endpoints; block listings
- __Files__: `.htaccess`, `diagnostic.php`
- __Changes__:
  - `.htaccess`: disable directory listing, block `/install/`, protect config/backup/info files. See `SECURITY_APACHE_HARDENING.md` for exact directives.
  - `diagnostic.php`: restrict access to localhost; avoid forcing error display.

### E) Server-side configuration (Apache/PHP)
- __Apache__:
  - Ensure HTTPS termination; if behind a reverse proxy/load balancer, forward `X-Forwarded-Proto https`.
  - Optionally enable HSTS at the proxy or Apache level after confirming HTTPS everywhere.
  - Apply `.htaccess` rules from repo; confirm `AllowOverride All` where required.
- __PHP__:
  - Production `php.ini`: `display_errors=Off`, `log_errors=On`, `error_log` path configured.
  - Session cookie flags (if not set in code):
    - `session.cookie_httponly=1`
    - `session.cookie_secure=1` on HTTPS
    - `session.cookie_samesite=Lax` (PHP 7.3+)
  - On PHP 7.2, rely on code-based `ini_set()` as implemented in `doSessionStart()` for SameSite.

### F) Testing checklist (login/session/CSRF)
- __Login cookie__:
  - On login POST response, confirm `Set-Cookie: PHPSESSID=...; HttpOnly; [Secure]; [SameSite]`.
  - Subsequent GET requests include `Cookie: PHPSESSID=...`.
- __Session validity__:
  - `index.php` should not redirect back to `login.php` unless session expired; verify `$_SESSION['userID']` set and `lastActivity` updates.
- __CSRF__:
  - Forms include hidden CSRF token fields and server validates; AJAX endpoints validate and reject missing/invalid tokens.

### G) Rollout/rollback notes
- Deploy `common.php` and `login.php` together to ensure consistent session behavior.
- If issues arise, temporarily revert the early `doSessionStart(true)` in `login.php` and capture server logs plus HAR to diagnose headers.

### 1) Stored XSS (Inventory module)
- Status: Pending
- Risk: High
- Suspected Areas: `lib/inventory/` (e.g., `setInventory.php`, `tlInventory.class.php`)
- Planned Actions:
  - Output encoding for all rendered fields.
  - Input validation/sanitization for text fields (whitelist permissible chars/lengths).
  - Use centralized escaping helpers where available.
- Evidence/Notes:
- Owner:
- ETA:

### 2) Time-based SQLi (Search)
- Status: Done (awaiting verification)
- Risk: High
- Suspected Areas: search endpoints/queries, dynamic filters.
- Planned Actions:
  - Parameterize all queries (prepared statements/bind params).
  - Remove string concatenation for SQL.
  - Add query timeouts/logging for anomaly detection.
- Evidence/Notes:
  - Parameter binding implemented across search modules in `lib/search/searchCommands.class.php`:
    - `searchTestSuites()`, `searchTestCases()`, `searchReqSpec()`, `searchReq()` use `$db->exec_query_params()` / `$db->fetchRowsIntoMapParams()` and `$db->likeParamContains()` for LIKEs.
  - Targeted SQL logging added in `lib/functions/database.class.php`:
    - `exec_query_params()` logs timings and safely truncated bound parameters for anomaly detection.
  - ORDER BY whitelist helpers added in `lib/functions/database.class.php`:
    - `orderByColumn($candidate, $allowed, $default)` and `orderByDirection($candidate, $default)` to prevent dynamic identifier injection.
  - No dynamic ORDER BY found in current search code; helpers are available for future use.
- Owner:
- ETA:

### 3) Role modification via DevTools
- Status: Done (awaiting verification)
- Risk: High
- Suspected Areas: client-side role/permission flags, server-side missing authorization.
- Planned Actions:
  - Enforce authorization on server side for all privilege-changing actions.
  - Ignore/validate any client-submitted role fields.
  - Add server-side checks using existing rights APIs before updates.
- Files and functions changed:
  - `lib/usermanagement/usersEdit.php`
    - In `doUpdate()`, added server-side hardening before `initializeUserProperties()`:
      - Validate requested `rights_id` against existing roles via `tlRole::getAll()`; preserve current role on invalid IDs.
      - Block non-admin users from assigning the global Admin role (`TL_ROLES_ADMIN`).
      - Default to the target user’s existing `globalRoleID` if tampering detected.
  - `lib/functions/users.inc.php`
    - Ensured array casting when consuming `resetPassword()` result in `usersEdit.php` to avoid type ambiguity.

- Verification steps:
  - As a non-admin, attempt to elevate a user to Admin by modifying the request via DevTools or API client → role should remain unchanged; other fields update as allowed.
  - As an Admin, assign Admin role successfully.
  - Review audit logs for saves and ensure no unauthorized role changes recorded.

- Rollback:
  - Revert `lib/usermanagement/usersEdit.php` changes; no schema changes.

### 4) Predictable machineOwner validation
- Status: Done (awaiting verification)
- Risk: High
- Suspected Areas: `lib/inventory/setInventory.php`, `lib/inventory/tlInventory.class.php` (direct assignment of `machineOwner`).
- Planned Actions:
  - Validate `machineOwner` against allowed set for current user.
  - Map external identifiers to internal IDs server-side; do not trust client-provided IDs.
  - Log and reject mismatches.
- Files and functions changed:
  - `lib/inventory/setInventory.php`
    - Before save, validate `machineOwner` maps to an existing active user (`tlUser::readFromDB()` and `isActive==1`).
    - Reject invalid/inactive owner with JSON `{ success:false, userfeedback: lang_get('inventory_invalid_owner') }` and exit.
  - `lib/functions/tlInventory.class.php`
    - `initInventoryData()` updated to accept both arrays and objects (caller-agnostic), preventing type mismatches.
    - Defense-in-depth validation added in `checkInventoryData()` to ensure `ownerId` is an active user; set `userFeedback` to `inventory_invalid_owner` and return error when invalid.
  - `locale/en_US/custom_strings.txt`, `locale/en_GB/custom_strings.txt`
    - Added `TLS_inventory_invalid_owner` message used by `lang_get('inventory_invalid_owner')`.

- Verification steps:
  - Missing/zero `machineOwner` → request rejected with `inventory_invalid_owner` message.
  - Non-existent user ID → rejected.
  - Inactive user ID → rejected.
  - Valid active user → inventory saves; `getCurrentData()` reflects owner ID.

- Rollback:
  - Revert `setInventory.php` and `tlInventory.class.php` changes; no schema changes.

### 5) Exposed config/dev/info files review
- Status: Done (awaiting verification)
- Risk: Medium
- Relevant Files: `.htaccess`
- Current Mitigations:
  - Sensitive files blocked via `FilesMatch`.
  - Directory listing disabled globally (`Options -Indexes`).
  - `/install/`, `/logs/`, `/sql/`, vendor and VCS folders blocked.
  - `upload_area` PHP execution blocked.
  - `diagnostic.php` restricted to localhost.
  - Security headers enabled: `X-Content-Type-Options: nosniff`, `X-Frame-Options: DENY`, `Referrer-Policy: strict-origin-when-cross-origin`, `Permissions-Policy` minimal; `X-Powered-By` unset.
- Recent Changes (2025-08-28):
  - Enabled security headers block in `.htaccess` and kept whitelist for `lib/usermanagement/userInfo.php`.
- Verification steps:
  - From a remote host, access `diagnostic.php` → expect 403.
  - Attempt to browse directories like `/lib/` → listing disabled.
  - Attempt to access `.env`, `*.sql`, `*.bak`, `.git/config`, `/install/` → denied.
  - Request any page and inspect response headers → headers present as configured.
- Rollback:
  - Comment the headers `<IfModule mod_headers.c>` block and rewrite deny rules as needed; reload Apache.

### 6) Improve error/stack exposure
- Status: Pending
- Risk: Medium
- Relevant Files: `.user.ini`, `php.ini`, `diagnostic.php`, global error handlers.
- Planned Actions:
  - Ensure display_errors Off in production.
  - Verify no stack traces or sensitive paths leak to users.
  - Standardize user-friendly error pages; detailed logs to files only.
- Evidence/Notes:
- Owner:
- ETA:

## Recently Completed (context)
- CSRF enforcement enabled: `lib/functions/csrf.php` (csrfguard_start()).
- Cookie flags hardened (Secure/HttpOnly/SameSite): `lib/functions/common.php`.
- `diagnostic.php` restricted to localhost and error display not forced.
- Apache hardening: `.htaccess` blocks `install/`, sensitive files; docs in `SECURITY_APACHE_HARDENING.md`.
  - Login flow validated end-to-end (CSRF tokens injected; session cookie persists across GET→POST).
  - Whitelisted `lib/usermanagement/userInfo.php` in `.htaccess` to resolve 403 while maintaining directory/file protections. Verified access works.

### L) Reflected XSS Hardening (debug_direct.php, redmine_direct.php)
- Status: Done (awaiting verification)
- Risk: High
- Objective: Prevent reflected XSS via unescaped outputs and add defensive headers.

- Files changed:
  - `debug_direct.php`
    - Added `X-Content-Type-Options: nosniff` before content output to reduce MIME sniffing risk.
  - `redmine_direct.php`
    - Escaped all user/remote-controlled fields with `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` in `displayIssue()` for `id`, `subject`, `status.name`, and `description`.
    - Built Redmine link using `rawurlencode()` for `id` and escaped href; added `rel="noopener noreferrer"`.
    - Added `<meta charset="utf-8">` and `X-Content-Type-Options: nosniff` header.
    - Sanitized request variables: `(string)$action`, `intval($id)` and sanitized POST handling for messages.

- Verification steps:
  - Access `redmine_direct.php?id=<payload>` and via POST forms; ensure no script execution and safe rendering.
  - Check response headers include `X-Content-Type-Options: nosniff`.

- Rollback:
  - Revert the listed files. No schema/config changes.

### M) API Input/Header Hardening (redmine_status_api.php)
- Status: Done (awaiting verification)
- Risk: Medium
- Objective: Prevent info leakage and enforce safe JSON responses.

- Files changed:
  - `redmine_status_api.php`
    - Disabled `display_errors` and enabled `log_errors`.
    - Always send `Content-Type: application/json; charset=UTF-8` and `X-Content-Type-Options: nosniff`.
    - Sanitized `bug_id` via `intval()` before composing URL path.
    - Kept JSON responses generic; no reflection of raw inputs.

- Verification steps:
  - Call `/redmine_status_api.php?bug_id=<payload>`; confirm numeric coercion, stable JSON, and presence of `nosniff` header.

- Rollback:
  - Revert `redmine_status_api.php`. No schema/config changes.

### H) Harden dynamic ORDER BY clauses (SQL injection prevention)
- Status: Verified
- Risk: High
- Objective: Eliminate dynamic ORDER BY concatenation by enforcing whitelist-based column and direction validation.

- Files and functions changed (application only; vendor excluded):
  - `lib/functions/database.class.php`
    - Added helpers: `orderByColumn($candidate, $allowed, $default)` and `orderByDirection($candidate, $default)`.
  - `lib/functions/testplan.class.php`
    - `get_builds()` — ORDER BY hardened with allowed columns: `name`, `id`, `notes`, `active`, `is_open`.
    - `get_builds_for_html_options()` — ORDER BY hardened; preserves natural sort when ordering by `name`.
    - `getLTCVOnTestPlan()` — ORDER BY hardened via whitelist helpers.
    - `getLTCVOnTestPlanPlatform()` — ORDER BY hardened via whitelist helpers.
  - `lib/functions/tlCodeTracker.class.php`
    - `getAll()` — ORDER BY hardened; supports formats like `name DESC` and `name:DESC`.
  - `lib/functions/tlIssueTracker.class.php`
    - `getAll()` — ORDER BY hardened; allowed columns: `name`, `id`, `type`.
  - `lib/functions/tlReqMgrSystem.class.php`
    - `getAll()` — ORDER BY hardened; allowed columns: `name`, `id`, `type`.

- Deployment notes (prod):
  - Replace only the above files; no database migrations required.
  - Clear opcode cache (e.g., PHP-FPM restart) to load changes.
  - No config changes needed; behavior preserved when no `orderBy` is provided.

- Rollback:
  - Revert the listed files to previous versions. No stateful changes involved.

- Verification steps (targeted to ORDER BY hardening):
  - TestPlan builds pages (that call `get_builds()` and `get_builds_for_html_options()`):
    - Sort by Name/ID/Active/Open; confirm correct ordering and natural sort on Name.
  - Code Trackers list (`tlCodeTracker::getAll()` consumer):
    - Validate sorting by Name/ID/Type; try inputs like `name DESC` and `name:DESC`.
  - Issue Trackers list (`tlIssueTracker::getAll()` consumer):
    - Validate sorting by Name/ID/Type.
  - Requirement Management Systems list (`tlReqMgrSystem::getAll()` consumer):
    - Validate sorting by Name/ID/Type.
  - Negative tests: attempt to inject invalid columns/directions; verify default safe ordering is applied and no SQL error occurs.

- Evidence/Notes:
  - Manual smoke tests executed on the above pages confirm correct sorting and no SQL errors.
  - Attempted invalid ORDER BY inputs fall back to default safe ordering.

### I) Stored XSS in Inventory Module
- Status: Verified
- Risk: High
- Objective: Prevent stored HTML/JS from rendering in Inventory grid and forms.

- Files and functions changed:
  - `gui/templates/tl-classic/inventory/inventoryView.tpl`
    - Added `renderer: Ext.util.Format.htmlEncode` to columns: `name`, `ipaddress`, `purpose`, `hardware`, `owner`, `notes`.
  - `lib/inventory/setInventory.php`
    - In `init_args()`, applied `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` to string fields: `machineName`, `machineIp`, `machineNotes`, `machinePurpose`, `machineHw` before persisting.

- Approach:
  - Defense-in-depth: sanitize on input, encode on output.
  - Client-side grid encoding to neutralize any existing stored payloads returned by `getInventory.php`.

- Verification steps:
  - Create/update inventory items with payloads like `<img src=x onerror=alert(1)>`, `<script>alert(1)</script>`, and `" onmouseover=alert(1) "` in `name`, `purpose`, `hardware`, `notes`.
  - Open Inventory page (`inventoryView.php`) and confirm payloads render as text (no JS execution) in all columns.
  - Verify normal text still displays correctly. Confirm CRUD continues to work.

- Rollback:
  - Revert the listed files. No schema changes.

- Evidence/Notes:
  - Manual smoke test on 2025-08-27 confirmed no JS execution, tags rendered as text in all grid columns.
  - Delete confirmation encodes device name; no HTML injection observed.

### J) Reflected XSS in Link Entry Points (linkto.php, ltx.php)
- Status: Verified
- Risk: High
- Objective: Prevent reflected XSS via GET parameters in direct-link controllers.

- Files and functions changed:
  - `linkto.php`
    - `init_args()` — cast inputs to string, whitelist `item` to `req`, `reqspec`, `testcase`, `testsuite`; sanitize `anchor` to safe fragment chars and normalize; keep `version` as trimmed string.
    - `buildLink()` — use `rawurlencode()` for `item`, `id`, `version`, `tprojectPrefix`; pass encoded `anchor` as param to inner load.
    - Message composition — wrap user-controlled values with `htmlspecialchars(..., ENT_QUOTES, 'UTF-8')` before `sprintf()` for `invalid_item` and `testproject_not_found`.
  - `ltx.php`
    - Inner dispatch — whitelist `item`/driver to `exec`, `xta2m` before calling `launch_inner_*`.
    - `init_args()` — sanitize `anchor` (safe chars only), whitelist `item` (`exec`, `xta2m`), cast IDs to `intval`, strings via `(string)`; `feature_id` normalized to `int`.
    - `build_link_exec()` — URL-encode all parameters with `rawurlencode()`; include missing `tcversion_id` concatenation; pass encoded `anchor`.

- Approach:
  - Defense-in-depth: validate/whitelist inputs, constrain anchors, URL-encode query construction, and escape any user-controlled text in messages.

- Verification steps:
  - Access `linkto.php` and `ltx.php` with payloads in `id`, `item`, `anchor`, and `version` such as `<img src=x onerror=alert(1)>`, `<script>alert(1)</script>`.
  - Confirm no JavaScript executes; parameters appear encoded in built URLs; invalid `item` values are rejected with safe messages.
  - Anchor navigation works only with sanitized fragments; no HTML injection in error messages.

- Rollback:
  - Revert `linkto.php` and/or `ltx.php` to previous versions. No schema or config changes.

## Verification Steps
- Re-run VAPT tests for each item once code changes are in place.
- Capture evidence (screenshots/logs) and update Status to Verified.

## UAT → PROD Deployment Plan (2025-08-28)

### 1) Scope of this release
- __Security fixes__: Role escalation prevention and Inventory owner validation
- __Files changed__:
  - `lib/usermanagement/usersEdit.php`
  - `lib/inventory/setInventory.php`
  - `lib/functions/tlInventory.class.php`
  - `locale/en_US/custom_strings.txt`
  - `locale/en_GB/custom_strings.txt`
- __No DB schema changes__ and no config changes required.

### 2) Pre-deployment checklist
- __Backups__:
  - Take a filesystem backup of the target files on PROD.
  - Ensure routine DB backup is current (no schema changes, but good practice).
- __Parities__:
  - PHP version in PROD matches UAT major/minor; SameSite/HttpOnly behavior consistent.
  - Confirm `.htaccess` hardening already deployed as per previous release.
- __Access window__:
  - Maintenance/low-traffic window agreed.
- __Permissions__:
  - Verify web server user has read access to updated files.

### 3) Backup on PROD (before copy)
Create timestamped backups of each file being replaced (commands illustrative):
```
cp lib/usermanagement/usersEdit.php lib/usermanagement/usersEdit.php.bak_20250828
cp lib/inventory/setInventory.php lib/inventory/setInventory.php.bak_20250828
cp lib/functions/tlInventory.class.php lib/functions/tlInventory.class.php.bak_20250828
cp locale/en_US/custom_strings.txt locale/en_US/custom_strings.txt.bak_20250828
cp locale/en_GB/custom_strings.txt locale/en_GB/custom_strings.txt.bak_20250828
```

### 4) Deployment steps
1. __Copy artifacts__ from UAT build to PROD target paths:
   - `lib/usermanagement/usersEdit.php`
   - `lib/inventory/setInventory.php`
   - `lib/functions/tlInventory.class.php`
   - `locale/en_US/custom_strings.txt`
   - `locale/en_GB/custom_strings.txt`
2. __Permissions/ownership__: ensure same as existing application files (e.g., `www-data:www-data`, `0644`).
3. __Clear opcode cache__:
   - If PHP-FPM: `systemctl restart php-fpm` (version-specific) or `service php7.x-fpm restart`.
   - If Apache mod_php: `apachectl graceful` or `systemctl reload apache2`.
4. __Do not change__ DB or configs; no migrations required.

#### 4b) Per-Item Deployment Matrix (files to deploy + quick checks)
- __Stored XSS (Inventory module)__ — Status: Verified — Risk: High
  - Files: `gui/templates/tl-classic/inventory/inventoryView.tpl`, `lib/inventory/setInventory.php`
  - Verify: Payloads render as text in grid; CRUD still works (see section I verification).

- __Reflected XSS (linkto.php, ltx.php)__ — Status: Verified — Risk: High
  - Files: `linkto.php`, `ltx.php`
  - Verify: Encoded params, sanitized anchors, safe error messages; no script execution (see section J).

- __File download/header hardening (direct_image.php)__ — Status: Verified — Risk: Medium
  - Files: `direct_image.php`
  - Verify: Correct headers, safe filename handling, `nosniff` present (see section K).

- __Reflected XSS (debug_direct.php, redmine_direct.php)__ — Status: Done (awaiting verification) — Risk: High
  - Files: `debug_direct.php`, `redmine_direct.php`
  - Verify: Escaped outputs, `nosniff` header, safe rendering on crafted inputs (see section L).

- __API input/header hardening (redmine_status_api.php)__ — Status: Done (awaiting verification) — Risk: Medium
  - Files: `redmine_status_api.php`
  - Verify: JSON content type, `nosniff`, numeric coercion of `bug_id` (see section M).

- __Time-based SQLi (Search) + ORDER BY hardening__ — Status: Done (awaiting verification) — Risk: High
  - Files: `lib/search/searchCommands.class.php`, `lib/functions/database.class.php`,
           `lib/functions/testplan.class.php`, `lib/functions/tlCodeTracker.class.php`,
           `lib/functions/tlIssueTracker.class.php`, `lib/functions/tlReqMgrSystem.class.php`
  - Verify: Searches function with parameters; no concat SQL; ORDER BY sorts with whitelist; negative tests fallback (see sections 2 and H).

- __Role modification via DevTools__ — Status: Done (awaiting verification) — Risk: High
  - Files: `lib/usermanagement/usersEdit.php`
  - Verify: Non-admin escalation blocked; admin assignment allowed (see section 3).

- __Predictable machineOwner validation__ — Status: Done (awaiting verification) — Risk: High
  - Files: `lib/inventory/setInventory.php`, `lib/functions/tlInventory.class.php`, locales
  - Verify: Invalid/missing/inactive owner rejected; valid active owner accepted (see section 4).

- __Exposed config/dev/info files review__ — Status: In Progress — Risk: Medium
  - Files: `.htaccess`, `diagnostic.php` (incremental updates)
  - Verify: Directory listing disabled; sensitive paths blocked; `diagnostic.php` restricted.

- __Improve error/stack exposure__ — Status: Pending — Risk: Medium
  - Files: `.user.ini` (app), `php.ini` (server), `diagnostic.php`
  - Verify: `display_errors=Off`; user-friendly errors only; no stack traces leaked.

- __Login flow validation (cookies + CSRF)__ — Status: Done — Risk: Medium
  - Files: `lib/functions/common.php`, `login.php`, `lib/functions/csrf.php`
  - Verify: Session cookie `HttpOnly`/`SameSite`/`Secure` (HTTPS); CSRF tokens issued/validated; login redirects stable.

### 5) Post-deployment verification (smoke tests)
- __Role Escalation Prevention__ (`lib/usermanagement/usersEdit.php`):
  - Login as non-admin. Attempt to set a user’s global role to Admin using DevTools/request tampering → update should be blocked; role unchanged; other permitted fields can update.
  - Login as Admin. Assign Admin role to a user → should succeed.
- __Inventory Owner Validation__ (`lib/inventory/setInventory.php`, `tlInventory.class.php`):
  - Try save with missing/zero `machineOwner` → rejected with `inventory_invalid_owner`.
  - Try non-existent owner ID → rejected.
  - Try inactive user ID → rejected.
  - Use a valid active user ID → save succeeds; `ownerId` persisted.
- __Regression checks__:
  - Inventory create/update still works for other fields.
  - Users edit page functions normally (including password reset flow).

### 6) Monitoring and logs
- __Web server logs__: `access.log` and `error.log` during/after deploy window.
- __Application logs__ (`logs/`):
  - `auth_debug.txt`, `auth_trace.log`, and any custom inventory/user logs if enabled.
- __Error visibility__: ensure no PHP warnings/notices leak to users; `.user.ini`/`php.ini` have `display_errors=Off`.

### 7) Rollback plan
- Restore backups made in step 3:
```
mv lib/usermanagement/usersEdit.php.bak_20250828 lib/usermanagement/usersEdit.php
mv lib/inventory/setInventory.php.bak_20250828 lib/inventory/setInventory.php
mv lib/functions/tlInventory.class.php.bak_20250828 lib/functions/tlInventory.class.php
mv locale/en_US/custom_strings.txt.bak_20250828 locale/en_US/custom_strings.txt
mv locale/en_GB/custom_strings.txt.bak_20250828 locale/en_GB/custom_strings.txt
```
- Reload web server/PHP-FPM to clear opcode cache.

### 8) Evidence capture and sign-off
- __Evidence__:
  - Screenshots of blocked escalation attempt and successful admin assignment.
  - Inventory save responses showing `inventory_invalid_owner` and successful save.
  - Timestamps and log extracts for deploy window.
- __Sign-off__:
  - Security/QA review; update Status from "Done (awaiting verification)" to "Verified" in this document.

### K) File Download/Header Hardening (direct_image.php)
- Status: Verified
- Risk: Medium
- Objective: Prevent header injection and reduce attack surface when serving attachments directly.

- Files changed:
  - `direct_image.php`
    - Switched to prepared statement for `attachments` lookup; set connection charset to `utf8mb4`.
    - Sanitized filename for `Content-Disposition` and added RFC 5987 `filename*`.
    - Added `X-Content-Type-Options: nosniff` and conditional `Content-Length`.
    - Kept error responses generic; no reflection of user input.

- Verification steps:
  - Request with valid `id` returns correct content type and inline display; headers include `nosniff` and safe filename.
  - Invalid/unknown `id` returns 404 without echoing input values.
  - Attempted CRLF injection in stored filenames does not break headers due to sanitization.

- Rollback:
  - Revert `direct_image.php`. No schema changes.
