# SKILL: TestLink Custom Integrations — Multi-Integration Bug Creation Picker

## System Context

This skill covers the **next phase** of the TestLink Custom Bug Tracking Integration system.
The infrastructure (database, CRUD UI, backend engine, bug display) is already fully built and working.
This skill focuses exclusively on the gap: **when a project has multiple active integrations, how does the user choose which one to use when creating a bug?**

### What Already Exists (Do Not Rebuild)

| Component | File / Location | Status |
|---|---|---|
| Integration CRUD UI | `lib/execute/custom_bugtrack_integration.html` | ✅ Complete |
| CRUD API endpoints | `custom_bugtrack_integrator.php` | ✅ Complete |
| Integration engine | `lib/execute/custom_issue_integration_safe.php` | ✅ Complete |
| Bug display / fetching | `redmine_status_api.php` | ✅ Complete |
| DB: Integrations table | `custom_bugtrack_integrations` | ✅ Complete |
| DB: Project mapping table | `custom_bugtrack_project_mapping` | ✅ Complete |

### The Current Gap

`getCustomIntegrationForProject()` currently returns the **first active integration found** for a project. This works for single-integration projects but silently ignores all other integrations when multiple exist. The user has no control or awareness of which integration is used.

---

### Real Database Schema

```sql
-- Stores each integration's credentials and config
custom_bugtrack_integrations:
  id, name, type, url, api_key, username, password,
  project_key, default_priority, custom_fields,
  is_active, created_by, created_on, updated_by, updated_on

-- Links TestLink projects to integrations (many-to-many)
custom_bugtrack_project_mapping:
  tproject_id,       -- TestLink project ID
  integration_id,    -- FK to custom_bugtrack_integrations.id
  is_active,         -- Whether this specific project↔integration link is active
  created_on
```

**Query to fetch active integrations for a project:**
```sql
SELECT i.id, i.name, i.type, i.url
FROM custom_bugtrack_integrations i
INNER JOIN custom_bugtrack_project_mapping m ON m.integration_id = i.id
WHERE m.tproject_id = :tproject_id
  AND m.is_active = 1
  AND i.is_active = 1
ORDER BY i.name ASC
```

Both `m.is_active` AND `i.is_active` must be checked — the mapping can be active while the integration itself is globally disabled, and vice versa.

### Real Database Example

**Current Integration Data:**
```sql
-- custom_bugtrack_integrations table
"id","name","type","url","api_key","username","password","project_key","is_active","default_priority","default_tracker_id","default_status_id","custom_fields","created_by","created_on","updated_by","updated_on"
7,Redmine1,REDMINE,https://support.profinch.com,c16548f2503932a9ef6d6d8f9a59393436e67f39,"","",nmb-fcubs-14-7-uat2,1,Normal,,,,8,2026-02-23 11:03:55.000,8,2026-02-27 01:12:06.000

-- custom_bugtrack_project_mapping table  
"id","tproject_id","integration_id","is_active","created_by","created_on","updated_by","updated_on"
4,242099,7,1,8,2026-02-23 15:39:58.000,,
5,448287,7,1,8,2026-02-23 21:12:01.000,,
```

**Expected API Response for Project 242099:**
```json
{
  "status": "ok",
  "tproject_id": 242099,
  "integrations": [
    {
      "id": "7",
      "name": "Redmine1",
      "type": "REDMINE",
      "url": "https://support.profinch.com"
    }
  ]
}
```

**Expected API Response for Project 448287:**
```json
{
  "status": "ok", 
  "tproject_id": 448287,
  "integrations": [
    {
      "id": "7",
      "name": "Redmine1", 
      "type": "REDMINE",
      "url": "https://support.profinch.com"
    }
  ]
}
```

**Expected API Response for Project with No Integrations:**
```json
{
  "status": "ok",
  "tproject_id": 999999,
  "integrations": []
}
```

---

## Key Existing Functions (in `lib/execute/custom_issue_integration_safe.php`)

```php
// Currently returns FIRST active integration only — this is what we are extending
getCustomIntegrationForProject($db, $tproject_id)

// Fetches bug data using a resolved integration
getCustomIssueData($db, $tproject_id, $issue_id)

// Safe Redmine API call with proper error handling
getRedmineIssueDataSafe($url, $api_key, $issue_id)

// Fallback to hardcoded credentials
getRedmineIssueDataFallback($issue_id)
```

