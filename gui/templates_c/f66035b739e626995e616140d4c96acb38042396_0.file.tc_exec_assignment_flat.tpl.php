<?php
/* Smarty version 3.1.33, created on 2026-03-09 13:09:11
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\plan\tc_exec_assignment_flat.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69aeb8676ebc32_24633277',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f66035b739e626995e616140d4c96acb38042396' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\plan\\tc_exec_assignment_flat.tpl',
      1 => 1744611206,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:inc_head.tpl' => 1,
    'file:inc_jsCheckboxes.tpl' => 1,
    'file:inc_del_onclick.tpl' => 1,
    'file:inc_update.tpl' => 1,
  ),
),false)) {
function content_69aeb8676ebc32_24633277 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\tl-uat\\vendor\\smarty\\smarty\\libs\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),));
?>

<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('var'=>"labels",'s'=>"user_bulk_assignment,btn_do,check_uncheck_all_checkboxes,th_id,
     btn_update_selected_tc,show_tcase_spec,can_not_execute,
     send_mail_to_tester,platform,no_testcase_available,chosen_blank_option,
     exec_assign_no_testcase,warning,check_uncheck_children_checkboxes,
     th_test_case,version,assigned_to,assign_to,note_keyword_filter,priority,
     check_uncheck_all_tc,execution,design,execution_history,btn_apply_assign,
     btn_save_assign,btn_remove_assignments,remove,btn_send_link,btn_remove_all_users,user_bulk_action,no_user_selected"),$_smarty_tpl ) );?>


<?php $_smarty_tpl->_subTemplateRender("file:inc_head.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('openHead'=>"yes"), 0, false);
$_smarty_tpl->_subTemplateRender("file:inc_jsCheckboxes.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
$_smarty_tpl->_subTemplateRender("file:inc_del_onclick.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['basehref']->value;?>
gui/javascript/shift_select.js"><?php echo '</script'; ?>
>

<?php echo '<script'; ?>
 type="text/javascript">
// Escape all messages (string)
var check_msg="<?php echo strtr($_smarty_tpl->tpl_vars['labels']->value['exec_assign_no_testcase'], array("\\" => "\\\\", "'" => "\\'", "\"" => "\\\"", "\r" => "\\r", "\n" => "\\n", "</" => "<\/" ));?>
";
var check_user_msg="<?php echo strtr($_smarty_tpl->tpl_vars['labels']->value['no_user_selected'], array("\\" => "\\\\", "'" => "\\'", "\"" => "\\\"", "\r" => "\\r", "\n" => "\\n", "</" => "<\/" ));?>
";

var alert_box_title = "<?php echo strtr($_smarty_tpl->tpl_vars['labels']->value['warning'], array("\\" => "\\\\", "'" => "\\'", "\"" => "\\\"", "\r" => "\\r", "\n" => "\\n", "</" => "<\/" ));?>
";

function check_action_precondition(container_id,action)
{
	if(checkbox_count_checked(container_id) <= 0) {
		alert_message(alert_box_title,check_msg);
		return false;
	}
  
  if (jQuery("#bulk_tester_div").val() == 0) {
    alert_message(alert_box_title,check_user_msg);
    return false;
  }

	return true;
}

/**
 * Uses JQuery.
 * Needed if select uses chosen plugin !!!
 */
