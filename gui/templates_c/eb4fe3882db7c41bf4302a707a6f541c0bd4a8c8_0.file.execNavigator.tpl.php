<?php
/* Smarty version 3.1.33, created on 2026-03-05 21:20:26
  from 'C:\xampp\htdocs\tl-uat\gui\templates\tl-classic\execute\execNavigator.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_69a9e58a173a98_89611610',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'eb4fe3882db7c41bf4302a707a6f541c0bd4a8c8' => 
    array (
      0 => 'C:\\xampp\\htdocs\\tl-uat\\gui\\templates\\tl-classic\\execute\\execNavigator.tpl',
      1 => 1772105082,
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
function content_69a9e58a173a98_89611610 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'C:\\xampp\\htdocs\\tl-uat\\vendor\\smarty\\smarty\\libs\\plugins\\modifier.replace.php','function'=>'smarty_modifier_replace',),));
?>

<?php echo call_user_func_array( $_smarty_tpl->smarty->registered_plugins[Smarty::PLUGIN_FUNCTION]['lang_get'][0], array( array('var'=>"labels",'s'=>"filter_result,caption_nav_filter_settings,filter_owner,test_plan,filter_on,
             platform,exec_build,btn_apply_filter,build,keyword,filter_tcID,execution_type,
             include_unassigned_testcases,priority,caption_nav_filters,caption_nav_settings,
             block_filter_not_run_latest_exec"),$_smarty_tpl ) );?>
       

<?php $_smarty_tpl->_subTemplateRender("file:inc_head.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('openHead'=>"yes"), 0, false);
$_smarty_tpl->_subTemplateRender("file:inc_ext_js.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array('bResetEXTCss'=>1), 0, false);
?>

<?php echo '<script'; ?>
 type="text/javascript" src='gui/javascript/ext_extensions.js'><?php echo '</script'; ?>
>

<?php echo '<script'; ?>
 type="text/javascript">
var msg_block_filter_not_run_latest_exec = '<?php echo $_smarty_tpl->tpl_vars['labels']->value['block_filter_not_run_latest_exec'];?>
';
var code_lastest_exec_method = <?php echo $_smarty_tpl->tpl_vars['gui']->value->lastest_exec_method;?>
;
var code_not_run = '<?php echo $_smarty_tpl->tpl_vars['gui']->value->not_run;?>
';

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

  // CRITIC - this has to be done NOT ALWAYS but according operation
  // Example: after a Test Execution is WRONG
  // Basically this has to be done ONLY if submit has been done on LEFT PANE TREE
  // Need to understand if I can know this
  //
  <?php if ($_smarty_tpl->tpl_vars['gui']->value->loadExecDashboard) {?>
    EXDS(); // Load on right pane EXecution DaShboard
  <?php }?>  
  
  // Performance optimization: Automatically uncheck refresh tree on action
  // This should happen AFTER the tree has finished loading to avoid interference
  // We'll use a timeout to ensure tree is fully loaded
  setTimeout(function() {
    var refreshTreeCheckbox = document.getElementById('cbsetting_refresh_tree_on_action');
    if (refreshTreeCheckbox && refreshTreeCheckbox.checked) {
      refreshTreeCheckbox.checked = false;
      console.log('Performance optimization: "Refresh Tree on Action" checkbox automatically unchecked after tree loaded for better performance');
    console.log('Page has been loaded!')
      // Optional: Trigger change event if needed
      if (typeof refreshTreeCheckbox.onchange === 'function') {
        refreshTreeCheckbox.onchange();
      }
    }
  }, 2000); // Wait 2 seconds for tree to fully load
});

/**
 * 
 * IMPORTANT DEVELOPMENT NOTICE
 * ATTENTION args is a GLOBAL Javascript variable, then be CAREFULL
 */
function openExportTestPlan(windows_title,tproject_id,tplan_id,platform_id,build_id,mode,form_token) 
{
  wargs = "tproject_id=" + tproject_id + "&tplan_id=" + tplan_id + "&platform_id=" + platform_id + "&build_id=" + build_id;  
  wargs = wargs + "&closeOnCancel=1&exportContent=" + mode;
  wargs = wargs + "&form_token=" + form_token;
  wref = window.open(fRoot+"lib/plan/planExport.php?"+wargs,
                     windows_title,"menubar=no,width=650,height=500,toolbar=no,scrollbars=yes");
  wref.focus();
}

/**
 * Performance optimization: Update single test case node in tree after execution
 * This avoids refreshing entire tree for 48k+ test cases
 */
window.updateTestCaseNodeInTree = function(tcaseId, newStatus, statusColor) {
  console.log('DEBUG: updateTestCaseNodeInTree called with:', tcaseId, newStatus, statusColor);
  try {
    // Find the tree node for this test case
    var treeNode = findTreeNodeById(tcaseId);
    if (treeNode) {
      // Update the node's status icon and color
      if (statusColor) {
        treeNode.attributes.style = 'color:' + statusColor;
      }
      
      // Update the node text to reflect new status
      var currentText = treeNode.text || '';
      var statusText = ' [' + newStatus.toUpperCase() + ']';
      
      // Remove old status if present
      currentText = currentText.replace(/\s*\[.*?\]/g, '');
      
      // Add new status
      treeNode.text = currentText + statusText;
      
      // Refresh just this node, not the entire tree
      if (typeof treePanel !== 'undefined' && treePanel.getNodeById) {
        treePanel.getNodeById(tcaseId).setText(treeNode.text);
        treePanel.getNodeById(tcaseId).ui = treeNode;
      }
      
      console.log('Performance optimization: Updated test case ' + tcaseId + ' status to ' + newStatus + ' without full tree refresh');
    } else {
      console.log('DEBUG: Tree node not found for tcaseId:', tcaseId);
    }
  } catch (e) {
    console.log('Error updating tree node:', e);
  }
};

