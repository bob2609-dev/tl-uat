<?php
/* Smarty version 3.1.33, created on 2026-03-05 21:20:18
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\frmInner.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69a9e58259b119_53270635',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f2975d207e7098fed670c52dd3fd2aefa47ea340' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\frmInner.tpl',
      1 => 1579460696,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:bootstrap.inc.tpl' => 1,
  ),
),false)) {
function content_69a9e58259b119_53270635 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<html>
    <head>
    	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_smarty_tpl->tpl_vars['pageCharset']->value;?>
" />
    	<meta http-equiv="Content-language" content="en" />
    	<meta http-equiv="expires" content="-1" />
    	<meta http-equiv="pragma" content="no-cache" />
    	<meta name="generator" content="testlink" />
    	<meta name="author" content="Martin Havlat" />
    	<meta name="copyright" content="GNU" />
    	<meta name="robots" content="NOFOLLOW" />
    	<base href="<?php echo $_smarty_tpl->tpl_vars['basehref']->value;?>
" />
    	<title>TestLink Inner Frame</title>
    	<style media="all" type="text/css">@import "<?php echo $_smarty_tpl->tpl_vars['css']->value;?>
";</style>
    	<link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['basehref']->value;
echo $_smarty_tpl->tpl_vars['tlCfg']->value->theme_dir;?>
/css/frame.css">
    	<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['basehref']->value;?>
third_party/jquery/<?php echo @constant('TL_JQUERY');?>
" language="javascript"><?php echo '</script'; ?>
>
    	<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['basehref']->value;?>
third_party/chosen/chosen.jquery.js"><?php echo '</script'; ?>
>
    	<?php $_smarty_tpl->_subTemplateRender("file:bootstrap.inc.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
    </head>
    <body>
      <iframe src="<?php echo $_smarty_tpl->tpl_vars['treeframe']->value;?>
" name="treeframe" class="treeframe"></iframe>
      <iframe src="<?php echo $_smarty_tpl->tpl_vars['workframe']->value;?>
" name="workframe" class="workframe"></iframe>
    </body>
</html>
<?php }
}
