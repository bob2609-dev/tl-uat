# execSetResults.php 500 Error - Progress and Analysis

## Context
- Environment: `tl-uat`
- Date of this report: March 4, 2026
- Main error:
  - `GET /lib/execute/execSetResults.php?...` returns HTTP 500 when selecting a testcase.

## Reported Symptom
- Browser console repeatedly showed:
  - `GET https://test-management.nmbtz.com:9443/lib/execute/execSetResults.php?... 500 (Internal Server Error)`
- This happened during testcase selection, before execution action.

## Work Done So Far (Chronological Summary)

### 1) Early custom integration fixes
- Removed hardcoded integration project fallback in frontend logic.
- Added dynamic project resolution and selected integration propagation.
- Added hidden field support for selected integration ID.
- Added backend parsing of selected integration ID and routing into custom issue creation flow.
- Fixed submission flow locking behavior so execution could continue after modal selection.

### 2) 500 debugging phase
- Investigated `execSetResults.php`, `custom_issue_handler.php`, `custom_issue_integration.php`.
- Removed/adjusted risky pieces introduced during debugging:
  - reflection-based fallback code
  - extra runtime guards that changed flow unpredictably
  - logger function calls in contexts where function scope could fail
- Reverted several experiments when they did not resolve the 500.

### 3) Root cause discovery from server logs
- Checked `logs/php_errors.log` and found concrete fatal error:
  - `PHP Fatal error: Uncaught Error: Cannot use object of type stdClass as array`
  - File in stack trace: compiled Smarty template for `inc_exec_show_tc_exec.tpl`
  - This confirms:
    - request enters `execSetResults.php`
    - processing reaches template rendering
    - crash occurs while rendering template data shape, not at initial request routing.

### 4) Production baseline strategy
- You requested comparison against `execSetResults_PROD.php` (known working production baseline).
- `execSetResults.php` was restored from `execSetResults_PROD.php`.
- Backup saved:
  - `lib/execute/execSetResults.pre_prod_restore.bak`
- Reapplied only minimal custom integration hooks (to keep custom bug tracker behavior without broad divergence from PROD).

## Current `execSetResults.php` Strategy
- Base file: `execSetResults_PROD.php` content.
- Minimal custom additions only:
  1. `require_once('custom_issue_integration.php')`
  2. In save flow:
     - if `use_custom_integration` -> `custom_write_execution(...)`
     - else -> default `write_execution(...)`
  3. Parse `selected_integration_id` from request.
  4. In `init_args()`:
     - custom integration lookup first
     - fallback to default TestLink issue tracker if none found.
  5. In `initializeGui()`:
     - minimal custom integration UI assignment (`issueTrackerIntegrationOn`, `tlCanCreateIssue`, `createIssueURL`, etc).

## Additional Files Touched During This Investigation
- `lib/execute/execSetResults.php`
- `lib/execute/custom_issue_handler.php`
- `lib/execute/custom_issue_integration.php`
- Frontend/template files were also touched earlier in the broader integration work, including:
  - `gui/javascript/execSetResults.js`
  - `gui/templates/tl-classic/execute/execSetResults.tpl`
  - `gui/templates/tl-classic/execute/inc_exec_controls.tpl`
  - `gui/templates/tl-classic/execute/inc_exec_img_controls.tpl`

## Key Analysis
- The 500 is not a generic network issue; it is a PHP fatal.
- Logs prove this specific fatal is template-render-time data-shape mismatch (`stdClass` used where array indexing is expected).
- Because of that, returning to PROD baseline for `execSetResults.php` is the safest stabilization path.
- Any further fix should now focus narrowly on:
  - template variable shape compatibility for `inc_exec_show_tc_exec.tpl`
  - ensuring custom integration hooks do not alter GUI structures expected by Smarty templates.

## Current Status
- `execSetResults.php` has been aligned to PROD baseline plus minimal custom integration hooks.
- This is the cleanest known state so far for isolating remaining 500 causes.
- Next validation should be done against fresh request + immediate check in `logs/php_errors.log` to confirm whether the same fatal persists or a new one appears.

## Suggested Next Debug Step
- If 500 persists after current baseline:
  1. Reproduce once.
  2. Capture newest lines from `logs/php_errors.log`.
  3. Patch only the exact variable/line from the new fatal stack trace.

