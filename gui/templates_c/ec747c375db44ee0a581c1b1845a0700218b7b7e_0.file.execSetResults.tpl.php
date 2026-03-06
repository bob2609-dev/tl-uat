<?php
/* Smarty version 3.1.33, created on 2026-03-05 21:20:42
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\execute\execSetResults.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69a9e59a33f421_66861104',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'ec747c375db44ee0a581c1b1845a0700218b7b7e' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\execute\\execSetResults.tpl',
      1 => 1772737698,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:inc_head.tpl' => 1,
    'file:inc_del_onclick.tpl' => 1,
    'file:inc_show_hide_mgmt.tpl' => 4,
    'file:execute/execSetResultsBulk.inc.tpl' => 1,
    'file:execute/execSetResultsRemoteExec.inc.tpl' => 1,
    'file:execute/inc_exec_show_tc_exec.tpl' => 1,
    'file:inc_refreshTreeWithFilters.tpl' => 1,
  ),
),false)) {
function content_69a9e59a33f421_66861104 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\tl-uat\\vendor\\smarty\\smarty\\libs\\plugins\\modifier.replace.php','function'=>'smarty_modifier_replace',),));
$_smarty_tpl->_assignInScope('attachment_model', $_smarty_tpl->tpl_vars['cfg']->value->exec_cfg->att_model);
$_smarty_tpl->_assignInScope('title_sep', @constant('TITLE_SEP'));
$_smarty_tpl->_assignInScope('title_sep_type3', @constant('TITLE_SEP_TYPE3'));?>

<?php $_smarty_tpl->_assignInScope('input_enabled_disabled', "disabled");
$_smarty_tpl->_assignInScope('att_download_only', true);
$_smarty_tpl->_assignInScope('enable_custom_fields', false);
$_smarty_tpl->_assignInScope('draw_submit_button', false);?>

<?php $_smarty_tpl->_assignInScope('show_current_build', 1);
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('s'=>'build','var'=>'build_title'),$_smarty_tpl ) );?>


<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('var'=>'labels','s'=>'access_test_steps_exec,
add_issue_note,
add_link_to_tlexec,
add_link_to_tlexec_print_view,
alt_attachment_mgmt,
alt_notes,
artifactComponent,
artifactVersion,
assign_exec_task_to_me,
assigned_to,
attachment_mgmt,
btn_export,
btn_export_testcases,
btn_next,
btn_next_tcase,
btn_print,
btn_save_all_tests_results,
btn_save_and_exit,
btn_save_exec_and_movetonext,
btn_save_tc_exec_results,
bug_add_note,
bug_copy_from_latest_exec,
bug_create_into_bts,
bug_description,
bug_link_tl_to_bts,
bug_mgmt,
bug_summary,
build,
build_is_closed,
builds_notes,
bulk_tc_status_management,
click_to_open,
closed_build,
copy_attachments_from_latest_exec,
create_issue_feedback,
created_by,
date_time_run,
delete,
deleted_user,
design,
details,
edit_execution,
edit_notes,
estimated_execution_duration,
exec_any_build,
exec_current_build,
exec_not_run_result_note,
exec_notes,
exec_status,
execute_and_save_results,
execution,
execution_duration,
execution_duration_short,
execution_history,
execution_type,
execution_type_auto,
execution_type_manual,
execution_type_short_descr,
expected_results,
has_no_assignment,
hasNewestVersionMsg,
img_title_bug_mgmt,
img_title_delete_execution,
import_xml_results,
issuePriority,
issueType,
keywords,
last_execution,
no_data_available,
only_test_cases_assigned_to,
or_unassigned_test_cases,
partialExecNoAttachmentsWarning,
partialExecNothingToSave,
platform,
platform_description,
preconditions,
remoteExecFeeback,
reqs,
requirement,
run_mode,
saveStepsForPartialExec,
show_tcase_spec,
step_actions,
step_number,
tc_not_tested_yet,
test_cases_cannot_be_executed,
test_exec_by,
test_exec_expected_r,
test_exec_notes,
test_exec_result,
test_exec_steps,
test_exec_summary,
test_plan_notes,
test_status_not_run,
testcase_customfields,
testcaseversion,
th_test_case_id,
th_testsuite,
title_t_r_on_build,
title_test_case,
updateLinkToLatestTCVersion,
version,
warning,
warning_delete_execution,
warning_nothing_will_be_saved,file_upload_ko,pleaseOpenTSuite'),$_smarty_tpl ) );?>



<?php $_smarty_tpl->_assignInScope('cfg_section', smarty_modifier_replace(call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'basename' ][ 0 ], array( basename($_smarty_tpl->source->filepath) )),".tpl",''));
$_smarty_tpl->smarty->ext->configLoad->_loadConfigFile($_smarty_tpl, "input_dimensions.conf", $_smarty_tpl->tpl_vars['cfg_section']->value, 0);
?>


<?php $_smarty_tpl->_assignInScope('exportAction', "lib/execute/execExport.php?tplan_id=");?>

<?php $_smarty_tpl->_subTemplateRender("file:inc_head.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('popup'=>'yes','openHead'=>'yes','jsValidate'=>"yes",'editorType'=>$_smarty_tpl->tpl_vars['gui']->value->editorType), 0, false);
echo '<script'; ?>
 language="JavaScript" src="gui/javascript/radio_utils.js" type="text/javascript"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 language="JavaScript" src="gui/javascript/expandAndCollapseFunctions.js" type="text/javascript"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 language="JavaScript" src="gui/javascript/execSetResults.js?v=<?php echo time();?>
&cachebust=20260303"
  type="text/javascript"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 language="JavaScript" src="gui/niftycube/niftycube.js?v=<?php echo time();?>
" type="text/javascript"><?php echo '</script'; ?>
>
<link rel="stylesheet" type="text/css" href="gui/niftycube/niftyCorners.css?v=<?php echo time();?>
" />
<?php echo '<script'; ?>
 language="JavaScript" type="text/javascript">
  var msg="<?php echo $_smarty_tpl->tpl_vars['labels']->value['warning_delete_execution'];?>
";
  var import_xml_results="<?php echo $_smarty_tpl->tpl_vars['labels']->value['import_xml_results'];?>
";
  window.currentTProjectId = <?php echo intval($_smarty_tpl->tpl_vars['gui']->value->tproject_id);?>
;

  // Test if Nifty loaded
  console.log('Nifty library status:', typeof Nifty !== 'undefined' ? 'LOADED' : 'NOT LOADED');
  if (typeof Nifty !== 'undefined') {
    console.log('Nifty version:', Nifty.version || 'unknown');
  }
<?php echo '</script'; ?>
>

<?php $_smarty_tpl->_subTemplateRender("file:inc_del_onclick.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<?php echo '<script'; ?>
 language="JavaScript" type="text/javascript">
  function load_notes(panel, exec_id) {
    // solved ONLY for  $webeditorType == 'none'
    var url2load = fRoot + 'lib/execute/getExecNotes.php?readonly=1&exec_id=' + exec_id;
    panel.load({ url: url2load });
  }

  /*
  Set value for a group of combo (have same prefix).
  */
  function set_combo_group(formid, combo_id_prefix, value_to_assign) {
    var f = document.getElementById(formid);
    var all_comboboxes = f.getElementsByTagName('select');
    var input_element;
    var idx = 0;

    for (idx = 0; idx < all_comboboxes.length; idx++) {
      input_element = all_comboboxes[idx];
      if (input_element.type == "select-one" &&
        input_element.id.indexOf(combo_id_prefix) == 0 &&
        !input_element.disabled) {
        input_element.value = value_to_assign;
      }
    }
  }

  // Escape all messages (string)
  var alert_box_title="<?php echo strtr($_smarty_tpl->tpl_vars['labels']->value['warning'], array("\\" => "\\\\", "'" => "\\'", "\"" => "\\\"", "\r" => "\\r", "\n" => "\\n", "</" => "<\/" ));?>