// Also create a regular function for compatibility
function updateTestCaseNodeInTree(tcaseId, newStatus, statusColor) {
  return window.updateTestCaseNodeInTree(tcaseId, newStatus, statusColor);
}

// Debug: Log when function is available
console.log('DEBUG: updateTestCaseNodeInTree function is now available:', typeof window.updateTestCaseNodeInTree);

// Test function availability
if (typeof window.updateTestCaseNodeInTree === 'function') {
  console.log('DEBUG: Function is ready for iframe calls');
} else {
  console.log('DEBUG: Function is NOT ready for iframe calls');
}

// Add a global test function that iframes can call
window.testFunctionAvailability = function() {
  console.log('DEBUG: testFunctionAvailability called');
  console.log('DEBUG: updateTestCaseNodeInTree type:', typeof window.updateTestCaseNodeInTree);
  return typeof window.updateTestCaseNodeInTree === 'function';
};

// Log that we're done setting up
console.log('DEBUG: execNavigator.tpl setup complete');

// Polling mechanism to check for pending tree updates from iframes
setInterval(function() {
  if (window.pendingTreeUpdate) {
    console.log('DEBUG: Found pending tree update:', window.pendingTreeUpdate);
    if (typeof window.updateTestCaseNodeInTree === 'function') {
      window.updateTestCaseNodeInTree(
        window.pendingTreeUpdate.tcaseId,
        window.pendingTreeUpdate.status,
        window.pendingTreeUpdate.color
      );
      console.log('DEBUG: Processed pending tree update');
      window.pendingTreeUpdate = null; // Clear the pending update
    } else {
      console.log('DEBUG: updateTestCaseNodeInTree function not available for pending update');
    }
  }
}, 500); // Check every 500ms

/**
 * Helper function to find tree node by test case ID
 */
function findTreeNodeById(tcaseId) {
  // This function will need to be implemented based on your tree structure
  // It should search through the tree nodes and return the matching node
  if (typeof treePanel !== 'undefined' && treePanel.getRootNode) {
    return findNodeRecursive(treePanel.getRootNode(), tcaseId);
  }
  return null;
}

/**
 * Recursive helper to search tree nodes
 */
function findNodeRecursive(node, tcaseId) {
  if (node.id == 'tc_' + tcaseId) {
    return node;
  }
  if (node.children) {
    for (var i = 0; i < node.children.length; i++) {
      var found = findNodeRecursive(node.children[i], tcaseId);
      if (found) return found;
    }
  }
  return null;
}



/**
 * 
 *
 */
function validateForm(the_form)
{
  var filterMethod = document.getElementById('filter_result_method');
  var execStatus = document.getElementById('filter_result_result');
  var loop2do = execStatus.length;
  var idx = 0;
  var notRunFound = false;
  var status_ok = true;

  if( filterMethod.value == code_lastest_exec_method)
  {
    for(idx=0; idx<loop2do; idx++)
    {
      if(execStatus[idx].selected && execStatus[idx].value == code_not_run)
      {
        status_ok = false;
        console.log('Filter blocked: Cannot filter on "Not Run" latest execution');
        break;
      }
    }
  }
  return status_ok;
}

treeCfg.root_name='<?php echo strtr($_smarty_tpl->tpl_vars['gui']->value->ajaxTree->root_node->name, array("\\" => "\\\\", "'" => "\\'", "\"" => "\\\"", "\r" => "\\r", "\n" => "\\n", "</" => "<\/" ));?>
';
treeCfg.root_id=<?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->root_node->id;?>
;
treeCfg.root_href='<?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->root_node->href;?>
';
treeCfg.children=<?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->children;?>
;
treeCfg.cookiePrefix='<?php echo $_smarty_tpl->tpl_vars['gui']->value->ajaxTree->cookiePrefix;?>
';
<?php echo '</script'; ?>
>

<?php echo '<script'; ?>
 type="text/javascript" src='gui/javascript/execTreeWithMenu.js'><?php echo '</script'; ?>
>
<?php echo '<script'; ?>
 language="JavaScript" src="gui/javascript/expandAndCollapseFunctions.js" type="text/javascript"><?php echo '</script'; ?>
>
<?php $_smarty_tpl->_subTemplateRender('file:inc_filter_panel_js.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>

  
<?php $_smarty_tpl->_assignInScope('cfg_section', smarty_modifier_replace(call_user_func_array($_smarty_tpl->registered_plugins[ 'modifier' ][ 'basename' ][ 0 ], array( basename($_smarty_tpl->source->filepath) )),".tpl",''));
$_smarty_tpl->_assignInScope('build_number', $_smarty_tpl->tpl_vars['control']->value->settings['setting_build']['selected']);
$_smarty_tpl->smarty->ext->configLoad->_loadConfigFile($_smarty_tpl, "input_dimensions.conf", $_smarty_tpl->tpl_vars['cfg_section']->value, 0);
?>


<h1 class="title"><?php echo $_smarty_tpl->tpl_vars['gui']->value->pageTitle;?>
</h1>
<?php $_smarty_tpl->_subTemplateRender('file:inc_filter_panel.tpl', $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
$_smarty_tpl->_subTemplateRender("file:inc_tree_control.tpl", $_smarty_tpl->cache_id, $_smarty_tpl->compile_id, 0, $_smarty_tpl->cache_lifetime, array(), 0, false);
?>
<div id="tree_div" style="overflow:auto; height:100%;border:1px solid #c3daf9;"></div>
</body>
</html><?php }
}
