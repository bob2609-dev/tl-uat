<?php
/* Smarty version 3.1.33, created on 2026-03-05 21:20:42
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\inc_show_hide_mgmt.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69a9e59a4931f1_61653807',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '752f0795c427ec92b14a1caa966fb31f085f6c04' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\inc_show_hide_mgmt.tpl',
      1 => 1579460696,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_69a9e59a4931f1_61653807 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_assignInScope('show_hide_container_draw', (($tmp = @$_smarty_tpl->tpl_vars['show_hide_container_draw']->value)===null||$tmp==='' ? false : $tmp));
$_smarty_tpl->_assignInScope('show_hide_container_class', (($tmp = @$_smarty_tpl->tpl_vars['show_hide_container_class']->value)===null||$tmp==='' ? "exec_additional_info" : $tmp));?>


<input type='hidden' id="<?php echo $_smarty_tpl->tpl_vars['show_hide_container_view_status_id']->value;?>
"
         name="<?php echo $_smarty_tpl->tpl_vars['show_hide_container_view_status_id']->value;?>
"  value="0" />

<div class="x-panel-header x-unselectable">
	<div class="x-tool x-tool-toggle" style="background-position:0 -75px; float:left;"
		onclick="show_hide('<?php echo $_smarty_tpl->tpl_vars['show_hide_container_id']->value;?>
',
	              '<?php echo $_smarty_tpl->tpl_vars['show_hide_container_view_status_id']->value;?>
',
	              document.getElementById('<?php echo $_smarty_tpl->tpl_vars['show_hide_container_id']->value;?>
').style.display=='none')">
	</div>
	<span style="padding:2px;"><?php echo $_smarty_tpl->tpl_vars['show_hide_container_title']->value;?>
</span>
</div>

<?php if ($_smarty_tpl->tpl_vars['show_hide_container_draw']->value) {?>
	<div id="<?php echo $_smarty_tpl->tpl_vars['show_hide_container_id']->value;?>
" class="<?php echo $_smarty_tpl->tpl_vars['show_hide_container_class']->value;?>
">
		<?php echo $_smarty_tpl->tpl_vars['show_hide_container_html']->value;?>

	</div>
<?php }
}
}
