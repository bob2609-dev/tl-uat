{* 
Testlink Open Source Project - http://testlink.sourceforge.net/ 
@filesource inc_show_bug_table_direct.tpl

@internal revisions
*}

{lang_get var="l10nb"
          s="build,caption_bugtable,bug_id,delete_bug,del_bug_warning_msg,
             add_issue_note,step"}

<table class="simple" style="width:100%">
  <tr>
	  <th style="text-align:left">{$l10nb.build}</th>
	  <th style="text-align:left;width:35px">{$l10nb.step}</th>
	  <th style="text-align:left">Bug ID</th>
	  <th style="text-align:left">Status</th>
	  <th style="text-align:left">Priority</th>
	  <th style="text-align:left">Assignee</th>
	  <th style="text-align:left">Updated</th>
	  <th style="text-align:left">Actions</th>
  </tr>
  
  {foreach from=$bugs_map key=bug_id item=bug_elem}
  <tr>
    <td>{$bug_elem.build_name|escape}</td>
    <td>{if $bug_elem.tcstep_id >0} {$bug_elem.step_number} {/if}</td>
    <td><b>#{$bug_id}</b></td>
    <td id="bug-status-{$bug_id}">{$bug_elem.status|escape}</td>
    <td id="bug-priority-{$bug_id}">{$bug_elem.priority|escape}</td>
    <td id="bug-assignee-{$bug_id}">{$bug_elem.assignee|escape}</td>
    <td id="bug-updated-{$bug_id}">{$bug_elem.updated_on|escape}</td>
    <td><a href="https://support.profinch.com/issues/{$bug_id}" target="_blank">View in Redmine</a></td>
    <td>
      {if $can_delete}
        <img src="{$tlImages.delete}" style="cursor:pointer;" 
             title="{$l10nb.del_bug_warning_msg}" 
             onclick="delete_bug({$bug_id},{$exec_id});" />
      {/if}
    </td>
  </tr>
  
  {foreachelse}
  <tr>
    <td colspan="8" style="text-align:center; background-color:#f8f8f8; padding:20px;">
      <strong>No bugs found for this execution</strong>
    </td>
  </tr>
  {/foreach}
</table>

<script>
// Fetch Redmine status for all bugs on page load
{foreach from=$bugs_map key=bug_id item=bug_elem}
fetchBugStatus('{$bug_id}');
{/foreach}

function fetchBugStatus(bugId) {
    console.log('Fetching Redmine status for bug ID:', bugId);
    
    fetch('redmine_status_api.php?bug_id=' + bugId)
        .then(response => response.json())
        .then(data => {
            if (data && data.status) {
                document.getElementById('bug-status-' + bugId).innerHTML = data.status;
                document.getElementById('bug-priority-' + bugId).innerHTML = data.priority;
                document.getElementById('bug-assignee-' + bugId).innerHTML = data.assignee;
                document.getElementById('bug-updated-' + bugId).innerHTML = data.updated_on;
                
                // Color code the status
                var statusElement = document.getElementById('bug-status-' + bugId);
                if (statusElement) {
                    var status = data.status.toLowerCase();
                    if (status === 'new' || status === 'open') {
                        statusElement.style.color = '#ff6600';
                        statusElement.style.fontWeight = 'bold';
                    } else if (status === 'in progress' || status === 'assigned') {
                        statusElement.style.color = '#ffa500';
                        statusElement.style.fontWeight = 'bold';
                    } else if (status === 'resolved' || status === 'closed') {
                        statusElement.style.color = '#008000';
                        statusElement.style.fontWeight = 'bold';
                    } else if (status === 'feedback' || status === 'rejected') {
                        statusElement.style.color = '#800080';
                        statusElement.style.fontWeight = 'bold';
                    } else {
                        statusElement.style.color = '#666666';
                    }
                }
                
                // Color code the priority
                var priorityElement = document.getElementById('bug-priority-' + bugId);
                if (priorityElement && data.priority) {
                    var priority = data.priority.toLowerCase();
                    if (priority === 'urgent' || priority === 'high') {
                        priorityElement.style.color = '#d32f2f';
                        priorityElement.style.fontWeight = 'bold';
                    } else if (priority === 'normal' || priority === 'medium') {
                        priorityElement.style.color = '#ffa500';
                        priorityElement.style.fontWeight = 'normal';
                    } else if (priority === 'low') {
                        priorityElement.style.color = '#008000';
                        priorityElement.style.fontWeight = 'normal';
                    } else {
                        priorityElement.style.color = '#666666';
                    }
                }
                
                console.log('Updated bug', bugId, 'with status:', data.status, 'priority:', data.priority);
            } else {
                console.error('Failed to fetch status for bug', bugId, ':', data);
                document.getElementById('bug-status-' + bugId).innerHTML = 'Error';
                document.getElementById('bug-priority-' + bugId).innerHTML = 'Error';
                document.getElementById('bug-assignee-' + bugId).innerHTML = 'Error';
                document.getElementById('bug-updated-' + bugId).innerHTML = 'Error';
            }
        })
        .catch(error => {
            console.error('Error fetching bug status for', bugId, ':', error);
            document.getElementById('bug-status-' + bugId).innerHTML = 'Error';
            document.getElementById('bug-priority-' + bugId).innerHTML = 'Error';
            document.getElementById('bug-assignee-' + bugId).innerHTML = 'Error';
            document.getElementById('bug-updated-' + bugId).innerHTML = 'Error';
        });
}
</script>
