# TestLink UAT — Debug Handoff Log

This file is updated every debug session to preserve context for future work.

---

## 2026-03-04 — Session 1: Login DB Error

**Error:** `DB Access Error` on login — TestLink trying to write to a non-existent `transactions` table.  
**Fix:** Disabled transaction logging mechanism so users can log in.  
**Files touched:** (refer to conversation `da9bae5d-f4e8-4246-9830-ad3a1216e7df`)

---

## 2026-03-04 — Session 2: execSetResults.php 500 on testcase click

### Symptom

```
GET /lib/execute/execSetResults.php?version_id=431185&level=testcase&id=431184&... 500
```

Clicking a test case in the execution page returned 500. Test case details never populated in the right pane.

### Root Cause Confirmed (from `logs/php_errors.log`)

```
PHP Fatal error: Uncaught --> Smarty Compiler: Syntax error in template
  "inc_exec_show_tc_exec.tpl" on line 539/540
  unclosed '{if}' tag
```

The Smarty compiler choked on `is_array()` (a native PHP function NOT valid in Smarty 2/3), which caused the compiler to report an "unclosed `{if}`" error — because the `{if}` containing `is_array()` was never successfully opened from the compiler's perspective.

### Issues Fixed in `inc_exec_show_tc_exec.tpl`

| #   | Issue                                                                  | Severity     | Fix Applied                                             |
| --- | ---------------------------------------------------------------------- | ------------ | ------------------------------------------------------- |
| 1   | `is_array($gui->bugs)` used directly in Smarty template                | **CRITICAL** | Removed `is_array()` check — kept only `isset()` checks |
| 2   | Orphaned `{/if}` with no matching `{if}` (line ~651)                   | **CRITICAL** | Removed the extra `{/if}`                               |
| 3   | Stray `</a>` after disabled bug icon `<img>` tags                      | High         | Removed the orphaned `</a>` tags                        |
| 4   | Debug `<div>` + `<script>` block at top of file                        | Low          | Removed entire 20-line debug block                      |
| 5   | DEBUG `<!-- comment -->` and `console.log()` inside inner foreach loop | Low          | Removed                                                 |
| 6   | DEBUG HTML comment at bottom of inner foreach                          | Low          | Removed                                                 |

### Smarty Cache Cleared

All compiled template `.php` files in `templates_c/` were deleted so the fixed template is recompiled on next request.

### Files Modified

- `gui/templates/tl-classic/execute/inc_exec_show_tc_exec.tpl`

### Files NOT Modified (still custom-patched from Session 1 work)

- `lib/execute/execSetResults.php` — using PROD baseline + minimal custom integration hooks
- `lib/execute/custom_issue_integration.php`
- `lib/execute/custom_issue_handler.php`
- `gui/javascript/execSetResults.js`
- `gui/templates/tl-classic/execute/execSetResults.tpl`
- `gui/templates/tl-classic/execute/inc_exec_controls.tpl`

### Remaining Known Issues (not fixed yet)