---

## Existing API Endpoints (in `custom_bugtrack_integrator.php`)

```
GET  ?action=list_integrations            — List all integrations (admin use)
POST ?action=create_integration           — Create a new integration
POST ?action=edit_integration             — Edit an existing integration
```

**New endpoint to add in this phase:**
```
GET  ?action=list_integrations_for_project&tproject_id=X
```
Returns only the active integrations linked to a specific project. This is what the frontend picker calls.

**Response shape:**
```json
{
  "status": "ok",
  "tproject_id": 42,
  "integrations": [
    {
      "id": 7,
      "name": "Redmine - QA Board",
      "type": "redmine",
      "url": "https://redmine.example.com"
    },
    {
      "id": 12,
      "name": "Redmine - Dev Team",
      "type": "redmine",
      "url": "https://redmine-dev.example.com"
    }
  ]
}
```



## Bug Creation Entry Points

**Primary files to modify:**
- `lib/execute/bugAdd.php` - Standalone bug creation (from test case view)
- `lib/execute/bugAdd.php` - Bug creation during test execution (from execution panel)
- **Bug addition to existing execution** - When adding bugs to an existing test run
- Any other bug creation triggers in execution interface

The current flow silently resolves an integration via `getCustomIntegrationForProject()` and proceeds. This needs to become a two-step flow when multiple integrations exist for a project.

### Bug Addition to Existing Execution

**Entry Point:** Usually in execution view where "Add Bug" button appears next to existing bugs

**Current Flow:** 
```
User clicks "Add Bug to Execution" → 
Silently uses first integration → 
Opens bug form → 
Bug created with first integration
```

**Required Multi-Integration Flow:**
```
User clicks "Add Bug to Execution" →
Check if project has multiple integrations →
    ├─ 1 integration → Skip picker, use first integration
    └─ 2+ integrations → Show integration picker
    User selects integration →
Open bug form with selected integration →
Bug created with selected integration
```

**Implementation Notes:**
- Same AJAX call: `list_integrations_for_project?tproject_id=X`
- Same picker modal logic as bug creation
- Pass `execution_id` parameter to bug form (context: existing execution)
- Backend validates integration belongs to project before use

### Checkbox-Based Bug Creation During Test Execution

**Entry Point:** Existing checkbox in test execution interface (no separate bug form)

**Current Flow:**
```
User is executing test case → 
Checks "Create Issue" checkbox → 
Clicks checkbox → 
Bug creation form appears inline → 
Bug created with first integration
```

**Required Multi-Integration Flow:**
```
User is executing test case → 
Checks "Create Issue" checkbox → 
JavaScript function called → 
Check if project has multiple integrations →
    ├─ 1 integration → Skip picker, use first integration
    └─ 2+ integrations → Show integration picker
    User selects integration → 
Inline bug form uses selected integration →
Bug created with selected integration
```

**Implementation Notes:**
- **No separate bug form** - bug creation happens inline in execution interface
- **JavaScript trigger function** - `toogleShowHide()` called on checkbox click
- **Same AJAX call** to `list_integrations_for_project?tproject_id=X`
- **Same picker modal** logic as regular bug creation
- **Integration selection stored** in JavaScript variable for inline form use
- **Backend receives** `integration_id` along with other bug creation parameters
- **No page reload** - bug creation happens inline, maintaining test execution context

**Note:** The checkbox functionality already exists in the current system. This implementation plan enhances the existing checkbox behavior to support multi-integration selection rather than creating new checkbox functionality from scratch.

---

## Resolution Logic

```
User clicks BugAdd button
         │
         ▼
AJAX → list_integrations_for_project?tproject_id=X
         │
         ├─ 0 active integrations
         │     → Show: "No integrations configured for this project."
         │     → Admin users: show link to integration settings
         │     → Bug form does NOT open
         │
         ├─ 1 active integration
         │     → Skip picker entirely — zero UI change for the user
         │     → Proceed directly to bug creation form
         │     → Pass integration_id to bug form
         │
         └─ 2+ active integrations
               → Show Integration Picker modal
               → User selects one integration
               → Close modal
               → Proceed to bug creation form
               → Pass selected integration_id to bug form
```

**Key principle:** The single-integration path must remain completely transparent. No extra clicks, no visual change for users who only have one integration. Only multi-integration projects see anything new.

---

## Integration Picker Modal — UI Spec