";
  var warning_nothing_will_be_saved="<?php echo strtr($_smarty_tpl->tpl_vars['labels']->value['warning_nothing_will_be_saved'], array("\\" => "\\\\", "'" => "\\'", "\"" => "\\\"", "\r" => "\\r", "\n" => "\\n", "</" => "<\/" ));?>
";

  /**
   *
   */
  function validateForm(f) {
    var status_ok = true;


    if (status_ok) {
      status_ok = checkCustomFields(f);
    }

    if (status_ok && saveStepsPartialExecClicked) {
      var msg="<?php echo $_smarty_tpl->tpl_vars['labels']->value['partialExecNothingToSave'];?>
";
      status_ok = checkStepsHaveContent(msg);
    }

    return status_ok;
  }






  function OLDvalidateForm(f) {
    var status_ok = true;
    var cfields_inputs = '';
    var cfValidityChecks;
    var cfield_container;
    var access_key;
    cfield_container = document.getElementById('save_button_clicked').value;
    access_key = 'cfields_exec_time_tcversionid_' + cfield_container;

    if (document.getElementById(access_key) != null) {
      cfields_inputs = document.getElementById(access_key).getElementsByTagName('input');
      cfValidityChecks = validateCustomFields(cfields_inputs);
      if (!cfValidityChecks.status_ok) {
        var warning_msg = cfMessages[cfValidityChecks.msg_id];
        alert_message(alert_box_title, warning_msg.replace(/%s/, cfValidityChecks.cfield_label));
        return false;
      }
    }
    return true;
  }

  /*
    function: checkSubmitForStatusCombo
              $statusCode has been checked, then false is returned to block form submit().

              Dev. Note - remember this:

              KO:
                 onclick="foo();checkSubmitForStatus('n')"
              OK
                 onclick="foo();return checkSubmitForStatus('n')"
                                ^^^^^^ 


    args :

    returns: 

  */
  function checkSubmitForStatusCombo(oid, statusCode2block) {
    var access_key;
    var isChecked;

    if (document.getElementById(oid).value == statusCode2block) {
      alert_message(alert_box_title, warning_nothing_will_be_saved);
      return false;
    }
    return true;
  }


  /**
   * 
   * IMPORTANT DEVELOPMENT NOTICE
   * ATTENTION args is a GLOBAL Javascript variable, then be CAREFULL
   */
  function openExportTestCases(windows_title, tsuite_id, tproject_id, tplan_id, build_id, platform_id, tcversion_set) {
    wargs = "tsuiteID=" + tsuite_id + "&tprojectID=" + tproject_id + "&tplanID=" + tplan_id;
    wargs += "&buildID=" + build_id + "&platformID=" + platform_id;
    wargs += "&tcversionSet=" + tcversion_set;
    wref = window.open(fRoot + "lib/execute/execExport.php?" + wargs,
      windows_title, "menubar=no,width=650,height=500,toolbar=no,scrollbars=yes");
    wref.focus();
  }


  <?php $_smarty_tpl->_assignInScope('tplan_notes_view_memory_id', "tpn_view_status");
