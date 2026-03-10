<?php
/* Smarty version 3.1.33, created on 2026-03-09 13:09:49
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\plan\planAddTC_m1.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69aeb88dbf6dd3_73170303',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '78400350393488451c89b843003113bd87631675' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\plan\\planAddTC_m1.tpl',
      1 => 1744611690,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:inc_head.tpl' => 1,
    'file:inc_jsCheckboxes.tpl' => 1,
    'file:inc_ext_js.tpl' => 1,
    'file:inc_help.tpl' => 1,
    'file:inc_update.tpl' => 1,
    'file:inc_refreshTreeWithFilters.tpl' => 1,
  ),
),false)) {
function content_69aeb88dbf6dd3_73170303 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\tl-uat\\vendor\\smarty\\smarty\\libs\\plugins\\function.html_options.php','function'=>'smarty_function_html_options',),));
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('var'=>"labels",'s'=>'note_keyword_filter, check_uncheck_all_for_remove,
             th_id,th_test_case,version,execution_order,th_platform,
             no_testcase_available,btn_save_custom_fields,send_mail_to_tester,
             inactive_testcase,btn_save_exec_order,info_added_on_date,
             executed_can_not_be_removed,added_on_date,btn_save_platform,
             check_uncheck_all_checkboxes,removal_tc,show_tcase_spec,
             tester_assignment_on_add,adding_tc,check_uncheck_all_tc,for,
             build_to_assign_on_add,importance,execution,design,
             execution_history,warning_remove_executed,th_status,
             note_platform_filter'),$_smarty_tpl ) );?>


   
<?php $_smarty_tpl->_assignInScope('add_cb', "achecked_tc");?> 
<?php $_smarty_tpl->_assignInScope('rm_cb', "remove_checked_tc");?>

<?php
$_smarty_tpl->smarty->ext->configLoad->_loadConfigFile($_smarty_tpl, "input_dimensions.conf", "planAddTC", 0);
?>

<?php $_smarty_tpl->_subTemplateRender("file:inc_head.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('openHead'=>"yes"), 0, false);
$_smarty_tpl->_subTemplateRender("file:inc_jsCheckboxes.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<?php $_smarty_tpl->_subTemplateRender("file:inc_ext_js.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['basehref']->value;?>
gui/javascript/shift_select.js"><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript">
<!--
js_warning_remove_executed = '<?php echo $_smarty_tpl->tpl_vars['labels']->value['warning_remove_executed'];?>
';
js_remove_executed_counter = 0;

function updateRemoveExecCounter(oid)
{
	var obj = document.getElementById(oid)
	if( obj.checked )
	{
		js_remove_executed_counter++;
	}
	else
	{
		js_remove_executed_counter--;
	}
}

function checkDelete(removeExecCounter)
{
	if(js_remove_executed_counter > 0)
	{
		return confirm(js_warning_remove_executed);
	}
	else
	{
		return true;
	}
}


function tTip(tcID,vID)
{
	var fUrl = fRoot+'lib/ajax/gettestcasesummary.php?tcase_id=';
	new Ext.ToolTip({
        target: 'tooltip-'+tcID,
        width: 500,
        autoLoad: { url: fUrl+tcID+'&tcversion_id='+vID },
        dismissDelay: 0,
        trackMouse: true
    });
}

function showTT(e)
{
	alert(e);
}

js_tcase_importance = new Array();
js_tcase_wkfstatus = new Array();

attrDomain = new Object();
attrDomain.importance = new Array();
attrDomain.wkfstatus = new Array();

<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['gsmarty_option_importance']->value, 'item', false, 'key');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['item']->value) {
?>
	attrDomain.importance[<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
] = "<?php echo $_smarty_tpl->tpl_vars['item']->value;?>
";
<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['gsmarty_option_wkfstatus']->value, 'item', false, 'key');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['item']->value) {
?>
  attrDomain.wkfstatus[<?php echo $_smarty_tpl->tpl_vars['key']->value;?>
] = "<?php echo $_smarty_tpl->tpl_vars['item']->value;?>
";
<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>


// Update test case attributes when selecting a different test case version
// - workflow status
// - importance
//
function updTCAttr(tcID,tcvID) 
{
  var impOID = "importance_"+tcID;
  var wkfOID = "wkfstatus_"+tcID;
  var val;
  var poid;

  val = js_tcase_importance[tcID][tcvID];
	poid = document.getElementById(impOID);
  poid.firstChild.nodeValue = attrDomain.importance[val];

  val = js_tcase_wkfstatus[tcID][tcvID];
  poid = document.getElementById(wkfOID);
  poid.firstChild.nodeValue = attrDomain.wkfstatus[val];
}

Ext.onReady(function(){ 
<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['gui']->value->items, 'info', false, 'idx');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['idx']->value => $_smarty_tpl->tpl_vars['info']->value) {
?>
  <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['info']->value['testcases'], 'tcversionInfo', false, 'tcidx');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['tcidx']->value => $_smarty_tpl->tpl_vars['tcversionInfo']->value) {
?>
   <?php $_smarty_tpl->_assignInScope('tcversionLinked', $_smarty_tpl->tpl_vars['tcversionInfo']->value['linked_version_id']);?>
	   tTip(<?php echo $_smarty_tpl->tpl_vars['tcidx']->value;?>
,<?php echo $_smarty_tpl->tpl_vars['tcversionLinked']->value;?>
);
  <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>  
<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
});
//-->
<?php echo '</script'; ?>
>
</head>
<body class="fixedheader">
<form name="addTcForm" id="addTcForm" method="post" 
      onSubmit="javascript:return checkDelete(js_remove_executed_counter);">

  <div id="header-wrap">
	  	<h1 class="title">
        <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->pageTitle, ENT_QUOTES, 'UTF-8', true);
echo $_smarty_tpl->tpl_vars['tlCfg']->value->gui->title_separator_2;
echo $_smarty_tpl->tpl_vars['gui']->value->actionTitle;?>

	  	  <?php $_smarty_tpl->_subTemplateRender("file:inc_help.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('helptopic'=>"hlp_planAddTC",'show_help_icon'=>true), 0, false);
?>
        <div class="workBack" style="margin-top: 5px; padding: 5px; color: black; background-color: #FFFFCC; border: 1px solid #CCC; font-size: 90%;">
          <strong>Tip:</strong> You can select multiple test cases at once by clicking the first checkbox, then holding SHIFT and clicking another checkbox. All test cases between them will be selected.
        </div>
	  	</h1>

	    <?php if ($_smarty_tpl->tpl_vars['gui']->value->has_tc) {?>
	  	  <?php $_smarty_tpl->_subTemplateRender("file:inc_update.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('result'=>$_smarty_tpl->tpl_vars['sqlResult']->value), 0, false);
?>
        
    		    		    		<?php if ($_smarty_tpl->tpl_vars['gui']->value->build['count'] && $_smarty_tpl->tpl_vars['gui']->value->canAssignExecTask) {?>
       		<div class="groupBtn">
      				<?php echo $_smarty_tpl->tpl_vars['labels']->value['tester_assignment_on_add'];?>

      				<select name="testerID"
      				        id="testerID">
      					<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['gui']->value->testers,'selected'=>$_smarty_tpl->tpl_vars['gui']->value->testerID),$_smarty_tpl);?>

      				</select>
      				
      				<?php echo $_smarty_tpl->tpl_vars['labels']->value['build_to_assign_on_add'];?>

      				<select name="build_id">
      				<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['gui']->value->build['items'],'selected'=>$_smarty_tpl->tpl_vars['gui']->value->build['selected']),$_smarty_tpl);?>

      				</select>
      		
      				<input type="checkbox" name="send_mail" id="send_mail" <?php echo $_smarty_tpl->tpl_vars['gui']->value->send_mail_checked;?>
/>
      				<?php echo $_smarty_tpl->tpl_vars['labels']->value['send_mail_to_tester'];?>

          </div>

		    <?php }?> 		    
    	  <div class="groupBtn">
    			<div style="float: left; margin-right: 2em">
    				<?php echo $_smarty_tpl->tpl_vars['labels']->value['check_uncheck_all_tc'];?>

    				<?php if ($_smarty_tpl->tpl_vars['gui']->value->usePlatforms) {?>
      				<select id="select_platform">
      					<?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['gui']->value->bulk_platforms),$_smarty_tpl);?>

      				</select>
    				<?php } else { ?>
      				<input type="hidden" id="select_platform" value="0">
    				<?php }?>
    				<?php echo $_smarty_tpl->tpl_vars['labels']->value['for'];?>

    				<?php if ($_smarty_tpl->tpl_vars['gui']->value->full_control) {?>
      				<button
               onclick="cs_all_checkbox_in_div_with_platform('addTcForm', '<?php echo $_smarty_tpl->tpl_vars['add_cb']->value;?>
', Ext.get('select_platform').getValue()); return false"><?php echo $_smarty_tpl->tpl_vars['labels']->value['adding_tc'];?>
</button>
    				<?php }?>
    				<button onclick="cs_all_checkbox_in_div_with_platform('addTcForm', '<?php echo $_smarty_tpl->tpl_vars['rm_cb']->value;?>
', Ext.get('select_platform').getValue()); return false"><?php echo $_smarty_tpl->tpl_vars['labels']->value['removal_tc'];?>
</button>
    			</div>
    	  	<input type="hidden" name="doAction" id="doAction" value="default" />
    	  	<input type="submit" name="doAddRemove" value="<?php echo $_smarty_tpl->tpl_vars['gui']->value->buttonValue;?>
"
    	  	  		     onclick="doAction.value=this.name" />
    	  	<?php if ($_smarty_tpl->tpl_vars['gui']->value->full_control == 1) {?>
    	  	  <input type="submit" name="doReorder" value="<?php echo $_smarty_tpl->tpl_vars['labels']->value['btn_save_exec_order'];?>
"
    	  	  		       onclick="doAction.value=this.name" />
            
    	  	  <?php if ($_smarty_tpl->tpl_vars['gui']->value->drawSaveCFieldsButton) {?>
    	  	  		  <input type="submit" name="doSaveCustomFields" value="<?php echo $_smarty_tpl->tpl_vars['labels']->value['btn_save_custom_fields'];?>
"
    	  	  			       onclick="doAction.value=this.name" />
    	  	  <?php }?>
    	  	  <?php if ($_smarty_tpl->tpl_vars['gui']->value->drawSavePlatformsButton) {?>
    	  	  		  <input type="submit" name="doSavePlatforms" value="<?php echo $_smarty_tpl->tpl_vars['labels']->value['btn_save_platform'];?>
"
    	  	  			       onclick="doAction.value=this.name" />
    	  	  <?php }?>
    	  	<?php }?>
          <p>
          <div style="margin: 10px; font-size: smaller; border: thin solid; padding:3px">
            <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->status_feedback, ENT_QUOTES, 'UTF-8', true);?>

            <?php if ($_smarty_tpl->tpl_vars['gui']->value->keywords_filter_feedback != '') {?>
                <br/><?php echo $_smarty_tpl->tpl_vars['labels']->value['note_keyword_filter'];?>
: <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->keywords_filter_feedback, ENT_QUOTES, 'UTF-8', true);?>

            <?php }?>
            <?php if ($_smarty_tpl->tpl_vars['gui']->value->platforms_filter_feedback != '') {?>
                <br/><?php echo $_smarty_tpl->tpl_vars['labels']->value['note_platform_filter'];?>
: <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->platforms_filter_feedback, ENT_QUOTES, 'UTF-8', true);?>

            <?php }?>
          </div>  
    	  </div>
      <?php } else { ?>
  	      <div class="info"><?php echo $_smarty_tpl->tpl_vars['labels']->value['no_testcase_available'];?>
</div>
  	  <?php }?>  
  </div> <!-- header-wrap -->

  <?php if ($_smarty_tpl->tpl_vars['gui']->value->has_tc) {?>
    <div class="workBack" id="workback">
                	<?php $_smarty_tpl->_assignInScope('item_number', 0);?>
    	<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['gui']->value->items, 'ts', false, NULL, 'tSuiteLoop', array (
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['ts']->value) {
?>
    		<?php $_smarty_tpl->_assignInScope('item_number', $_smarty_tpl->tpl_vars['item_number']->value+1);?>
    		<?php $_smarty_tpl->_assignInScope('ts_id', $_smarty_tpl->tpl_vars['ts']->value['testsuite']['id']);?>
    		<?php $_smarty_tpl->_assignInScope('div_id', "div_".((string)$_smarty_tpl->tpl_vars['ts_id']->value));?>
    	      		<div id="<?php echo $_smarty_tpl->tpl_vars['div_id']->value;?>
"  style="margin:0px 0px 0px <?php echo $_smarty_tpl->tpl_vars['ts']->value['level'];?>
0px;"><h2 class="testlink"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['ts']->value['testsuite']['name'], ENT_QUOTES, 'UTF-8', true);?>
</h2><?php if ($_smarty_tpl->tpl_vars['item_number']->value == 1) {?><hr /><?php }?>           <input type="hidden" name="add_value_<?php echo $_smarty_tpl->tpl_vars['ts_id']->value;?>
"  id="add_value_<?php echo $_smarty_tpl->tpl_vars['ts_id']->value;?>
"  value="0" /><input type="hidden" name="rm_value_<?php echo $_smarty_tpl->tpl_vars['ts_id']->value;?>
"  id="rm_value_<?php echo $_smarty_tpl->tpl_vars['ts_id']->value;?>
"  value="0" /><?php if (($_smarty_tpl->tpl_vars['gui']->value->full_control && $_smarty_tpl->tpl_vars['ts']->value['testcase_qty'] > 0) || $_smarty_tpl->tpl_vars['ts']->value['linked_testcase_qty'] > 0) {?><table cellspacing="0" border="0" style="font-size:small;" width="100%"><tr style="background-color:blue;font-weight:bold;color:white"><td width="5" align="center"><?php if ($_smarty_tpl->tpl_vars['gui']->value->full_control) {?><img class="clickable" src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['toggle_all'];?>
"onclick='cs_all_checkbox_in_div("<?php echo $_smarty_tpl->tpl_vars['div_id']->value;?>
","<?php echo $_smarty_tpl->tpl_vars['add_cb']->value;?>
","add_value_<?php echo $_smarty_tpl->tpl_vars['ts_id']->value;?>
");'title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['check_uncheck_all_checkboxes'];?>
" /><?php } else { ?>&nbsp;<?php }?></td><?php if ($_smarty_tpl->tpl_vars['gui']->value->usePlatforms) {?> <td><?php echo $_smarty_tpl->tpl_vars['labels']->value['th_platform'];?>
</td> <?php }?><td><?php echo $_smarty_tpl->tpl_vars['labels']->value['th_test_case'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['labels']->value['version'];?>
</td><td><?php echo $_smarty_tpl->tpl_vars['labels']->value['th_status'];?>
</td><?php if ($_smarty_tpl->tpl_vars['gui']->value->priorityEnabled) {?> <td><?php echo $_smarty_tpl->tpl_vars['labels']->value['importance'];?>
</td> <?php }?><td align="center"><img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['execution_order'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['execution_order'];?>
" /></td><?php if ($_smarty_tpl->tpl_vars['ts']->value['linked_testcase_qty'] > 0) {?><td>&nbsp;</td><td><img class="clickable" src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['disconnect'];?>
"onclick='cs_all_checkbox_in_div("<?php echo $_smarty_tpl->tpl_vars['div_id']->value;?>
","<?php echo $_smarty_tpl->tpl_vars['rm_cb']->value;?>
","rm_value_<?php echo $_smarty_tpl->tpl_vars['ts_id']->value;?>
");'title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['check_uncheck_all_for_remove'];?>
" /></td><td align="center"><img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['date'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['added_on_date'];?>
" /></td><?php }?></tr><?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['ts']->value['testcases'], 'tcase', false, NULL, 'tCaseLoop', array (
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['tcase']->value) {
$_smarty_tpl->_assignInScope('is_active', 0);
$_smarty_tpl->_assignInScope('linked_version_id', $_smarty_tpl->tpl_vars['tcase']->value['linked_version_id']);
$_smarty_tpl->_assignInScope('tcID', $_smarty_tpl->tpl_vars['tcase']->value['id']);
if ($_smarty_tpl->tpl_vars['linked_version_id']->value != 0) {
if ($_smarty_tpl->tpl_vars['tcase']->value['tcversions_active_status'][$_smarty_tpl->tpl_vars['linked_version_id']->value] == 1) {
$_smarty_tpl->_assignInScope('is_active', 1);
}
} else {
if ($_smarty_tpl->tpl_vars['tcase']->value['tcversions_qty'] != 0) {
$_smarty_tpl->_assignInScope('is_active', 1);
}
}?>                <?php if ($_smarty_tpl->tpl_vars['is_active']->value || $_smarty_tpl->tpl_vars['linked_version_id']->value != 0) {
if ($_smarty_tpl->tpl_vars['gui']->value->full_control || $_smarty_tpl->tpl_vars['linked_version_id']->value != 0) {
$_smarty_tpl->_assignInScope('drawPlatformChecks', 0);
if ($_smarty_tpl->tpl_vars['gui']->value->usePlatforms) {?>                      <?php if (!isset($_smarty_tpl->tpl_vars['tcase']->value['feature_id'][0])) {
$_smarty_tpl->_assignInScope('drawPlatformChecks', 1);
}
}?><tr<?php if ($_smarty_tpl->tpl_vars['linked_version_id']->value != 0 && $_smarty_tpl->tpl_vars['drawPlatformChecks']->value == 0) {?> style="<?php echo @constant('TL_STYLE_FOR_ADDED_TC');?>
"<?php }?>><td width="20">        			        <?php if (!$_smarty_tpl->tpl_vars['gui']->value->usePlatforms || $_smarty_tpl->tpl_vars['drawPlatformChecks']->value == 0) {
if ($_smarty_tpl->tpl_vars['gui']->value->full_control) {
if ($_smarty_tpl->tpl_vars['is_active']->value == 0 || $_smarty_tpl->tpl_vars['linked_version_id']->value != 0) {?>&nbsp;&nbsp;<?php } else { ?><input type="checkbox" name="<?php echo $_smarty_tpl->tpl_vars['add_cb']->value;?>
[<?php echo $_smarty_tpl->tpl_vars['tcID']->value;?>
][0]" id="<?php echo $_smarty_tpl->tpl_vars['add_cb']->value;
echo $_smarty_tpl->tpl_vars['tcID']->value;?>
[0]" value="<?php echo $_smarty_tpl->tpl_vars['tcID']->value;?>
" /><?php }?><input type="hidden" name="a_tcid[<?php echo $_smarty_tpl->tpl_vars['tcID']->value;?>
]" value="<?php echo $_smarty_tpl->tpl_vars['tcID']->value;?>
" /><?php } else { ?>&nbsp;&nbsp;<?php }
}?></td><?php if ($_smarty_tpl->tpl_vars['gui']->value->usePlatforms) {?><td><?php if ($_smarty_tpl->tpl_vars['drawPlatformChecks']->value) {?>&nbsp;<?php } else { ?><select name="feature2fix[<?php echo $_smarty_tpl->tpl_vars['tcase']->value['feature_id'][0];?>
][<?php echo $_smarty_tpl->tpl_vars['linked_version_id']->value;?>
]"><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['gui']->value->platformsForHtmlOptions,'selected'=>0),$_smarty_tpl);?>
</select><?php }?></td><?php }?><td><img class="clickable" src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['history_small'];?>
"onclick="javascript:openExecHistoryWindow(<?php echo $_smarty_tpl->tpl_vars['tcase']->value['id'];?>
);"title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['execution_history'];?>
" /><img class="clickable" src="<?php echo @constant('TL_THEME_IMG_DIR');?>
/edit_icon.png"onclick="javascript:openTCaseWindow(<?php echo $_smarty_tpl->tpl_vars['tcase']->value['id'];?>
);"title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['design'];?>
" /><span id="tooltip-<?php echo $_smarty_tpl->tpl_vars['tcID']->value;?>
"><img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['summary_small'];?>
">&nbsp;<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->testCasePrefix, ENT_QUOTES, 'UTF-8', true);
echo $_smarty_tpl->tpl_vars['tcase']->value['external_id'];
echo $_smarty_tpl->tpl_vars['gsmarty_gui']->value->title_separator_1;
echo htmlspecialchars($_smarty_tpl->tpl_vars['tcase']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</span></td><td><?php if ($_smarty_tpl->tpl_vars['gui']->value->priorityEnabled) {
echo '<script'; ?>
 type="text/javascript">js_tcase_importance[<?php echo $_smarty_tpl->tpl_vars['tcID']->value;?>
] = new Array();js_tcase_wkfstatus[<?php echo $_smarty_tpl->tpl_vars['tcID']->value;?>
] = new Array();<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['tcase']->value['importance'], 'value', false, 'version');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['version']->value => $_smarty_tpl->tpl_vars['value']->value) {
?>js_tcase_importance[<?php echo $_smarty_tpl->tpl_vars['tcID']->value;?>
][<?php echo $_smarty_tpl->tpl_vars['version']->value;?>
] = <?php echo $_smarty_tpl->tpl_vars['value']->value;?>
;<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['tcase']->value['status'], 'value', false, 'version');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['version']->value => $_smarty_tpl->tpl_vars['value']->value) {
?>js_tcase_wkfstatus[<?php echo $_smarty_tpl->tpl_vars['tcID']->value;?>
][<?php echo $_smarty_tpl->tpl_vars['version']->value;?>
] = <?php echo $_smarty_tpl->tpl_vars['value']->value;?>
;<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
echo '</script'; ?>
><select name="tcversion_for_tcid[<?php echo $_smarty_tpl->tpl_vars['tcID']->value;?>
]"onchange="updTCAttr(<?php echo $_smarty_tpl->tpl_vars['tcID']->value;?>
,this.options[this.selectedIndex].value);"<?php if ($_smarty_tpl->tpl_vars['linked_version_id']->value != 0) {?> disabled<?php }?>><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['tcase']->value['tcversions'],'selected'=>$_smarty_tpl->tpl_vars['linked_version_id']->value),$_smarty_tpl);?>
</select></td><?php if ($_smarty_tpl->tpl_vars['linked_version_id']->value != 0) {
$_smarty_tpl->_assignInScope('importance', $_smarty_tpl->tpl_vars['tcase']->value['importance'][$_smarty_tpl->tpl_vars['linked_version_id']->value]);
$_smarty_tpl->_assignInScope('wkf', $_smarty_tpl->tpl_vars['tcase']->value['status'][$_smarty_tpl->tpl_vars['linked_version_id']->value]);
} else { ?>                        <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['tcase']->value['importance'], 'item', false, 'key', 'oneLoop', array (
  'first' => true,
  'index' => true,
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['__smarty_foreach_oneLoop']->value['index']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_oneLoop']->value['first'] = !$_smarty_tpl->tpl_vars['__smarty_foreach_oneLoop']->value['index'];
if ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_oneLoop']->value['first']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_oneLoop']->value['first'] : null)) {
$_smarty_tpl->_assignInScope('firstElement', $_smarty_tpl->tpl_vars['key']->value);
}
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
$_smarty_tpl->_assignInScope('importance', $_smarty_tpl->tpl_vars['tcase']->value['importance'][$_smarty_tpl->tpl_vars['firstElement']->value]);
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['tcase']->value['status'], 'item', false, 'key', 'oneLoop', array (
  'first' => true,
  'index' => true,
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['key']->value => $_smarty_tpl->tpl_vars['item']->value) {
$_smarty_tpl->tpl_vars['__smarty_foreach_oneLoop']->value['index']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_oneLoop']->value['first'] = !$_smarty_tpl->tpl_vars['__smarty_foreach_oneLoop']->value['index'];
if ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_oneLoop']->value['first']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_oneLoop']->value['first'] : null)) {
$_smarty_tpl->_assignInScope('firstElement', $_smarty_tpl->tpl_vars['key']->value);
}
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);
$_smarty_tpl->_assignInScope('wkf', $_smarty_tpl->tpl_vars['tcase']->value['status'][$_smarty_tpl->tpl_vars['firstElement']->value]);
}?><td id="wkfstatus_<?php echo $_smarty_tpl->tpl_vars['tcID']->value;?>
" style="width:15%"><?php echo $_smarty_tpl->tpl_vars['gsmarty_option_wkfstatus']->value[$_smarty_tpl->tpl_vars['wkf']->value];?>
</td><td id="importance_<?php echo $_smarty_tpl->tpl_vars['tcID']->value;?>
" style="width:7%"><?php echo $_smarty_tpl->tpl_vars['gsmarty_option_importance']->value[$_smarty_tpl->tpl_vars['importance']->value];?>
</td><?php } else { ?><select name="tcversion_for_tcid[<?php echo $_smarty_tpl->tpl_vars['tcID']->value;?>
]"<?php if ($_smarty_tpl->tpl_vars['linked_version_id']->value != 0) {?> disabled<?php }?>><?php echo smarty_function_html_options(array('options'=>$_smarty_tpl->tpl_vars['tcase']->value['tcversions'],'selected'=>$_smarty_tpl->tpl_vars['linked_version_id']->value),$_smarty_tpl);?>
</select><?php }?><td style="text-align:center;"><input name="exec_order[<?php echo $_smarty_tpl->tpl_vars['tcID']->value;?>
]" <?php echo $_smarty_tpl->tpl_vars['gui']->value->exec_order_input_disabled;?>
style="text-align:right;" size="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'EXECUTION_ORDER_SIZE');?>
" maxlength="<?php echo $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'EXECUTION_ORDER_MAXLEN');?>
"value="<?php echo $_smarty_tpl->tpl_vars['tcase']->value['execution_order'];?>
" /><?php if ($_smarty_tpl->tpl_vars['linked_version_id']->value != 0) {?><input type="hidden" name="linked_version[<?php echo $_smarty_tpl->tpl_vars['tcID']->value;?>
]" value="<?php echo $_smarty_tpl->tpl_vars['linked_version_id']->value;?>
" /><input type="hidden" name="linked_exec_order[<?php echo $_smarty_tpl->tpl_vars['tcID']->value;?>
]"  value="<?php echo $_smarty_tpl->tpl_vars['tcase']->value['execution_order'];?>
" /><?php }?></td><?php if ($_smarty_tpl->tpl_vars['ts']->value['linked_testcase_qty'] > 0 && $_smarty_tpl->tpl_vars['drawPlatformChecks']->value == 0) {?><td>&nbsp;</td><td><?php $_smarty_tpl->_assignInScope('show_remove_check', 0);
$_smarty_tpl->_assignInScope('executed', 0);
if ($_smarty_tpl->tpl_vars['tcase']->value['executed'][0] == 'yes') {
$_smarty_tpl->_assignInScope('executed', 1);
}
if ($_smarty_tpl->tpl_vars['linked_version_id']->value) {
$_smarty_tpl->_assignInScope('show_remove_check', 1);
if ($_smarty_tpl->tpl_vars['tcase']->value['executed'][0] == 'yes') {
$_smarty_tpl->_assignInScope('show_remove_check', $_smarty_tpl->tpl_vars['gui']->value->can_remove_executed_testcases);
}
}
if ($_smarty_tpl->tpl_vars['show_remove_check']->value) {?><input type='checkbox' name='<?php echo $_smarty_tpl->tpl_vars['rm_cb']->value;?>
[<?php echo $_smarty_tpl->tpl_vars['tcID']->value;?>
][0]' id='<?php echo $_smarty_tpl->tpl_vars['rm_cb']->value;
echo $_smarty_tpl->tpl_vars['tcID']->value;?>
[0]'value='<?php echo $_smarty_tpl->tpl_vars['linked_version_id']->value;?>
'<?php if ($_smarty_tpl->tpl_vars['executed']->value) {?>onclick="updateRemoveExecCounter('<?php echo $_smarty_tpl->tpl_vars['rm_cb']->value;
echo $_smarty_tpl->tpl_vars['tcID']->value;?>
[0]')"<?php }?>/><?php } else { ?>&nbsp;<?php }
if ($_smarty_tpl->tpl_vars['tcase']->value['executed'][0] == 'yes') {?>&nbsp;&nbsp;&nbsp;<img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['executed'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['gui']->value->warning_msg->executed;?>
" /><?php }
if ($_smarty_tpl->tpl_vars['is_active']->value == 0) {?>&nbsp;&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['labels']->value['inactive_testcase'];
}?></td><td align="center" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['info_added_on_date'];?>
"><?php if ($_smarty_tpl->tpl_vars['tcase']->value['linked_ts'][0] != '') {
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['localize_date'][0], array( array('d'=>$_smarty_tpl->tpl_vars['tcase']->value['linked_ts'][0]),$_smarty_tpl ) );
} else { ?>&nbsp;<?php }?></td><?php }?></tr>    			        <?php if (isset($_smarty_tpl->tpl_vars['tcase']->value['custom_fields'][0])) {?><input type='hidden' name='linked_with_cf[<?php echo $_smarty_tpl->tpl_vars['tcase']->value['feature_id'][0];?>
]' value='<?php echo $_smarty_tpl->tpl_vars['tcase']->value['feature_id'][0];?>
' /><tr><td colspan="9"><?php echo $_smarty_tpl->tpl_vars['tcase']->value['custom_fields'][0];?>
</td></tr><?php }
}?>                <?php if ($_smarty_tpl->tpl_vars['gui']->value->usePlatforms && $_smarty_tpl->tpl_vars['drawPlatformChecks']->value) {
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['gui']->value->platforms, 'platform');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['platform']->value) {
?><tr <?php if (isset($_smarty_tpl->tpl_vars['tcase']->value['feature_id'][$_smarty_tpl->tpl_vars['platform']->value['id']])) {?>style="<?php echo @constant('TL_STYLE_FOR_ADDED_TC');?>
" <?php }?> ><td><?php if ($_smarty_tpl->tpl_vars['gui']->value->full_control) {
if ($_smarty_tpl->tpl_vars['is_active']->value == 0 || isset($_smarty_tpl->tpl_vars['tcase']->value['feature_id'][$_smarty_tpl->tpl_vars['platform']->value['id']])) {?>&nbsp;&nbsp;<?php } else { ?><input type="checkbox" name="<?php echo $_smarty_tpl->tpl_vars['add_cb']->value;?>
[<?php echo $_smarty_tpl->tpl_vars['tcID']->value;?>
][<?php echo $_smarty_tpl->tpl_vars['platform']->value['id'];?>
]" id="<?php echo $_smarty_tpl->tpl_vars['add_cb']->value;
echo $_smarty_tpl->tpl_vars['tcID']->value;?>
" value="<?php echo $_smarty_tpl->tpl_vars['tcID']->value;?>
" /><?php }?><input type="hidden" name="a_tcid[<?php echo $_smarty_tpl->tpl_vars['tcID']->value;?>
][<?php echo $_smarty_tpl->tpl_vars['platform']->value['id'];?>
]" value="<?php echo $_smarty_tpl->tpl_vars['tcID']->value;?>
" /><?php } else { ?>&nbsp;&nbsp;<?php }?></td><td><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['platform']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><?php if ($_smarty_tpl->tpl_vars['gui']->value->priorityEnabled) {?> <td>&nbsp;</td> <?php }?>                        <?php if (isset($_smarty_tpl->tpl_vars['tcase']->value['feature_id'][$_smarty_tpl->tpl_vars['platform']->value['id']])) {?><td>&nbsp;</td><td>&nbsp;</td><td>  							        <?php $_smarty_tpl->_assignInScope('show_remove_check', 0);
if ($_smarty_tpl->tpl_vars['linked_version_id']->value) {
$_smarty_tpl->_assignInScope('show_remove_check', 1);
if (isset($_smarty_tpl->tpl_vars['tcase']->value['executed'][$_smarty_tpl->tpl_vars['platform']->value['id']]) && $_smarty_tpl->tpl_vars['tcase']->value['executed'][$_smarty_tpl->tpl_vars['platform']->value['id']] == 'yes') {
$_smarty_tpl->_assignInScope('show_remove_check', $_smarty_tpl->tpl_vars['gui']->value->can_remove_executed_testcases);
}
}
if ($_smarty_tpl->tpl_vars['show_remove_check']->value) {?><input type='checkbox' name='<?php echo $_smarty_tpl->tpl_vars['rm_cb']->value;?>
[<?php echo $_smarty_tpl->tpl_vars['tcID']->value;?>
][<?php echo $_smarty_tpl->tpl_vars['platform']->value['id'];?>
]' id='<?php echo $_smarty_tpl->tpl_vars['rm_cb']->value;
echo $_smarty_tpl->tpl_vars['tcID']->value;?>
[<?php echo $_smarty_tpl->tpl_vars['platform']->value['id'];?>
]'value='<?php echo $_smarty_tpl->tpl_vars['linked_version_id']->value;?>
' /><?php } else { ?>&nbsp;&nbsp;<?php }
if (isset($_smarty_tpl->tpl_vars['tcase']->value['executed'][$_smarty_tpl->tpl_vars['platform']->value['id']]) && $_smarty_tpl->tpl_vars['tcase']->value['executed'][$_smarty_tpl->tpl_vars['platform']->value['id']] == 'yes') {?>&nbsp;&nbsp;&nbsp;<img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['executed'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['gui']->value->warning_msg->executed;?>
" /><?php }?>                                                            <?php if ($_smarty_tpl->tpl_vars['is_active']->value == 0) {?>&nbsp;&nbsp;&nbsp;<?php echo $_smarty_tpl->tpl_vars['labels']->value['inactive_testcase'];
}?></td><td align="center" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['info_added_on_date'];?>
"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['localize_date'][0], array( array('d'=>$_smarty_tpl->tpl_vars['tcase']->value['linked_ts'][$_smarty_tpl->tpl_vars['platform']->value['id']]),$_smarty_tpl ) );?>
</td><?php }?></tr><?php if (isset($_smarty_tpl->tpl_vars['tcase']->value['custom_fields'][$_smarty_tpl->tpl_vars['platform']->value['id']])) {?><tr><td colspan="8"><input type='hidden' name='linked_with_cf[<?php echo $_smarty_tpl->tpl_vars['tcase']->value['feature_id'];?>
]' value='<?php echo $_smarty_tpl->tpl_vars['tcase']->value['feature_id'];?>
' /><?php echo $_smarty_tpl->tpl_vars['tcase']->value['custom_fields'][$_smarty_tpl->tpl_vars['platform']->value['id']];?>
</td></tr><?php }
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?><tr><td colspan="10"><hr/></td></tr><?php }
}?>       	      <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?></table><br /><?php }?>          
        </div>
    	<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </div>
  <?php }?>
</form>

<?php if ($_smarty_tpl->tpl_vars['gui']->value->refreshTree) {?>
	<?php $_smarty_tpl->_subTemplateRender("file:inc_refreshTreeWithFilters.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}?>

</body>
</html><?php }
}