### When to Show
Only after the AJAX call confirms ≥ 2 active integrations exist for the project.

### Modal Requirements

| Property | Spec |
|---|---|
| Type | Modal overlay (blocks background interaction) |
| Dismissible | ESC key, Cancel button, clicking outside modal |
| Size | Compact list — not full-screen |
| Loading state | Spinner while AJAX call is in-flight |
| Error state | Inline error + Retry button if AJAX fails |
| On dismiss | No action taken — bug form does NOT open |

### Each Integration Row Must Show

- **Integration name** — the `name` field (e.g. "Redmine - QA Board")
- **Integration type** — the `type` field as a readable label (e.g. "Redmine")
- **Target URL** — the `url` field so the user can confirm which system they're targeting
- **Select button** — or make the entire row clickable

### UX Rules

- No row is pre-selected — the user must make an explicit, deliberate choice
- Clicking a row immediately closes the modal and opens the bug form for that integration
- Do NOT auto-remember the last-used integration in this phase (deferred to Phase 3)

---

## Bug Form Handoff

After integration resolution (either auto or picker), pass these to the bug form:

| Parameter | Source |
|---|---|
| `integration_id` | Picker selection OR auto-resolved single integration |
| `tproject_id` | Current TestLink project context |
| `tcversion_id` | Test case version triggering the bug add |
| `execution_id` | Current test execution run |

The bug creation backend must then use `integration_id` to fetch the correct credentials from `custom_bugtrack_integrations` directly — it should NOT fall back to `getCustomIntegrationForProject()` (first-found behavior) when an explicit `integration_id` is provided.

---

## Backend Changes Required

### 1. New function: `getIntegrationsForProject()`

Add to `lib/execute/custom_issue_integration_safe.php`:

```php
/**
 * Returns ALL active integrations for a project.
 * Used to determine whether to show the picker or proceed directly.
 * Returns credentials-stripped data only (id, name, type, url).
 *
 * @param object $db          DB connection
 * @param int    $tproject_id TestLink project ID
 * @return array              Array of integrations. Empty array if none found.
 */
function getIntegrationsForProject($db, $tproject_id) {
    // SELECT i.id, i.name, i.type, i.url
    // FROM custom_bugtrack_integrations i
    // INNER JOIN custom_bugtrack_project_mapping m ON m.integration_id = i.id
    // WHERE m.tproject_id = :tproject_id
    //   AND m.is_active = 1
    //   AND i.is_active = 1
    // ORDER BY i.name ASC
}
```

### 2. Extend `getCustomIntegrationForProject()` (backward-compatible)

Add an optional `$integration_id` parameter. When provided, fetch that specific integration and verify it belongs to the given `tproject_id`. When not provided, preserve existing first-found behavior (used by bug display path).

```php
function getCustomIntegrationForProject($db, $tproject_id, $integration_id = null) {
    if ($integration_id !== null) {
        // Fetch this specific integration
        $sql = "SELECT i.* FROM custom_bugtrack_integrations i
                INNER JOIN custom_bugtrack_project_mapping m ON m.integration_id = i.id
                WHERE m.tproject_id = :tproject_id 
                AND m.integration_id = :integration_id
                AND m.is_active = 1
                AND i.is_active = 1";
        
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':tproject_id', $tproject_id);
        $stmt->bindParam(':integration_id', $integration_id);
        $stmt->execute();
        $result = $stmt->fetch();
        
        // Verify integration exists and belongs to project
        if ($result) {
            return $result; // Return full record including credentials for bug creation
        } else {
            return null; // Invalid integration_id or not linked to project
        }
    } else {
        // EXISTING behavior preserved: return first active integration
        // Used by: bug display, redmine_status_api.php, fallback paths
        return getFirstActiveIntegration($db, $tproject_id);
    }
}

// Helper function for existing behavior
function getFirstActiveIntegration($db, $tproject_id) {
    $sql = "SELECT i.* FROM custom_bugtrack_integrations i
            INNER JOIN custom_bugtrack_project_mapping m ON m.integration_id = i.id
            WHERE m.tproject_id = :tproject_id
            AND m.is_active = 1
            AND i.is_active = 1
            ORDER BY i.name ASC
            LIMIT 1";
    
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':tproject_id', $tproject_id);
    $stmt->execute();
    return $stmt->fetch();
}
```

### 3. Modify bug creation handlers to accept integration_id