$_smarty_tpl->_assignInScope('build_notes_view_memory_id', "bn_view_status");
$_smarty_tpl->_assignInScope('bulk_controls_view_memory_id', "bc_view_status");
$_smarty_tpl->_assignInScope('platform_notes_view_memory_id', "platform_notes_view_status");?>

<body onLoad="show_hide('tplan_notes','<?php echo $_smarty_tpl->tpl_vars['tplan_notes_view_memory_id']->value;?>
',<?php echo $_smarty_tpl->tpl_vars['gui']->value->tpn_view_status;?>
);
              show_hide('build_notes','<?php echo $_smarty_tpl->tpl_vars['build_notes_view_memory_id']->value;?>
',<?php echo $_smarty_tpl->tpl_vars['gui']->value->bn_view_status;?>
);
              show_hide('bulk_controls','<?php echo $_smarty_tpl->tpl_vars['bulk_controls_view_memory_id']->value;?>
',<?php echo $_smarty_tpl->tpl_vars['gui']->value->bc_view_status;?>
);
              show_hide('platform_notes','<?php echo $_smarty_tpl->tpl_vars['platform_notes_view_memory_id']->value;?>
',<?php echo $_smarty_tpl->tpl_vars['gui']->value->platform_notes_view_status;?>
);

              <?php if ($_smarty_tpl->tpl_vars['tsuite_info']->value != null) {?>
                multiple_show_hide('<?php echo $_smarty_tpl->tpl_vars['tsd_div_id_list']->value;?>
','<?php echo $_smarty_tpl->tpl_vars['tsd_hidden_id_list']->value;?>
',
                                   '<?php echo $_smarty_tpl->tpl_vars['tsd_val_for_hidden_list']->value;?>
');
              <?php }?>" onUnload="storeWindowSize('TCExecPopup')">

    <?php if ($_smarty_tpl->tpl_vars['round_enabled']->value || $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'ROUND_TC_SPEC') || $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'ROUND_EXEC_HISTORY') || $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'ROUND_TC_TITLE')) {?>
    <?php echo '<script'; ?>
 type="text/javascript">
      // Wait for DOM to be ready, then apply Nifty corners
      jQuery(document).ready(function() {
        console.log('Applying Nifty corners...');

        <?php if ($_smarty_tpl->tpl_vars['round_enabled']->value) {?>
          if (typeof Nifty !== 'undefined') {
            Nifty('div.exec_additional_info');
            console.log('Applied Nifty to exec_additional_info');
          } else {
            console.error('Nifty not loaded for exec_additional_info');
          }
        <?php }?>

        <?php if ($_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'ROUND_TC_SPEC')) {?>
          if (typeof Nifty !== 'undefined') {
            Nifty('div.exec_test_spec');
            console.log('Applied Nifty to exec_test_spec');
          } else {
            console.error('Nifty not loaded for exec_test_spec');
          }
        <?php }?>

        <?php if ($_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'ROUND_EXEC_HISTORY')) {?>
          if (typeof Nifty !== 'undefined') {
            Nifty('div.exec_history');
            console.log('Applied Nifty to exec_history');
          } else {
            console.error('Nifty not loaded for exec_history');
          }
        <?php }?>

        <?php if ($_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'ROUND_TC_TITLE')) {?>
          if (typeof Nifty !== 'undefined') {
            Nifty('div.exec_tc_title');
            console.log('Applied Nifty to exec_tc_title');
          } else {
            console.error('Nifty not loaded for exec_tc_title');
          }
        <?php }?>
      });
    <?php echo '</script'; ?>
