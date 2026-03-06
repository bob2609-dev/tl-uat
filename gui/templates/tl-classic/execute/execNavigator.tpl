{* 
TestLink Open Source Project - http://testlink.sourceforge.net/
@filesource execNavigator.tpl

@internal revisions
@since 1.9.13
*}

{lang_get var="labels"
          s="filter_result,caption_nav_filter_settings,filter_owner,test_plan,filter_on,
             platform,exec_build,btn_apply_filter,build,keyword,filter_tcID,execution_type,
             include_unassigned_testcases,priority,caption_nav_filters,caption_nav_settings,
             block_filter_not_run_latest_exec"}       

{include file="inc_head.tpl" openHead="yes"}
{include file="inc_ext_js.tpl" bResetEXTCss=1}

{* includes Ext.ux.CollapsiblePanel *}
<script type="text/javascript" src='gui/javascript/ext_extensions.js'></script>

<script type="text/javascript">
var msg_block_filter_not_run_latest_exec = '{$labels.block_filter_not_run_latest_exec}';
var code_lastest_exec_method = {$gui->lastest_exec_method};
var code_not_run = '{$gui->not_run}';

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
  {if $gui->loadExecDashboard}
    EXDS(); // Load on right pane EXecution DaShboard
  {/if}  
  
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

treeCfg.root_name='{$gui->ajaxTree->root_node->name|escape:'javascript'}';
treeCfg.root_id={$gui->ajaxTree->root_node->id};
treeCfg.root_href='{$gui->ajaxTree->root_node->href}';
treeCfg.children={$gui->ajaxTree->children};
treeCfg.cookiePrefix='{$gui->ajaxTree->cookiePrefix}';
</script>

<script type="text/javascript" src='gui/javascript/execTreeWithMenu.js'></script>
<script language="JavaScript" src="gui/javascript/expandAndCollapseFunctions.js" type="text/javascript"></script>
{include file='inc_filter_panel_js.tpl'}

{* 
 * !!!!! IMPORTANT !!!!!
 * Above included file closes <head> tag and opens <body>, so this is not done here.
 *}
  
{$cfg_section=$smarty.template|basename|replace:".tpl":""}
{$build_number=$control->settings.setting_build.selected}
{config_load file="input_dimensions.conf" section=$cfg_section}

<h1 class="title">{$gui->pageTitle}</h1>
{include file='inc_filter_panel.tpl'}
{include file="inc_tree_control.tpl"}
<div id="tree_div" style="overflow:auto; height:100%;border:1px solid #c3daf9;"></div>
</body>
</html>