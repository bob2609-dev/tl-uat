<?php
/* Smarty version 3.1.33, created on 2026-03-09 13:09:39
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\plan\planAddTCNavigator.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69aeb883019f46_96046863',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '1135c6d8ea1b0fbe043a8559b22ec9ffd1773cb5' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\plan\\planAddTCNavigator.tpl',
      1 => 1579460696,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
    'file:inc_head.tpl' => 1,
    'file:inc_ext_js.tpl' => 1,
    'file:inc_filter_panel_js.tpl' => 1,
    'file:inc_filter_panel.tpl' => 1,
    'file:tree_control_add_tc_navigator.inc.tpl' => 1,
  ),
),false)) {
function content_69aeb883019f46_96046863 (Smarty_Internal_Template $_smarty_tpl) {
echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('var'=>"labels",'s'=>'keywords_filter_help,btn_apply_filter,execution_type,importance,
             btn_update_menu,title_navigator,keyword,test_plan,keyword,caption_nav_filter_settings'),$_smarty_tpl ) );?>


<?php $_smarty_tpl->_subTemplateRender("file:inc_head.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('openHead'=>"yes"), 0, false);
$_smarty_tpl->_subTemplateRender("file:inc_ext_js.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('bResetEXTCss'=>1), 0, false);
?>

<?php echo '<script'; ?>
 type="text/javascript" src='gui/javascript/ext_extensions.js'><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript">
  treeCfg = { tree_div_id:'tree_div',root_name:"",root_id:0,root_href:"",loader:"", 
              enableDD:false, dragDropBackEndUrl:"",children:"" };

  Ext.onReady(function() {
    Ext.state.Manager.setProvider(new Ext.state.CookieProvider());

    // Use a collapsible panel for filter settings
    // and place a help icon in ther header
    var settingsPanel = new Ext.ux.CollapsiblePanel({
      id: 'tl_exec_filter',
      applyTo: 'settings_panel',
      tools: [{
        id: 'help',
        handler: function(event, toolEl, panel) {
          show_help(help_localized_text);
        }
      }]
    });
    var filtersPanel = new Ext.ux.CollapsiblePanel({
      id: 'tl_exec_settings',
      applyTo: 'filter_panel'
    });

    <?php if ($_smarty_tpl->tpl_vars['gui']->value->loadRightPaneAddTC) {?>  
      EP();
    <?php }?>

  });
<?php echo '</script'; ?>
>
<?php if ($_smarty_tpl->tpl_vars['gui']->value->ajaxTree->loader == '') {?>
  <?php echo '<script'; ?>
 type="text/javascript">
  treeCfg = { tree_div_id:'tree_div',root_name:"",root_id:0,root_href:"",
              loader:"", enableDD:false, dragDropBackEndUrl:'',children:"" };

  treeCfg.root_name='<?php echo strtr($_smarty_tpl->tpl_vars['gui']->value->ajaxTree->root_node->name, array("\\" => "\\\\", "'" => "\\'", "\"" => "\\\"", "\r" => "\\r", "\n" => "\\n", "</" => "<\/" ));?>
';
  treeCfg.root_id=<?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->root_node->id;?>
;
  treeCfg.root_href='<?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->root_node->href;?>
';
  treeCfg.children=<?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->children;?>
;
  treeCfg.cookiePrefix = "<?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->cookiePrefix;?>
";
  <?php echo '</script'; ?>
>
  <?php echo '<script'; ?>
 type="text/javascript" src='gui/javascript/execTree.js'><?php echo '</script'; ?>
>
<?php } else { ?>
  <?php echo '<script'; ?>
 type="text/javascript">
  treeCfg = { tree_div_id:'tree_div',root_name:"",root_id:0,root_href:"",
              root_testlink_node_type:'',useBeforeMoveNode:false,
              loader:"", enableDD:false, dragDropBackEndUrl:'' };

  treeCfg.loader = "<?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->loader;?>
";
  treeCfg.root_name = '<?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->root_node->wrapOpen;?>
' + 
                      "<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->ajaxTree->root_node->name, ENT_QUOTES, 'UTF-8', true);?>
" +
                      '<?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->root_node->wrapClose;?>
';

  treeCfg.root_id = <?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->root_node->id;?>
;
  treeCfg.root_href = "<?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->root_node->href;?>
";
  treeCfg.cookiePrefix = "<?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->cookiePrefix;?>
";
  <?php echo '</script'; ?>
>
        
 <?php echo '<script'; ?>
 type="text/javascript" src="gui/javascript/treebyloader.js"><?php echo '</script'; ?>
>
<?php }?>

<?php echo '<script'; ?>
 type="text/javascript">
function pre_submit()
{
  document.getElementById('called_url').value=parent.workframe.location;
  return true;
}
<?php echo '</script'; ?>
>

<?php $_smarty_tpl->_subTemplateRender('file:inc_filter_panel_js.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

  
<h1 class="title"><?php echo $_smarty_tpl->tpl_vars['gui']->value->title_navigator;?>
</h1>
<div style="margin: 3px;">

<?php if ($_smarty_tpl->tpl_vars['gui']->value->loadRightPaneAddTC) {?>
    
<?php }?>

<?php $_smarty_tpl->_subTemplateRender('file:inc_filter_panel.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
$_smarty_tpl->_subTemplateRender("file:tree_control_add_tc_navigator.inc.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<div id="tree_div" style="overflow:auto; height:100%;border:1px solid #c3daf9;"></div>

<?php echo '<script'; ?>
 type="text/javascript"><?php echo '</script'; ?>
>
</body>
</html><?php }
}
