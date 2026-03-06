<?php
/* Smarty version 3.1.33, created on 2026-03-05 21:20:26
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\inc_tree_control.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69a9e58a3f3924_34739665',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '198368a91f5e707d50e5b5cb66441f4f375212a7' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\inc_tree_control.tpl',
      1 => 1579460696,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69a9e58a3f3924_34739665 (Smarty_Internal_Template $_smarty_tpl) {
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('var'=>'labels','s'=>'expand_tree, collapse_tree'),$_smarty_tpl ) );?>


<div class="x-panel-body exec_additional_info" style="padding:3px; padding-left: 9px;border:1px solid #99BBE8;">

<input type="button"
       value="<?php echo $_smarty_tpl->tpl_vars['labels']->value['expand_tree'];?>
" 
       id="expand_tree" 
       name="expand_tree"
       onclick="tree.expandAll();"
       style="font-size: 90%;" />

<input type="button"
       value="<?php echo $_smarty_tpl->tpl_vars['labels']->value['collapse_tree'];?>
"
       id="collapse_tree"
       name="collapse_tree"
       onclick="tree.collapseAll();"
       style="font-size: 90%;" />

</div>
<?php }
}
