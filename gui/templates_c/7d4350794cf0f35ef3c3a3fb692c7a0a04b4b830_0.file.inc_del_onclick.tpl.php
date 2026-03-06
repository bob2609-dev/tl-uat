<?php
/* Smarty version 3.1.33, created on 2026-03-05 21:20:42
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\inc_del_onclick.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69a9e59a434315_40806725',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7d4350794cf0f35ef3c3a3fb692c7a0a04b4b830' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\inc_del_onclick.tpl',
      1 => 1579460696,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:inc_ext_js.tpl' => 1,
  ),
),false)) {
function content_69a9e59a434315_40806725 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_subTemplateRender("file:inc_ext_js.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('s'=>'Yes','var'=>"yes_b"),$_smarty_tpl ) );?>

<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('s'=>'No','var'=>"no_b"),$_smarty_tpl ) );?>

<?php $_smarty_tpl->_assignInScope('body_onload', "onload=\"init_yes_no_buttons('".((string)$_smarty_tpl->tpl_vars['yes_b']->value)."','".((string)$_smarty_tpl->tpl_vars['no_b']->value)."');\"");
echo '<script'; ?>
 type="text/javascript">
/*
  function: delete_confirmation

  args: o_id: object id, id of object on with do_action() will be done.
              is not a DOM id, but an specific application id.
              IMPORTANT: do_action() is a function defined in this file

        o_name: name of object, used to to give user feedback.

        title: pop up title
                    
        msg: can contain a wildcard (%s), that will be replaced
             with o_name.     
  
  returns: 

*/
function delete_confirmation(o_id,o_name,title,msg,pFunction) {
	var safe_name = escapeHTML(o_name);
  var safe_title = title;
  var my_msg = msg.replace('%s',safe_name);
  if (!pFunction) {
		pFunction = do_action;
  }
  Ext.Msg.confirm(safe_title, my_msg,
			            function(btn, text)
			            { 
					         pFunction(btn,text,o_id);
			            });
}

/*
  function: 

  args:
  
  returns: 

*/
function init_yes_no_buttons(yes_btn,no_btn) {
  Ext.MessageBox.buttonText.yes=yes_btn;
  Ext.MessageBox.buttonText.no=no_btn;
}

/*
  function: 

  args:
  
  returns: 

*/
function do_action(btn, text, o_id) { 
  // IMPORTANT:
  // del_action is defined in SMARTY TEMPLATE that is using this logic.
  //
	var my_action='';
  
  if( btn == 'yes' ) {
    my_action=del_action+o_id;
	  window.location=my_action;
	}
}					

/*
  function: 

  args:
  
  returns: 

*/
function alert_message(title,msg) {
  Ext.MessageBox.alert(escapeHTML(title), escapeHTML(msg));
}

/**
 * Displays an alert message. title and message must be escaped.
 */
function alert_message_html(title,msg){
  Ext.MessageBox.alert(title, msg);
}


/**
 *
 *
 */
function escapeHTML(str) {
  return str
    .replace(/&/g, '&amp;')
    .replace(/>/g, '&gt;')
    .replace(/</g, '&lt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&apos;');
}

<?php echo '</script'; ?>
><?php }
}