function setComboIfCbx(oid,combo_id_prefix,oid4value)
{
  var f=document.getElementById(oid);
  var all_inputs = f.getElementsByTagName('input');
  var input_element;
  var check_id='';
  var apieces='';
  var combo_id_suffix='';
  var cb_id= new Array();
  var jdx=0;
  var idx=0;
  var cv;  

  // Build an array with the html select ids
  //  
  for(idx = 0; idx < all_inputs.length; idx++)
  {
    input_element=all_inputs[idx];    
    if(input_element.type == "checkbox" &&  
       input_element.checked  && !input_element.disabled)
    {
      check_id=input_element.id;
      
      // Consider the id a list with '_' as element separator
      apieces=check_id.split("_");
      
      // apieces.length-2 => test case id
      // apieces.length-1 => platform id
      combo_id_suffix=apieces[apieces.length-2] + '_' + apieces[apieces.length-1];
      cb_id[jdx]=combo_id_prefix + combo_id_suffix;
      jdx++;
    } 
  }

  // To avoid issues with $  
  jQuery.noConflict();

  // now set the combos
  for(idx = 0; idx < cb_id.length; idx++)
  {
    value_to_assign = String(jQuery('#' + oid4value).val()); 

    if(value_to_assign == 0)
    {
      jQuery('#' + cb_id[idx]).val(value_to_assign);
    }  
    else
    {
      cv = value_to_assign.split(",");
      var zx = cv.indexOf(0);
      if(zx != -1) 
      {
        cv.splice(zx, 1);
      }
      jQuery('#' + cb_id[idx]).val(cv);
    }  
    jQuery('#' + cb_id[idx]).trigger("chosen:updated");  // needed by chosen
  }
}
<?php echo '</script'; ?>
>

</head>
   
<?php $_smarty_tpl->_assignInScope('add_cb', "achecked_tc");?>

<body class="fixedheader">
<form id='tc_exec_assignment' name='tc_exec_assignment' method='post'>

      <div id="header-wrap" style="z-index:999;height:220px;"> <!-- header-wrap -->
	<h1 class="title"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->main_descr, ENT_QUOTES, 'UTF-8', true);?>
</h1>
  <?php if ($_smarty_tpl->tpl_vars['gui']->value->has_tc) {?>
    <?php $_smarty_tpl->_subTemplateRender("file:inc_update.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('result'=>$_smarty_tpl->tpl_vars['sqlResult']->value,'refresh'=>"yes"), 0, false);
?>
    <div class="workBack" style="margin-top: 5px; padding: 5px; background-color: #FFFFCC; border: 1px solid #CCC; font-size: 90%;">
      <strong>Tip:</strong> You can select multiple test cases at once by clicking the first checkbox, then holding SHIFT and clicking another checkbox. All test cases between them will be selected.
    </div>
	<div class="groupBtn">
		<div>
			<?php if ($_smarty_tpl->tpl_vars['gui']->value->usePlatforms) {?>
			<select id="select_platform">
				<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['gui']->value->bulk_platforms),$_smarty_tpl);?>

			</select>
			<?php } else { ?>
			<input type="hidden" id="select_platform" value="0">
			<?php }?>
			<button onclick="cs_all_checkbox_in_div_with_platform('tc_exec_assignment_cb', '<?php echo $_smarty_tpl->tpl_vars['add_cb']->value;?>
', Ext.get('select_platform').getValue()); return false"><?php echo $_smarty_tpl->tpl_vars['labels']->value['check_uncheck_all_tc'];?>
</button>
		</div>
    <br>

		<div>
      <fieldset>
			<img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['user_group'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['user_bulk_assignment'];?>
">
      <?php echo $_smarty_tpl->tpl_vars['labels']->value['user_bulk_action'];?>
<br>
      <select class="chosen-bulk-select" multiple="multiple"
              name="bulk_tester_div[]" id="bulk_tester_div" >
				<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['gui']->value->testers,'selected'=>0),$_smarty_tpl);?>

			</select>
			<input type='button' name='bulk_user_assignment' id='bulk_user_assignment'
				onclick='if(check_action_precondition("tc_exec_assignment","default"))
						        setComboIfCbx("tc_exec_assignment_cb","tester_for_tcid_",
                                  "bulk_tester_div")'
				value="<?php echo $_smarty_tpl->tpl_vars['labels']->value['btn_apply_assign'];?>
" />
			<input type="submit" name="doActionButton" id="doActionButton" value="<?php echo $_smarty_tpl->tpl_vars['labels']->value['btn_save_assign'];?>