- `{literal}` / `{/literal}` scoping around `renderTo:'exec_notes_container_{$tc_old_exec.execution_id}'` (Issues #4 from tpl_syntax_findings.md) — technically risky but Smarty may tolerate it at runtime; will surface only if exec notes panels break.
- `$execID` reassigned mid-loop (Issue #5 from findings) — functionally OK since each outer iteration reuses the name, but worth cleaning up long-term.

### Next Debug Steps (if 500 persists)

1. Reproduce once in browser.
2. Check `logs/php_errors.log` immediately after — look for new errors.
3. Patch based on the new stack trace line number.

---

## Reference Files

- `execSetResults_500_progress_2026-03-04.md` — detailed timeline of previous investigation
- `gui/templates/tl-classic/execute/tpl_syntax_findings.md` — full Smarty syntax audit results
- `lib/execute/execSetResults_PROD.php` — known-good production baseline backup
- `lib/execute/execSetResults.pre_prod_restore.bak` — pre-restore backup

---

## 2026-03-05 — Session 3: Custom Bug Tracker Integration Fixes

After restoring `inc_exec_show_tc_exec.tpl` to production baseline (which fixed the 500 error), three custom integration issues were identified and expanded to 6 after log analysis.

### Issues Fixed

| #   | Issue                                                                                                                                             | File                                                         | Fix                                                                                        |
| --- | ------------------------------------------------------------------------------------------------------------------------------------------------- | ------------------------------------------------------------ | ------------------------------------------------------------------------------------------ |
| 1   | `logCustomIntegration()` called but never defined — fatal error crashes after Redmine issue is created, before it's linked to TestLink            | `lib/execute/custom_issue_integration.php` lines 173,181,183 | Replaced with `error_log("[CUSTOM_INTEGRATION] ...")`                                      |
| 2   | JS prepending `--- Integration Selection ---\nSelected Integration ID: X` into `#bug_summary` before form submit — pollutes Redmine issue subject | `gui/javascript/execSetResults.js` ~line 1149                | Removed the injection block; `selected_integration_id` hidden field handles this correctly |
| 3   | Redmine2 (ID:9) and Redmine4 (ID:11) API keys truncated by 2 chars (`...67f` instead of `...67f39`) causing HTTP 401                              | Database `custom_bugtrack_integrations`                      | SQL UPDATE set correct 40-char key for ids 9,11                                            |
| 4   | Debug artifacts left in `inc_exec_controls.tpl`: lime-green floating div, red-border test dropdown                                                | `gui/templates/tl-classic/execute/inc_exec_controls.tpl`     | Removed debug elements                                                                     |
| 5   | Bug details never fetched: type compared as `'redmine'` but stored as `'REDMINE'`; URL field named `api_endpoint` but column is `url`             | `lib/execute/custom_issue_integration.php` lines 305,319     | Fixed case + field name                                                                    |
| 6   | Debug console.log script blocks around template include in execSetResults.tpl                                                                     | `gui/templates/tl-classic/execute/execSetResults.tpl`        | Removed debug scripts                                                                      |

### Key Architecture Note

The bug creation flow is:

1. `execSetResults.php` → detects `use_custom_integration=true`
2. → calls `custom_write_execution()` in `custom_issue_handler.php`
3. → calls `write_execution()` first (saves execution status, gets execution_id)
4. → calls `createCustomIssue()` in `custom_issue_integration.php`
5. → POSTs to `custom_bugtrack_integrator_simple.php?action=create_issue` via cURL (localhost)
6. → On success, calls `write_execution_bug($db, $execution_id, $bug_id, 0)` to link in TestLink

The `selected_integration_id` is passed from the hidden form field → `$_REQUEST` → `custom_issue_handler.php` → `createCustomIssue()` → POSTed to `custom_bugtrack_integrator_simple.php`.

### Integration Modal Flow (JS)

When "Create Issue" is checked and Save is clicked:

- `saveExecutionStatus()` checks if `window.selectedIntegrationId` is set
- If not → shows `#integrationModal` (via `showIntegrationModal()`)
- User selects integration → `confirmIntegrationSelection()` → `syncSelectedIntegrationField()` updates `#selected_integration_id` hidden field
- Pending action (`resumePendingExecutionAction()`) then submits the form

### Smarty Cache Cleared

27 compiled template files deleted from `gui/templates_c/`.

### Files Modified This Session

- `lib/execute/custom_issue_integration.php`
- `gui/javascript/execSetResults.js`
- `gui/templates/tl-classic/execute/inc_exec_controls.tpl`
- `gui/templates/tl-classic/execute/execSetResults.tpl`
- DB: `custom_bugtrack_integrations` (ids 9, 11 api_key corrected)

### Remaining Items to Watch

- The `inc_exec_controls.tpl` still has a legacy `integration_dropdown_container` div (with `display:none !important`) — this is the old non-modal approach, kept harmlessly hidden for now.
- `execSetResults.js` has heavy console.log debug output — can be cleaned up in a future session if needed.
- The `toogleShowHide` / `syncIssueSummaryVisibility` are now both subscribed to `#createIssue` change events. They do the same thing (show `#issue_summary`) from two different places — functionally fine, just redundant.

---

## 2026-03-05 — Session 4: execSetResults.tpl Smarty Syntax Error (Recurring)

### Symptom

```
PHP Fatal error: Uncaught --> Smarty Compiler: Syntax error in template "execSetResults.tpl"
  on line 373 "$gui - > uploadOp - > tcLevel - > statusOK == false" - Unexpected "> "
```

500 error returned after production file restore overwrote the previously-fixed template.

### Root Cause

`execSetResults.tpl` had spaces around `->` arrow operators inside two Smarty `{if}` blocks (lines 373 and 381). Smarty interprets `>` as a closing angle bracket, breaking the condition syntax.

### Fix Applied

Replaced spaced arrows `- >` with proper `->` in two conditions:

| Line | Before                                                  | After                                          |
| ---- | ------------------------------------------------------- | ---------------------------------------------- |
| 373  | `$gui - > uploadOp - > tcLevel - > statusOK == false`   | `$gui->uploadOp->tcLevel->statusOK == false`   |
| 381  | `$gui - > uploadOp - > stepLevel - > statusOK == false` | `$gui->uploadOp->stepLevel->statusOK == false` |

**Fix was applied via PowerShell** (not IDE edit tool) due to a tool caching issue that caused the in-editor edit to silently not persist.

### Smarty Cache Cleared

All compiled `.php` files in `gui/templates_c/` were deleted.

### Files Modified

- `gui/templates/tl-classic/execute/execSetResults.tpl`

### ⚠️ Risk Note

This file gets overwritten if a production restore is done. Before any future restore of `execSetResults.tpl`, verify these two lines are correct. The pattern to watch for is any `- >` (with spaces) inside a Smarty `{if}` block.

---

## 2026-03-05 — Session 5: Linked Bug Details Not Displaying

### Symptom

After executing a test case that has a linked Redmine bug, the execution history section shows `"Loading..."` for Status / Priority / Assignee / Updated — and never resolves.  
The `🐛 Bugs` row in the execution history never appears at all when custom integration is active.

### Root Cause Analysis — Full Display Pipeline

```
execSetResults.php:exec_additional_info()
  └─► $gui->bugs[$execID] = get_bugs_for_exec($db, $bugInterface, $exec_id)
         │
         └─► Only called when $bugInterfaceOn is true AND $bugInterface != null
             (with custom integration, $bugInterface ($its) = null → $gui->bugs never set)

inc_show_bug_table.tpl:
  └─► {if isset($gui->bugs[$execID])} → never fires → no bug row rendered

Each rendered bug row calls:
  fetch('redmine_status_api.php?bug_id=X') → JS populates Status/Priority/Assignee/Updated
  └─► Uses hardcoded API key, works fine independently
```

### Bugs Fixed

#### 1. `custom_issue_integration_safe.php` — Field name and case mismatch

| Line | Bug                                  | Fix                                           |
| ---- | ------------------------------------ | --------------------------------------------- |
| 73   | `$integration['type'] === 'redmine'` | `=== 'REDMINE'` (DB stores uppercase)         |
| 95   | `$integration['api_endpoint']`       | `$integration['url']` (actual DB column name) |

Both bugs caused every call to silently fall through to the hardcoded fallback Redmine credentials.

#### 2. `lib/execute/execSetResults.php:exec_additional_info()` — $gui->bugs never populated for custom integration

When custom integration is active, `$its` (the standard TestLink ITS object) is `null`.  
`get_bugs_for_exec($db, null, $exec_id)` was being called but returned nothing usable, so `$gui->bugs[$execID]` was never set → the template bug row was never rendered.

**Fix:** Added an `else` branch that, when `$bugInterface` is null, queries `execution_bugs` directly and builds a minimal bugs map with the `bug_id` and `tcstep_id` fields the template needs. The JS in the template then fetches the Redmine details via `redmine_status_api.php`.

### Files Modified

- `custom_issue_integration_safe.php` (lines 73, 95)
- `lib/execute/execSetResults.php` (function `exec_additional_info`, lines ~1024–1053)
- Smarty cache cleared (`gui/templates_c/*.php` deleted)

### Status

Fixes applied. Requires browser testing to verify the bug row renders and Redmine details populate correctly.

---

## 2026-03-05 — Session 6: Unclosed If / Broken Smarty Comments in inc_exec_show_tc_exec.tpl

### Symptom

After applying both the `execSetResults.tpl` arrow fix and fixing the HTML in `inc_show_bug_table.tpl`, visiting the execution page threw a NEW 500 error:  
`PHP Fatal error: Uncaught --> Smarty Compiler: Syntax error in template "file:C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\execute\inc_exec_show_tc_exec.tpl" on line 452 "{* ----------------------------------------------------------------------------------- *}" unclosed '{if}' tag`

### Root Cause Analysis

A regex scan of `inc_exec_show_tc_exec.tpl` revealed that the `{if}` and `{/if}` tags were perfectly balanced (52 of each).

The true cause was **two broken multi-line Smarty comments** on lines 150 and 382.

```smarty
            {* Initialize panel if notes exists. There might be multiple note panels
              visible at the same time, so we need to collect those init functions in
                an array and execute them from Ext.onReady().See execSetResults.tpl *
              }
```

In Smarty, comments are enclosed by `{*` and `*}`. If the closing asterisk and brace are separated by a newline (`* \n }`), the parser fails to recognize the closing tag. Subsquently, Smarty swallows large chunks of the actual template code evaluating until the _next_ valid `*}`, which included swallowing valid `{if}` tags, causing the compiler to report an "unclosed if".

### Fix Applied

Using PowerShell, the broken `* \n }` pattern was explicitly replaced with a correct inline `*}`.

- `gui/templates/tl-classic/execute/inc_exec_show_tc_exec.tpl` (Lines 147-151, 379-383)
- Smarty cache cleared (`gui/templates_c/*.php` deleted)

---

## 2026-03-05 — Session 7: Removing the Bulky Legacy Bug Table

### Symptom

The execution history properly displayed the custom Redmine bug information in a minimal "pill" UI row ("Single Row Bug Display") as designed, but it **also** rendered the original, bulky, multi-column legacy TestLink bug table structure underneath it.

### Root Cause Analysis

`inc_show_bug_table.tpl` contained two distinct templating sections:

1. Lines 8–97: The newly written "Single Row Bug Display" (with AJAX `fetch` for pills)
2. Lines 100–276: The legacy TestLink `<table>` loop that printed each bug into a full table grid.

### Fix Applied

- **Removed lines 100–276** entirely from `inc_show_bug_table.tpl`.
- The template now exclusively contains the clean "Single Row Bug Display" UI.
- Cleared the Smarty cache.
