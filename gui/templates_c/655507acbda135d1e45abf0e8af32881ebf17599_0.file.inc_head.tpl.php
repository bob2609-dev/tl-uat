<?php
/* Smarty version 3.1.33, created on 2026-03-05 21:20:08
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\inc_head.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69a9e578b923a7_25632803',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '655507acbda135d1e45abf0e8af32881ebf17599' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\inc_head.tpl',
      1 => 1769721410,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:inc_jsCfieldsValidation.tpl' => 1,
    'file:inc_tinymce_init.tpl' => 1,
    'file:custom_inc_head.tpl' => 1,
  ),
),false)) {
function content_69a9e578b923a7_25632803 (Smarty_Internal_Template $_smarty_tpl) {
?><!DOCTYPE html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_smarty_tpl->tpl_vars['pageCharset']->value;?>
" />
	<meta http-equiv="Content-language" content="en" />
	<meta http-equiv="expires" content="-1" />
	<meta http-equiv="pragma" content="no-cache" />
	<meta name="author" content="Martin Havlat" />
	<meta name="copyright" content="GNU" />
	<meta name="robots" content="NOFOLLOW" />
	<base href="<?php echo $_smarty_tpl->tpl_vars['basehref']->value;?>
"/>
	<title><?php echo (($tmp = @$_smarty_tpl->tpl_vars['pageTitle']->value)===null||$tmp==='' ? "TestLink" : $tmp);?>
</title>
	<link rel="icon" href="<?php echo $_smarty_tpl->tpl_vars['basehref']->value;
echo @constant('TL_THEME_IMG_DIR');?>
favicon.ico" type="image/x-icon" />
	
	<!-- Redmine Bug ID Fix removed to fix syntax errors -->
	
 
	<style media="all" type="text/css">@import "<?php echo $_smarty_tpl->tpl_vars['css']->value;?>
";</style>

	<?php if ($_smarty_tpl->tpl_vars['use_custom_css']->value) {?>
	<style media="all" type="text/css">@import "<?php echo $_smarty_tpl->tpl_vars['custom_css']->value;?>
";</style>
	<?php }?>
	
	<?php if ($_smarty_tpl->tpl_vars['testproject_coloring']->value == 'background') {?>
  	<style type="text/css"> body {background: <?php echo $_smarty_tpl->tpl_vars['testprojectColor']->value;?>
;}</style>
	<?php }?>
  
	<style media="print" type="text/css">@import "<?php echo $_smarty_tpl->tpl_vars['basehref']->value;
echo @constant('TL_PRINT_CSS');?>
";</style>

 
	<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['basehref']->value;?>
gui/javascript/testlink_library.js" language="javascript"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['basehref']->value;?>
gui/javascript/test_automation.js" language="javascript"><?php echo '</script'; ?>
>
	
	<?php if ($_smarty_tpl->tpl_vars['jsValidate']->value == "yes") {?> 
	<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['basehref']->value;?>
gui/javascript/validate.js" language="javascript"><?php echo '</script'; ?>
>
    <?php $_smarty_tpl->_subTemplateRender("file:inc_jsCfieldsValidation.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
	<?php }?>
   
	<?php if ($_smarty_tpl->tpl_vars['editorType']->value == 'tinymce') {?>
    <?php echo '<script'; ?>
 type="text/javascript" language="javascript"
    	src="<?php echo $_smarty_tpl->tpl_vars['basehref']->value;?>
third_party/tinymce/jscripts/tiny_mce/tiny_mce.js"><?php echo '</script'; ?>
>
    <?php $_smarty_tpl->_subTemplateRender("file:inc_tinymce_init.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
	<?php }?>

	<?php if (@constant('TL_SORT_TABLE_ENGINE') == 'kryogenix.org') {?>
	<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['basehref']->value;?>
gui/javascript/sorttable.js" 
		language="javascript"><?php echo '</script'; ?>
>
	<?php }?>


  	<link rel="stylesheet" href="<?php echo $_smarty_tpl->tpl_vars['basehref']->value;?>
third_party/chosen/chosen.css">
	<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['basehref']->value;?>
third_party/jquery/<?php echo @constant('TL_JQUERY');?>
" language="javascript"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['basehref']->value;?>
third_party/chosen/chosen.jquery.js"><?php echo '</script'; ?>
>
	<?php echo '<script'; ?>
 type="text/javascript" src="<?php echo $_smarty_tpl->tpl_vars['basehref']->value;?>
gui/javascript/execSetResults.js" language="javascript"><?php echo '</script'; ?>
>

	<?php echo '<script'; ?>
 type="text/javascript" language="javascript">
	//<!--
	var fRoot = '<?php echo $_smarty_tpl->tpl_vars['basehref']->value;?>
';
	var menuUrl = '<?php echo $_smarty_tpl->tpl_vars['menuUrl']->value;?>
';
	var args  = '<?php echo $_smarty_tpl->tpl_vars['args']->value;?>
';
	var additionalArgs  = '<?php echo $_smarty_tpl->tpl_vars['additionalArgs']->value;?>
';
	var printPreferences = '<?php echo $_smarty_tpl->tpl_vars['printPreferences']->value;?>
';
	
	// To solve problem diplaying help
	var SP_html_help_file  = '<?php echo $_smarty_tpl->tpl_vars['SP_html_help_file']->value;?>
';
	
	//attachment related JS-Stuff
	var attachmentDlg_refWindow = null;
	var attachmentDlg_refLocation = null;
	var attachmentDlg_bNoRefresh = false;
	
	// bug management (using logic similar to attachment)
	var bug_dialog = new bug_dialog();

	// for ext js
	var extjsLocation = '<?php echo @constant('TL_EXTJS_RELATIVE_PATH');?>
';
	
	//-->
	<?php echo '</script'; ?>
> 

    <?php $_smarty_tpl->_subTemplateRender("file:custom_inc_head.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

<?php if ($_smarty_tpl->tpl_vars['openHead']->value == "no") {?> </head>
<?php }
}
}
