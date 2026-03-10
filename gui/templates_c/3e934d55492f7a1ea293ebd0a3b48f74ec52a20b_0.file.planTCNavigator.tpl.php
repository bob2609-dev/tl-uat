<?php
/* Smarty version 3.1.33, created on 2026-03-09 13:08:58
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\plan\planTCNavigator.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69aeb85a7defc9_90414168',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '3e934d55492f7a1ea293ebd0a3b48f74ec52a20b' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\plan\\planTCNavigator.tpl',
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
    'file:inc_tree_control.tpl' => 1,
  ),
),false)) {
function content_69aeb85a7defc9_90414168 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\tl-uat\\vendor\\smarty\\smarty\\libs\\plugins\\modifier.replace.php','function'=>'smarty_modifier_replace',),));
?>

<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('var'=>"labels",'s'=>'btn_update_menu,btn_apply_filter,keyword,keywords_filter_help,title_navigator,
             btn_bulk_update_to_latest_version,
             filter_owner,TestPlan,test_plan,caption_nav_filters,
             build,filter_tcID,filter_on,filter_result,platform, include_unassigned_testcases'),$_smarty_tpl ) );?>


<?php $_smarty_tpl->_subTemplateRender("file:inc_head.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('openHead'=>"yes"), 0, false);
$_smarty_tpl->_subTemplateRender("file:inc_ext_js.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('bResetEXTCss'=>1), 0, false);
?>

<?php echo '<script'; ?>
 type="text/javascript" src='gui/javascript/ext_extensions.js'><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 type="text/javascript">
  treeCfg = { tree_div_id:'tree_div',root_name:"",root_id:0,root_href:"",
              loader:"", enableDD:false, dragDropBackEndUrl:'',children:"" };
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
  });
  <?php echo '</script'; ?>
>

    <?php echo '<script'; ?>
 type="text/javascript">
    treeCfg = { tree_div_id:'tree_div',root_name:"",root_id:0,root_href:"",
                loader:"", enableDD:false, dragDropBackEndUrl:'',children:"" };
    <?php echo '</script'; ?>
>
    
    <?php echo '<script'; ?>
 type="text/javascript">
      treeCfg.root_name = '<?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->root_node->name;?>
';
      treeCfg.root_id = <?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->root_node->id;?>
;
      treeCfg.root_href = '<?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->root_node->href;?>
';
      treeCfg.children = <?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->children;?>
;
      treeCfg.cookiePrefix = "<?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->cookiePrefix;?>
";
    <?php echo '</script'; ?>
>
    
    <?php echo '<script'; ?>
 type="text/javascript" src='gui/javascript/execTree.js'>
    <?php echo '</script'; ?>
>

<?php echo '<script'; ?>
 type="text/javascript">
function pre_submit()
{
  document.getElementById('called_url').value = parent.workframe.location;
  return true;
}

/*
  function: update2latest
  args :
  returns:
*/
function update2latest(id)
{
  var action_url = fRoot+'/'+menuUrl+"?doAction=doBulkUpdateToLatest&level=testplan&id="+id+args;
  parent.workframe.location = action_url;
}
<?php echo '</script'; ?>
>


<?php $_smarty_tpl->_subTemplateRender('file:inc_filter_panel_js.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

 
<?php $_smarty_tpl->_assignInScope('cfg_section', smarty_modifier_replace(call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'basename' ][ 0 ], array( basename($_smarty_tpl->source->filepath) )),".tpl",''));
$_smarty_tpl->smarty->ext->configLoad->_loadConfigFile($_smarty_tpl, "input_dimensions.conf", $_smarty_tpl->tpl_vars['cfg_section']->value, 0);
?>


<h1 class="title"><?php echo $_smarty_tpl->tpl_vars['gui']->value->title_navigator;?>
 <?php echo htmlspecialchars($_smarty_tpl->tpl_vars['gui']->value->additional_string, ENT_QUOTES, 'UTF-8', true);?>
</h1>

<?php $_smarty_tpl->_subTemplateRender('file:inc_filter_panel.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
$_smarty_tpl->_subTemplateRender("file:inc_tree_control.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<div id="tree_div" style="overflow:auto; height:100%;border:1px solid #c3daf9;"></div>

<?php echo '<script'; ?>
 type="text/javascript">
<?php if ($_smarty_tpl->tpl_vars['gui']->value->src_workframe != '') {?>
  parent.workframe.location='<?php echo $_smarty_tpl->tpl_vars['gui']->value->src_workframe;?>
';
<?php }
echo '</script'; ?>
>

</body>
</html><?php }
}
