<?php
/* Smarty version 3.1.33, created on 2026-03-05 21:20:42
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\inc_show_bug_table.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69a9e59a6166c0_67809068',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '6e9600917138db2af5801796e47a932cff4dd692' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\inc_show_bug_table.tpl',
      1 => 1772741951,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69a9e59a6166c0_67809068 (Smarty_Internal_Template $_smarty_tpl) {
?>
<!-- SINGLE ROW BUG DISPLAY - Fits within existing execution layout -->
<?php if (isset($_smarty_tpl->tpl_vars['exec_id']->value) && $_smarty_tpl->tpl_vars['exec_id']->value > 0) {?>
	<!-- Bug details row - integrates with existing table structure -->
	<tr id="bug-details-row-<?php echo $_smarty_tpl->tpl_vars['exec_id']->value;?>
" style="background-color: #f8f9fa;">
		<td colspan="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['my_colspan']->value)===null||$tmp==='' ? '6' : $tmp);?>
" style="padding: 0;">
			<div id="bug-summary-<?php echo $_smarty_tpl->tpl_vars['exec_id']->value;?>
" style="padding: 8px 12px; border-left: 4px solid #007bff;">
				<div style="display: flex; align-items: center; justify-content: space-between;">
					<div style="display: flex; align-items: center; gap: 15px;">
						<span style="font-weight: bold; color: #333;">🐛 Bugs:</span>
						<span id="bug-count-<?php echo $_smarty_tpl->tpl_vars['exec_id']->value;?>
" style="color: #666;">Loading...</span>
						<div id="bug-list-<?php echo $_smarty_tpl->tpl_vars['exec_id']->value;?>
" style="display: flex; gap: 10px; flex-wrap: wrap;"></div>
					</div>
					<div id="bug-actions-<?php echo $_smarty_tpl->tpl_vars['exec_id']->value;?>
" style="font-size: 12px; color: #999;"></div>
				</div>
			</div>
		</td>
	</tr>

	<?php echo '<script'; ?>
>
		console.log('=== SINGLE ROW BUG DISPLAY ===');
		console.log('Loading bugs for execution ID:', <?php echo json_encode($_smarty_tpl->tpl_vars['exec_id']->value);?>
);

		// Fetch bug data and display as single row
		fetch('redmine_status_api.php?execution_id=<?php echo $_smarty_tpl->tpl_vars['exec_id']->value;?>
')
		.then(response => response.json())
			.then(data => {
				console.log('Bug data received:', data);
				const countElement = document.getElementById('bug-count-<?php echo $_smarty_tpl->tpl_vars['exec_id']->value;?>
');
				const listElement = document.getElementById('bug-list-<?php echo $_smarty_tpl->tpl_vars['exec_id']->value;?>
');
				const actionsElement = document.getElementById('bug-actions-<?php echo $_smarty_tpl->tpl_vars['exec_id']->value;?>
');

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
				document.getElementById('bug-count-<?php echo $_smarty_tpl->tpl_vars['exec_id']->value;?>
').innerHTML = '<span style="color: #cc0000;">Error loading bugs</span>';
			});
	<?php echo '</script'; ?>
>
<?php } else { ?>
	<tr style="background-color: #fff3cd;">
		<td colspan="<?php echo (($tmp = @$_smarty_tpl->tpl_vars['my_colspan']->value)===null||$tmp==='' ? '6' : $tmp);?>
" style="padding: 8px; text-align: center; color: #856404;">
			<strong>Invalid execution ID: <?php echo (($tmp = @$_smarty_tpl->tpl_vars['exec_id']->value)===null||$tmp==='' ? 'NOT_SET' : $tmp);?>
</strong>
		</td>
	</tr>
<?php }?>

<?php }
}
