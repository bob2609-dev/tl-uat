<?php
/* Smarty version 3.1.33, created on 2026-03-05 21:20:08
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\custom_inc_head.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69a9e578bcbb75_98346085',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'c67e4430bf2dd3a4ec55702dfb94145f4d9739a2' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\custom_inc_head.tpl',
      1 => 1754931804,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69a9e578bcbb75_98346085 (Smarty_Internal_Template $_smarty_tpl) {
?>
<!-- Custom JavaScript for Redmine integration -->
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['basehref']->value;?>
redmine_hook.js"><?php echo '</script'; ?>
>

<!-- Custom JavaScript for image display fix -->
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['basehref']->value;?>
image_fix.js"><?php echo '</script'; ?>
>

<!-- Custom JavaScript for auto-filling bug descriptions -->
<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['basehref']->value;?>
gui/javascript/bug_description_autofill.js"><?php echo '</script'; ?>
>
<?php }
}