**In `bugAdd.php` or relevant bug creation files:**
- Accept `integration_id` from GET/POST parameters
- When `integration_id` is provided, use `getCustomIntegrationForProject($db, $tproject_id, $integration_id)`
- When `integration_id` is NOT provided, use existing `getCustomIntegrationForProject($db, $tproject_id)` (backward compatibility)
- Validate that the integration belongs to the project before using credentials

### 3. New action in `custom_bugtrack_integrator.php`

```php
case 'list_integrations_for_project':
    $tproject_id = intval($_GET['tproject_id'] ?? 0);
    if (!$tproject_id) {
        echo json_encode(['status' => 'error', 'message' => 'tproject_id required']);
        exit;
    }
    $integrations = getIntegrationsForProject($db, $tproject_id);
    echo json_encode([
        'status' => 'ok',
        'tproject_id' => $tproject_id,
        'integrations' => $integrations   // credentials already stripped in function
    ]);
    break;
```

---

## Frontend Changes Required

### In `lib/execute/bugAdd.php` (standalone bug creation)
### In execution interface bug creation (during test runs)

**Replace current direct-integration-call pattern with this flow:**

```javascript
// Universal function for both standalone and execution bug creation
function onBugAddClick(tproject_id, execution_id = null, tcversion_id = null) {
    showLoadingIndicator();

    fetch(`custom_bugtrack_integrator.php?action=list_integrations_for_project&tproject_id=${tproject_id}`)
        .then(r => r.json())
        .then(data => {
            hideLoadingIndicator();

            const integrations = data.integrations || [];

            if (integrations.length === 0) {
                showNoIntegrationsMessage(); // "No integrations configured for this project"
                return; // Stop here - no bug form opens
            } else if (integrations.length === 1) {
                // Single integration: skip picker entirely
                openBugForm(integrations[0].id, tproject_id, execution_id, tcversion_id);
            } else {
                // Multiple integrations: show picker
                showIntegrationPicker(integrations, function(selectedIntegrationId) {
                    openBugForm(selectedIntegrationId, tproject_id, execution_id, tcversion_id);
                });
            }
        })
        .catch(err => {
            hideLoadingIndicator();
            showFetchError(); // Show inline error + Retry button
        });
}

// Checkbox-based bug creation during test execution
function onCreateIssueCheckboxClick(tproject_id, execution_id) {
    showLoadingIndicator();

    fetch(`custom_bugtrack_integrator.php?action=list_integrations_for_project&tproject_id=${tproject_id}`)
        .then(r => r.json())
        .then(data => {
            hideLoadingIndicator();

            const integrations = data.integrations || [];

            if (integrations.length === 0) {
                showNoIntegrationsMessage(); // "No integrations configured for this project"
                return; // Stop here - no bug creation
            } else if (integrations.length === 1) {
                // Single integration: skip picker entirely
                showInlineBugForm(integrations[0].id, tproject_id, execution_id);
            } else {
                // Multiple integrations: show picker
                showIntegrationPicker(integrations, function(selectedIntegrationId) {
                    showInlineBugForm(selectedIntegrationId, tproject_id, execution_id);
                });
            }
        })
        .catch(err => {
            hideLoadingIndicator();
            showFetchError(); // Show inline error + Retry button
        });
}

// Bug addition to existing execution
function onAddBugToExecutionClick(execution_id, tproject_id) {
    showLoadingIndicator();

    fetch(`custom_bugtrack_integrator.php?action=list_integrations_for_project&tproject_id=${tproject_id}`)
        .then(r => r.json())
        .then(data => {
            hideLoadingIndicator();

            const integrations = data.integrations || [];

            if (integrations.length === 0) {
                showNoIntegrationsMessage(); // "No integrations configured for this project"
                return; // Stop here - no bug form opens
            } else if (integrations.length === 1) {
                // Single integration: skip picker entirely
                openBugFormForExecution(integrations[0].id, execution_id, tproject_id);
            } else {
                // Multiple integrations: show picker
                showIntegrationPicker(integrations, function(selectedIntegrationId) {
                    openBugFormForExecution(selectedIntegrationId, execution_id, tproject_id);
                });
            }
        })
        .catch(err => {
            hideLoadingIndicator();
            showFetchError(); // Show inline error + Retry button
        });
}

// Show inline bug form (checkbox-based creation)
function showInlineBugForm(integration_id, tproject_id, execution_id) {
    // Store selected integration for inline bug creation
    window.selectedIntegrationId = integration_id;
    
    // Show bug creation form inline in execution interface
    // This would show/hide the bug form fields in the current page
    showBugCreationFields(integration_id);
}

// Show integration picker for inline bug creation
function showInlineIntegrationPicker(integrations, onSelection) {
    // Show picker modal for checkbox-based bug creation
    // Same modal logic as regular bug creation but for inline context
    showIntegrationPicker(integrations, onSelection);
}

function openBugForm(integration_id, tproject_id, execution_id, tcversion_id) {
    // Open existing bug creation form, passing integration_id
    // This works for both standalone and execution bug creation
    const bugFormUrl = `bugAdd.php?integration_id=${integration_id}&tproject_id=${tproject_id}&execution_id=${execution_id}&tcversion_id=${tcversion_id}`;
    window.open(bugFormUrl, '_blank');
}

// Bug form for existing execution (includes execution_id context)
function openBugFormForExecution(integration_id, execution_id, tproject_id) {
    // Open bug creation form for existing execution, passing integration_id and execution_id
    const bugFormUrl = `bugAdd.php?integration_id=${integration_id}&tproject_id=${tproject_id}&execution_id=${execution_id}`;
    window.open(bugFormUrl, '_blank');
}

// Execution-specific bug creation (if different entry point needed)
function onExecutionBugAddClick(execution_id, tproject_id) {
    onBugAddClick(tproject_id, execution_id, null); // tcversion_id not needed in execution context
}

// Standalone bug creation (if different entry point needed)  
function onStandaloneBugAddClick(tproject_id, tcversion_id) {
    onBugAddClick(tproject_id, null, tcversion_id); // execution_id not needed in standalone context
}
```

