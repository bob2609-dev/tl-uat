<?php
/* Smarty version 3.1.33, created on 2026-03-05 21:20:07
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\main.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69a9e577647ba9_28779654',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '4852ccacb0b135c5af5270279baa35cc01ee9efc' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\main.tpl',
      1 => 1579460696,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69a9e577647ba9_28779654 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_smarty_tpl->tpl_vars['pageCharset']->value;?>
" />
	<meta http-equiv="Content-language" content="en" />
	<meta name="generator" content="testlink" />
	<meta name="author" content="TestLink Development Team" />
	<meta name="copyright" content="TestLink Development Team" />
	<meta name="robots" content="NOFOLLOW" />
	<title>TestLink <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['tlVersion']->value, ENT_QUOTES, 'UTF-8', true);?>
</title>
	<meta name="description" content="TestLink - <?php echo (($tmp = @$_smarty_tpl->tpl_vars['gui']->value->title)===null||$tmp==='' ? "Main page" : $tmp);?>
" />
	<link rel="icon" href="<?php echo $_smarty_tpl->tpl_vars['basehref']->value;
echo @constant('TL_THEME_IMG_DIR');?>
favicon.ico" type="image/x-icon" />

  <!-- for the iframes -->
  <link rel="stylesheet" type="text/css" href="<?php echo $_smarty_tpl->tpl_vars['basehref']->value;
echo $_smarty_tpl->tpl_vars['tlCfg']->value->theme_dir;?>
/css/frame.css">


</head>

<body>
  <iframe src="<?php echo $_smarty_tpl->tpl_vars['gui']->value->titleframe;?>
" name="titlebar" class="navigationBar"></iframe>
  <iframe src="<?php echo $_smarty_tpl->tpl_vars['gui']->value->mainframe;?>
" name="mainframe" class="siteContent"></iframe>
</body>

</html>
<?php }
}
