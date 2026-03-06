<?php
/* Smarty version 3.1.33, created on 2026-03-05 21:20:42
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\execute\inc_exec_img_controls.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69a9e59a8b4b62_54452040',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '73e65b2327d5bc3d81846cc1430fac1a98d0ea2f' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\execute\\inc_exec_img_controls.tpl',
      1 => 1772562330,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:attachments_simple.inc.tpl' => 1,
    'file:./issueTrackerMetadata.inc.tpl' => 1,
  ),
),false)) {
function content_69a9e59a8b4b62_54452040 (Smarty_Internal_Template $_smarty_tpl) {
?>	
<?php $_smarty_tpl->_assignInScope('tcvID', $_smarty_tpl->tpl_vars['args_tcversion_id']->value);?>  
      <?php $_smarty_tpl->_assignInScope('ResultsStatusCode', $_smarty_tpl->tpl_vars['tlCfg']->value->results['status_code']);?>
      <?php if ($_smarty_tpl->tpl_vars['args_save_type']->value == 'bulk') {?>
        <?php $_smarty_tpl->_assignInScope('radio_id_prefix', "bulk_status");?>
      <?php } else { ?>
        <?php $_smarty_tpl->_assignInScope('radio_id_prefix', "statusSingle");?>
      <?php }?>

      <?php if ($_smarty_tpl->tpl_vars['gui']->value->grants->execute) {?>
  		<table class="no-border" style="border: thick solid white">
  		<tr border='0'>
  			<td style="text-align: center;width:75%; border: 0px">
  				<div class="title"><?php echo $_smarty_tpl->tpl_vars['args_labels']->value['test_exec_notes'];?>
</div>
          <?php echo $_smarty_tpl->tpl_vars['args_webeditor']->value;?>
 
        <br>  
        <?php $_smarty_tpl->_subTemplateRender("file:attachments_simple.inc.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('attach_id'=>0), 0, false);
?>
  			</td>
  			<td valign="top" style="width:25%; border: 0px">			
    				      			<div class="title" style="text-align: center;">
            <?php if ($_smarty_tpl->tpl_vars['args_save_type']->value == 'bulk') {?> <?php echo $_smarty_tpl->tpl_vars['args_labels']->value['test_exec_result'];?>
 <?php } else { ?> &nbsp; <?php }?>
            </div>

    				<div class="resultBox">
              <?php if ($_smarty_tpl->tpl_vars['args_save_type']->value == 'bulk') {?>
                <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['tlCfg']->value->results['status_label_for_exec_ui'], 'locale_status', false, 'verbose_status');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['verbose_status']->value => $_smarty_tpl->tpl_vars['locale_status']->value) {
?>
    						      <input type="radio" <?php echo $_smarty_tpl->tpl_vars['args_input_enable_mgmt']->value;?>
 name="<?php echo $_smarty_tpl->tpl_vars['radio_id_prefix']->value;?>
[<?php echo $_smarty_tpl->tpl_vars['tcvID']->value;?>
]" 
    						      id="<?php echo $_smarty_tpl->tpl_vars['radio_id_prefix']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['tcvID']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['ResultsStatusCode']->value[$_smarty_tpl->tpl_vars['verbose_status']->value];?>
" 
    							    value="<?php echo $_smarty_tpl->tpl_vars['ResultsStatusCode']->value[$_smarty_tpl->tpl_vars['verbose_status']->value];?>
"
    											onclick="javascript:set_combo_group('execSetResults','status_','<?php echo $_smarty_tpl->tpl_vars['ResultsStatusCode']->value[$_smarty_tpl->tpl_vars['verbose_status']->value];?>
');"
    							    <?php if ($_smarty_tpl->tpl_vars['verbose_status']->value == $_smarty_tpl->tpl_vars['tlCfg']->value->results['default_status']) {?>
    							        checked="checked" 
    							    <?php }?> /> &nbsp;<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('s'=>$_smarty_tpl->tpl_vars['locale_status']->value),$_smarty_tpl ) );?>
<br />
    					  <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
              <?php }?>

              <?php if ($_smarty_tpl->tpl_vars['tlCfg']->value->exec_cfg->features->exec_duration->enabled) {?>	
                <br />	
                <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['execution_duration'];?>
" 
                       title="<?php echo $_smarty_tpl->tpl_vars['args_labels']->value['execution_duration'];?>
">
                <input type="text" name="execution_duration" id="execution_duration"
                       size="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'EXEC_DURATION_SIZE');?>
" 
                       onkeyup="this.value=this.value.replace(/[^0-9]/g,'');"
                       maxlength="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'EXEC_DURATION_MAXLEN');?>
">  
                <?php }?>       		 			
              <?php if ($_smarty_tpl->tpl_vars['args_save_type']->value == 'single') {?>
                <br />
                <br />
                <?php $_smarty_tpl->_assignInScope('addBR', 0);?>
                <?php if ($_smarty_tpl->tpl_vars['tc_exec']->value['assigned_user'] == '') {?>
                 <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['assign_task'];?>
" 
                       title="<?php echo $_smarty_tpl->tpl_vars['args_labels']->value['assign_exec_task_to_me'];?>
">
                  <input type="checkbox" name="assignTask"  id="assignTask"
                  <?php if ($_smarty_tpl->tpl_vars['gui']->value->assignTaskChecked) {?> checked <?php }?>>
                  &nbsp;
                <?php }?>

                <?php if ($_smarty_tpl->tpl_vars['tlCfg']->value->exec_cfg->exec_mode->new_exec == 'latest') {?>
                  <?php $_smarty_tpl->_assignInScope('addBR', 1);?>
                 <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['copy_attachments'];?>
" 
                       title="<?php echo $_smarty_tpl->tpl_vars['args_labels']->value['copy_attachments_from_latest_exec'];?>
">
                  <input type="checkbox" name="copyAttFromLEXEC"  id="copyAttFromLEXEC">
                  &nbsp;
                <?php }?>


                
                <?php if ($_smarty_tpl->tpl_vars['gui']->value->tlCanCreateIssue) {?>
                  <?php $_smarty_tpl->_assignInScope('addBR', 1);?>
                  <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['bug_create_into_bts'];?>
" 
                       title="<?php echo $_smarty_tpl->tpl_vars['args_labels']->value['bug_create_into_bts'];?>
">
                  <input type="checkbox" name="createIssue"  id="createIssue" 
                         onclick="javascript:toogleShowHide('issue_summary');
                         javascript:toogleRequiredOnShowHide('bug_summary');
                         javascript:toogleRequiredOnShowHide('artifactVersion');
                         javascript:toogleRequiredOnShowHide('artifactComponent');
                         console.log('Create Issue checkbox clicked, current state:', this.checked);
                         if(this.checked) {
                           console.log('Checkbox checked - checking for integration dropdown');
                           // Don't show dropdown here - wait for execution button click
                         } else {
                           console.log('Checkbox unchecked - hiding integration dropdown');
                           window.toggleIntegrationDropdown(false);
                         }">
                         
                                    <?php echo '<script'; ?>
>
                  console.log('=== TEMPLATE INTEGRATION DEBUG ===');
                  console.log('Integration dropdown HTML is being added to template');
                  console.log('tlCanCreateIssue value:', '<?php echo $_smarty_tpl->tpl_vars['gui']->value->tlCanCreateIssue;?>
');
                  console.log('=== END TEMPLATE INTEGRATION DEBUG ===');
                  <?php echo '</script'; ?>
>
                  
                  <div id="integrationModal" class="modal" style="display:none; position:fixed; top:0; left:0; z-index:10000; width:100%; height:100%; background-color:rgba(0,0,0,0.5);">
  <div class="modal-content" style="background-color:white; margin:15% auto; padding:20px; border-radius:5px; width:400px; box-shadow:0 4px 8px rgba(0,0,0,0.2); border:2px solid #007bff;">
    <h3 style="margin-top:0; color:#007bff;">Select Integration for Bug Creation</h3>
    <select id="integrationModalDropdown" style="width:100%; padding:8px; margin:10px 0; border:1px solid #ccc; border-radius:4px; font-size:14px;">
      <option value="">-- Select Integration --</option>
    </select>
    <div style="text-align:center; margin-top:20px;">
      <button type="button" onclick="confirmIntegrationSelection()" style="background:#007bff; color:white; padding:10px 20px; border:none; border-radius:4px; margin-right:10px; cursor:pointer; font-size:14px;">Select</button>
      <button type="button" onclick="cancelIntegrationSelection()" style="background:#6c757d; color:white; padding:10px 20px; border:none; border-radius:4px; cursor:pointer; font-size:14px;">Cancel</button>
    </div>
  </div>
</div>
                  
                                                    <?php }?>

                <?php if ($_smarty_tpl->tpl_vars['tlCfg']->value->exec_cfg->copyLatestExecIssues->enabled) {?>
                  <?php if ($_smarty_tpl->tpl_vars['addBR']->value) {?><br><?php }?>
                  <?php echo $_smarty_tpl->tpl_vars['args_labels']->value['bug_copy_from_latest_exec'];?>
&nbsp;
                   <input type="checkbox" name="copyIssues[<?php echo $_smarty_tpl->tpl_vars['tcvID']->value;?>
]" id="copyIssues" 
                    <?php if ($_smarty_tpl->tpl_vars['tlCfg']->value->exec_cfg->copyLatestExecIssues->default) {?> checked <?php }?>>
                   <br />
                <?php }?>

                 <input type="hidden" name="statusSingle[<?php echo $_smarty_tpl->tpl_vars['tcversion_id']->value;?>
]" 
                        id="statusSingle_<?php echo $_smarty_tpl->tpl_vars['tcversion_id']->value;?>
" value="">
                 <input type="hidden" name="selected_integration_id" id="selected_integration_id" value="">
                 <input type="hidden" name="save_results" id="save_results" value="0">
                 <br />
                 <br />
                 <button style="display: none;" type="submit" 
                         id="hidden-submit-button"></button>
                 <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['gui']->value->execStatusIcons, 'ikval', false, 'kode');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['kode']->value => $_smarty_tpl->tpl_vars['ikval']->value) {
?>
                   <?php $_smarty_tpl->_assignInScope('in', $_smarty_tpl->tpl_vars['ikval']->value['img']);?>
                   <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value[$_smarty_tpl->tpl_vars['in']->value];?>
" title="<?php echo $_smarty_tpl->tpl_vars['ikval']->value['title'];?>
"
                        name="fastExec<?php echo $_smarty_tpl->tpl_vars['kode']->value;?>
[<?php echo $_smarty_tpl->tpl_vars['tcversion_id']->value;?>
]"
                        id="fastExec<?php echo $_smarty_tpl->tpl_vars['kode']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['tcversion_id']->value;?>
"
                        onclick="javascript:saveExecStatus(<?php echo $_smarty_tpl->tpl_vars['tcvID']->value;?>
,'<?php echo $_smarty_tpl->tpl_vars['kode']->value;?>
');">&nbsp;
                 <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>  
                 <br />
                 <br />

                 <input type="hidden" name="save_and_next" 
                                      id="save_and_next" value="0">
                 <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['gui']->value->execStatusIconsNext, 'ikval', false, 'kode');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['kode']->value => $_smarty_tpl->tpl_vars['ikval']->value) {
?>
                   <?php $_smarty_tpl->_assignInScope('in', $_smarty_tpl->tpl_vars['ikval']->value['img']);?>
                   <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value[$_smarty_tpl->tpl_vars['in']->value];?>
" title="<?php echo $_smarty_tpl->tpl_vars['ikval']->value['title'];?>
"
                        name="fastExecNext<?php echo $_smarty_tpl->tpl_vars['kode']->value;?>
[<?php echo $_smarty_tpl->tpl_vars['tcversion_id']->value;?>
]"
                        id="fastExecNext<?php echo $_smarty_tpl->tpl_vars['kode']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['tcversion_id']->value;?>
"
                        onclick="javascript:saveExecStatus(<?php echo $_smarty_tpl->tpl_vars['tcvID']->value;?>
,'<?php echo $_smarty_tpl->tpl_vars['kode']->value;?>
','',1);">&nbsp;
                 <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>  
                 <br />
                 <br />
                  <input type="submit" name="move2next[<?php echo $_smarty_tpl->tpl_vars['tcvID']->value;?>
]" 
                      <?php echo $_smarty_tpl->tpl_vars['args_input_enable_mgmt']->value;?>

                      onclick="javascript:moveToNextTC(<?php echo $_smarty_tpl->tpl_vars['tcvID']->value;?>
);"
                      value="<?php echo $_smarty_tpl->tpl_vars['args_labels']->value['btn_next_tcase'];?>
" />
    		 			  <?php } else { ?>
     	    	        <input type="submit" id="do_bulk_save" name="do_bulk_save"
      	    	             value="<?php echo $_smarty_tpl->tpl_vars['args_labels']->value['btn_save_tc_exec_results'];?>
"/>
    		 			  <?php }?>       
    				</div>
    			</td>
    		</tr>
        <?php if ($_smarty_tpl->tpl_vars['args_save_type']->value == 'bulk' && $_smarty_tpl->tpl_vars['args_execution_time_cfields']->value != '') {?>
          <tr><td colspan="2">
  					<div id="cfields_exec_time_tcversionid_<?php echo $_smarty_tpl->tpl_vars['tcvID']->value;?>
" class="custom_field_container" 
  						style="background-color:#dddddd;">
            <?php echo $_smarty_tpl->tpl_vars['args_labels']->value['testcase_customfields'];?>

            <?php echo $_smarty_tpl->tpl_vars['args_execution_time_cfields']->value[0];?>
             </div> 
          </td></tr>
        <?php }?>
  		</table>
      
      <?php } else { ?>
        <input type="submit" name="move2next[<?php echo $_smarty_tpl->tpl_vars['tcvID']->value;?>
]" 
               <?php echo $_smarty_tpl->tpl_vars['args_input_enable_mgmt']->value;?>

               onclick="javascript:moveToNextTC(<?php echo $_smarty_tpl->tpl_vars['tcvID']->value;?>
);"
               value="<?php echo $_smarty_tpl->tpl_vars['args_labels']->value['btn_next_tcase'];?>
" />
      <?php }?>


      <?php if ($_smarty_tpl->tpl_vars['gui']->value->addIssueOp != '' && !is_null($_smarty_tpl->tpl_vars['gui']->value->addIssueOp) && !is_null($_smarty_tpl->tpl_vars['gui']->value->addIssueOp['type'])) {?>  
        <?php $_smarty_tpl->_assignInScope('ak', $_smarty_tpl->tpl_vars['gui']->value->addIssueOp['type']);?> 
        <hr> 
        <table id="addIssueFeedback">
        <tr>
          <td colspan="2" class="label"><?php echo $_smarty_tpl->tpl_vars['args_labels']->value['create_issue_feedback'];?>
</td>
        </tr>
  
        <?php if ($_smarty_tpl->tpl_vars['ak']->value == 'createIssue') {?>
          <tr>
            <td colspan="2">
              <div class="label"><?php echo $_smarty_tpl->tpl_vars['gui']->value->addIssueOp[$_smarty_tpl->tpl_vars['ak']->value]['msg'];?>
</div>
            </td>
          </tr>
        <?php } else { ?>
          <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['gui']->value->addIssueOp[$_smarty_tpl->tpl_vars['ak']->value], 'ikmsg', false, 'ik');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['ik']->value => $_smarty_tpl->tpl_vars['ikmsg']->value) {
?>
          <tr>
            <td colspan="2">
              <div class="label"><?php echo $_smarty_tpl->tpl_vars['ikmsg']->value['msg'];?>
</div>
            </td>
          </tr>
          <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
        <?php }?>
        </table>
        <hr>
      <?php }?>

      <table style="display:none;" id="issue_summary">
      <tr>
        <td colspan="2">
                    <div class="label"><?php echo $_smarty_tpl->tpl_vars['args_labels']->value['bug_summary'];?>
</div>
           <input type="text" id="bug_summary" name="bug_summary" value="<?php echo $_smarty_tpl->tpl_vars['gui']->value->bug_summary;?>
"
                  size="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'BUGSUMMARY_SIZE');?>
" maxlength="<?php echo $_smarty_tpl->tpl_vars['gui']->value->issueTrackerCfg->bugSummaryMaxLength;?>
">
        </td>
      </tr>

      <?php $_smarty_tpl->_assignInScope('itMetaData', $_smarty_tpl->tpl_vars['gui']->value->issueTrackerMetaData);?>
      <?php if ('' != $_smarty_tpl->tpl_vars['itMetaData']->value && null != $_smarty_tpl->tpl_vars['itMetaData']->value) {?>
        <tr>
        <td colspan="2">
        <?php $_smarty_tpl->_subTemplateRender("file:./issueTrackerMetadata.inc.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('useOnSteps'=>0), 0, false);
?>  
        </td>
        </tr>
      <?php }?>

      <tr>
        <td colspan="2">
          <div class="label"><?php echo $_smarty_tpl->tpl_vars['args_labels']->value['bug_description'];?>
</div>
          <textarea id="bug_notes" name="bug_notes" 
                  rows="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'BUGNOTES_ROWS');?>
" cols="<?php echo $_smarty_tpl->tpl_vars['gui']->value->issueTrackerCfg->bugSummaryMaxLength;?>
" ></textarea>          
        </td>
      </tr>

      <tr>
        <td colspan="2">
          <input type="checkbox" name="addLinkToTL"  id="addLinkToTL"
                 <?php if ($_smarty_tpl->tpl_vars['gui']->value->addLinkToTLChecked) {?> checked <?php }?> >
          <span class="label"><?php echo $_smarty_tpl->tpl_vars['args_labels']->value['add_link_to_tlexec'];?>
</span>
        </td>
      </tr>

      <tr>
        <td colspan="2">
          <input type="checkbox" name="addLinkToTLPrintView"
                 id="addLinkToTLPrintView"
                 <?php if ($_smarty_tpl->tpl_vars['gui']->value->addLinkToTLPrintViewChecked) {?> checked <?php }?> >
          <span class="label"><?php echo $_smarty_tpl->tpl_vars['args_labels']->value['add_link_to_tlexec_print_view'];?>
</span>
        </td>
      </tr>

      </table>
      
      </br>
      <div class="messages" style="align:center;">
      <?php echo $_smarty_tpl->tpl_vars['args_labels']->value['exec_not_run_result_note'];?>

      </div>


      <?php echo '<script'; ?>
>
      jQuery( document ).ready(function() {
          // IMPORTANT
          // For some chosen select I want on page load to be DISPLAY NONE
          // That's why I've changes from original example on the line where styles were applied
          // 
          jQuery(".chosen-select-artifact").chosen({ width: "35%" });

          // From https://github.com/harvesthq/chosen/issues/515
          jQuery(".chosen-select-artifact").each(function(){
          
          // take each select and put it as a child of the chosen container
          // this mean it'll position any validation messages correctly
          jQuery(this).next(".chosen-container").prepend(jQuery(this).detach());

          // apply all the styles, personally, I've added this to my stylesheet
          // TESTLINK NOTE
          jQuery(this).attr("style","display:none!important; position:absolute; clip:rect(0,0,0,0)");

          // to all of these events, trigger the chosen to open and receive focus
          jQuery(this).on("click focus keyup",function(event){
              jQuery(this).closest(".chosen-container").trigger("mousedown.chosen");
          });
      });
      });
      <?php echo '</script'; ?>
>
<?php }
}