---

## Edge Cases & Guardrails

| Scenario | Handling |
|---|---|
| 0 integrations | Informational message shown, no bug form opens |
| Only `m.is_active` checked but not `i.is_active` | **Bug** — must check both. A globally disabled integration should never appear |
| AJAX fails or times out | Show inline error + Retry. Do not open an empty picker |
| User dismisses picker (ESC / Cancel / backdrop) | Nothing happens. Bug form does NOT open |
| Client sends arbitrary `integration_id` | Server must verify the ID belongs to the given `tproject_id` before using its credentials |
| Picker opened, then integration gets deactivated | Bug form submission will fail — handle with clear error message at that point |
| Integration name is very long | Truncate with CSS ellipsis, show full name on hover tooltip |
| Single-integration project | Picker never shown — existing user experience is 100% preserved |

---

## What Is Out of Scope for This Phase

- The bug creation form itself per integration type (already exists)
- Changes to `redmine_status_api.php` or bug display path (already working, must not break)
- Integration management CRUD UI (already complete)
- Default/preferred integration per project (`is_default` flag — Phase 3)
- Showing which integration was used to view a bug (Phase 2)
- Any new integration types beyond Redmine (separate concern)

---

## Future Phases (Planned, Not Now)

**Phase 2 — Bug Viewing Integration Context**
Show which integration was used to create/fetch each bug. Requires storing `integration_id` alongside the bug record in TestLink.

**Phase 3 — Default Integration Per Project**
Add `is_default` flag to `custom_bugtrack_project_mapping`. Auto-selects in picker with ability to override. Eliminates picker for projects where a default is set.

---

## Implementation Checklist

### Backend
- [ ] Add `getIntegrationsForProject($db, $tproject_id)` to `custom_issue_integration_safe.php`
- [ ] Confirm query checks BOTH `m.is_active = 1` AND `i.is_active = 1`
- [ ] Confirm function returns ONLY `id, name, type, url` — no credentials
- [ ] Add `list_integrations_for_project` case to `custom_bugtrack_integrator.php`
- [ ] Extend `getCustomIntegrationForProject()` with optional `$integration_id` param
- [ ] Server-side: verify provided `integration_id` belongs to `tproject_id` before use
- [ ] Confirm all existing callers of `getCustomIntegrationForProject()` still work (no `$integration_id` = old behavior)

### Frontend (`lib/execute/bugAdd.php` / bug trigger)
- [ ] Add AJAX call to `list_integrations_for_project` on BugAdd click
- [ ] Implement 0 / 1 / 2+ branching logic
- [ ] Build Integration Picker modal (HTML + CSS + JS)
- [ ] Loading state while AJAX is in-flight
- [ ] Error state with Retry button
- [ ] Empty state (0 integrations) message
- [ ] Pass `integration_id` through to bug creation form

