<?php
/* Smarty version 3.1.33, created on 2026-03-05 21:20:42
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\execute\inc_exec_test_spec.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69a9e59a7b3b55_99319082',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '674ab8e4401334d4bb5364170fb23caa43f97853' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\execute\\inc_exec_test_spec.tpl',
      1 => 1579460696,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:testcases/inc_steps.tpl' => 1,
    'file:execute/exec_tc_relations.inc.tpl' => 1,
    'file:attachments.inc.tpl' => 1,
    'file:inc_show_scripts_table.tpl' => 1,
  ),
),false)) {
function content_69a9e59a7b3b55_99319082 (Smarty_Internal_Template $_smarty_tpl) {
?>  
    <?php $_smarty_tpl->_assignInScope('tableColspan', "4");?>
    <?php if ($_smarty_tpl->tpl_vars['tlCfg']->value->exec_cfg->steps_exec) {?>
      <?php $_smarty_tpl->_assignInScope('tableColspan', "6");?>
    <?php }?>
    
    <?php $_smarty_tpl->_assignInScope('getReqAction', "lib/requirements/reqView.php?showReqSpecTitle=1&requirement_id=");?>
    <?php $_smarty_tpl->_assignInScope('testcase_id', $_smarty_tpl->tpl_vars['args_tc_exec']->value['testcase_id']);?>
    <?php $_smarty_tpl->_assignInScope('tcversion_id', $_smarty_tpl->tpl_vars['args_tc_exec']->value['id']);?>
    
     
    <div class="exec_test_spec">
    <table class="simple">
    
    <?php $_smarty_tpl->_assignInScope('freshAirBeforeSteps', 0);?>
    <?php if ('' != $_smarty_tpl->tpl_vars['args_tc_exec']->value['summary']) {?>
      <?php $_smarty_tpl->_assignInScope('freshAirBeforeSteps', 1);?>
      <tr>
        <th colspan="<?php echo $_smarty_tpl->tpl_vars['tableColspan']->value;?>
" class="title"><?php echo $_smarty_tpl->tpl_vars['args_labels']->value['test_exec_summary'];?>
</th>
      </tr>
      <tr>
        <td colspan="<?php echo $_smarty_tpl->tpl_vars['tableColspan']->value;?>
"><?php if ($_smarty_tpl->tpl_vars['gui']->value->testDesignEditorType == 'none') {
echo nl2br($_smarty_tpl->tpl_vars['args_tc_exec']->value['summary']);
} else {
echo $_smarty_tpl->tpl_vars['args_tc_exec']->value['summary'];
}?></td>
      </tr>
    <?php }?>

    <?php if ('' != $_smarty_tpl->tpl_vars['args_tc_exec']->value['preconditions']) {?>
      <?php $_smarty_tpl->_assignInScope('freshAirBeforeSteps', 1);?>
      <tr>
        <th colspan="<?php echo $_smarty_tpl->tpl_vars['tableColspan']->value;?>
" class="title"><?php echo $_smarty_tpl->tpl_vars['args_labels']->value['preconditions'];?>
</th>
      </tr>
      <tr>
        <td colspan="<?php echo $_smarty_tpl->tpl_vars['tableColspan']->value;?>
"><?php if ($_smarty_tpl->tpl_vars['gui']->value->testDesignEditorType == 'none') {
echo nl2br($_smarty_tpl->tpl_vars['args_tc_exec']->value['preconditions']);
} else {
echo $_smarty_tpl->tpl_vars['args_tc_exec']->value['preconditions'];
}?></td>
      </tr>
    <?php }?>

        <?php if (1 == $_smarty_tpl->tpl_vars['freshAirBeforeSteps']->value) {?>
      <tr> <td colspan="<?php echo $_smarty_tpl->tpl_vars['tableColspan']->value;?>
">&nbsp;</td></tr>
    <?php }?> 

    <?php if ($_smarty_tpl->tpl_vars['args_design_time_cf']->value[$_smarty_tpl->tpl_vars['testcase_id']->value]['before_steps_results'] != '') {?>
    <tr>
      <td colspan="<?php echo $_smarty_tpl->tpl_vars['tableColspan']->value;?>
"> <?php echo $_smarty_tpl->tpl_vars['args_design_time_cf']->value[$_smarty_tpl->tpl_vars['testcase_id']->value]['before_steps_results'];?>
</td>
    </tr>
    <?php }?>

    <?php if ($_smarty_tpl->tpl_vars['args_tc_exec']->value['steps'] != '' && !is_null($_smarty_tpl->tpl_vars['args_tc_exec']->value['steps'])) {?>
      <?php $_smarty_tpl->_subTemplateRender("file:testcases/inc_steps.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('layout'=>$_smarty_tpl->tpl_vars['args_cfg']->value->exec_cfg->steps_results_layout,'edit_enabled'=>false,'ghost_control'=>false,'add_exec_info'=>$_smarty_tpl->tpl_vars['tlCfg']->value->exec_cfg->steps_exec,'steps'=>$_smarty_tpl->tpl_vars['args_tc_exec']->value['steps']), 0, false);
?>

    <tr>
      <td colspan="<?php echo $_smarty_tpl->tpl_vars['tableColspan']->value;?>
" style="text-align: center;"> 
      <b><?php echo $_smarty_tpl->tpl_vars['args_labels']->value['partialExecNoAttachmentsWarning'];?>
</b>
      </td>
    <tr>

    <tr>
      <td colspan="<?php echo $_smarty_tpl->tpl_vars['tableColspan']->value;?>
" style="text-align: center;"> 
       <button class="btn btn-primary" name="saveStepsPartialExec"
         id="saveStepsPartialExec" type="submit"><?php echo $_smarty_tpl->tpl_vars['args_labels']->value['saveStepsForPartialExec'];?>
</button>
      </td>
    <tr>
    <?php }?>

    <tr> <td colspan="<?php echo $_smarty_tpl->tpl_vars['tableColspan']->value;?>
"> &nbsp; </td></tr>
    <tr> <td colspan="<?php echo $_smarty_tpl->tpl_vars['tableColspan']->value;?>
"> &nbsp; </td></tr>

    <tr>
      <td colspan="<?php echo $_smarty_tpl->tpl_vars['tableColspan']->value;?>
"><b><?php echo $_smarty_tpl->tpl_vars['args_labels']->value['execution_type'];
echo @constant('TITLE_SEP');?>
</b>
                                       <?php echo $_smarty_tpl->tpl_vars['args_execution_types']->value[$_smarty_tpl->tpl_vars['args_tc_exec']->value['execution_type']];?>
</td>
    </tr>
    <tr>
      <td colspan="<?php echo $_smarty_tpl->tpl_vars['tableColspan']->value;?>
"><b><?php echo $_smarty_tpl->tpl_vars['args_labels']->value['estimated_execution_duration'];
echo @constant('TITLE_SEP');?>
</b>
        <?php echo $_smarty_tpl->tpl_vars['args_tc_exec']->value['estimated_exec_duration'];?>

      </td>
    </tr>

        <?php if ($_smarty_tpl->tpl_vars['args_relations']->value != '' && !is_null($_smarty_tpl->tpl_vars['args_relations']->value)) {?>
      <tr>
        <td colspan="<?php echo $_smarty_tpl->tpl_vars['tableColspan']->value;?>
">
        <?php $_smarty_tpl->_subTemplateRender("file:execute/exec_tc_relations.inc.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('argsColSpan'=>$_smarty_tpl->tpl_vars['tableColspan']->value,'argsRelSet'=>$_smarty_tpl->tpl_vars['args_relations']->value), 0, false);
?>  
        </td>
      </tr>
    <?php }?>

    <tr>
    <td colspan="<?php echo $_smarty_tpl->tpl_vars['tableColspan']->value;?>
">
      <?php if ($_smarty_tpl->tpl_vars['args_design_time_cf']->value[$_smarty_tpl->tpl_vars['testcase_id']->value]['standard_location'] != '') {?>
          <div id="cfields_design_time_tcversionid_<?php echo $_smarty_tpl->tpl_vars['tcversion_id']->value;?>
" class="custom_field_container" 
          style="background-color:#dddddd;"><?php echo $_smarty_tpl->tpl_vars['args_design_time_cf']->value[$_smarty_tpl->tpl_vars['testcase_id']->value]['standard_location'];?>

          </div>
      <?php }?> 
      </td>
    </tr>
 
    <tr>
        <?php if ($_smarty_tpl->tpl_vars['args_enable_custom_field']->value && $_smarty_tpl->tpl_vars['args_tc_exec']->value['active'] == 1) {?>
      <?php if (isset($_smarty_tpl->tpl_vars['args_execution_time_cf']->value[$_smarty_tpl->tpl_vars['testcase_id']->value]) && $_smarty_tpl->tpl_vars['args_execution_time_cf']->value[$_smarty_tpl->tpl_vars['testcase_id']->value] != '') {?>
        <tr>
          <td colspan="<?php echo $_smarty_tpl->tpl_vars['tableColspan']->value;?>
">
            <div id="cfields_exec_time_tcversionid_<?php echo $_smarty_tpl->tpl_vars['tcversion_id']->value;?>
" class="custom_field_container" 
                 style="background-color:#dddddd;"><?php echo $_smarty_tpl->tpl_vars['args_execution_time_cf']->value[$_smarty_tpl->tpl_vars['testcase_id']->value];?>

            </div>
          </td>
        </tr>
      <?php }?>
    <?php }?>         
      <td colspan="<?php echo $_smarty_tpl->tpl_vars['tableColspan']->value;?>
">
      <?php if ($_smarty_tpl->tpl_vars['args_testplan_design_time_cf']->value[$_smarty_tpl->tpl_vars['testcase_id']->value] != '') {?>
          <div id="cfields_testplan_design_time_tcversionid_<?php echo $_smarty_tpl->tpl_vars['tcversion_id']->value;?>
" class="custom_field_container" 
          style="background-color:#dddddd;"><?php echo $_smarty_tpl->tpl_vars['args_testplan_design_time_cf']->value[$_smarty_tpl->tpl_vars['testcase_id']->value];?>

          </div>
      <?php }?> 
      </td>
    </tr>
    
    <tr>
      <td colspan="<?php echo $_smarty_tpl->tpl_vars['tableColspan']->value;?>
">
      <?php if ($_smarty_tpl->tpl_vars['args_tcAttachments']->value[$_smarty_tpl->tpl_vars['testcase_id']->value] != null) {?>
        <?php $_smarty_tpl->_subTemplateRender("file:attachments.inc.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('attach_downloadOnly'=>true,'attach_attachmentInfos'=>$_smarty_tpl->tpl_vars['args_tcAttachments']->value[$_smarty_tpl->tpl_vars['testcase_id']->value],'attach_tableClassName'=>"bordered",'attach_tableStyles'=>"background-color:#dddddd;width:100%"), 0, false);
?>
      <?php }?>
      </td>
    </tr>

        <?php if (isset($_smarty_tpl->tpl_vars['gui']->value->scripts[$_smarty_tpl->tpl_vars['tcversion_id']->value]) && !is_null($_smarty_tpl->tpl_vars['gui']->value->scripts[$_smarty_tpl->tpl_vars['tcversion_id']->value])) {?>
      <tr style="background-color: #dddddd">
        <?php $_smarty_tpl->_subTemplateRender("file:inc_show_scripts_table.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('scripts_map'=>$_smarty_tpl->tpl_vars['gui']->value->scripts[$_smarty_tpl->tpl_vars['tcversion_id']->value],'can_delete'=>false,'tcase_id'=>$_smarty_tpl->tpl_vars['tcversion_id']->value,'tproject_id'=>$_smarty_tpl->tpl_vars['gui']->value->tproject_id), 0, false);
?>
      </tr>
    <?php }?>

    <?php if (isset($_smarty_tpl->tpl_vars['args_keywords']->value)) {?>
      <tr>
        <td colspan="<?php echo $_smarty_tpl->tpl_vars['tableColspan']->value;?>
">
          <b><?php echo $_smarty_tpl->tpl_vars['args_labels']->value['keywords'];
echo @constant('TITLE_SEP');?>
</b>&nbsp
          <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['args_keywords']->value, 'keyword_item', false, NULL, 'itemKeywords', array (
  'last' => true,
  'iteration' => true,
  'total' => true,
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['keyword_item']->value) {
$_smarty_tpl->tpl_vars['__smarty_foreach_itemKeywords']->value['iteration']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_itemKeywords']->value['last'] = $_smarty_tpl->tpl_vars['__smarty_foreach_itemKeywords']->value['iteration'] === $_smarty_tpl->tpl_vars['__smarty_foreach_itemKeywords']->value['total'];
?>
            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['keyword_item']->value['keyword'], ENT_QUOTES, 'UTF-8', true);
if (!(isset($_smarty_tpl->tpl_vars['__smarty_foreach_itemKeywords']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_itemKeywords']->value['last'] : null)) {?>,&nbsp;<?php }?> 
          <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        </td>
      </tr>
    <?php }?>

    </table>
    </div>

    <br />
    <?php if (isset($_smarty_tpl->tpl_vars['args_req_details']->value)) {?>
    <div class="exec_test_spec">
      <table class="test_exec"  >
      <tr>
        <th colspan="<?php echo $_smarty_tpl->tpl_vars['tableColspan']->value;?>
" class="title"><?php echo $_smarty_tpl->tpl_vars['args_labels']->value['reqs'];?>
</th>
      </tr>
        
      <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['args_req_details']->value, 'req_elem', false, 'id');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['id']->value => $_smarty_tpl->tpl_vars['req_elem']->value) {
?>
      <tr>
        <td>
        <span class="bold">
         <?php echo $_smarty_tpl->tpl_vars['tlCfg']->value->gui_separator_open;
echo $_smarty_tpl->tpl_vars['req_elem']->value['req_spec_title'];
echo $_smarty_tpl->tpl_vars['tlCfg']->value->gui_separator_close;?>
&nbsp;
         <a href="javascript:openLinkedReqWindow(<?php echo $_smarty_tpl->tpl_vars['req_elem']->value['id'];?>
)"  
            title="<?php echo $_smarty_tpl->tpl_vars['args_labels']->value['click_to_open'];?>
">
          <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['req_elem']->value['req_doc_id'], ENT_QUOTES, 'UTF-8', true);
echo $_smarty_tpl->tpl_vars['tlCfg']->value->gui_title_separator_1;
echo htmlspecialchars($_smarty_tpl->tpl_vars['req_elem']->value['title'], ENT_QUOTES, 'UTF-8', true);?>
 [<?php echo $_smarty_tpl->tpl_vars['args_labels']->value['version'];?>
 <?php echo $_smarty_tpl->tpl_vars['req_elem']->value['version'];?>
]
         </a>
        </span>
       </td>
      </tr>
      <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
      </table>
      </div>
      <br />
    <?php }
}
}
