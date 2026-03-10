<?php
/* Smarty version 3.1.33, created on 2026-03-09 08:14:40
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\cfields\cfieldsViewControls.inc.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69ae736036b8c0_90503161',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'dd7079aa52cdf631ed7e08fbe2c2bba5a6cfc670' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\cfields\\cfieldsViewControls.inc.tpl',
      1 => 1579460696,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69ae736036b8c0_90503161 (Smarty_Internal_Template $_smarty_tpl) {
?>  <div class="page-content">
    <form method="post" id="f<?php echo $_smarty_tpl->tpl_vars['suffix']->value;?>
"
      action="#">
      <a class="btn btn-primary" role="button" href="<?php echo $_smarty_tpl->tpl_vars['cfCreateAction']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['btn_cfields_create'];?>
</a>  

      <a class="btn btn-primary" role="button" href="<?php echo $_smarty_tpl->tpl_vars['exportCfieldsAction']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['btn_export'];?>
</a>  

      <a class="btn btn-primary" role="button" href="<?php echo $_smarty_tpl->tpl_vars['importCfieldsAction']->value;?>
"><?php echo $_smarty_tpl->tpl_vars['labels']->value['btn_import'];?>
</a>  
    </form>
  </div><?php }
}
