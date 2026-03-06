<?php
/* Smarty version 3.1.33, created on 2026-03-05 21:20:26
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\inc_filter_panel_js.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69a9e58a1f5e96_74789289',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1acdfbe11d48ef29ac9c3a51804974e91446b3fa' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\inc_filter_panel_js.tpl',
      1 => 1771425448,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69a9e58a1f5e96_74789289 (Smarty_Internal_Template $_smarty_tpl) {
echo '<script'; ?>
 type="text/javascript">

<?php if ($_smarty_tpl->tpl_vars['control']->value->filters['filter_assigned_user']) {?>
  /**
   * Used to disable the "include unassigned testcases" checkbox when anything else 
   * but a username is selected in "assigned to" select box.
   * In case of a selected username the box will be activated again.
   * (testcase execution & testcase execution assignment, BUGID 2455, BUGID 3026)
   * 
   * @author Andreas Simon
   * @param filter_assigned_to combobox in which assignment is chosen
   * @param include_unassigned checkbox for including unassigned testcases
   * @param str_option_any string value anybody
   * @param str_option_none string value nobody
   * @param str_option_somebody string value somebody
   */
  function triggerAssignedBox(filter_assigned_to_id, include_unassigned_id,
                              str_option_any, str_option_none, str_option_somebody) 
  {
    var filter_assigned_to = document.getElementById(filter_assigned_to_id);
    var include_unassigned = document.getElementById(include_unassigned_id);
    var index = filter_assigned_to.options.selectedIndex;
    var choice = filter_assigned_to.options[index].label;
    include_unassigned.disabled = false;
    if (choice == str_option_any || choice == str_option_none || choice == str_option_somebody) 
    {
      include_unassigned.disabled = true;
      include_unassigned.checked = false;
    } 
  }
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['control']->value->filters['filter_result']) {?>
  /**
   * If filter method ("filter on...") selection is set to "specific build",
   * enable build selector, otherwise disable it.
   * (testcase execution & testcase execution assignment, BUGID 2455, BUGID 3026)
   * 
   * @author Andreas Simon
   * @param build_id_combo box in which the build is chosen
   * @param filter_method_combo box in which the filter method is chosen
   * @param specific_build_value value for which the box shall be disabled
   */
  function triggerBuildChooser(deactivatable_id, filter_method_combo_id, specific_build_value)
  {
    var deactivatable = document.getElementById(deactivatable_id);
    var filter_method_combo = document.getElementById(filter_method_combo_id);
    var index = filter_method_combo.options.selectedIndex;  
    if(filter_method_combo[index].value == specific_build_value) 
    {
      deactivatable.style.visibility = "visible";
    } 
    else 
    {
      deactivatable.style.visibility = "hidden";
    }
  }
    
  /**
   * Disable unneeded filters in the filter method combo box.
   * If only one build is selectable in filter, then the filter method
   * gets set to "build chosen for execution" because no other method should
   * be used in that case.
   * (testcase execution & testcase execution assignment, BUGID 2455, BUGID 3026)
   *  
   * @author Andreas Simon
   * @param filter_method_combo_id the id of the box which shall be disabled
   * @param value2select the string which shall be selected in the box before disabling it
   */
  function triggerFilterMethodSelector(filter_method_combo_id, value2select) 
  {
    filter_method_combo = document.getElementById(filter_method_combo_id);
    var length = filter_method_combo.options.length;
      
    for (var index = 0; index < length; index ++) 
    {
      if (filter_method_combo.options[index].value == value2select) 
      {
        filter_method_combo.options.selectedIndex = index;
      }
    }
    filter_method_combo.disabled = true;
  }
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['control']->value->draw_tc_unassign_button) {?>
/**
 * Open the tc_exec_assignment page in workframe to delete
 * all tester assignments for a build. 
 */
function delete_testers_from_build(id) 
{
  var action_url = fRoot + 'lib/plan/tc_exec_unassign_all.php' + 
                   '?confirmed=no&build_id=' + id;
  parent.workframe.location = action_url;
}
<?php }?>

<?php if ($_smarty_tpl->tpl_vars['control']->value->draw_tc_assignment_bulk_copy_button) {?>
/**
 * Open page in workframe to copy all tester assignments 
 * from a build (that user will select on GUI) onto build 
 * (identified by id). 
 */
function copy_tester_assignments_from_build(destination) 
{
  var action_url = fRoot + 'lib/plan/buildCopyExecTaskAssignment.php' + 
                   '?confirmed=no&build_id=' + destination;
  parent.workframe.location = action_url;
}
<?php }?>


  // Performance optimization: Automatically uncheck refresh tree on action
  // This should happen AFTER the tree has finished loading to avoid interference
  // We'll use a timeout to ensure tree is fully loaded
  setTimeout(function() {
    var refreshTreeCheckbox = document.getElementById('cbsetting_refresh_tree_on_action');
    if (refreshTreeCheckbox && refreshTreeCheckbox.checked) {
      refreshTreeCheckbox.checked = false;
      console.log('Performance optimization: "Refresh Tree on Action" checkbox automatically unchecked after tree loaded for better performance');
    // alert('Page has been loaded!')
      // Optional: Trigger change event if needed
      if (typeof refreshTreeCheckbox.onchange === 'function') {
        refreshTreeCheckbox.onchange();
      }
    }
  }, 2000); // Wait 2 seconds for tree to fully load


  

<?php echo '</script'; ?>
>

</head>

<?php if ($_smarty_tpl->tpl_vars['control']->value->filters['filter_result'] || $_smarty_tpl->tpl_vars['control']->value->filters['filter_assigned_user']) {?>
  <body onload="javascript:
    <?php if ($_smarty_tpl->tpl_vars['control']->value->filters['filter_result']) {?>

      <?php if (count($_smarty_tpl->tpl_vars['control']->value->filters['filter_result']['filter_result_build']['items']) == 1) {?>
        triggerFilterMethodSelector('filter_result_method',
          <?php echo $_smarty_tpl->tpl_vars['control']->value->filters['filter_result']['filter_result_method']['js_selection'];?>
);
      <?php }?>
      triggerBuildChooser('filter_result_build_row',
                          'filter_result_method',
                          <?php echo $_smarty_tpl->tpl_vars['control']->value->configuration->filter_methods['status_code']['specific_build'];?>
);
    <?php }?>
    
    <?php if ($_smarty_tpl->tpl_vars['control']->value->filters['filter_assigned_user']) {?>
      triggerAssignedBox('filter_assigned_user',
                         'filter_assigned_user_include_unassigned',
                         '<?php echo $_smarty_tpl->tpl_vars['control']->value->option_strings['any'];?>
',
                         '<?php echo $_smarty_tpl->tpl_vars['control']->value->option_strings['none'];?>
',
                         '<?php echo $_smarty_tpl->tpl_vars['control']->value->option_strings['somebody'];?>
');
    <?php }?>
  ">
<?php } else { ?>
  <body>
<?php }
}
}