" />
      <input type="hidden" name="doAction" id="doAction" value='std' />

      <input type='button' name='bulk_user_remove' id='bulk_user_remove'
        onclick='if(check_action_precondition("tc_exec_assignment","default"))
                 { doAction.value="doBulkUserRemove"; tc_exec_assignment.submit(); }'
        value="<?php echo $_smarty_tpl->tpl_vars['labels']->value['btn_remove_assignments'];?>
" />

			<span style="margin-left:20px;">
        <img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['email'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['send_mail_to_tester'];?>
">
        <input type="checkbox" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['send_mail_to_tester'];?>
"
          name="send_mail" id="send_mail" <?php echo $_smarty_tpl->tpl_vars['gui']->value->send_mail_checked;?>
 />
			</span>
      </fieldset>

		</div>
    <br>

    <div>
      <input type="submit" name="doRemoveAll" id="doRemoveAll" value="<?php echo $_smarty_tpl->tpl_vars['labels']->value['btn_remove_all_users'];?>
" />
      <input type="button" name="linkByMail" 
             id="linkByMail" 
             onclick="doAction.value='linkByMail';tc_exec_assignment.submit();" 
             value="<?php echo $_smarty_tpl->tpl_vars['labels']->value['btn_send_link'];?>
" />
      
      <input type="hidden" name="targetFeatureBulk" id="targetFeatureBulk" value="0"/>
      <input type="hidden" name="targetUserBulk" id="targetUserBulk" value="0"/>

    </div>

	</div>
  <?php } else { ?>
	  <div class="workBack"><?php echo $_smarty_tpl->tpl_vars['labels']->value['no_testcase_available'];?>
</div>
  <?php }?>
	</div> <!-- header-wrap -->

  <p>&nbsp;<p>&nbsp;<p>
  <?php if ($_smarty_tpl->tpl_vars['gui']->value->has_tc) {?>
   <div class="workBack" id="tc_exec_assignment_cb">
    <input type="hidden" name="targetFeature" id="targetFeature" value="0"/>
    <input type="hidden" name="targetUser" id="targetUser" value="0"/>

	  <?php $_smarty_tpl->_assignInScope('table_counter', 0);?>
	  <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['gui']->value->items, 'ts', false, 'idx', 'div_drawing', array (
  'iteration' => true,
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['idx']->value => $_smarty_tpl->tpl_vars['ts']->value) {
$_smarty_tpl->tpl_vars['__smarty_foreach_div_drawing']->value['iteration']++;
?>
	    <?php $_smarty_tpl->_assignInScope('ts_id', $_smarty_tpl->tpl_vars['ts']->value['testsuite']['id']);?>
	    <?php $_smarty_tpl->_assignInScope('div_id', "div_".((string)$_smarty_tpl->tpl_vars['ts_id']->value));?>
	    <?php if ($_smarty_tpl->tpl_vars['ts_id']->value != '') {?>
	      <div id="<?php echo $_smarty_tpl->tpl_vars['div_id']->value;?>
" style="margin-left:0px; border:1;">
        <br />
	      <h3 class="testlink"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['ts']->value['testsuite']['name'], ENT_QUOTES, 'UTF-8', true);?>
</h3>

                <input type="hidden" name="add_value_<?php echo $_smarty_tpl->tpl_vars['ts_id']->value;?>
"  id="add_value_<?php echo $_smarty_tpl->tpl_vars['ts_id']->value;?>
"  value="0" />

    	  <?php if ($_smarty_tpl->tpl_vars['ts']->value['write_buttons'] == 'yes') {?>
          <?php if ($_smarty_tpl->tpl_vars['ts']->value['testcase_qty'] > 0) {?>
	          <?php $_smarty_tpl->_assignInScope('table_counter', $_smarty_tpl->tpl_vars['table_counter']->value+1);?>
            <table cellspacing="0" style="font-size:small;" width="100%" id="the-table-<?php echo $_smarty_tpl->tpl_vars['table_counter']->value;?>
" class="tableruler">
            			      			      <thead>
			      <tr style="background-color:#059; font-weight:bold; color:white">
			      	<th width="35px" align="center">
			          <img class="clickable" src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['toggle_all'];?>
"
			               onclick='cs_all_checkbox_in_div("<?php echo $_smarty_tpl->tpl_vars['div_id']->value;?>
","<?php echo $_smarty_tpl->tpl_vars['add_cb']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['ts_id']->value;?>
_","add_value_<?php echo $_smarty_tpl->tpl_vars['ts_id']->value;?>
");'
                     title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['check_uncheck_all_checkboxes'];?>
" />
			      	</th>
              <th><?php echo $_smarty_tpl->tpl_vars['labels']->value['th_test_case'];?>
&nbsp;<?php echo $_smarty_tpl->tpl_vars['gsmarty_gui']->value->role_separator_open;?>

              	<?php echo $_smarty_tpl->tpl_vars['labels']->value['version'];
echo $_smarty_tpl->tpl_vars['gsmarty_gui']->value->role_separator_close;?>
</th>
              	
              <?php if ($_smarty_tpl->tpl_vars['gui']->value->platforms != '') {?>
			      	  <th><?php echo $_smarty_tpl->tpl_vars['labels']->value['platform'];?>
</th>
              <?php }?>	
			      	<?php if ($_smarty_tpl->tpl_vars['session']->value['testprojectOptions']->testPriorityEnabled) {?>
			      	  <th align="center"><?php echo $_smarty_tpl->tpl_vars['labels']->value['priority'];?>
</th>
			      	<?php }?>
              <th style="align:left;">&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['labels']->value['assigned_to'];?>
</th>
              <th style="align:center;">&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['labels']->value['assign_to'];?>
</th>
            </tr>
			      </thead>
                        <tbody>  
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['ts']->value['testcases'], 'tcase');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['tcase']->value) {
?>

                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['tcase']->value['feature_id'], 'feature', false, 'platform_id');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['platform_id']->value => $_smarty_tpl->tpl_vars['feature']->value) {
?>
                <?php if ($_smarty_tpl->tpl_vars['tcase']->value['linked_version_id'] != 0) {?>
                  <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['tcase']->value['user_id'][$_smarty_tpl->tpl_vars['platform_id']->value], 'userItem', false, 'udx', 'testerSet', array (
  'iteration' => true,
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['udx']->value => $_smarty_tpl->tpl_vars['userItem']->value) {
$_smarty_tpl->tpl_vars['__smarty_foreach_testerSet']->value['iteration']++;
?>
                    <?php $_smarty_tpl->_assignInScope('userID', 0);?>
             	      <?php if (isset($_smarty_tpl->tpl_vars['tcase']->value['user_id'][$_smarty_tpl->tpl_vars['platform_id']->value][$_smarty_tpl->tpl_vars['udx']->value])) {?> 
                      <?php $_smarty_tpl->_assignInScope('userID', $_smarty_tpl->tpl_vars['tcase']->value['user_id'][$_smarty_tpl->tpl_vars['platform_id']->value][$_smarty_tpl->tpl_vars['udx']->value]);?> 
                    <?php }?> 

              	    <tr>
                    <?php if ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_testerSet']->value['iteration']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_testerSet']->value['iteration'] : null) == 1) {?>
              	    	<td>
                      		<input type="checkbox" name='<?php echo $_smarty_tpl->tpl_vars['add_cb']->value;?>
[<?php echo $_smarty_tpl->tpl_vars['tcase']->value['id'];?>
][<?php echo $_smarty_tpl->tpl_vars['platform_id']->value;?>
]' align="middle"
                    			                        id='<?php echo $_smarty_tpl->tpl_vars['add_cb']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['ts_id']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['tcase']->value['id'];?>
_<?php echo $_smarty_tpl->tpl_vars['platform_id']->value;?>
' 
                      		                        value="<?php echo $_smarty_tpl->tpl_vars['tcase']->value['linked_version_id'];?>
" />
                    			<input type="hidden" name="a_tcid[<?php echo $_smarty_tpl->tpl_vars['tcase']->value['id'];?>
][<?php echo $_smarty_tpl->tpl_vars['platform_id']->value;?>
]" 
                    			                     value="<?php echo $_smarty_tpl->tpl_vars['tcase']->value['linked_version_id'];?>
" />
                    			<input type="hidden" name="has_prev_assignment[<?php echo $_smarty_tpl->tpl_vars['tcase']->value['id'];?>
][<?php echo $_smarty_tpl->tpl_vars['platform_id']->value;?>
]" 
                    			                     value="<?php echo $_smarty_tpl->tpl_vars['userID']->value;?>
