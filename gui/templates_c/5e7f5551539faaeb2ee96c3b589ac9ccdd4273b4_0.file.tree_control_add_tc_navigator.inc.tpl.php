<?php
/* Smarty version 3.1.33, created on 2026-03-09 13:09:39
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\tree_control_add_tc_navigator.inc.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69aeb883214b02_42307448',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5e7f5551539faaeb2ee96c3b589ac9ccdd4273b4' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\tree_control_add_tc_navigator.inc.tpl',
      1 => 1579460696,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69aeb883214b02_42307448 (Smarty_Internal_Template $_smarty_tpl) {
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('var'=>'labels','s'=>'expand_tree, collapse_tree, show_whole_spec_on_right_panel'),$_smarty_tpl ) );?>


<div class="x-panel-body exec_additional_info" style="padding:3px; padding-left: 9px;border:1px solid #99BBE8;">

<input type="button" value="<?php echo $_smarty_tpl->tpl_vars['labels']->value['expand_tree'];?>
" id="expand_tree" name="expand_tree" 
       onclick="tree.expandAll();" style="font-size: 90%;" />

<input type="button" value="<?php echo $_smarty_tpl->tpl_vars['labels']->value['collapse_tree'];?>
" id="collapse_tree" name="collapse_tree" 
       onclick="tree.collapseAll();" style="font-size: 90%;" />

<input type="button" value="<?php echo $_smarty_tpl->tpl_vars['labels']->value['show_whole_spec_on_right_panel'];?>
" id="show_whole_test_spec" name="show_whole_test_spec" 
       onclick="javascript:ETS(<?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->root_node->id;?>
);" style="font-size: 90%;" />

</div>
<?php }
}