>
  <?php }?>


  <?php if ($_smarty_tpl->tpl_vars['gui']->value->uploadOp != null) {?>
    <?php echo '<script'; ?>
>
      var uplMsg = "<?php echo $_smarty_tpl->tpl_vars['labels']->value['file_upload_ko'];?>
<br>";
      var doAlert = false;
      <?php if ($_smarty_tpl->tpl_vars['gui']->value->uploadOp->tcLevel != null && $_smarty_tpl->tpl_vars['gui']->value->uploadOp->tcLevel->statusOK == false) {?>
      uplMsg += "<?php echo $_smarty_tpl->tpl_vars['gui']->value->uploadOp->tcLevel->msg;?>
<br>";
      doAlert = true;
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['gui']->value->uploadOp->stepLevel != null && $_smarty_tpl->tpl_vars['gui']->value->uploadOp->stepLevel->statusOK == false) {?>
    uplMsg += "<?php echo $_smarty_tpl->tpl_vars['gui']->value->uploadOp->stepLevel->msg;?>
<br>";
    if (doAlert == false) {
      doAlert = true;
    }
    <?php }?>
    if (doAlert) {
      bootbox.alert(uplMsg);
    }
  <?php echo '</script'; ?>
>
  <?php }?>

  <?php if ($_smarty_tpl->tpl_vars['gui']->value->headsUpTSuite) {?>
    <?php echo '<script'; ?>
>
      var uplMsg = "<?php echo $_smarty_tpl->tpl_vars['labels']->value['pleaseOpenTSuite'];?>
<br>";
      bootbox.alert(uplMsg);
    <?php echo '</script'; ?>
>
  <?php }?>


  <h1 class="title">
    <?php echo $_smarty_tpl->tpl_vars['labels']->value['title_t_r_on_build'];?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->build_name, ENT_QUOTES, 'UTF-8', true);?>

    <?php if ($_smarty_tpl->tpl_vars['gui']->value->platform_info['name'] != '') {?>
      <?php echo $_smarty_tpl->tpl_vars['title_sep_type3']->value;
echo $_smarty_tpl->tpl_vars['labels']->value['platform'];
echo $_smarty_tpl->tpl_vars['title_sep']->value;
echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->platform_info['name'], ENT_QUOTES, 'UTF-8', true);?>

    <?php }?>
  </h1>

  <?php if ($_smarty_tpl->tpl_vars['gui']->value->ownerDisplayName != '') {?>
    <h1 class="title">
      <?php echo $_smarty_tpl->tpl_vars['labels']->value['only_test_cases_assigned_to'];
echo $_smarty_tpl->tpl_vars['title_sep']->value;?>

      <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['gui']->value->ownerDisplayName, 'assignedUser');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['assignedUser']->value) {
?>
        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['assignedUser']->value, ENT_QUOTES, 'UTF-8', true);?>

      <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
      <?php if ($_smarty_tpl->tpl_vars['gui']->value->include_unassigned) {?>
        <br /><?php echo $_smarty_tpl->tpl_vars['labels']->value['or_unassigned_test_cases'];?>

      <?php }?>
    </h1>
  <?php }?>

  <div id="main_content" class="workBack">
    <?php if ($_smarty_tpl->tpl_vars['gui']->value->user_feedback != '') {?>
      <div class="error"><?php echo $_smarty_tpl->tpl_vars['gui']->value->user_feedback;?>
</div>
    <?php }?>
    <?php if ($_smarty_tpl->tpl_vars['gui']->value->build_is_open == 0) {?>
      <div class="messages" style="align:center;">
        <?php echo $_smarty_tpl->tpl_vars['labels']->value['build_is_closed'];?>
<br />
        <?php echo $_smarty_tpl->tpl_vars['labels']->value['test_cases_cannot_be_executed'];?>

      </div>
      <br />
    <?php }?>


    <form method="post" id="execSetResults" name="execSetResults" enctype="multipart/form-data"
      onSubmit="javascript:return validateForm(this);">

      <input type="hidden" id="save_button_clicked" name="save_button_clicked" value="0" />
      <input type="hidden" id="do_delete" name="do_delete" value="0" />
      <input type="hidden" id="exec_to_delete" name="exec_to_delete" value="0" />
      <input type="hidden" id="form_token" name="form_token" value="<?php echo $_smarty_tpl->tpl_vars['gui']->value->treeFormToken;?>