" />
                    			<input type="hidden" name="feature_id[<?php echo $_smarty_tpl->tpl_vars['tcase']->value['id'];?>
][<?php echo $_smarty_tpl->tpl_vars['platform_id']->value;?>
]" 
                    			                     value="<?php echo $_smarty_tpl->tpl_vars['tcase']->value['feature_id'][$_smarty_tpl->tpl_vars['platform_id']->value];?>
" />
              	    	</td>
              	    	<td>
              	    		<img class="clickable" src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['history_small'];?>
"
              	    		     onclick="javascript:openExecHistoryWindow(<?php echo $_smarty_tpl->tpl_vars['tcase']->value['id'];?>
);"
              	    		     title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['execution_history'];?>
" />
              	    		<img class="clickable" src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['exec_icon'];?>
"
              	    		     onclick="javascript:openExecutionWindow(<?php echo $_smarty_tpl->tpl_vars['tcase']->value['id'];?>
,<?php echo $_smarty_tpl->tpl_vars['tcase']->value['linked_version_id'];?>
,<?php echo $_smarty_tpl->tpl_vars['gui']->value->build_id;?>
,<?php echo $_smarty_tpl->tpl_vars['gui']->value->tplan_id;?>
,<?php echo $_smarty_tpl->tpl_vars['platform_id']->value;?>
);"
              	    		     title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['execution'];?>
