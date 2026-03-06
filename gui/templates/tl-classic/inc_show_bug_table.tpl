{* 
Testlink Open Source Project - http://testlink.sourceforge.net/ 
@filesource inc_show_bug_table.tpl

@internal revisions
*}

<!-- SINGLE ROW BUG DISPLAY - Fits within existing execution layout -->
{if isset($exec_id) && $exec_id > 0}
	<!-- Bug details row - integrates with existing table structure -->
	<tr id="bug-details-row-{$exec_id}" style="background-color: #f8f9fa;">
		<td colspan="{$my_colspan|default:'6'}" style="padding: 0;">
			<div id="bug-summary-{$exec_id}" style="padding: 8px 12px; border-left: 4px solid #007bff;">
				<div style="display: flex; align-items: center; justify-content: space-between;">
					<div style="display: flex; align-items: center; gap: 15px;">
						<span style="font-weight: bold; color: #333;">🐛 Bugs:</span>
						<span id="bug-count-{$exec_id}" style="color: #666;">Loading...</span>
						<div id="bug-list-{$exec_id}" style="display: flex; gap: 10px; flex-wrap: wrap;"></div>
					</div>
					<div id="bug-actions-{$exec_id}" style="font-size: 12px; color: #999;"></div>
				</div>
			</div>
		</td>
	</tr>

	<script>
		console.log('=== SINGLE ROW BUG DISPLAY ===');
		console.log('Loading bugs for execution ID:', {$exec_id|@json_encode});

		// Fetch bug data and display as single row
		fetch('redmine_status_api.php?execution_id={$exec_id}')
		.then(response => response.json())
			.then(data => {
				console.log('Bug data received:', data);
				const countElement = document.getElementById('bug-count-{$exec_id}');
				const listElement = document.getElementById('bug-list-{$exec_id}');
				const actionsElement = document.getElementById('bug-actions-{$exec_id}');

				if (data.success && data.bugs && data.bugs.length > 0) {
					// Update count
					countElement.innerHTML = '<strong>' + data.bugs.length + '</strong> bug(s) found';

					// Create bug pills
					let bugHtml = '';
					data.bugs.forEach(function(bug) {
						// Apply color coding
						let statusColor = '#333';
						const status = bug.status.toLowerCase();
						if (status === 'new' || status === 'open') {
							statusColor = '#ff6600';
						} else if (status === 'in progress' || status === 'assigned') {
							statusColor = '#ffa500';
						} else if (status === 'resolved' || status === 'closed') {
							statusColor = '#008000';
						}

						let priorityColor = '#333';
						const priority = bug.priority.toLowerCase();
						if (priority === 'urgent' || priority === 'high') {
							priorityColor = '#d32f2f';
						} else if (priority === 'normal' || priority === 'medium') {
							priorityColor = '#ffa500';
						} else if (priority === 'low') {
							priorityColor = '#008000';
						}

						bugHtml +=
							'<div style="display: inline-flex; align-items: center; gap: 6px; padding: 4px 8px; background: white; border: 1px solid #ddd; border-radius: 12px; font-size: 12px;">';
						bugHtml += '<strong>#' + bug.bug_id + '</strong>';
						bugHtml += '<span style="color: ' + statusColor + '; font-weight: bold;">' + bug.status +
							'</span>';
						bugHtml += '<span style="color: ' + priorityColor + ';">' + bug.priority + '</span>';
						bugHtml += '<a href="https://support.profinch.com/issues/' + bug.bug_id +
							'" target="_blank" style="color: #0066cc; text-decoration: none;">🔗</a>';
						bugHtml += '</div>';
					});

					listElement.innerHTML = bugHtml;
					actionsElement.innerHTML = 'Updated: ' + new Date().toLocaleTimeString();
				} else {
					countElement.innerHTML = '<span style="color: #999;">No bugs</span>';
					listElement.innerHTML = '';
					actionsElement.innerHTML = '';
				}
			})
			.catch(error => {
				console.error('Error loading bugs:', error);
				document.getElementById('bug-count-{$exec_id}').innerHTML = '<span style="color: #cc0000;">Error loading bugs</span>';
			});
	</script>
{else}
	<tr style="background-color: #fff3cd;">
		<td colspan="{$my_colspan|default:'6'}" style="padding: 8px; text-align: center; color: #856404;">
			<strong>Invalid execution ID: {$exec_id|default:'NOT_SET'}</strong>
		</td>
	</tr>
{/if}

{* -------------------------------------------------------------------------------------- *}