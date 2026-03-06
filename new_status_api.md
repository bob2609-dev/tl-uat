# Plan: Re-implement Bug Status Display with `redmine_status_api.php`

## 1. Objective
The previous attempt to implement a secure, bulk AJAX call using `ajax_redmine_status.php` was not successful. This plan outlines the steps to revert to the simpler, per-bug fetching mechanism using the `redmine_status_api.php` endpoint, as originally found in the templates.

## 2. Revert Recent Changes

### File: `gui/templates/tl-classic/execute/inc_exec_show_tc_exec.tpl`
-   **Action**: Remove the `<script>` block that was added at the end of the file. This script handled the `DOMContentLoaded` event to make a bulk AJAX call.

### File: `lib/execute/execSetResults.php`
-   **Action**: Remove the custom code block that was added to generate and assign the `ajax_csrf_name` and `ajax_csrf_token` Smarty variables. The logic to fetch bug IDs for the latest execution will be preserved as it is necessary.

## 3. Implement Simple Fetch Logic

### File: `gui/templates/tl-classic/inc_show_bug_table.tpl`
-   **Action**: Restore the original inline `<script>` block inside the `foreach` loop. This script will execute for each bug ID and call the `redmine_status_api.php` endpoint.

#### Code to be re-inserted:
```html
<script>
    // Fetch the real status from Redmine for each bug
    fetch('redmine_status_api.php?bug_id={$bug_id}')
        .then(response => response.json())
        .then(data => {
            const statusElement = document.getElementById('bug-status-{$bug_id}');
            if (data.status) {
                statusElement.innerText = data.status.toUpperCase();
                // Change color based on status
                const statusLower = data.status.toLowerCase();
                if (statusLower.includes('closed') || statusLower.includes('resolved')) {
                    statusElement.style.color = '#009900'; // Green
                } else if (statusLower.includes('progress')) {
                    statusElement.style.color = '#ff9900'; // Orange
                } else if (statusLower.includes('reject')) {
                    statusElement.style.color = '#cc0000'; // Red
                } else {
                    statusElement.style.color = '#0066cc'; // Blue for Open/New
                }
            } else {
                statusElement.innerText = 'UNKNOWN';
            }
        })
        .catch(error => {
            console.error('Error fetching status for bug {$bug_id}:', error);
            document.getElementById('bug-status-{$bug_id}').innerText = 'ERROR';
        });
</script>
```

## 4. Execution Flow
1.  Remove the centralized AJAX script from `inc_exec_show_tc_exec.tpl`.
2.  Remove the CSRF token generation from `execSetResults.php`.
3.  Re-insert the individual fetch script into the loop in `inc_show_bug_table.tpl`.
4.  This will result in a separate API call for each bug displayed on the page, which, while less efficient, is simpler and avoids the CSRF issues encountered previously.
5. Make sure API calls are made to Redmine and returns the statuses for each bug in that testcase and displays details in a table.


### File: `gui/templates/tl-classic/inc_show_bug_table.tpl`
-   **Action**: Restore the original inline `<script>` block inside the `foreach` loop. This script will execute for each bug ID and call the `redmine_status_api.php` endpoint.

## 5. DEBUG if any Errors 