" />
              	    		<img class="clickable" src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['edit'];?>
"
              	    		     onclick="javascript:openTCaseWindow(<?php echo $_smarty_tpl->tpl_vars['tcase']->value['id'];?>
,<?php echo $_smarty_tpl->tpl_vars['tcase']->value['linked_version_id'];?>
);"
              	    		     title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['design'];?>
" />
              	    		<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->testCasePrefix, ENT_QUOTES, 'UTF-8', true);
echo htmlspecialchars($_smarty_tpl->tpl_vars['tcase']->value['external_id'], ENT_QUOTES, 'UTF-8', true);
echo $_smarty_tpl->tpl_vars['gsmarty_gui']->value->title_separator_1;
echo htmlspecialchars($_smarty_tpl->tpl_vars['tcase']->value['name'], ENT_QUOTES, 'UTF-8', true);?>

              	    		&nbsp;<?php echo $_smarty_tpl->tpl_vars['gsmarty_gui']->value->role_separator_open;?>
 <?php echo $_smarty_tpl->tpl_vars['tcase']->value['tcversions'][$_smarty_tpl->tpl_vars['tcase']->value['linked_version_id']];?>

              	    		<?php echo $_smarty_tpl->tpl_vars['gsmarty_gui']->value->role_separator_close;?>

              	    	</td>

                      <?php if ($_smarty_tpl->tpl_vars['gui']->value->platforms != '') {?>
  			      	        <td><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->platforms[$_smarty_tpl->tpl_vars['platform_id']->value], ENT_QUOTES, 'UTF-8', true);?>
