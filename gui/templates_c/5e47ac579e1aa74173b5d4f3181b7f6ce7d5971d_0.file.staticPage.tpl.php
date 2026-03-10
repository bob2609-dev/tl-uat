<?php
/* Smarty version 3.1.33, created on 2026-03-09 13:08:58
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\staticPage.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69aeb85ad90767_09484242',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '5e47ac579e1aa74173b5d4f3181b7f6ce7d5971d' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\staticPage.tpl',
      1 => 1579460696,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:inc_head.tpl' => 1,
    'file:inc_refreshTree.tpl' => 1,
  ),
),false)) {
function content_69aeb85ad90767_09484242 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:inc_head.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<body>

<?php if ($_smarty_tpl->tpl_vars['gui']->value->pageTitle != '') {?>
	<h1 class="title"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->pageTitle, ENT_QUOTES, 'UTF-8', true);?>
</h1>
<?php }?>

<div class="workBack">
<?php echo $_smarty_tpl->tpl_vars['gui']->value->pageContent;?>

</div>

<?php if ($_smarty_tpl->tpl_vars['gui']->value->refreshTree) {?>
   <?php $_smarty_tpl->_subTemplateRender("file:inc_refreshTree.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}?>
</body>
</html><?php }
}
