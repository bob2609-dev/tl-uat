<?php
/* Smarty version 3.1.33, created on 2026-03-05 21:20:42
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\execute\inc_exec_show_tc_exec.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69a9e59a590916_90965971',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'a1bf8cadf0e983b3cffec939537876aba0fc11dd' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\execute\\inc_exec_show_tc_exec.tpl',
      1 => 1772741493,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:inc_show_hide_mgmt.tpl' => 1,
    'file:inc_attachments.tpl' => 1,
    'file:attachments.inc.tpl' => 1,
    'file:inc_show_bug_table.tpl' => 1,
    'file:execute/inc_exec_test_spec.tpl' => 1,
  ),
),false)) {
function content_69a9e59a590916_90965971 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\tl-uat\\vendor\\smarty\\smarty\\libs\\plugins\\modifier.replace.php','function'=>'smarty_modifier_replace',),1=>array('file'=>'C:\\xampp\\htdocs\\tl-uat\\vendor\\smarty\\smarty\\libs\\plugins\\function.cycle.php','function'=>'smarty_function_cycle',),));
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['gui']->value->map_last_exec, 'tc_exec');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['tc_exec']->value) {
?>

    <?php $_smarty_tpl->_assignInScope('printExecutionAction', "lib/execute/execPrint.php");?>

  <?php $_smarty_tpl->_assignInScope('version_number', $_smarty_tpl->tpl_vars['tc_exec']->value['version']);?>
  <?php $_smarty_tpl->_assignInScope('tc_id', $_smarty_tpl->tpl_vars['tc_exec']->value['testcase_id']);?>
  <?php $_smarty_tpl->_assignInScope('tcversion_id', $_smarty_tpl->tpl_vars['tc_exec']->value['id']);?>
  <?php $_smarty_tpl->_assignInScope('div_id', "tsdetails_".((string)$_smarty_tpl->tpl_vars['tc_id']->value));?>
  <?php $_smarty_tpl->_assignInScope('memstatus_id', "tsdetails_view_status_".((string)$_smarty_tpl->tpl_vars['tc_id']->value));?>
  <?php $_smarty_tpl->_assignInScope('can_delete_exec', 0);?>
  <?php $_smarty_tpl->_assignInScope('can_edit_exec_notes', $_smarty_tpl->tpl_vars['gui']->value->grants->edit_exec_notes);?>
  <?php $_smarty_tpl->_assignInScope('can_manage_attachments', $_smarty_tpl->tpl_vars['gsmarty_attachments']->value->enabled);?>
  <?php if ($_smarty_tpl->tpl_vars['tc_exec']->value['can_be_executed']) {?>
    <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants->delete_execution) {?>
      <?php $_smarty_tpl->_assignInScope('can_delete_exec', 1);?>
    <?php }?>
  <?php } else { ?>
    <?php $_smarty_tpl->_assignInScope('can_edit_exec_notes', 0);?>
    <?php $_smarty_tpl->_assignInScope('can_manage_attachments', 0);?>
  <?php }?>


  <input type='hidden' name='tc_version[<?php echo $_smarty_tpl->tpl_vars['tcversion_id']->value;?>
]' value='<?php echo $_smarty_tpl->tpl_vars['tc_id']->value;?>
' />
  <input type='hidden' name='version_number[<?php echo $_smarty_tpl->tpl_vars['tcversion_id']->value;?>
]' value='<?php echo $_smarty_tpl->tpl_vars['version_number']->value;?>
' />
  
    <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('s'=>'th_testsuite','var'=>'container_title'),$_smarty_tpl ) );?>

  <?php $_smarty_tpl->_assignInScope('ts_name', $_smarty_tpl->tpl_vars['tsuite_info']->value[$_smarty_tpl->tpl_vars['tc_id']->value]['tsuite_name']);?>
  <?php $_smarty_tpl->_assignInScope('container_title', ((string)$_smarty_tpl->tpl_vars['container_title']->value).((string)$_smarty_tpl->tpl_vars['title_sep']->value).((string)$_smarty_tpl->tpl_vars['ts_name']->value));?>
  <?php $_smarty_tpl->_subTemplateRender("file:inc_show_hide_mgmt.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('show_hide_container_title'=>$_smarty_tpl->tpl_vars['container_title']->value,'show_hide_container_id'=>$_smarty_tpl->tpl_vars['div_id']->value,'show_hide_container_draw'=>false,'show_hide_container_class'=>'exec_additional_info','show_hide_container_view_status_id'=>$_smarty_tpl->tpl_vars['memstatus_id']->value), 0, true);
?>

  <div id="<?php echo $_smarty_tpl->tpl_vars['div_id']->value;?>
" name="<?php echo $_smarty_tpl->tpl_vars['div_id']->value;?>
" class="exec_additional_info">
    <br />
    <div class="exec_testsuite_details" style="width:95%;">
      <span class="legend_container"><?php echo $_smarty_tpl->tpl_vars['labels']->value['details'];?>
</span><br />
      <?php if ($_smarty_tpl->tpl_vars['gui']->value->testDesignEditorType == 'none') {
echo nl2br($_smarty_tpl->tpl_vars['tsuite_info']->value[$_smarty_tpl->tpl_vars['tc_id']->value]['details']);
} else {
echo $_smarty_tpl->tpl_vars['tsuite_info']->value[$_smarty_tpl->tpl_vars['tc_id']->value]['details'];
}?>
    </div>

    <?php if ($_smarty_tpl->tpl_vars['ts_cf_smarty']->value[$_smarty_tpl->tpl_vars['tc_id']->value] != '') {?>
      <br />
      <div class="custom_field_container" style="border-color:black;width:95%;">
        <?php echo $_smarty_tpl->tpl_vars['ts_cf_smarty']->value[$_smarty_tpl->tpl_vars['tc_id']->value];?>

      </div>
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['gui']->value->tSuiteAttachments != null && $_smarty_tpl->tpl_vars['gui']->value->tSuiteAttachments[$_smarty_tpl->tpl_vars['tc_exec']->value['tsuite_id']] != null) {?>
      <br />
      <?php $_smarty_tpl->_subTemplateRender("file:inc_attachments.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('attach_tableName'=>"nodes_hierarchy",'attach_downloadOnly'=>true,'attach_attachmentInfos'=>$_smarty_tpl->tpl_vars['gui']->value->tSuiteAttachments[$_smarty_tpl->tpl_vars['tc_exec']->value['tsuite_id']],'attach_inheritStyle'=>1,'attach_tableClassName'=>"none",'attach_tableStyles'=>"background-color:#ffffcc;width:100%"), 0, true);
?>
    <?php }?>
    <br />
  </div>
    <br />
  <?php $_smarty_tpl->_assignInScope('drawNotRun', 0);?>
  <?php if ($_smarty_tpl->tpl_vars['cfg']->value->exec_cfg->show_last_exec_any_build) {?>
    <?php $_smarty_tpl->_assignInScope('abs_last_exec', $_smarty_tpl->tpl_vars['gui']->value->map_last_exec_any_build[$_smarty_tpl->tpl_vars['tcversion_id']->value]);?>
    <?php $_smarty_tpl->_assignInScope('my_build_name', htmlspecialchars($_smarty_tpl->tpl_vars['abs_last_exec']->value['build_name'], ENT_QUOTES, 'UTF-8', true));?>
    <?php $_smarty_tpl->_assignInScope('show_current_build', 1);?>

        <?php if ($_smarty_tpl->tpl_vars['my_build_name']->value == '') {?>
      <?php $_smarty_tpl->_assignInScope('my_build_name', htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->build_name, ENT_QUOTES, 'UTF-8', true));?>
      <?php $_smarty_tpl->_assignInScope('drawNotRun', 1);?>
    <?php }?>
  <?php }?>
  <?php if ($_smarty_tpl->tpl_vars['gui']->value->history_on) {?>
    <?php $_smarty_tpl->_assignInScope('my_build_name', htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->build_name, ENT_QUOTES, 'UTF-8', true));?>
  <?php }?>
  <?php $_smarty_tpl->_assignInScope('exec_build_title', ((string)$_smarty_tpl->tpl_vars['build_title']->value)." ".((string)$_smarty_tpl->tpl_vars['title_sep']->value)." ".((string)$_smarty_tpl->tpl_vars['my_build_name']->value));?>


  <div id="execution_history" class="exec_history">
    <div class="exec_history_title">
      <?php if ($_smarty_tpl->tpl_vars['gui']->value->issueTrackerIntegrationOn) {?>
        <a style="font-weight:normal" target="_blank" href="<?php echo $_smarty_tpl->tpl_vars['gui']->value->createIssueURL;?>
">
          <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['bug'];?>
" title="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->accessToIssueTracker, ENT_QUOTES, 'UTF-8', true);?>
">
        </a>
      <?php }?>

      <?php if ($_smarty_tpl->tpl_vars['gui']->value->history_on) {?>
        <?php echo $_smarty_tpl->tpl_vars['labels']->value['execution_history'];?>
 <?php echo $_smarty_tpl->tpl_vars['title_sep_type3']->value;?>

        <?php if (!$_smarty_tpl->tpl_vars['cfg']->value->exec_cfg->show_history_all_builds) {?>
          <?php echo $_smarty_tpl->tpl_vars['exec_build_title']->value;?>

        <?php }?>
      <?php } else { ?>
        <?php echo $_smarty_tpl->tpl_vars['labels']->value['last_execution'];?>

        <?php if ($_smarty_tpl->tpl_vars['show_current_build']->value) {?> <?php echo $_smarty_tpl->tpl_vars['labels']->value['exec_any_build'];?>
 <?php }?>
      <?php }?>
    </div>


        <?php if ($_smarty_tpl->tpl_vars['cfg']->value->exec_cfg->show_last_exec_any_build && $_smarty_tpl->tpl_vars['gui']->value->history_on == 0) {?>
      <?php if ($_smarty_tpl->tpl_vars['abs_last_exec']->value['status'] != '' && $_smarty_tpl->tpl_vars['abs_last_exec']->value['status'] != $_smarty_tpl->tpl_vars['tlCfg']->value->results['status_code']['not_run']) {?>
        <?php $_smarty_tpl->_assignInScope('status_code', $_smarty_tpl->tpl_vars['abs_last_exec']->value['status']);?>
        <div class="<?php echo $_smarty_tpl->tpl_vars['tlCfg']->value->results['code_status'][$_smarty_tpl->tpl_vars['status_code']->value];?>
">
          <?php echo $_smarty_tpl->tpl_vars['labels']->value['date_time_run'];?>
 <?php echo $_smarty_tpl->tpl_vars['title_sep']->value;?>
 <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['localize_timestamp'][0], array( array('ts'=>$_smarty_tpl->tpl_vars['abs_last_exec']->value['execution_ts']),$_smarty_tpl ) );?>

          <?php echo $_smarty_tpl->tpl_vars['title_sep_type3']->value;?>

          <?php echo $_smarty_tpl->tpl_vars['labels']->value['test_exec_by'];?>
 <?php echo $_smarty_tpl->tpl_vars['title_sep']->value;?>


          <?php if (isset($_smarty_tpl->tpl_vars['users']->value[$_smarty_tpl->tpl_vars['abs_last_exec']->value['tester_id']])) {?>
            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['users']->value[$_smarty_tpl->tpl_vars['abs_last_exec']->value['tester_id']]->getDisplayName(), ENT_QUOTES, 'UTF-8', true);?>

          <?php } else { ?>
            <?php $_smarty_tpl->_assignInScope('deletedTester', $_smarty_tpl->tpl_vars['abs_last_exec']->value['tester_id']);?>
            <?php $_smarty_tpl->_assignInScope('deletedUserString', smarty_modifier_replace($_smarty_tpl->tpl_vars['labels']->value['deleted_user'],"%s",$_smarty_tpl->tpl_vars['deletedTester']->value));?>
            <?php echo $_smarty_tpl->tpl_vars['deletedUserString']->value;?>

          <?php }?>

          <?php echo $_smarty_tpl->tpl_vars['title_sep_type3']->value;?>

          <?php echo $_smarty_tpl->tpl_vars['labels']->value['build'];
echo $_smarty_tpl->tpl_vars['title_sep']->value;?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['abs_last_exec']->value['build_name'], ENT_QUOTES, 'UTF-8', true);?>

          <?php echo $_smarty_tpl->tpl_vars['title_sep_type3']->value;?>

          <?php echo $_smarty_tpl->tpl_vars['labels']->value['exec_status'];?>
 <?php echo $_smarty_tpl->tpl_vars['title_sep']->value;?>
 <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['localize_tc_status'][0], array( array('s'=>$_smarty_tpl->tpl_vars['status_code']->value),$_smarty_tpl ) );?>


          <?php if ($_smarty_tpl->tpl_vars['gui']->value->issueTrackerIntegrationOn) {?>
            <span style="background: white;padding: 6px 15px 6px 40px;">
              <a href="javascript:open_bug_add_window(<?php echo $_smarty_tpl->tpl_vars['gui']->value->tproject_id;?>
,
                <?php echo $_smarty_tpl->tpl_vars['gui']->value->tplan_id;?>
,<?php echo $_smarty_tpl->tpl_vars['abs_last_exec']->value['id'];?>
,<?php echo $_smarty_tpl->tpl_vars['abs_last_exec']->value['execution_id'];?>
,0,'link')">
                <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['bug_link_tl_to_bts'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['bug_link_tl_to_bts'];?>
" style="border:none" /></a>
            </span>
          <?php }?>

        </div>

                <?php if ($_smarty_tpl->tpl_vars['abs_last_exec']->value['execution_notes'] != '') {?>
          <?php echo '<script'; ?>
>
                var panel_init = function() {
    var p = new Ext.Panel({
      title:'<?php echo $_smarty_tpl->tpl_vars['labels']->value['exec_notes'];?>
',
      collapsible: true,
      collapsed: true,
      baseCls: 'x-tl-panel',
      renderTo:'latest_exec_any_build_notes',
      width: '100%',
      html: ''
    });
    p.on({'expand' : 
    function()
    {load_notes(this,<?php echo $_smarty_tpl->tpl_vars['abs_last_exec']->value['execution_id'];?>
);}
    });
    };
    panel_init_functions.push(panel_init);
  <?php echo '</script'; ?>
>
  <div id="latest_exec_any_build_notes" style="margin:8px;">
  </div>
  <hr>

  


          <?php } else { ?>
            <?php $_smarty_tpl->_assignInScope('drawNotRun', 1);?>
          <?php }?>
        <?php }?>
      <?php }?>

      <?php if ($_smarty_tpl->tpl_vars['drawNotRun']->value) {?>
        <div class="not_run"><?php echo $_smarty_tpl->tpl_vars['labels']->value['test_status_not_run'];?>
</div>
        <?php echo $_smarty_tpl->tpl_vars['labels']->value['tc_not_tested_yet'];?>

      <?php }?>



            <?php if ($_smarty_tpl->tpl_vars['gui']->value->other_execs[$_smarty_tpl->tpl_vars['tcversion_id']->value]) {?>
        <?php $_smarty_tpl->_assignInScope('my_colspan', $_smarty_tpl->tpl_vars['attachment_model']->value->num_cols);?>

                <?php if ($_smarty_tpl->tpl_vars['tlCfg']->value->exec_cfg->steps_exec) {?>
          <?php $_smarty_tpl->_assignInScope('my_colspan', $_smarty_tpl->tpl_vars['my_colspan']->value+1);?>
        <?php }?>

        <?php if ($_smarty_tpl->tpl_vars['gui']->value->history_on == 0 && $_smarty_tpl->tpl_vars['show_current_build']->value) {?>
          <div class="exec_history_title">
            <?php echo $_smarty_tpl->tpl_vars['labels']->value['last_execution'];?>
 <?php echo $_smarty_tpl->tpl_vars['labels']->value['exec_current_build'];?>

            <?php echo $_smarty_tpl->tpl_vars['title_sep_type3']->value;?>
 <?php echo $_smarty_tpl->tpl_vars['build_title']->value;?>
 <?php echo $_smarty_tpl->tpl_vars['title_sep']->value;?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->build_name, ENT_QUOTES, 'UTF-8', true);?>

          </div>
        <?php }?>

        <table cellspacing="0" class="exec_history">
          <tr>
            <th style="text-align:left"><?php echo $_smarty_tpl->tpl_vars['labels']->value['date_time_run'];?>
</th>

            <?php if ($_smarty_tpl->tpl_vars['gui']->value->history_on == 0 || $_smarty_tpl->tpl_vars['cfg']->value->exec_cfg->show_history_all_builds) {?>
              <th style="text-align:left"><?php echo $_smarty_tpl->tpl_vars['labels']->value['build'];?>
</th>
            <?php }?>

            <?php if ($_smarty_tpl->tpl_vars['gui']->value->has_platforms && ($_smarty_tpl->tpl_vars['gui']->value->history_on == 0 || $_smarty_tpl->tpl_vars['cfg']->value->exec_cfg->show_history_all_platforms)) {?>
            <?php $_smarty_tpl->_assignInScope('my_colspan', $_smarty_tpl->tpl_vars['my_colspan']->value+1);?>
            <th style="text-align:left"><?php echo $_smarty_tpl->tpl_vars['labels']->value['platform'];?>
</th>
          <?php }?>
          <th style="text-align:left"><?php echo $_smarty_tpl->tpl_vars['labels']->value['test_exec_by'];?>
</th>
          <th style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['labels']->value['exec_status'];?>
</th>
          <th style="text-align:right" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['execution_duration'];?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['execution_duration_short'];?>
</th>
          <th style="text-align:center" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['testcaseversion'];?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['version'];?>
</th>

                    <?php if ($_smarty_tpl->tpl_vars['attachment_model']->value->show_upload_column && $_smarty_tpl->tpl_vars['can_manage_attachments']->value) {?>
            <th style="text-align:center">&nbsp;</th>
          <?php } else { ?>
            <?php $_smarty_tpl->_assignInScope('my_colspan', $_smarty_tpl->tpl_vars['my_colspan']->value-1);?>
          <?php }?>

          <?php if ($_smarty_tpl->tpl_vars['gui']->value->issueTrackerIntegrationOn) {?>
            <th style="text-align:left"><?php echo $_smarty_tpl->tpl_vars['labels']->value['bug_mgmt'];?>
</th>
            <?php $_smarty_tpl->_assignInScope('my_colspan', $_smarty_tpl->tpl_vars['my_colspan']->value+1);?>
          <?php }?>

          <?php if ($_smarty_tpl->tpl_vars['can_delete_exec']->value) {?>
            <th style="text-align:left">&nbsp;</th>
            <?php $_smarty_tpl->_assignInScope('my_colspan', $_smarty_tpl->tpl_vars['my_colspan']->value+1);?>
          <?php }?>

          <th style="text-align:left"><?php echo $_smarty_tpl->tpl_vars['labels']->value['run_mode'];?>
</th>

          <th style="text-align:left">&nbsp;</th>

          <?php $_smarty_tpl->_assignInScope('my_colspan', $_smarty_tpl->tpl_vars['my_colspan']->value+2);?>
        </tr>

                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['gui']->value->other_execs[$_smarty_tpl->tpl_vars['tcversion_id']->value], 'tc_old_exec');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['tc_old_exec']->value) {
?>
          <?php $_smarty_tpl->_assignInScope('tc_status_code', $_smarty_tpl->tpl_vars['tc_old_exec']->value['status']);?>
          <?php echo smarty_function_cycle(array('values'=>'#eeeeee,#d0d0d0','assign'=>"bg_color"),$_smarty_tpl);?>

          <tr style="border-top:1px solid black; background-color: <?php echo $_smarty_tpl->tpl_vars['bg_color']->value;?>
">
            <td>
                            <?php if ($_smarty_tpl->tpl_vars['can_edit_exec_notes']->value && $_smarty_tpl->tpl_vars['tc_old_exec']->value['build_is_open']) {?>
                <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['note_edit'];?>
" style="vertical-align:middle" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['edit_execution'];?>
" onclick="javascript: openExecEditWindow(
  		           <?php echo $_smarty_tpl->tpl_vars['tc_old_exec']->value['execution_id'];?>
,<?php echo $_smarty_tpl->tpl_vars['tc_old_exec']->value['id'];?>
,<?php echo $_smarty_tpl->tpl_vars['gui']->value->tplan_id;?>
,<?php echo $_smarty_tpl->tpl_vars['gui']->value->tproject_id;?>
);">
              <?php } else { ?>
                <?php if ($_smarty_tpl->tpl_vars['can_edit_exec_notes']->value) {?>
                  <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['note_edit_greyed'];?>
" style="vertical-align:middle" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['closed_build'];?>
">
                <?php }?>
              <?php }?>
              <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['localize_timestamp'][0], array( array('ts'=>$_smarty_tpl->tpl_vars['tc_old_exec']->value['execution_ts']),$_smarty_tpl ) );?>

            </td>
            <?php if ($_smarty_tpl->tpl_vars['gui']->value->history_on == 0 || $_smarty_tpl->tpl_vars['cfg']->value->exec_cfg->show_history_all_builds) {?>
              <td><?php if (!$_smarty_tpl->tpl_vars['tc_old_exec']->value['build_is_open']) {?>
                <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['lock'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['closed_build'];?>
"><?php }?>
                <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['tc_old_exec']->value['build_name'], ENT_QUOTES, 'UTF-8', true);?>

              </td>
            <?php }?>

            <?php if ($_smarty_tpl->tpl_vars['gui']->value->has_platforms && ($_smarty_tpl->tpl_vars['gui']->value->history_on == 0 || $_smarty_tpl->tpl_vars['cfg']->value->exec_cfg->show_history_all_platforms)) {?>
            <td>
              <?php echo $_smarty_tpl->tpl_vars['tc_old_exec']->value['platform_name'];?>

            </td>
          <?php }?>

          <td>
            <?php if (isset($_smarty_tpl->tpl_vars['users']->value[$_smarty_tpl->tpl_vars['tc_old_exec']->value['tester_id']])) {?>
              <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['users']->value[$_smarty_tpl->tpl_vars['tc_old_exec']->value['tester_id']]->getDisplayName(), ENT_QUOTES, 'UTF-8', true);?>

            <?php } else { ?>
              <?php $_smarty_tpl->_assignInScope('deletedTester', $_smarty_tpl->tpl_vars['tc_old_exec']->value['tester_id']);?>
              <?php $_smarty_tpl->_assignInScope('deletedUserString', smarty_modifier_replace($_smarty_tpl->tpl_vars['labels']->value['deleted_user'],"%s",$_smarty_tpl->tpl_vars['deletedTester']->value));?>
              <?php echo $_smarty_tpl->tpl_vars['deletedUserString']->value;?>

            <?php }?>
          </td>
          <td class="<?php echo $_smarty_tpl->tpl_vars['tlCfg']->value->results['code_status'][$_smarty_tpl->tpl_vars['tc_status_code']->value];?>
" style="text-align:center"
            title="(ID:<?php echo $_smarty_tpl->tpl_vars['tc_old_exec']->value['execution_id'];?>
)">
            <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['localize_tc_status'][0], array( array('s'=>$_smarty_tpl->tpl_vars['tc_old_exec']->value['status']),$_smarty_tpl ) );?>

          </td>

          
          <td style="text-align:right"><?php echo $_smarty_tpl->tpl_vars['tc_old_exec']->value['execution_duration'];?>
</td>

          <td style="text-align:center"><?php echo $_smarty_tpl->tpl_vars['tc_old_exec']->value['tcversion_number'];?>
</td>

                    <?php if (($_smarty_tpl->tpl_vars['attachment_model']->value->show_upload_column && !$_smarty_tpl->tpl_vars['att_download_only']->value && $_smarty_tpl->tpl_vars['tc_old_exec']->value['build_is_open'] && $_smarty_tpl->tpl_vars['can_manage_attachments']->value) || ($_smarty_tpl->tpl_vars['attachment_model']->value->show_upload_column && $_smarty_tpl->tpl_vars['gui']->value->history_on == 1 && $_smarty_tpl->tpl_vars['tc_old_exec']->value['build_is_open'] && $_smarty_tpl->tpl_vars['can_manage_attachments']->value)) {?>
          <td align="center"><a href="javascript:openFileUploadWindow(<?php echo $_smarty_tpl->tpl_vars['tc_old_exec']->value['execution_id'];?>
,'executions')">
              <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['upload'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['alt_attachment_mgmt'];?>
" alt="<?php echo $_smarty_tpl->tpl_vars['labels']->value['alt_attachment_mgmt'];?>
"
                style="border:none" /></a>
          </td>
        <?php } else { ?>
          <?php if ($_smarty_tpl->tpl_vars['attachment_model']->value->show_upload_column && $_smarty_tpl->tpl_vars['can_manage_attachments']->value) {?>
            <td align="center">
              <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['upload_greyed'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['closed_build'];?>
">
            </td>
          <?php }?>
        <?php }?>

        <?php if ($_smarty_tpl->tpl_vars['gui']->value->issueTrackerIntegrationOn) {?>
          <td align="center">
            <?php if ($_smarty_tpl->tpl_vars['tc_old_exec']->value['build_is_open']) {?>
              <a href="javascript:open_bug_add_window(<?php echo $_smarty_tpl->tpl_vars['gui']->value->tproject_id;?>
,
              <?php echo $_smarty_tpl->tpl_vars['gui']->value->tplan_id;?>
,<?php echo $_smarty_tpl->tpl_vars['tc_old_exec']->value['id'];?>
,<?php echo $_smarty_tpl->tpl_vars['tc_old_exec']->value['execution_id'];?>
,0,'link')">
                <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['bug_link_tl_to_bts'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['bug_link_tl_to_bts'];?>
" style="border:none" /></a>
              &nbsp;&nbsp;
              <?php if ($_smarty_tpl->tpl_vars['gui']->value->tlCanCreateIssue) {?>
                <a
                  href="javascript:open_bug_add_window(<?php echo $_smarty_tpl->tpl_vars['gui']->value->tproject_id;?>
,<?php echo $_smarty_tpl->tpl_vars['gui']->value->tplan_id;?>
,<?php echo $_smarty_tpl->tpl_vars['tc_old_exec']->value['id'];?>
,<?php echo $_smarty_tpl->tpl_vars['tc_old_exec']->value['execution_id'];?>
,0,'create')">
                  <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['bug_create_into_bts'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['bug_create_into_bts'];?>
" style="border:none" /></a>
              <?php }?>
            <?php } else { ?>
              <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['bug_link_tl_to_bts_disabled'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['bug_link_tl_to_bts'];?>
"
                style="border:none" /></a>
              &nbsp;&nbsp;
              <?php if ($_smarty_tpl->tpl_vars['gui']->value->tlCanCreateIssue) {?>
                <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['bug_create_into_bts_disabled'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['bug_create_into_bts'];?>
"
                  style="border:none" /></a>
              <?php }?>
            <?php }?>
          </td>
        <?php }?>

                <?php if ($_smarty_tpl->tpl_vars['can_delete_exec']->value && $_smarty_tpl->tpl_vars['tc_old_exec']->value['build_is_open']) {?>
          <td align="center">
            <a href="javascript:confirm_and_submit(msg,'execSetResults','exec_to_delete',
             	                                       <?php echo $_smarty_tpl->tpl_vars['tc_old_exec']->value['execution_id'];?>
,'do_delete',1);">
              <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['delete'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['img_title_delete_execution'];?>
" style="border:none" /></a>
          </td>
        <?php } else { ?>
          <?php if ($_smarty_tpl->tpl_vars['can_delete_exec']->value) {?>
            <td align="center">
              <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['delete_disabled'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['closed_build'];?>
">
            </td>
          <?php }?>
        <?php }?>

        <td class="icon_cell" align="center">
          <?php if ($_smarty_tpl->tpl_vars['tc_old_exec']->value['execution_run_type'] == @constant('TESTCASE_EXECUTION_TYPE_MANUAL')) {?>
            <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['testcase_execution_type_manual'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['execution_type_manual'];?>
"
              style="border:none" />
          <?php } else { ?>
            <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['testcase_execution_type_automatic'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['execution_type_auto'];?>
"
              style="border:none" />
          <?php }?>
        </td>

                <?php if ($_smarty_tpl->tpl_vars['tlCfg']->value->exec_cfg->steps_exec) {?>
          <td class="icon_cell" align="center">
            <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['steps'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['access_test_steps_exec'];?>
" onclick="javascript:openPrintPreview('exec',<?php echo $_smarty_tpl->tpl_vars['tc_old_exec']->value['execution_id'];?>
,
                                                        null,null,'<?php echo $_smarty_tpl->tpl_vars['printExecutionAction']->value;?>
');" />
          </td>
        <?php }?>


      </tr>
      <?php if ($_smarty_tpl->tpl_vars['tc_old_exec']->value['execution_notes'] != '') {?>
        <?php echo '<script'; ?>
>
                      var panel_init = function() {
            var p = new Ext.Panel({
              title:'<?php echo $_smarty_tpl->tpl_vars['labels']->value['exec_notes'];?>
',
              collapsible: true,
              collapsed: true,
              baseCls: 'x-tl-panel',
              renderTo:'exec_notes_container_<?php echo $_smarty_tpl->tpl_vars['tc_old_exec']->value['execution_id'];?>
',
                width: '100%',
                html: ''
              });
              p.on({'expand' : 
              function()
              
              {load_notes(this,<?php echo $_smarty_tpl->tpl_vars['tc_old_exec']->value['execution_id'];?>
);}
              });
              };
              panel_init_functions.push(panel_init);
            <?php echo '</script'; ?>
>
            <tr style="background-color: <?php echo $_smarty_tpl->tpl_vars['bg_color']->value;?>
">
              <td colspan="<?php echo $_smarty_tpl->tpl_vars['my_colspan']->value;?>
" id="exec_notes_container_<?php echo $_smarty_tpl->tpl_vars['tc_old_exec']->value['execution_id'];?>
"
                style="padding:5px 5px 5px 5px;">
              </td>
            </tr>


            <?php }?>

                    <tr style="background-color: <?php echo $_smarty_tpl->tpl_vars['bg_color']->value;?>
">
            <td colspan="<?php echo $_smarty_tpl->tpl_vars['my_colspan']->value;?>
">
              <?php $_smarty_tpl->_assignInScope('execID', $_smarty_tpl->tpl_vars['tc_old_exec']->value['execution_id']);?>
              <?php $_smarty_tpl->_assignInScope('cf_value_info', $_smarty_tpl->tpl_vars['gui']->value->other_exec_cfields[$_smarty_tpl->tpl_vars['execID']->value]);?>
              <?php echo $_smarty_tpl->tpl_vars['cf_value_info']->value;?>

            </td>
          </tr>



                    <?php if (isset($_smarty_tpl->tpl_vars['gui']->value->attachments[$_smarty_tpl->tpl_vars['execID']->value])) {?>
            <tr style="background-color: <?php echo $_smarty_tpl->tpl_vars['bg_color']->value;?>
">
              <td colspan="<?php echo $_smarty_tpl->tpl_vars['my_colspan']->value;?>
">
                <?php $_smarty_tpl->_assignInScope('execID', $_smarty_tpl->tpl_vars['tc_old_exec']->value['execution_id']);?>

                <?php $_smarty_tpl->_assignInScope('attach_info', $_smarty_tpl->tpl_vars['gui']->value->attachments[$_smarty_tpl->tpl_vars['execID']->value]);?>
                <?php $_smarty_tpl->_subTemplateRender("file:attachments.inc.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('attach_attachmentInfos'=>$_smarty_tpl->tpl_vars['attach_info']->value,'attach_id'=>$_smarty_tpl->tpl_vars['execID']->value,'attach_tableName'=>"executions",'attach_show_upload_btn'=>$_smarty_tpl->tpl_vars['attachment_model']->value->show_upload_btn,'attach_show_title'=>$_smarty_tpl->tpl_vars['attachment_model']->value->show_title,'attach_downloadOnly'=>$_smarty_tpl->tpl_vars['att_download_only']->value,'attach_tableClassName'=>null,'attach_inheritStyle'=>0,'attach_tableStyles'=>null), 0, true);
?>
              </td>
            </tr>
          <?php }?>

                    <?php if (isset($_smarty_tpl->tpl_vars['gui']->value->bugs[$_smarty_tpl->tpl_vars['execID']->value])) {?>
            <tr style="background-color: <?php echo $_smarty_tpl->tpl_vars['bg_color']->value;?>
">
              <td colspan="<?php echo $_smarty_tpl->tpl_vars['my_colspan']->value;?>
">
                <?php $_smarty_tpl->_subTemplateRender("file:inc_show_bug_table.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('bugs_map'=>$_smarty_tpl->tpl_vars['gui']->value->bugs[$_smarty_tpl->tpl_vars['execID']->value],'can_delete'=>$_smarty_tpl->tpl_vars['tc_old_exec']->value['build_is_open'],'exec_id'=>$_smarty_tpl->tpl_vars['execID']->value), 0, true);
?>
              </td>
            </tr>
          <?php }?>
        <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        
      </table>
    <?php }?>
  </div>

  <br />
  <?php $_smarty_tpl->_assignInScope('theClass', "exec_tc_title");?>
  <?php $_smarty_tpl->_assignInScope('hasNewestVersionMsg', '');?>
  <?php if ($_smarty_tpl->tpl_vars['gui']->value->hasNewestVersion) {?>
    <?php $_smarty_tpl->_assignInScope('theClass', "exec_tc_title_alert");?>
    <?php $_smarty_tpl->_assignInScope('hasNewestVersionMsg', $_smarty_tpl->tpl_vars['labels']->value['hasNewestVersionMsg']);?>
  <?php }?>
  <div class="<?php echo $_smarty_tpl->tpl_vars['theClass']->value;?>
">
    <?php if ('' !== $_smarty_tpl->tpl_vars['hasNewestVersionMsg']->value) {?>
      <div style="text-align: center;"><?php echo $_smarty_tpl->tpl_vars['hasNewestVersionMsg']->value;?>
</div>

      <?php if ($_smarty_tpl->tpl_vars['gui']->value->hasNewestVersion) {?>
        <div style="text-align: center;">
          <input type="hidden" id="TCVToUpdate" name="TCVToUpdate" value="<?php echo $_smarty_tpl->tpl_vars['gui']->value->tcversionSet;?>
">
          <input type="submit" id="linkLatestVersion" name="linkLatestVersion"
            value="<?php echo $_smarty_tpl->tpl_vars['labels']->value['updateLinkToLatestTCVersion'];?>
" />
        </div>
        <br>
      <?php }?>
    <?php }?>
    <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants->edit_testcase) {?>
      <?php $_smarty_tpl->_assignInScope('tplan', $_smarty_tpl->tpl_vars['gui']->value->tplan_id);?>
      <?php $_smarty_tpl->_assignInScope('metaMode', "editOnExec&tplan_id=".((string)$_smarty_tpl->tpl_vars['tplan']->value));?>
      <a href="javascript:openTCaseWindow(<?php echo $_smarty_tpl->tpl_vars['tc_exec']->value['testcase_id'];?>
,<?php echo $_smarty_tpl->tpl_vars['tc_exec']->value['id'];?>
,'<?php echo $_smarty_tpl->tpl_vars['metaMode']->value;?>
')">
        <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['note_edit'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['show_tcase_spec'];?>
">
      </a>
    <?php }?>

    <?php echo $_smarty_tpl->tpl_vars['labels']->value['title_test_case'];?>
&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->tcasePrefix, ENT_QUOTES, 'UTF-8', true);
echo $_smarty_tpl->tpl_vars['cfg']->value->testcase_cfg->glue_character;
echo htmlspecialchars($_smarty_tpl->tpl_vars['tc_exec']->value['tc_external_id'], ENT_QUOTES, 'UTF-8', true);?>

    :: <?php echo $_smarty_tpl->tpl_vars['labels']->value['version'];?>
: <?php echo $_smarty_tpl->tpl_vars['tc_exec']->value['version'];?>
 :: <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['tc_exec']->value['name'], ENT_QUOTES, 'UTF-8', true);?>

    <br />
    <?php if ($_smarty_tpl->tpl_vars['tc_exec']->value['assigned_user'] == '') {?>
      <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['warning'];?>
" style="border:none" />&nbsp;<?php echo $_smarty_tpl->tpl_vars['labels']->value['has_no_assignment'];?>

    <?php } else { ?>
      <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['user'];?>
" style="border:none" />&nbsp;
      <?php echo $_smarty_tpl->tpl_vars['labels']->value['assigned_to'];
echo $_smarty_tpl->tpl_vars['title_sep']->value;
echo htmlspecialchars($_smarty_tpl->tpl_vars['tc_exec']->value['assigned_user'], ENT_QUOTES, 'UTF-8', true);?>

    <?php }?>
  </div>


    <div>
    <?php $_smarty_tpl->_subTemplateRender("file:execute/inc_exec_test_spec.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('args_tc_exec'=>$_smarty_tpl->tpl_vars['tc_exec']->value,'args_labels'=>$_smarty_tpl->tpl_vars['labels']->value,'args_enable_custom_field'=>$_smarty_tpl->tpl_vars['enable_custom_fields']->value,'args_execution_time_cf'=>$_smarty_tpl->tpl_vars['gui']->value->execution_time_cfields,'args_design_time_cf'=>$_smarty_tpl->tpl_vars['gui']->value->design_time_cfields,'args_testplan_design_time_cf'=>$_smarty_tpl->tpl_vars['gui']->value->testplan_design_time_cfields,'args_execution_types'=>$_smarty_tpl->tpl_vars['gui']->value->execution_types,'args_tcAttachments'=>$_smarty_tpl->tpl_vars['gui']->value->tcAttachments,'args_req_details'=>$_smarty_tpl->tpl_vars['gui']->value->req_details,'args_relations'=>$_smarty_tpl->tpl_vars['gui']->value->relations,'args_keywords'=>$_smarty_tpl->tpl_vars['gui']->value->kw,'args_cfg'=>$_smarty_tpl->tpl_vars['cfg']->value), 0, true);
?>

    <?php if ($_smarty_tpl->tpl_vars['tc_exec']->value['can_be_executed']) {?>
      <?php $_smarty_tpl->_subTemplateRender("execute/".((string)$_smarty_tpl->tpl_vars['tplConfig']->value['inc_exec_controls']), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('args_save_type'=>'single','args_input_enable_mgmt'=>$_smarty_tpl->tpl_vars['input_enabled_disabled']->value,'args_tcversion_id'=>$_smarty_tpl->tpl_vars['tcversion_id']->value,'args_webeditor'=>$_smarty_tpl->tpl_vars['gui']->value->exec_notes_editors[$_smarty_tpl->tpl_vars['tc_id']->value],'args_labels'=>$_smarty_tpl->tpl_vars['labels']->value), 0, true);
?>
    <?php }?>
    <?php if ($_smarty_tpl->tpl_vars['tc_exec']->value['active'] == 0) {?>
      <h1 class="title">
        <center><?php echo $_smarty_tpl->tpl_vars['labels']->value['testcase_version_is_inactive_on_exec'];?>
</center>
      </h1>
    <?php }?>
    <hr />
  </div>
  
<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
}
}
