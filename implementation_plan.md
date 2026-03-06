# Fix Custom Bug Tracker Integration — 3 Issues

After restoring the production template ([inc_exec_show_tc_exec.tpl](file:///c:/xampp/htdocs/tl-uat/gui/templates/tl-classic/execute/inc_exec_show_tc_exec.tpl)), the 500 error is gone but 3 integration bugs remain.

---

## Issue 1 — Fatal error: `logCustomIntegration()` undefined

**File:** [lib/execute/custom_issue_integration.php](file:///c:/xampp/htdocs/tl-uat/lib/execute/custom_issue_integration.php)

[createCustomIssue()](file:///c:/xampp/htdocs/tl-uat/lib/execute/custom_issue_integration.php#61-191) calls `logCustomIntegration()` on lines 173, 181, 183 — but this function is **never defined anywhere**. It also calls `error_log()` for logging elsewhere in the same file, so the fix is simply to **replace the 3 calls to `logCustomIntegration()`** with `error_log()` calls (same pattern used elsewhere in the file).

> [!IMPORTANT]
> This is the **root cause of issue submission failure**. The fatal error fires after the Redmine issue is successfully created but before the result is returned — so the bug is created in Redmine but not linked in TestLink.

---

## Issue 2 — Issue Summary field hidden, never shown

**File:** [gui/templates/tl-classic/execute/inc_exec_controls.tpl](file:///c:/xampp/htdocs/tl-uat/gui/templates/tl-classic/execute/inc_exec_controls.tpl)

The `#createIssue` checkbox `onclick` calls `toogleShowHide('issue_summary')` but the function `toogleShowHide` is defined in a global JS file and works correctly. The `issue_summary` div (line 180) is present with `display:none`.

However, two **debug artifacts** were left in the template:
1. **A debug "TEMPLATE TEST ELEMENT" floating div** with green `background: lime` (lines 63–68) — leftover from debugging, visually obtrusive.
2. **A debug `integration_dropdown_container_test` div** (lines 99–104) with `display:block !important` red border — a test element that should have been removed.

These debug elements should be **cleaned up**. The actual `issue_summary` toggle mechanism is already correct — once the template is clean it should work properly.

> [!NOTE]
> The `issue_summary` `<table>` at line 180 with `display:none` is the correct element that gets shown by `toogleShowHide()`. The checkbox handler is intact.

---

## Issue 3 — Bug details from Redmine not displayed

**File:** [lib/execute/custom_issue_integration.php](file:///c:/xampp/htdocs/tl-uat/lib/execute/custom_issue_integration.php), function [getCustomIssueData()](file:///c:/xampp/htdocs/tl-uat/lib/execute/custom_issue_integration.php#292-312) (line 305)

The code checks:
```php
if ($integration['type'] === 'redmine') {
```
But the database stores the type as **uppercase `'REDMINE'`** (as seen in [custom_bugtrack_integrator.php](file:///c:/xampp/htdocs/tl-uat/lib/execute/custom_bugtrack_integrator.php) everywhere). This case-sensitive comparison always fails, so [getCustomIssueData()](file:///c:/xampp/htdocs/tl-uat/lib/execute/custom_issue_integration.php#292-312) always returns `null` and bug details are never fetched.

**Fix:** Change `'redmine'` → `'REDMINE'` (or use `strcasecmp` for robustness).

Also, in [getRedmineIssueData()](file:///c:/xampp/htdocs/tl-uat/lib/execute/custom_issue_integration_safe.php#249-252) (line 319), the URL is built with `$integration['api_endpoint']` — but the database column is named **`url`** (not `api_endpoint`), as confirmed in all SQL queries in [custom_bugtrack_integrator.php](file:///c:/xampp/htdocs/tl-uat/lib/execute/custom_bugtrack_integrator.php). This needs to be changed to `$integration['url']`.

---

## Proposed Changes

### PHP Backend

#### [MODIFY] [custom_issue_integration.php](file:///c:/xampp/htdocs/tl-uat/lib/execute/custom_issue_integration.php)

1. **Lines 173, 181, 183** — Replace `logCustomIntegration(...)` with `error_log("[CUSTOM_INTEGRATION] " . ...)` 
2. **Line 305** — Change `'redmine'` → `'REDMINE'`
3. **Line 319** — Change `$integration['api_endpoint']` → `$integration['url']`

---

### Template Cleanup

#### [MODIFY] [inc_exec_controls.tpl](file:///c:/xampp/htdocs/tl-uat/gui/templates/tl-classic/execute/inc_exec_controls.tpl)

1. **Lines 63–68** — Remove the debug floating "TEMPLATE TEST ELEMENT" div + script block
2. **Lines 99–104** — Remove the debug `integration_dropdown_container_test` div

---

## Verification Plan

### Automated: PHP Log Check
After applying the fix, trigger a test execution with "Create Issue" checked, then check:
- [c:\xampp\htdocs\tl-uat\logs\php_errors.log](file:///c:/xampp/htdocs/tl-uat/logs/php_errors.log) — should have **no** `Call to undefined function logCustomIntegration()` error
- [c:\xampp\htdocs\tl-uat\lib\execute\custom_integration_execution.log](file:///c:/xampp/htdocs/tl-uat/lib/execute/custom_integration_execution.log) — should show `SUCCESS` linking bug to execution

### Manual Verification

**Issue 1 (Bug Submission):**
1. Open TestLink execution page, select a test case
2. Check the `#createIssue` checkbox
3. Fill in Issue Summary and click Save
4. Verify → no 500 error occurs; a Redmine issue is created and linked to the execution

**Issue 2 (Issue Summary field):**
1. Open TestLink execution page
2. Check the `#createIssue` checkbox
3. Verify → the "Issue Summary" input field appears below the checkbox

**Issue 3 (Bug Details):**
1. On the execution page for a test case that already has linked bugs
2. Verify → the bug details (status, assignee, priority, updated date) are displayed in the bug table
