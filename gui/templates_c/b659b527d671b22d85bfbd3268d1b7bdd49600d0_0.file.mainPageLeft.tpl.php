<?php
/* Smarty version 3.1.33, created on 2026-03-05 21:20:10
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\mainPageLeft.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69a9e57a9c20b5_70178628',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'b659b527d671b22d85bfbd3268d1b7bdd49600d0' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\mainPageLeft.tpl',
      1 => 1771824500,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69a9e57a9c20b5_70178628 (Smarty_Internal_Template $_smarty_tpl) {
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('var'=>'labels','s'=>'title_product_mgmt,href_tproject_management,href_admin_modules,
   href_assign_user_roles,href_cfields_management,system_config,
   href_cfields_tproject_assign,href_keywords_manage,
   title_user_mgmt,href_user_management,
   href_roles_management,title_requirements,
   href_req_spec,href_req_assign,link_report_test_cases_created_per_user,
   title_test_spec,href_edit_tc,href_browse_tc,href_search_tc,
   href_search_req, href_search_req_spec,href_inventory,
   href_platform_management, href_inventory_management,
   href_print_tc,href_keywords_assign, href_req_overview,
   href_print_req,title_plugins,title_documentation,href_issuetracker_management,
   href_codetracker_management,href_reqmgrsystem_management,href_req_monitor_overview,
   href_excel_import'),$_smarty_tpl ) );?>


<?php $_smarty_tpl->_assignInScope('display_left_block_1', false);
$_smarty_tpl->_assignInScope('display_left_block_2', false);
$_smarty_tpl->_assignInScope('display_left_block_3', false);
$_smarty_tpl->_assignInScope('display_left_block_4', false);
$_smarty_tpl->_assignInScope('display_left_block_5', $_smarty_tpl->tpl_vars['tlCfg']->value->userDocOnDesktop);
$_smarty_tpl->_assignInScope('display_left_block_top', false);
$_smarty_tpl->_assignInScope('display_left_block_bottom', false);?>

<?php if ($_smarty_tpl->tpl_vars['gui']->value->testprojectID && ($_smarty_tpl->tpl_vars['gui']->value->grants['project_edit'] == "yes" || $_smarty_tpl->tpl_vars['gui']->value->grants['tproject_user_role_assignment'] == "yes" || $_smarty_tpl->tpl_vars['gui']->value->grants['cfield_management'] == "yes" || $_smarty_tpl->tpl_vars['gui']->value->grants['platform_management'] == "yes" || $_smarty_tpl->tpl_vars['gui']->value->grants['keywords_view'] == "yes")) {?>

<?php $_smarty_tpl->_assignInScope('display_left_block_1', true);
}?>

<?php if ($_smarty_tpl->tpl_vars['gui']->value->testprojectID && ($_smarty_tpl->tpl_vars['gui']->value->grants['cfield_management'] || $_smarty_tpl->tpl_vars['gui']->value->grants['cfield_assignment'] || $_smarty_tpl->tpl_vars['gui']->value->grants['issuetracker_management'] || $_smarty_tpl->tpl_vars['gui']->value->grants['codetracker_management'] || $_smarty_tpl->tpl_vars['gui']->value->grants['issuetracker_view'] || $_smarty_tpl->tpl_vars['gui']->value->grants['codetracker_view'])) {
$_smarty_tpl->_assignInScope('display_left_block_2', true);
}?>

<?php if ($_smarty_tpl->tpl_vars['gui']->value->testprojectID && $_smarty_tpl->tpl_vars['gui']->value->opt_requirements == TRUE && ($_smarty_tpl->tpl_vars['gui']->value->grants['reqs_view'] == "yes" || $_smarty_tpl->tpl_vars['gui']->value->grants['reqs_edit'] == "yes" || $_smarty_tpl->tpl_vars['gui']->value->grants['monitor_req'] == "yes" || $_smarty_tpl->tpl_vars['gui']->value->grants['req_tcase_link_management'] == "yes")) {
$_smarty_tpl->_assignInScope('display_left_block_3', true);
}?>

<?php if ($_smarty_tpl->tpl_vars['gui']->value->testprojectID && ($_smarty_tpl->tpl_vars['gui']->value->grants['view_tc'] == "yes" || $_smarty_tpl->tpl_vars['gui']->value->grants['modify_tc'] == "yes")) {
$_smarty_tpl->_assignInScope('display_left_block_4', true);
}?>

<?php $_smarty_tpl->_assignInScope('display_left_block_top', false);
$_smarty_tpl->_assignInScope('display_left_block_bottom', false);?>

<?php if (isset($_smarty_tpl->tpl_vars['gui']->value->plugins['EVENT_LEFTMENU_TOP']) && $_smarty_tpl->tpl_vars['gui']->value->plugins['EVENT_LEFTMENU_TOP']) {?>
  <?php $_smarty_tpl->_assignInScope('display_left_block_top', true);
}
if (isset($_smarty_tpl->tpl_vars['gui']->value->plugins['EVENT_LEFTMENU_BOTTOM']) && $_smarty_tpl->tpl_vars['gui']->value->plugins['EVENT_LEFTMENU_BOTTOM']) {?>
  <?php $_smarty_tpl->_assignInScope('display_left_block_bottom', true);
}?>



<?php $_smarty_tpl->_assignInScope('divStyle', "width:300px;padding: 0px 0px 0px 10px;");
$_smarty_tpl->_assignInScope('aStyle', "padding: 3px 15px;font-size:16px");?>

<?php $_smarty_tpl->_assignInScope('projectView', "lib/project/projectView.php");
$_smarty_tpl->_assignInScope('usersAssign', "lib/usermanagement/usersAssign.php?featureType=testproject&featureID=");
$_smarty_tpl->_assignInScope('cfAssignment', "lib/cfields/cfieldsTprojectAssign.php");
$_smarty_tpl->_assignInScope('keywordsAssignment', "lib/keywords/keywordsView.php?tproject_id=");
$_smarty_tpl->_assignInScope('platformsView', "lib/platforms/platformsView.php?tproject_id=");
$_smarty_tpl->_assignInScope('cfieldsView', "lib/cfields/cfieldsView.php?tproject_id=");
$_smarty_tpl->_assignInScope('issueTrackerView', "lib/issuetrackers/issueTrackerView.php?tproject_id=");
$_smarty_tpl->_assignInScope('codeTrackerView', "lib/codetrackers/codeTrackerView.php?tproject_id=");
$_smarty_tpl->_assignInScope('reqOverView', "lib/requirements/reqOverview.php");
$_smarty_tpl->_assignInScope('reqMonOverView', "lib/requirements/reqMonitorOverview.php?tproject_id=");
$_smarty_tpl->_assignInScope('tcSearch', "lib/testcases/tcSearch.php?doAction=userInput&tproject_id=");
$_smarty_tpl->_assignInScope('tcCreatedUser', "lib/results/tcCreatedPerUserOnTestProject.php?do_action=uinput&tproject_id=");
$_smarty_tpl->_assignInScope('assignReq', "lib/general/frmWorkArea.php?feature=assignReqs");
$_smarty_tpl->_assignInScope('inventoryView', "lib/inventory/inventoryView.php");
$_smarty_tpl->_assignInScope('bugsView', "lib/execute/bugs_view.php");
$_smarty_tpl->_assignInScope('excelImportView', "lib/admin/excelImport.php");?>

<div class="vertical_menu" style="float: left; margin:0px 10px 10px 0px; width: 320px;">

  <?php if ($_smarty_tpl->tpl_vars['display_left_block_top']->value) {?>
    <?php if (isset($_smarty_tpl->tpl_vars['gui']->value->plugins['EVENT_LEFTMENU_TOP'])) {?>
      <div class="list-group" style="<?php echo $_smarty_tpl->tpl_vars['divStyle']->value;?>
" id="plugin_left_top">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['gui']->value->plugins['EVENT_LEFTMENU_TOP'], 'menu_item');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['menu_item']->value) {
?>
          <a href="<?php echo $_smarty_tpl->tpl_vars['menu_item']->value['href'];?>
" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['menu_item']->value['label'];?>
</a>
          <br />
        <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
      </div>
    <?php }?>
  <?php }?>

  <?php if ($_smarty_tpl->tpl_vars['display_left_block_2']->value) {?>
    <div class="list-group" style="<?php echo $_smarty_tpl->tpl_vars['divStyle']->value;?>
">
      <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['cfield_management'] == "yes") {?>
        <a href="<?php echo $_smarty_tpl->tpl_vars['cfieldsView']->value;?>
" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_cfields_management'];?>
</a>
      <?php }?>

      <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['issuetracker_management'] || $_smarty_tpl->tpl_vars['gui']->value->grants['issuetracker_view']) {?>
        <a href="<?php echo $_smarty_tpl->tpl_vars['issueTrackerView']->value;
echo $_smarty_tpl->tpl_vars['gui']->value->testprojectID;?>
" class="list-group-item"
          style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_issuetracker_management'];?>
</a>
      <?php }?>

      <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['codetracker_management'] || $_smarty_tpl->tpl_vars['gui']->value->grants['codetracker_view']) {?>
        <a href="<?php echo $_smarty_tpl->tpl_vars['codeTrackerView']->value;
echo $_smarty_tpl->tpl_vars['gui']->value->testprojectID;?>
" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
">
          <?php echo $_smarty_tpl->tpl_vars['labels']->value['href_codetracker_management'];?>
</a>
      <?php }?>

      <!--   <a href="<?php echo $_smarty_tpl->tpl_vars['bugsView']->value;?>
" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
">Execution Bugs</a> -->
      <!-- COMMENTED -->
      <!--    <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['testplan_execute'] || $_smarty_tpl->tpl_vars['gui']->value->grants['testplan_metrics']) {?>
      <a href="<?php echo $_smarty_tpl->tpl_vars['bugsView']->value;?>
" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
">Execution Bugs</a>
    <?php }?> -->
    </div>
  <?php }?>

  <?php if ($_smarty_tpl->tpl_vars['display_left_block_1']->value) {?>
    <div class="list-group" style="<?php echo $_smarty_tpl->tpl_vars['divStyle']->value;?>
">
      <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['project_edit'] == "yes") {?>
        <a href="<?php echo $_smarty_tpl->tpl_vars['projectView']->value;?>
" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
">
          <?php echo $_smarty_tpl->tpl_vars['labels']->value['href_tproject_management'];?>
</a>
      <?php }?>

      <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['tproject_user_role_assignment'] == "yes") {?>
        <a href="<?php echo $_smarty_tpl->tpl_vars['usersAssign']->value;
echo $_smarty_tpl->tpl_vars['gui']->value->testprojectID;?>
" class="list-group-item"
          style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_assign_user_roles'];?>
</a>
      <?php }?>

      <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['cfield_management'] == "yes") {?>
        <a href="<?php echo $_smarty_tpl->tpl_vars['cfAssignment']->value;?>
" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_cfields_tproject_assign'];?>
</a>
      <?php }?>

      <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['keywords_view'] == "yes") {?>
        <a href="<?php echo $_smarty_tpl->tpl_vars['keywordsAssignment']->value;
echo $_smarty_tpl->tpl_vars['gui']->value->testprojectID;?>
" class="list-group-item"
          style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_keywords_manage'];?>
</a>
      <?php }?>

      <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['platform_management'] || $_smarty_tpl->tpl_vars['gui']->value->grants['platform_view']) {?>
        <a href="<?php echo $_smarty_tpl->tpl_vars['platformsView']->value;
echo $_smarty_tpl->tpl_vars['gui']->value->testprojectID;?>
" class="list-group-item"
          style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_platform_management'];?>
</a>
      <?php }?>

      <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['project_inventory_view'] || $_smarty_tpl->tpl_vars['gui']->value->grants['project_inventory_management']) {?>
        <a href="<?php echo $_smarty_tpl->tpl_vars['inventoryView']->value;?>
" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_inventory_management'];?>
</a>
      <?php }?>
      <a href="<?php echo $_smarty_tpl->tpl_vars['bugsView']->value;?>
" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
">Execution Bugs</a>
      <a href="lib/execute/custom_bugtrack_integration.html" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
">
        <span style="color: rgb(40, 167, 69);font-weight: bold;">New! </span>
        <span style="color: rgb(23, 162, 184);font-weight: bold;">🔗 Bug Tracker Integration</span>
      </a>

      <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['project_edit'] == "yes") {?>

                <a href="<?php echo $_smarty_tpl->tpl_vars['excelImportView']->value;?>
" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_excel_import'];?>
</a>

      <?php }?>


    </div>
  <?php }?>

  <?php if ($_smarty_tpl->tpl_vars['display_left_block_3']->value) {?>
    <div class="list-group" style="<?php echo $_smarty_tpl->tpl_vars['divStyle']->value;?>
">
      <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['reqs_view'] == "yes" || $_smarty_tpl->tpl_vars['gui']->value->grants['reqs_edit'] == "yes") {?>
        <a href="<?php echo $_smarty_tpl->tpl_vars['gui']->value->launcher;?>
?feature=reqSpecMgmt" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_req_spec'];?>
</a>
        <a href="<?php echo $_smarty_tpl->tpl_vars['reqOverView']->value;?>
" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_req_overview'];?>
</a>
        <a href="<?php echo $_smarty_tpl->tpl_vars['gui']->value->launcher;?>
?feature=printReqSpec" class="list-group-item"
          style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_print_req'];?>
</a>
        <a href="<?php echo $_smarty_tpl->tpl_vars['gui']->value->launcher;?>
?feature=searchReq" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_search_req'];?>
</a>
        <a href="<?php echo $_smarty_tpl->tpl_vars['gui']->value->launcher;?>
?feature=searchReqSpec" class="list-group-item"
          style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_search_req_spec'];?>
</a>
      <?php }?>
      <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['req_tcase_link_management'] == "yes") {?>
        <a href="<?php echo $_smarty_tpl->tpl_vars['assignReq']->value;?>
" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_req_assign'];?>
</a>
      <?php }?>
      <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['monitor_req'] == "yes") {?>
        <a href="<?php echo $_smarty_tpl->tpl_vars['reqMonOverView']->value;
echo $_smarty_tpl->tpl_vars['gui']->value->testprojectID;?>
" class="list-group-item"
          style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_req_monitor_overview'];?>
</a>
      <?php }?>
    </div>
  <?php }?>

  <?php if ($_smarty_tpl->tpl_vars['display_left_block_4']->value) {?>
    <div class="list-group" style="<?php echo $_smarty_tpl->tpl_vars['divStyle']->value;?>
">
      <a href="<?php echo $_smarty_tpl->tpl_vars['gui']->value->launcher;?>
?feature=editTc" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
">
        <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['modify_tc'] == "yes") {?>
          <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('s'=>'href_edit_tc'),$_smarty_tpl ) );?>

        <?php } else { ?>
          <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('s'=>'href_browse_tc'),$_smarty_tpl ) );?>

        <?php }?>
      </a>
      <?php if ($_smarty_tpl->tpl_vars['gui']->value->hasTestCases) {?>
        <a href="<?php echo $_smarty_tpl->tpl_vars['tcSearch']->value;
echo $_smarty_tpl->tpl_vars['gui']->value->testprojectID;?>
" class="list-group-item" style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_search_tc'];?>
</a>
      <?php }?>

      <?php if ($_smarty_tpl->tpl_vars['gui']->value->hasKeywords) {?>
        <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['keyword_assignment'] == "yes") {?>
          <a href="<?php echo $_smarty_tpl->tpl_vars['gui']->value->launcher;?>
?feature=keywordsAssign" class="list-group-item"
            style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['href_keywords_assign'];?>
</a>
        <?php }?>
      <?php }?>

      <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants['modify_tc'] == "yes") {?>
        <a href="<?php echo $_smarty_tpl->tpl_vars['tcCreatedUser']->value;
echo $_smarty_tpl->tpl_vars['gui']->value->testprojectID;?>
" class="list-group-item"
          style="<?php echo $_smarty_tpl->tpl_vars['aStyle']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['link_report_test_cases_created_per_user'];?>
</a>
      <?php }?>

    </div>
  <?php }?>

  <?php if ($_smarty_tpl->tpl_vars['display_left_block_bottom']->value) {?>
    <?php if (isset($_smarty_tpl->tpl_vars['gui']->value->plugins['EVENT_LEFTMENU_BOTTOM'])) {?>
      <br />
      <div class="list-group" style="<?php echo $_smarty_tpl->tpl_vars['divStyle']->value;?>
" id="plugin_left_bottom">
        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['gui']->value->plugins['EVENT_LEFTMENU_BOTTOM'], 'menu_item');
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
      </div>
    <?php }?>
  <?php }?>

</div><?php }
}