### QA Test Scenarios

#### Bug Creation Scenarios
- [ ] Project with 0 integrations → message shown, no bug form opens (both standalone and execution)
- [ ] Project with 1 integration → picker skipped, form opens directly, correct integration used (both scenarios)
- [ ] Project with 2 integrations → picker shows both, each selection opens correct form (both scenarios)
- [ ] Project with 3+ integrations → all appear in picker (both scenarios)

#### Bug Addition to Existing Execution Scenarios
- [ ] Project with 0 integrations → message shown, no bug form opens
- [ ] Project with 1 integration → picker skipped, form opens directly, correct integration used
- [ ] Project with 2 integrations → picker shows both, each selection opens correct form
- [ ] Project with 3+ integrations → all appear in picker

#### Checkbox-Based Bug Creation During Test Execution
- [ ] Project with 0 integrations → message shown, no inline bug form
- [ ] Project with 1 integration → checkbox click shows inline bug form, correct integration used
- [ ] Project with 2 integrations → checkbox click shows picker, selection opens inline bug form
- [ ] Project with 3+ integrations → checkbox click shows picker, selection opens inline bug form

#### Integration Selection Validation
- [ ] Picker dismissed via ESC → nothing happens, no bug form opens (both scenarios)
- [ ] Picker dismissed via Cancel button → nothing happens, no bug form opens (both scenarios)
- [ ] Picker dismissed via backdrop click → nothing happens, no bug form opens (both scenarios)
- [ ] AJAX fetch fails → error + retry shown, no picker opens (both scenarios)
- [ ] Existing single-integration project → zero behavior change confirmed (both scenarios)
- [ ] Valid integration_id passed → correct integration used (both scenarios)
- [ ] Invalid integration_id passed → error message, fallback to first integration (both scenarios)
- [ ] Integration globally disabled (`i.is_active = 0`) → does not appear in picker even if mapping is active (both scenarios)
- [ ] Mapping disabled (`m.is_active = 0`) → does not appear in picker even if integration is globally active (both scenarios)
- [ ] Integration belongs to different project → error message, no bug creation (both scenarios)
- [ ] Integration name is very long → Truncate with CSS ellipsis, show full name on hover tooltip (both scenarios)
- [ ] Single-integration project → Picker never shown — existing user experience is 100% preserved (both scenarios)
- [ ] Bug display path (`redmine_status_api.php`) → unaffected, still works (both scenarios)
- [ ] AJAX fetch fails → error + retry shown, no picker opens (both scenarios)
- [ ] Bug creation with explicit integration_id → uses specified integration (both scenarios)
- [ ] Bug creation without integration_id → uses first active integration (backward compatibility) (both scenarios)
- [ ] Parameters passed correctly → tproject_id, execution_id, tcversion_id all preserved (both scenarios)
- [ ] Window.open() behavior → bug form opens in new tab with correct parameters (both scenarios)
- [ ] Checkbox-based bug creation → `toogleShowHide()` function called, inline form appears (execution context)
- [ ] Checkbox with multiple integrations → integration picker appears, selection stored for inline form
- [ ] Checkbox with single integration → inline bug form appears, correct integration used automatically
- [ ] Checkbox with no integrations → message shown, no inline bug form appears

#### Cross-Project Integration Security
- [ ] User passes integration_id from Project A but tproject_id from Project B → server rejects mismatch
- [ ] User attempts to access integration they don't have permission for → server rejects
- [ ] SQL injection attempts via integration_id parameter → properly sanitized and rejected common case |
| 2026-02-27 | No pre-selection in picker | User must be deliberate — wrong integration = bug goes to wrong system |
| 2026-02-27 | Extend `getCustomIntegrationForProject()` rather than replace | Full backward compatibility for bug display path and `redmine_status_api.php` |
| 2026-02-27 | `is_default` flag deferred to Phase 3 | Not needed for initial picker — can be added as polish without blocking launch |
| 2026-02-27 | Never expose credentials in `list_integrations_for_project` | `id, name, type, url` is all the UI needs — API keys stay server-side only |
| 2026-02-27 | Check both `m.is_active` and `i.is_active` | Either side can be disabled independently — both gates must be respected |