" />
      <input type="hidden" id="refresh_tree" name="refresh_tree" value="<?php echo $_smarty_tpl->tpl_vars['gui']->value->refreshTree;?>
" />
      <input type="hidden" id="<?php echo $_smarty_tpl->tpl_vars['gui']->value->history_status_btn_name;?>
" name="<?php echo $_smarty_tpl->tpl_vars['gui']->value->history_status_btn_name;?>
" value="1" />

      <?php $_smarty_tpl->_assignInScope('bulkExec', $_smarty_tpl->tpl_vars['cfg']->value->exec_cfg->show_testsuite_contents && $_smarty_tpl->tpl_vars['gui']->value->can_use_bulk_op);?>
      <?php $_smarty_tpl->_assignInScope('singleExec', !$_smarty_tpl->tpl_vars['bulkExec']->value);?>

      <?php if ($_smarty_tpl->tpl_vars['singleExec']->value) {?>
        <div class="groupBtn">
          <input type="hidden" id="history_on" name="history_on" value="<?php echo $_smarty_tpl->tpl_vars['gui']->value->history_on;?>
" />

          <?php echo $_smarty_tpl->tpl_vars['tlImages']->value['toggle_direct_link'];?>
 &nbsp;
          <div class="direct_link" style='display:none'>
            <img class="clip" src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['clipboard'];?>
" title="eye" data-clipboard-text="<?php echo $_smarty_tpl->tpl_vars['gui']->value->direct_link;?>
">
            <a href="<?php echo $_smarty_tpl->tpl_vars['gui']->value->direct_link;?>
" target="_blank">
              <?php echo $_smarty_tpl->tpl_vars['gui']->value->direct_link;?>
</a>
          </div>


          <input type="button" name="print" id="print" value="<?php echo $_smarty_tpl->tpl_vars['labels']->value['btn_print'];?>
" onclick="javascript:window.print();" />
          <input type="button" id="toggle_history_on_off" name="<?php echo $_smarty_tpl->tpl_vars['gui']->value->history_status_btn_name;?>
"
            value="<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('s'=>$_smarty_tpl->tpl_vars['gui']->value->history_status_btn_name),$_smarty_tpl ) );?>
" onclick="javascript:toogleRequiredOnShowHide('bug_summary');
                      javascript:toogleRequiredOnShowHide('artifactVersion');
                      javascript:toogleRequiredOnShowHide('artifactComponent');
                      execSetResults.submit();" />

          <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants->execute) {?>
            <input type="button" id="pop_up_import_button" name="import_xml_button" value="<?php echo $_smarty_tpl->tpl_vars['labels']->value['import_xml_results'];?>
"
              onclick="javascript: openImportResult('import_xml_results',<?php echo $_smarty_tpl->tpl_vars['gui']->value->tproject_id;?>
,
                                                   <?php echo $_smarty_tpl->tpl_vars['gui']->value->tplan_id;?>
,<?php echo $_smarty_tpl->tpl_vars['gui']->value->build_id;?>
,<?php echo $_smarty_tpl->tpl_vars['gui']->value->platform_id;?>
);" />

          <?php }?>

          <?php if ($_smarty_tpl->tpl_vars['tlCfg']->value->exec_cfg->enable_test_automation) {?>
            <input type="submit" id="execute_cases" name="execute_cases" value="<?php echo $_smarty_tpl->tpl_vars['labels']->value['execute_and_save_results'];?>
" />
          <?php }?>

          <?php if ($_smarty_tpl->tpl_vars['gui']->value->hasNewestVersion && 1 == 0) {?>
            <input type="hidden" id="TCVToUpdate" name="TCVToUpdate" value="<?php echo $_smarty_tpl->tpl_vars['gui']->value->tcversionSet;?>
">
            <input type="submit" id="linkLatestVersion" name="linkLatestVersion"
              value="<?php echo $_smarty_tpl->tpl_vars['labels']->value['updateLinkToLatestTCVersion'];?>
" />
          <?php }?>

        </div>
      <?php }?>

      <?php if ($_smarty_tpl->tpl_vars['gui']->value->plugins['EVENT_TESTRUN_DISPLAY']) {?>
        <div id="plugin_display">
          <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['gui']->value->plugins['EVENT_TESTRUN_DISPLAY'], 'testrun_item');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['testrun_item']->value) {
?>
            <?php echo $_smarty_tpl->tpl_vars['testrun_item']->value;?>

            <br />
          <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </div>
      <?php }?>

                        <?php $_smarty_tpl->_assignInScope('div_id', 'tplan_notes');?>
      <?php $_smarty_tpl->_assignInScope('memstatus_id', $_smarty_tpl->tpl_vars['tplan_notes_view_memory_id']->value);?>
      <?php $_smarty_tpl->_subTemplateRender("file:inc_show_hide_mgmt.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('show_hide_container_title'=>$_smarty_tpl->tpl_vars['gui']->value->testplan_div_title,'show_hide_container_id'=>$_smarty_tpl->tpl_vars['div_id']->value,'show_hide_container_draw'=>false,'show_hide_container_class'=>'exec_additional_info','show_hide_container_view_status_id'=>$_smarty_tpl->tpl_vars['memstatus_id']->value), 0, false);
?>

      <div id="<?php echo $_smarty_tpl->tpl_vars['div_id']->value;?>
" class="exec_additional_info">
        <?php if ($_smarty_tpl->tpl_vars['gui']->value->testPlanEditorType == 'none') {
echo nl2br($_smarty_tpl->tpl_vars['gui']->value->testplan_notes);
} else {
echo $_smarty_tpl->tpl_vars['gui']->value->testplan_notes;
}?>
        <?php if ($_smarty_tpl->tpl_vars['gui']->value->testplan_cfields != '') {?> <div id="cfields_testplan" class="custom_field_container">
          <?php echo $_smarty_tpl->tpl_vars['gui']->value->testplan_cfields;?>
</div><?php }?>
      </div>
      
                        <?php if ($_smarty_tpl->tpl_vars['gui']->value->platform_info['id'] > 0) {?>
        <?php $_smarty_tpl->_assignInScope('div_id', 'platform_notes');?>
        <?php $_smarty_tpl->_assignInScope('memstatus_id', $_smarty_tpl->tpl_vars['platform_notes_view_memory_id']->value);?>
        <?php if ($_smarty_tpl->tpl_vars['gui']->value->platformEditorType == 'none') {
$_smarty_tpl->_assignInScope('content', nl2br($_smarty_tpl->tpl_vars['gui']->value->platform_info['notes']));
} else {
$_smarty_tpl->_assignInScope('content', $_smarty_tpl->tpl_vars['gui']->value->platform_info['notes']);
}?>

        <?php $_smarty_tpl->_subTemplateRender("file:inc_show_hide_mgmt.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('show_hide_container_title'=>$_smarty_tpl->tpl_vars['gui']->value->platform_div_title,'show_hide_container_id'=>$_smarty_tpl->tpl_vars['div_id']->value,'show_hide_container_view_status_id'=>$_smarty_tpl->tpl_vars['memstatus_id']->value,'show_hide_container_draw'=>true,'show_hide_container_class'=>'exec_additional_info','show_hide_container_html'=>$_smarty_tpl->tpl_vars['content']->value), 0, true);
?>
      <?php }?>
      
                        <?php $_smarty_tpl->_assignInScope('div_id', 'build_notes');?>
      <?php $_smarty_tpl->_assignInScope('memstatus_id', $_smarty_tpl->tpl_vars['build_notes_view_memory_id']->value);?>
      <?php $_smarty_tpl->_subTemplateRender("file:inc_show_hide_mgmt.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('show_hide_container_title'=>$_smarty_tpl->tpl_vars['gui']->value->build_div_title,'show_hide_container_id'=>$_smarty_tpl->tpl_vars['div_id']->value,'show_hide_container_view_status_id'=>$_smarty_tpl->tpl_vars['memstatus_id']->value,'show_hide_container_draw'=>false,'show_hide_container_class'=>'exec_additional_info'), 0, true);
?>

      <div id="<?php echo $_smarty_tpl->tpl_vars['div_id']->value;?>
" class="exec_additional_info">
        <?php if ($_smarty_tpl->tpl_vars['gui']->value->buildEditorType == 'none') {
echo nl2br($_smarty_tpl->tpl_vars['gui']->value->build_notes);
} else {
echo $_smarty_tpl->tpl_vars['gui']->value->build_notes;
}?>
        <?php if ($_smarty_tpl->tpl_vars['gui']->value->build_cfields != '') {?> <div id="cfields_build" class="custom_field_container"><?php echo $_smarty_tpl->tpl_vars['gui']->value->build_cfields;?>

        </div><?php }?>
      </div>

            <?php if ($_smarty_tpl->tpl_vars['gui']->value->map_last_exec == '') {?>
        <div class="messages" style="text-align:center"> <?php echo $_smarty_tpl->tpl_vars['labels']->value['no_data_available'];?>
</div>
      <?php } else { ?>
        <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants->execute == 1 && $_smarty_tpl->tpl_vars['gui']->value->build_is_open == 1) {?>
          <?php $_smarty_tpl->_assignInScope('input_enabled_disabled', '');?>
          <?php $_smarty_tpl->_assignInScope('att_download_only', false);?>
          <?php $_smarty_tpl->_assignInScope('enable_custom_fields', true);?>
          <?php $_smarty_tpl->_assignInScope('draw_submit_button', true);?>

          <?php if ($_smarty_tpl->tpl_vars['bulkExec']->value) {?>
            <?php $_smarty_tpl->_assignInScope('div_id', 'bulk_controls');?>
            <?php $_smarty_tpl->_assignInScope('memstatus_id', ((string)$_smarty_tpl->tpl_vars['bulk_controls_view_memory_id']->value));?>
            <?php $_smarty_tpl->_subTemplateRender("file:inc_show_hide_mgmt.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('show_hide_container_title'=>$_smarty_tpl->tpl_vars['labels']->value['bulk_tc_status_management'],'show_hide_container_id'=>$_smarty_tpl->tpl_vars['div_id']->value,'show_hide_container_draw'=>false,'show_hide_container_class'=>'exec_additional_info','show_hide_container_view_status_id'=>$_smarty_tpl->tpl_vars['memstatus_id']->value), 0, true);
?>

            <div id="<?php echo $_smarty_tpl->tpl_vars['div_id']->value;?>
" name="<?php echo $_smarty_tpl->tpl_vars['div_id']->value;?>
">
              <?php $_smarty_tpl->_subTemplateRender("execute/".((string)$_smarty_tpl->tpl_vars['tplConfig']->value['inc_exec_controls']), $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('args_save_type'=>'bulk','args_input_enable_mgmt'=>$_smarty_tpl->tpl_vars['input_enabled_disabled']->value,'args_tcversion_id'=>'bulk','args_webeditor'=>$_smarty_tpl->tpl_vars['gui']->value->bulk_exec_notes_editor,'args_execution_time_cfields'=>$_smarty_tpl->tpl_vars['gui']->value->execution_time_cfields,'args_draw_save_and_exit'=>$_smarty_tpl->tpl_vars['gui']->value->draw_save_and_exit,'args_labels'=>$_smarty_tpl->tpl_vars['labels']->value), 0, true);
?>
            </div>
          <?php }?>
        <?php }?>
      <?php }?>

      <?php if ($_smarty_tpl->tpl_vars['bulkExec']->value) {?>
        <?php $_smarty_tpl->_subTemplateRender("file:execute/execSetResultsBulk.inc.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
      <?php }?>

      <?php if ($_smarty_tpl->tpl_vars['singleExec']->value) {?>
        <?php if ($_smarty_tpl->tpl_vars['tlCfg']->value->exec_cfg->enable_test_automation && $_smarty_tpl->tpl_vars['gui']->value->remoteExecFeedback != '') {?>
        <?php $_smarty_tpl->_subTemplateRender("file:execute/execSetResultsRemoteExec.inc.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
      <?php }?>

      <?php $_smarty_tpl->_subTemplateRender("file:execute/inc_exec_show_tc_exec.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
      <?php if (isset($_smarty_tpl->tpl_vars['gui']->value->refreshTree) && $_smarty_tpl->tpl_vars['gui']->value->refreshTree) {?>
        <?php $_smarty_tpl->_subTemplateRender("file:inc_refreshTreeWithFilters.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
      <?php }?>
      <?php }?>

    </form>
  </div>

  <?php echo '<script'; ?>
>
    jQuery(document).ready(function() {
      clipboard = new Clipboard('.clip');

      // Add an overlay div for the processing message
      jQuery('body').append(
        '<div id="bugSubmissionOverlay" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background-color:rgba(0,0,0,0.5); z-index:9999; text-align:center;"><div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); background-color:white; padding:20px; border-radius:5px;"><h3>Submitting to Bug Tracker...</h3><p>Please wait while your bug is being submitted.</p></div></div>'
      );

      // Function to disable/enable test execution buttons
      window.disableTestExecButtons = function(disable) {
        // Disable/enable the status buttons (green checkmark, red X, etc.)
        var $execButtons = jQuery('img[id^="fastExec"]');
        var $nextButtons = jQuery('img[id^="fastExecNext"]');
        var $moveNextButton = jQuery('input[name^="move2next"]');

        if (disable) {
          // Add semi-transparent overlay to indicate buttons are disabled
          $execButtons.css('opacity', '0.5');
          $nextButtons.css('opacity', '0.5');
          $moveNextButton.prop('disabled', true);

          // Show the overlay with the processing message
          jQuery('#bugSubmissionOverlay').show();
        } else {
          // Restore opacity
          $execButtons.css('opacity', '1');
          $nextButtons.css('opacity', '1');
          $moveNextButton.prop('disabled', false);

          // Hide the overlay
          jQuery('#bugSubmissionOverlay').hide();
        }
      };

      // Monitor the createIssue checkbox
      jQuery(document).on('change', '#createIssue', function() {
        // Toggle button state based on checkbox
        if (jQuery(this).is(':checked')) {
          console.log('Bug creation checkbox checked');

          // Make bug summary required
          toogleRequiredOnShowHide('bug_summary', '');

          // Don't disable execution buttons - user should be able to execute and create bugs
          // window.disableTestExecButtons(true);
        } else {
          console.log('Bug creation checkbox unchecked');

          // Don't disable execution buttons
          // window.disableTestExecButtons(false);
          toogleRequiredOnShowHide('bug_summary', 'none');

          // Enable execution buttons
          // window.disableTestExecButtons(false);
        }
      });

      // Create a global variable to track bug submission status
      window.bugSubmissionInProgress = false;
    });
  <?php echo '</script'; ?>
>
</body>

</html><?php }
}
