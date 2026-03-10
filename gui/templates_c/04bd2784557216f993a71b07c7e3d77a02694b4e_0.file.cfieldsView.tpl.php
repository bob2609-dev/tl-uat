<?php
/* Smarty version 3.1.33, created on 2026-03-09 08:14:40
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\cfields\cfieldsView.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69ae736021aa33_34258881',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '04bd2784557216f993a71b07c7e3d77a02694b4e' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\cfields\\cfieldsView.tpl',
      1 => 1579460696,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:inc_head.tpl' => 1,
    'file:bootstrap.inc.tpl' => 1,
    'file:cfields/".((string)$_smarty_tpl->tpl_vars[\'tplBN\']->value)."Controls.inc.tpl' => 2,
  ),
),false)) {
function content_69ae736021aa33_34258881 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\tl-uat\\vendor\\smarty\\smarty\\libs\\plugins\\modifier.replace.php','function'=>'smarty_modifier_replace',),));
$_smarty_tpl->_assignInScope('cfg_section', smarty_modifier_replace(call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'basename' ][ 0 ], array( basename($_smarty_tpl->source->filepath) )),".tpl",''));
$_smarty_tpl->_assignInScope('tplBN', $_smarty_tpl->tpl_vars['cfg_section']->value);
$_smarty_tpl->smarty->ext->configLoad->_loadConfigFile($_smarty_tpl, "input_dimensions.conf", $_smarty_tpl->tpl_vars['cfg_section']->value, 0);
?>


<?php $_smarty_tpl->_assignInScope('cfViewAction', "lib/cfields/cfieldsView.php");?>

<?php $_smarty_tpl->_assignInScope('cfCreateAction', "lib/cfields/cfieldsEdit.php?do_action=create");?>

<?php $_smarty_tpl->_assignInScope('cfImportAction', "lib/cfields/cfieldsImport.php?goback_url=");
$_smarty_tpl->_assignInScope('importCfieldsAction', ((string)$_smarty_tpl->tpl_vars['basehref']->value).((string)$_smarty_tpl->tpl_vars['cfImportAction']->value).((string)$_smarty_tpl->tpl_vars['basehref']->value).((string)$_smarty_tpl->tpl_vars['cfViewAction']->value));?>

<?php $_smarty_tpl->_assignInScope('cfExportAction', "lib/cfields/cfieldsExport.php?goback_url=");
$_smarty_tpl->_assignInScope('exportCfieldsAction', ((string)$_smarty_tpl->tpl_vars['basehref']->value).((string)$_smarty_tpl->tpl_vars['cfExportAction']->value).((string)$_smarty_tpl->tpl_vars['basehref']->value).((string)$_smarty_tpl->tpl_vars['cfViewAction']->value));?>


<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('var'=>"labels",'s'=>"name,label,type,title_cfields_mgmt,manage_cfield,btn_cfields_create,
             btn_export,btn_import,btn_goback,sort_table_by_column,enabled_on_context,
             display_on_exec,available_on"),$_smarty_tpl ) );?>


<?php $_smarty_tpl->_subTemplateRender("file:inc_head.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('enableTableSorting'=>"yes",'openHead'=>"yes"), 0, false);
$_smarty_tpl->_subTemplateRender("file:bootstrap.inc.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

</head>
<body class="testlink">
<h1 class="title"><?php echo $_smarty_tpl->tpl_vars['labels']->value['title_cfields_mgmt'];?>
</h1>

<div class="page-content">

<?php if ($_smarty_tpl->tpl_vars['gui']->value->cf_map != '' && $_smarty_tpl->tpl_vars['gui']->value->drawControlsOnTop) {?>
  <?php $_smarty_tpl->_subTemplateRender("file:cfields/".((string)$_smarty_tpl->tpl_vars['tplBN']->value)."Controls.inc.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('suffix'=>"Top"), 0, true);
}?>

<?php if ($_smarty_tpl->tpl_vars['gui']->value->cf_map != '') {?>
  <table class="table table-bordered sortable">
    <thead class="thead-dark">
      <tr>
        <th><?php echo $_smarty_tpl->tpl_vars['tlImages']->value['sort_hint'];
echo $_smarty_tpl->tpl_vars['labels']->value['name'];?>
</th>
        <th><?php echo $_smarty_tpl->tpl_vars['tlImages']->value['sort_hint'];
echo $_smarty_tpl->tpl_vars['labels']->value['label'];?>
</th>
        <th><?php echo $_smarty_tpl->tpl_vars['tlImages']->value['sort_hint'];
echo $_smarty_tpl->tpl_vars['labels']->value['type'];?>
</th>
        <th class="<?php echo $_smarty_tpl->tpl_vars['noSortableColumnClass']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['enabled_on_context'];?>
</th>
        <th class="<?php echo $_smarty_tpl->tpl_vars['noSortableColumnClass']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['display_on_exec'];?>
</th>
        <th><?php echo $_smarty_tpl->tpl_vars['tlImages']->value['sort_hint'];
echo $_smarty_tpl->tpl_vars['labels']->value['available_on'];?>
</th>
      </tr>
    </thead>

    <tbody>
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['gui']->value->cf_map, 'cf_def', false, 'cf_id');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['cf_id']->value => $_smarty_tpl->tpl_vars['cf_def']->value) {
?>
      <tr>
      <td width="10%" class="bold"><a href="lib/cfields/cfieldsEdit.php?do_action=edit&cfield_id=<?php echo $_smarty_tpl->tpl_vars['cf_def']->value['id'];?>
"
                          title="<?php echo $_smarty_tpl->tpl_vars['labels']->value['manage_cfield'];?>
"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cf_def']->value['name'], ENT_QUOTES, 'UTF-8', true);?>
</a></td>
      <td width="10%"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['cf_def']->value['label'], ENT_QUOTES, 'UTF-8', true);?>
</td>
      <td width="5%"><?php echo $_smarty_tpl->tpl_vars['gui']->value->cf_types[$_smarty_tpl->tpl_vars['cf_def']->value['type']];?>
</td>
      <td width="10%"><?php echo $_smarty_tpl->tpl_vars['cf_def']->value['enabled_on_context'];?>
</td>
      <td align="center" width="5%"><?php if ($_smarty_tpl->tpl_vars['cf_def']->value['show_on_execution']) {?><img src="<?php echo $_smarty_tpl->tpl_vars['tlImages']->value['checked'];?>
"><?php }?> </td>
      <td width="10%"><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('s'=>$_smarty_tpl->tpl_vars['cf_def']->value['node_description']),$_smarty_tpl ) );?>
</td>
      
      </tr>
    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
    </tbody>
  </table>
<?php }?>  

<?php $_smarty_tpl->_subTemplateRender("file:cfields/".((string)$_smarty_tpl->tpl_vars['tplBN']->value)."Controls.inc.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('suffix'=>"Bottom"), 0, true);
?>

</div>
</body>
</html><?php }
}
