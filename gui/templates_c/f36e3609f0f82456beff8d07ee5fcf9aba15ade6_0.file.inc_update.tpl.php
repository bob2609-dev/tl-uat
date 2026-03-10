<?php
/* Smarty version 3.1.33, created on 2026-03-09 13:09:11
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\inc_update.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69aeb867804099_89339695',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'f36e3609f0f82456beff8d07ee5fcf9aba15ade6' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\inc_update.tpl',
      1 => 1579460696,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:inc_refreshTreeWithFilters.tpl' => 1,
  ),
),false)) {
function content_69aeb867804099_89339695 (Smarty_Internal_Template $_smarty_tpl) {
if ($_smarty_tpl->tpl_vars['user_feedback']->value != '') {?>
    <?php if ($_smarty_tpl->tpl_vars['feedback_type']->value != '') {?>
    	<div class="<?php echo $_smarty_tpl->tpl_vars['feedback_type']->value;?>
">
  	<?php } else { ?>
     <div class="user_feedback">
  	 <?php }?>
		<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['user_feedback']->value, 'msg');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['msg']->value) {
?>
			<p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['msg']->value, ENT_QUOTES, 'UTF-8', true);?>
</p>
		<?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
     </div>

<?php } else { ?>
  <?php if ($_smarty_tpl->tpl_vars['result']->value == "ok") {?>
  
    <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('s'=>$_smarty_tpl->tpl_vars['action']->value,'var'=>'action'),$_smarty_tpl ) );?>

  	<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('s'=>$_smarty_tpl->tpl_vars['item']->value,'var'=>'item'),$_smarty_tpl ) );?>

  	
    <?php if ($_smarty_tpl->tpl_vars['feedback_type']->value == "soft") {?>
    	<div class="warning_<?php echo $_smarty_tpl->tpl_vars['feedback_type']->value;?>
">	
  		<p><?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value)===null||$tmp==='' ? "item" : $tmp);?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['name']->value, ENT_QUOTES, 'UTF-8', true);?>
</p> 
        	<p><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('s'=>'was_success'),$_smarty_tpl ) );?>
 <?php echo (($tmp = @$_smarty_tpl->tpl_vars['action']->value)===null||$tmp==='' ? "updated" : $tmp);?>
!</p>
    	</div>
  	<?php } else { ?>
    	<div class="user_feedback">
  	  	<p><?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value)===null||$tmp==='' ? "item" : $tmp);?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['name']->value, ENT_QUOTES, 'UTF-8', true);?>
 
           <?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('s'=>'was_success'),$_smarty_tpl ) );?>
 <?php echo (($tmp = @$_smarty_tpl->tpl_vars['action']->value)===null||$tmp==='' ? "updated" : $tmp);?>
!</p>
  	</div>
    <?php }?>
    
  
  <?php } elseif ($_smarty_tpl->tpl_vars['result']->value != '') {?>
  
    <?php if ($_smarty_tpl->tpl_vars['feedback_type']->value == "soft") {?>
  		<div class="warning_<?php echo $_smarty_tpl->tpl_vars['feedback_type']->value;?>
">	
  		  <p><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('s'=>'warning'),$_smarty_tpl ) );?>
</p> 
  			<p><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['result']->value, ENT_QUOTES, 'UTF-8', true);?>
</p>
    	</div>
  	<?php } else { ?>
    	<div class="error">
        <p>
    		<?php if ($_smarty_tpl->tpl_vars['name']->value == '') {?>
    			<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('s'=>'info_failed_db_upd'),$_smarty_tpl ) );?>

    		<?php } else { ?>
    			<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('s'=>'info_failed_db_upd_details'),$_smarty_tpl ) );?>
 <?php echo (($tmp = @$_smarty_tpl->tpl_vars['item']->value)===null||$tmp==='' ? "item" : $tmp);?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['name']->value, ENT_QUOTES, 'UTF-8', true);?>

    		<?php }?>
        </p>
    		<p><?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('s'=>'invalid_query'),$_smarty_tpl ) );?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['result']->value, ENT_QUOTES, 'UTF-8', true);?>
</p>
    	</div>
  	<?php }?>
  <?php }
}?>  
<?php if ($_smarty_tpl->tpl_vars['result']->value == "ok" && isset($_smarty_tpl->tpl_vars['refresh']->value) && $_smarty_tpl->tpl_vars['refresh']->value) {?>
	<?php $_smarty_tpl->_subTemplateRender("file:inc_refreshTreeWithFilters.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
}
}
}
