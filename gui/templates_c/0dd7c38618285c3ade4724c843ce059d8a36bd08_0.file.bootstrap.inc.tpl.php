<?php
/* Smarty version 3.1.33, created on 2026-03-05 21:20:10
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\bootstrap.inc.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69a9e57a6fc234_10589304',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '0dd7c38618285c3ade4724c843ce059d8a36bd08' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\bootstrap.inc.tpl',
      1 => 1579460696,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69a9e57a6fc234_10589304 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('bb', ($_smarty_tpl->tpl_vars['basehref']->value).("third_party/bootstrap/3.4.1"));?>
<link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['bb']->value;?>
/css/bootstrap.min.css" >

<link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['bb']->value;?>
/css/bootstrap-theme.min.css">

<?php echo '<script'; ?>
 src="<?php echo $_smarty_tpl->tpl_vars['bb']->value;?>
/js/bootstrap.min.js"><?php echo '</script'; ?>
>

<?php }
}
