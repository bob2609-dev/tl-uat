<?php
/* Smarty version 3.1.33, created on 2026-03-05 21:20:10
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\mainPageRight.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69a9e57a851650_24507437',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ba5cae8aa0b56dcfc42afee44d9bf753bc381d7d' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\mainPageRight.tpl',
      1 => 1771826378,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:inc_help.tpl' => 1,
  ),
),false)) {
function content_69a9e57a851650_24507437 (Smarty_Internal_Template $_smarty_tpl) {
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('var'=>"labels",'s'=>"current_test_plan,ok,testplan_role,msg_no_rights_for_tp,
             title_test_execution,href_execute_test,href_rep_and_metrics,
             href_update_tplan,href_newest_tcversions,title_plugins,
             href_my_testcase_assignments,href_platform_assign,
             href_tc_exec_assignment,href_plan_assign_urgency,
             href_upd_mod_tc,title_test_plan_mgmt,title_test_case_suite,
             href_plan_management,href_assign_user_roles,
             href_build_new,href_plan_mstones,href_plan_define_priority,
             href_metrics_dashboard,href_add_remove_test_cases,
             href_exec_ro_access"),$_smarty_tpl ) );?>


<?php $_smarty_tpl->_assignInScope('planView', "lib/plan/planView.php");
$_smarty_tpl->_assignInScope('buildView', "lib/plan/buildView.php?tplan_id=");
$_smarty_tpl->_assignInScope('mileView', "lib/plan/planMilestonesView.php");
$_smarty_tpl->_assignInScope('platformAssign', "lib/platforms/platformsAssign.php?tplan_id=");?>

<?php $_smarty_tpl->_assignInScope('menuLayout', $_smarty_tpl->tpl_vars['tlCfg']->value->gui->layoutMainPageRight);
$_smarty_tpl->_assignInScope('display_right_block_1', false);
$_smarty_tpl->_assignInScope('display_right_block_2', false);
$_smarty_tpl->_assignInScope('display_right_block_3', false);
$_smarty_tpl->_assignInScope('display_left_block_top', false);
$_smarty_tpl->_assignInScope('display_left_block_bottom', false);?>

<?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['testplan_planning'] == "yes" || $_smarty_tpl->tpl_vars['gui']->value->grants['mgt_testplan_create'] == "yes" || $_smarty_tpl->tpl_vars['gui']->value->grants['testplan_user_role_assignment'] == "yes" || $_smarty_tpl->tpl_vars['gui']->value->grants['testplan_create_build'] == "yes") {?>
   <?php $_smarty_tpl->_assignInScope('display_right_block_1', true);
}?>

<?php if ($_smarty_tpl->tpl_vars['gui']->value->countPlans > 0 && ($_smarty_tpl->tpl_vars['gui']->value->grants['testplan_execute'] == "yes" || $_smarty_tpl->tpl_vars['gui']->value->grants['testplan_metrics'] == "yes" || $_smarty_tpl->tpl_vars['gui']->value->grants['exec_ro_access'] == "yes")) {?>
   <?php $_smarty_tpl->_assignInScope('display_right_block_2', true);
}?>

<?php if ($_smarty_tpl->tpl_vars['gui']->value->countPlans > 0 && $_smarty_tpl->tpl_vars['gui']->value->grants['testplan_planning'] == "yes") {?>
   <?php $_smarty_tpl->_assignInScope('display_right_block_3', true);
}?>

<?php $_smarty_tpl->_assignInScope('display_right_block_top', false);
$_smarty_tpl->_assignInScope('display_right_block_bottom', true);?>

<?php if (isset($_smarty_tpl->tpl_vars['gui']->value->plugins['EVENT_RIGHTMENU_TOP']) && $_smarty_tpl->tpl_vars['gui']->value->plugins['EVENT_RIGHTMENU_TOP']) {?>
  <?php $_smarty_tpl->_assignInScope('display_right_block_top', true);
}?>


<?php $_smarty_tpl->_assignInScope('divStyle', "width:300px;padding: 0px 0px 0px 10px;");
$_smarty_tpl->_assignInScope('aStyle', "padding: 3px 15px;font-size:16px");?>

<div class="vertical_menu" style="float: right; margin:0px 0px 10px 10px;width: 320px;">
	<?php if ($_smarty_tpl->tpl_vars['gui']->value->num_active_tplans > 0) {?>
	  <div class="" style="padding: 3px 15px;">
     <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('s'=>'help','var'=>'common_prefix'),$_smarty_tpl ) );?>

     <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('s'=>'test_plan','var'=>"xx_alt"),$_smarty_tpl ) );?>

     <?php $_smarty_tpl->_assignInScope('text_hint', ((string)$_smarty_tpl->tpl_vars['common_prefix']->value).": ".((string)$_smarty_tpl->tpl_vars['xx_alt']->value));?>
     <?php $_smarty_tpl->_subTemplateRender("file:inc_help.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('helptopic'=>"hlp_testPlan",'show_help_icon'=>true,'inc_help_alt'=>((string)$_smarty_tpl->tpl_vars['text_hint']->value),'inc_help_title'=>((string)$_smarty_tpl->tpl_vars['text_hint']->value),'inc_help_style'=>"float: right;vertical-align: top;"), 0, false);
?>

 	   <form name="testplanForm" action="lib/general/mainPage.php">
       <?php if ($_smarty_tpl->tpl_vars['gui']->value->countPlans > 0) {?>
		     <?php echo $_smarty_tpl->tpl_vars['labels']->value['current_test_plan'];?>
:<br/>
		     <select class="chosen-select" name="testplan" onchange="this.form.submit();">
		     	<?php
$__section_tPlan_0_loop = (is_array(@$_loop=$_smarty_tpl->tpl_vars['gui']->value->arrPlans) ? count($_loop) : max(0, (int) $_loop));
$__section_tPlan_0_total = $__section_tPlan_0_loop;
$_smarty_tpl->tpl_vars['__smarty_section_tPlan'] = new Smarty_Variable(array());
if ($__section_tPlan_0_total !== 0) {
for ($__section_tPlan_0_iteration = 1, $_smarty_tpl->tpl_vars['__smarty_section_tPlan']->value['index'] = 0; $__section_tPlan_0_iteration <= $__section_tPlan_0_total; $__section_tPlan_0_iteration++, $_smarty_tpl->tpl_vars['__smarty_section_tPlan']->value['index']++){
?>
		     		<option value="<?php echo $_smarty_tpl->tpl_vars['gui']->value->arrPlans[(isset($_smarty_tpl->tpl_vars['__smarty_section_tPlan']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_tPlan']->value['index'] : null)]['id'];?>
"
		     		        <?php if ($_smarty_tpl->tpl_vars['gui']->value->arrPlans[(isset($_smarty_tpl->tpl_vars['__smarty_section_tPlan']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_tPlan']->value['index'] : null)]['selected']) {?> selected="selected" <?php }?>
		     		        title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->arrPlans[(isset($_smarty_tpl->tpl_vars['__smarty_section_tPlan']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_tPlan']->value['index'] : null)]['name'], ENT_QUOTES, 'UTF-8', true);?>
">
		     		        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->arrPlans[(isset($_smarty_tpl->tpl_vars['__smarty_section_tPlan']->value['index']) ? $_smarty_tpl->tpl_vars['__smarty_section_tPlan']->value['index'] : null)]['name'], ENT_QUOTES, 'UTF-8', true);?>

		     		</option>
		     	<?php
}
}
?>
		     </select>
		     
		     <?php if ($_smarty_tpl->tpl_vars['gui']->value->countPlans == 1) {?>
		     	<input type="button" onclick="this.form.submit();" value="<?php echo $_smarty_tpl->tpl_vars['labels']->value['ok'];?>
"/>
		     <?php }?>
		     
		     <?php if ($_smarty_tpl->tpl_vars['gui']->value->testplanRole != null) {?>
		     	<br /><?php echo $_smarty_tpl->tpl_vars['labels']->value['testplan_role'];?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->testplanRole, ENT_QUOTES, 'UTF-8', true);?>

		     <?php }?>
	     <?php } else { ?>
         <?php if ($_smarty_tpl->tpl_vars['gui']->value->num_active_tplans > 0) {
echo $_smarty_tpl->tpl_vars['labels']->value['msg_no_rights_for_tp'];
}?>
		   <?php }?>
	   </form>
	  </div>
  <?php }?>
  <br />

   <?php if ($_smarty_tpl->tpl_vars['display_right_block_top']->value) {?>
    <?php if (isset($_smarty_tpl->tpl_vars['gui']->value->plugins['EVENT_RIGHTMENU_TOP'])) {?>
      <div class="list-group" style="<?php echo $_smarty_tpl->tpl_vars['divStyle']->value;?>
" id="plugin_right_top">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['gui']->value->plugins['EVENT_RIGHTMENU_TOP'], 'menu_item');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['menu_item']->value) {
?>
		  <a href="<?php echo $_smarty_tpl->tpl_vars['menu_item']->value['href'];?>
" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['menu_item']->value['label'];?>
</a>
          <br/>
        <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
      </div>
    <?php }?>
  <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['display_right_block_1']->value) {?>
    <div class="list-group" style="<?php echo $_smarty_tpl->tpl_vars['divStyle']->value;?>
">
      <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['mgt_testplan_create'] == "yes") {?>
       		<a href="<?php echo $_smarty_tpl->tpl_vars['planView']->value;?>
" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_plan_management'];?>
</a>
	    <?php }?>
	    
	    <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['testplan_create_build'] == "yes" && $_smarty_tpl->tpl_vars['gui']->value->countPlans > 0) {?>
       	<a href="<?php echo $_smarty_tpl->tpl_vars['buildView']->value;
echo $_smarty_tpl->tpl_vars['gui']->value->testplanID;?>
" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_build_new'];?>
</a>
      <?php }?>
	    
      <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['testplan_milestone_overview'] == "yes" && $_smarty_tpl->tpl_vars['gui']->value->countPlans > 0) {?>
         <a href="<?php echo $_smarty_tpl->tpl_vars['mileView']->value;?>
" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_plan_mstones'];?>
</a>
      <?php }?>
    </div>
  <?php }?>

  <?php if ($_smarty_tpl->tpl_vars['display_right_block_2']->value) {?>
    <div class="list-group" style="<?php echo $_smarty_tpl->tpl_vars['divStyle']->value;?>
">
	<?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['testplan_execute'] == "yes" || $_smarty_tpl->tpl_vars['gui']->value->grants['exec_ro_access'] == "yes") {?>

        <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['testplan_execute'] == "yes") {?>
          <?php $_smarty_tpl->_assignInScope('lbx', $_smarty_tpl->tpl_vars['labels']->value['href_execute_test']);?>
        <?php }?>

        <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['exec_ro_access'] == "yes") {?>  
          <?php $_smarty_tpl->_assignInScope('lbx', $_smarty_tpl->tpl_vars['labels']->value['href_exec_ro_access']);?>
        <?php }?>

		<a href="<?php echo $_smarty_tpl->tpl_vars['gui']->value->launcher;?>
?feature=executeTest" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['lbx']->value;?>
</a>
		
				<a href="lib/execute/optimized_execution_module.php" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
">
			<span style="color: rgb(40, 167, 69); font-weight: bold;">⚡ Optimized Execution</span>
		</a>
		<a href="lib/execute/optimized_execution_standalone.html" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
" target="_blank">
			<span style="color: rgb(23, 162, 184); font-weight: bold;">🚀 Standalone Version</span>
		</a>

      <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['exec_testcases_assigned_to_me'] == "yes") {?>
			 <a href="<?php echo $_smarty_tpl->tpl_vars['gui']->value->url['testcase_assignments'];?>
" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_my_testcase_assignments'];?>
</a>
      <?php }?> 
		<?php }?> 
      
		<?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['testplan_metrics'] == "yes") {?>
			<a href="<?php echo $_smarty_tpl->tpl_vars['gui']->value->launcher;?>
?feature=showMetrics" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_rep_and_metrics'];?>
</a>
  			<a href="<?php echo $_smarty_tpl->tpl_vars['gui']->value->url['metrics_dashboard'];?>
" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_metrics_dashboard'];?>
</a>
		<?php }?> 
    </div>
	<?php }?>

    <br/><br/>
  <div class="list-group" style="<?php echo $_smarty_tpl->tpl_vars['divStyle']->value;?>
">
    <div style="padding: 10px 15px; background-color: #f8f9fa; border-left: 4px solid #007bff; margin-bottom: 10px;">
      <h6 style="margin: 0; color: #495057; font-weight: bold;">Reports</h6>
    </div>
       
		<?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['testplan_metrics'] == "yes") {?>
			<a href="lib/execute/test_execution_summary.php" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
">Execution Summary</a>
		<?php }?> 
        <a href="lib/execute/test_execution_summary_optimized_standalone.html" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
">  <span style="color: rgb(40, 167, 69);font-weight: bold;">Optimized! </span> Execution Summary </a>
    
        <a href="lib/execute/suite_execution_summary_proc.php" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
">  <span style="color: rgb(243, 111, 59);font-weight: bold;">New! </span> Test Suite Execution Summary </a>
    <a href="lib/execute/suite_execution_summary_optimized_standalone.html" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
">  <span style="color: rgb(40, 167, 69);font-weight: bold;">Optimized! </span> <span style="color: rgb(243, 111, 59);font-weight: bold;">Test Suite Execution Summary</span></a>
        			<a href="lib/execute/tester_execution_report_professional.html" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
">  <span style="color: rgb(40, 167, 69);font-weight: bold;">New! </span> <span style="color: rgb(23, 162, 184);font-weight: bold;">👤  Tester Execution Report</span></a>
			    <a href="lib/execute/tester_execution_report_breakdown.html" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
">  <span style="color: rgb(40, 167, 69);font-weight: bold;">New! </span> <span style="color: rgb(23, 162, 184);font-weight: bold;">🔍 Tester Execution Breakdown</span></a>
        <a href="lib/execute/other_custom_reports.php" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><span style="color: rgb(243, 111, 59); font-weight: bold;">Other Custom Reports</span></a>
  </div>
  <br/>

	<?php if ($_smarty_tpl->tpl_vars['display_right_block_3']->value) {?>
    <div class="list-group" style="<?php echo $_smarty_tpl->tpl_vars['divStyle']->value;?>
">
    <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['testplan_add_remove_platforms'] == "yes") {?>
  	  <a href="<?php echo $_smarty_tpl->tpl_vars['platformAssign']->value;
echo $_smarty_tpl->tpl_vars['gui']->value->testplanID;?>
" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_platform_assign'];?>
</a>
    <?php }?> 
		
	  <a href="<?php echo $_smarty_tpl->tpl_vars['gui']->value->launcher;?>
?feature=planAddTC" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_add_remove_test_cases'];?>
</a>

    <a href="<?php echo $_smarty_tpl->tpl_vars['gui']->value->launcher;?>
?feature=tc_exec_assignment" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_tc_exec_assignment'];?>
</a>
		
    <?php if ($_smarty_tpl->tpl_vars['session']->value['testprojectOptions']->testPriorityEnabled && $_smarty_tpl->tpl_vars['gui']->value->grants['testplan_set_urgent_testcases'] == "yes") {?>
      <a href="<?php echo $_smarty_tpl->tpl_vars['gui']->value->launcher;?>
?feature=test_urgency" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_plan_assign_urgency'];?>
</a>
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['testplan_update_linked_testcase_versions'] == "yes") {?>
	   	<a href="<?php echo $_smarty_tpl->tpl_vars['gui']->value->launcher;?>
?feature=planUpdateTC" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_update_tplan'];?>
</a>
    <?php }?> 

    <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['testplan_show_testcases_newest_versions'] == "yes") {?>
	   	<a href="<?php echo $_smarty_tpl->tpl_vars['gui']->value->launcher;?>
?feature=newest_tcversions" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_newest_tcversions'];?>
</a>
    <?php }?> 

    </div>
  <?php }?>

  <?php if ($_smarty_tpl->tpl_vars['display_right_block_bottom']->value) {?>

    <div class="list-group" style="<?php echo $_smarty_tpl->tpl_vars['divStyle']->value;?>
" id="plugin_right_bottom">
    <br/>
    <address>
    <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('var'=>"lbl_f",'s'=>"poweredBy,system_descr"),$_smarty_tpl ) );?>


    <strong><h6><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['lbl_f']->value['poweredBy'], ENT_QUOTES, 'UTF-8', true);?>
 <a href="<?php echo $_smarty_tpl->tpl_vars['tlCfg']->value->testlinkdotorg;?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['lbl_f']->value['system_descr'], ENT_QUOTES, 'UTF-8', true);?>
">TestLink <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['tlVersion']->value, ENT_QUOTES, 'UTF-8', true);?>
</a></h6></strong> <br>
    </address>

    <?php if (isset($_smarty_tpl->tpl_vars['gui']->value->plugins['EVENT_RIGHTMENU_BOTTOM'])) {?>
	  <br/>
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['gui']->value->plugins['EVENT_RIGHTMENU_BOTTOM'], 'menu_item');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['menu_item']->value) {
?>
		  <a href="<?php echo $_smarty_tpl->tpl_vars['menu_item']->value['href'];?>
" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['menu_item']->value['label'];?>
</a>
        <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    <?php }?>  
    </div>
  <?php }?>
  
</div>
<?php echo '<script'; ?>
>
jQuery( document ).ready(function() {
jQuery(".chosen-select").chosen({ width: "85%" });
});
<?php echo '</script'; ?>
>
<?php }
}