</td>
                      <?php }?>	

              	    	<?php if ($_smarty_tpl->tpl_vars['session']->value['testprojectOptions']->testPriorityEnabled) {?>
              	    		<td align="center">
                        <?php if (isset($_smarty_tpl->tpl_vars['gui']->value->priority_labels[$_smarty_tpl->tpl_vars['tcase']->value['priority']])) {
echo $_smarty_tpl->tpl_vars['gui']->value->priority_labels[$_smarty_tpl->tpl_vars['tcase']->value['priority']];
}?></td>
              	    	<?php }?>
                      
                    <?php } else { ?>
                        <td>&nbsp;</td><td>&nbsp;</td>
                        <?php if ($_smarty_tpl->tpl_vars['gui']->value->platforms != '') {?><td>&nbsp;</td><?php }?> 
                        <?php if ($_smarty_tpl->tpl_vars['session']->value['testprojectOptions']->testPriorityEnabled) {?><td>&nbsp;</td><?php }?>
                    <?php }?> 
              	    	<td style="align:left;">
                        &nbsp;&nbsp;&nbsp;&nbsp;
              	    		<?php if ($_smarty_tpl->tpl_vars['userID']->value > 0 && $_smarty_tpl->tpl_vars['gui']->value->users[$_smarty_tpl->tpl_vars['userID']->value] != '') {?>
                        <img class="clickable" src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['remove'];?>
"
                             onclick="doAction.value='doRemove';targetFeature.value=<?php echo $_smarty_tpl->tpl_vars['tcase']->value['feature_id'][$_smarty_tpl->tpl_vars['platform_id']->value];?>
;targetUser.value=<?php echo $_smarty_tpl->tpl_vars['userID']->value;?>
;tc_exec_assignment.submit();"
                             title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['remove'];?>
" /> 
                          <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->users[$_smarty_tpl->tpl_vars['userID']->value], ENT_QUOTES, 'UTF-8', true);?>

                          <?php if ($_smarty_tpl->tpl_vars['gui']->value->testers[$_smarty_tpl->tpl_vars['userID']->value] == '') {
echo $_smarty_tpl->tpl_vars['labels']->value['can_not_execute'];
}?>                         <?php }?>                          
              	    	</td>
                      
                      <?php if ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_testerSet']->value['iteration']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_testerSet']->value['iteration'] : null) == 1) {?>
                        <td align="center">
                      		  		<select class="chosen-select" multiple="multiple" 
                                        data-placeholder="<?php echo $_smarty_tpl->tpl_vars['labels']->value['chosen_blank_option'];?>
"
                                        name="tester_for_tcid[<?php echo $_smarty_tpl->tpl_vars['tcase']->value['id'];?>
][<?php echo $_smarty_tpl->tpl_vars['platform_id']->value;?>
][]" 
                      		  		        id="tester_for_tcid_<?php echo $_smarty_tpl->tpl_vars['tcase']->value['id'];?>
_<?php echo $_smarty_tpl->tpl_vars['platform_id']->value;?>
"
                      		  		        onchange='javascript: set_checkbox("<?php echo $_smarty_tpl->tpl_vars['add_cb']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['ts_id']->value;?>
_<?php echo $_smarty_tpl->tpl_vars['tcase']->value['id'];?>
_<?php echo $_smarty_tpl->tpl_vars['platform_id']->value;?>
",1)' >
                                 <?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['gui']->value->testers),$_smarty_tpl);?>

                      				  </select>
                        </td>
                      <?php } else { ?>
                        <td>&nbsp;</td>
                      <?php }?>

                    </tr>
                  <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>                 <?php }?> 		
              <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>   
            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>             </tbody>
          </table>
          <?php }?>
      <?php }?> 
      <?php if ($_smarty_tpl->tpl_vars['gui']->value->items_qty == (isset($_smarty_tpl->tpl_vars['__smarty_foreach_div_drawing']->value['iteration']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_div_drawing']->value['iteration'] : null)) {?>
          <?php $_smarty_tpl->_assignInScope('next_level', 0);?>
      <?php } else { ?>
          <?php $_smarty_tpl->_assignInScope('next_level', 0);?>
      <?php }?>
    
    <?php }?>     </div>
	<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
	</div>
 <?php }?>
  
</form>
<?php echo '<script'; ?>
>
jQuery( document ).ready(function() {
jQuery(".chosen-select").chosen({ width: "85%", allow_single_deselect: true });
jQuery(".chosen-bulk-select").chosen({ width: "35%", allow_single_deselect: true });

});
<?php echo '</script'; ?>
>
</body>
</html>
<?php }
}
