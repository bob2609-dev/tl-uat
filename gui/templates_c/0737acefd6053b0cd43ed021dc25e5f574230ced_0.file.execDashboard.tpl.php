<?php
/* Smarty version 3.1.33, created on 2026-03-05 21:20:26
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\execute\execDashboard.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69a9e58ac76168_73494974',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0737acefd6053b0cd43ed021dc25e5f574230ced' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\execute\\execDashboard.tpl',
      1 => 1579460696,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:inc_head.tpl' => 1,
  ),
),false)) {
function content_69a9e58ac76168_73494974 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\tl-uat\\vendor\\smarty\\smarty\\libs\\plugins\\modifier.replace.php','function'=>'smarty_modifier_replace',),));
$_smarty_tpl->_assignInScope('title_sep', @constant('TITLE_SEP'));
$_smarty_tpl->_assignInScope('title_sep_type3', @constant('TITLE_SEP_TYPE3'));
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('var'=>'labels','s'=>'build_is_closed,test_cases_cannot_be_executed,build,builds_notes,testplan,
             test_plan_notes,platform,platform_description,restAPIExecParameters'),$_smarty_tpl ) );?>


<?php $_smarty_tpl->_assignInScope('cfg_section', smarty_modifier_replace(call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'basename' ][ 0 ], array( basename($_smarty_tpl->source->filepath) )),".tpl",''));
$_smarty_tpl->smarty->ext->configLoad->_loadConfigFile($_smarty_tpl, "input_dimensions.conf", $_smarty_tpl->tpl_vars['cfg_section']->value, 0);
?>


<?php $_smarty_tpl->_subTemplateRender("file:inc_head.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('popup'=>'yes','openHead'=>'yes'), 0, false);
if ($_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'ROUND_EXEC_HISTORY') || $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'ROUND_TC_TITLE') || $_smarty_tpl->smarty->ext->configLoad->_getConfigVariable($_smarty_tpl, 'ROUND_TC_SPEC')) {?>
  <?php $_smarty_tpl->_assignInScope('round_enabled', 1);?>
  <?php echo '<script'; ?>
 language="JavaScript" src="<?php echo $_smarty_tpl->tpl_vars['basehref']->value;?>
gui/niftycube/niftycube.js" type="text/javascript"><?php echo '</script'; ?>
>
<?php }?>
</head>
<body>

<h1 class="title">
<?php echo $_smarty_tpl->tpl_vars['gui']->value->pageTitlePrefix;?>
  
<?php echo $_smarty_tpl->tpl_vars['labels']->value['testplan'];?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->testplan_name, ENT_QUOTES, 'UTF-8', true);?>
 <?php echo $_smarty_tpl->tpl_vars['title_sep_type3']->value;?>
 <?php echo $_smarty_tpl->tpl_vars['labels']->value['build'];?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->build_name, ENT_QUOTES, 'UTF-8', true);?>

<?php if ($_smarty_tpl->tpl_vars['gui']->value->platform_info['name'] != '') {?>
  <?php echo $_smarty_tpl->tpl_vars['title_sep_type3']->value;
echo $_smarty_tpl->tpl_vars['labels']->value['platform'];
echo $_smarty_tpl->tpl_vars['title_sep']->value;
echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->platform_info['name'], ENT_QUOTES, 'UTF-8', true);?>

<?php }?>
</h1>
<div id="main_content" class="workBack">
  <?php if ($_smarty_tpl->tpl_vars['gui']->value->build_is_open == 0) {?>
    <div class="messages" style="align:center;">
    <?php echo $_smarty_tpl->tpl_vars['labels']->value['build_is_closed'];?>
<br />
    <?php echo $_smarty_tpl->tpl_vars['labels']->value['test_cases_cannot_be_executed'];?>

    </div>
    <br />
  <?php }?>

  <div style="color: rgb(21, 66, 139);font-weight: bold;font-size: 11px;font-family: tahoma,arial,verdana,sans-serif;">
  <?php echo $_smarty_tpl->tpl_vars['labels']->value['testplan'];?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->testplan_name, ENT_QUOTES, 'UTF-8', true);?>

  </div>
  <div id="testplan_notes" class="exec_additional_info">
  <?php if ($_smarty_tpl->tpl_vars['gui']->value->testPlanEditorType == 'none') {
echo nl2br($_smarty_tpl->tpl_vars['gui']->value->testplan_notes);
} else {
echo $_smarty_tpl->tpl_vars['gui']->value->testplan_notes;
}?>
  <?php if ($_smarty_tpl->tpl_vars['gui']->value->testplan_cfields != '') {?> <div id="cfields_testplan" class="custom_field_container"><?php echo $_smarty_tpl->tpl_vars['gui']->value->testplan_cfields;?>
</div><?php }?>
  </div>

  <?php if ($_smarty_tpl->tpl_vars['gui']->value->platform_info['id'] > 0) {?>
    <div style="color: rgb(21, 66, 139);font-weight: bold;font-size: 11px;font-family: tahoma,arial,verdana,sans-serif;">
    <?php echo $_smarty_tpl->tpl_vars['labels']->value['platform'];?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->platform_info['name'], ENT_QUOTES, 'UTF-8', true);?>

    </div>
    <div id="platform_notes" class="exec_additional_info">
	<?php if ($_smarty_tpl->tpl_vars['gui']->value->platformEditorType == 'none') {
echo nl2br($_smarty_tpl->tpl_vars['gui']->value->platform_info['notes']);
} else {
echo $_smarty_tpl->tpl_vars['gui']->value->platform_info['notes'];
}?>
    </div>
  <?php }?>

  <div style="color: rgb(21, 66, 139);font-weight: bold;font-size: 11px;font-family: tahoma,arial,verdana,sans-serif;">
  <?php echo $_smarty_tpl->tpl_vars['labels']->value['build'];?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->build_name, ENT_QUOTES, 'UTF-8', true);?>

  </div>
  <div id="build_notes" class="exec_additional_info">
  <?php if ($_smarty_tpl->tpl_vars['gui']->value->buildEditorType == 'none') {
echo nl2br($_smarty_tpl->tpl_vars['gui']->value->build_notes);
} else {
echo $_smarty_tpl->tpl_vars['gui']->value->build_notes;
}?>
  <?php if ($_smarty_tpl->tpl_vars['gui']->value->build_cfields != '') {?> <div id="cfields_build" class="custom_field_container"><?php echo $_smarty_tpl->tpl_vars['gui']->value->build_cfields;?>
</div><?php }?>
  </div>

  <img class="clickable" src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['cog'];?>
" title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['restAPIExecParameters'];?>
"
       onclick="javascript:toggleShowHide('restAPI','inline');" />

  <div id="restAPI" style='display:none'>
  <?php echo $_smarty_tpl->tpl_vars['gui']->value->RESTArgsJSON;?>
  
  </div>  
</div>
</body>
</html><?php }
}
