 
### 1. Configuration File

**File:** `custom_config.inc.php`
Add this to your custom configuration file to control the feature.

```php
<?php
// ... existing config ...

// *******************************************************************************
// PERFORMANCE OPTIMIZATION
// *******************************************************************************
/** * @global boolean $g_disable_execution_counters
 * Set to TRUE to disable recursive calculation of passed/failed/blocked counters 
 * in the execution tree.
 * Recommended for projects with large datasets (>10k test cases) to improve load time.
 * Side Effect: "Hide Test Cases" filter and "Remove Empty Folders" logic 
 * will be disabled when this is active.
 */
$g_disable_execution_counters = false;
?>

```

---

### 2. Execution Tree Logic

**File:** `lib/functions/execTreeMenu.inc.php`

**Changes applied:**

1. Imported the global variable.
2. Wrapped the recursive `prepareExecTreeNode` call in a conditional check.
3. Ensured the render options respect the global flag.

```php
<?php
/**
 * TestLink Open Source Project - http://testlink.sourceforge.net/ 
 * ... (header comments) ...
 */

function execTree(&$dbHandler, &$menuUrl, $context, $objFilters, $objOptions)
{
  // 1. IMPORT GLOBAL VARIABLE
  global $g_disable_execution_counters;
  
  $chronos[] = microtime(true);

  $treeMenu = new stdClass();
  $treeMenu->rootnode = null;
  $treeMenu->menustring = '';
  $resultsCfg = config_get('results');
  $glueChar = config_get('testcase_cfg')->glue_character;

  $menustring = null;
  $tplan_tcases = null;
  $tck_map = null;
  $idx = 0;
  $testCaseQty = 0;
  $testCaseSet = null;

  $renderTreeNodeOpt = array();
  $renderTreeNodeOpt['showTestCaseID'] = config_get('treemenu_show_testcase_id');

  $renderTreeNodeOpt['alertOnTestSuiteTCQty'] = 0;
  if (property_exists($objOptions, 'alertOnTestSuiteTCQty')) {
    $renderTreeNodeOpt['alertOnTestSuiteTCQty'] = $objOptions->alertOnTestSuiteTCQty;
  }

  list(
    $filters,
    $options,
    $renderTreeNodeOpt['showTestSuiteContents'],
    $renderTreeNodeOpt['useCounters'],
    $renderTreeNodeOpt['useColors'],
    $colorBySelectedBuild
  ) = initExecTree($objFilters, $objOptions);

  $renderTreeNodeOpt['showTestCaseExecStatus'] = $options['showTestCaseExecStatus'];

  if (property_exists($objOptions, 'actionJS')) {
    if (isset($objOptions->actionJS['testproject'])) {
      $renderTreeNodeOpt['actionJS']['testproject'] = $objOptions->actionJS['testproject'];
    }
  }

  $tplan_mgr = new testplan($dbHandler);
  $tproject_mgr = new testproject($dbHandler);
  $tcase_node_type = $tplan_mgr->tree_manager->node_descr_id['testcase'];

  $hash_descr_id = $tplan_mgr->tree_manager->get_available_node_types();
  $hash_id_descr = array_flip($hash_descr_id);

  $tcase_prefix = $tproject_mgr->getTestCasePrefix($context['tproject_id']) . $glueChar;

  $my['options'] = array(
    'recursive' => true,
    'remove_empty_nodes_of_type' => $tplan_mgr->tree_manager->node_descr_id['testsuite'],
    'order_cfg' => array("type" => 'exec_order', "tplan_id" => $context['tplan_id'])
  );

  $my['filters'] = array(
    'exclude_node_types' =>
      array(
        'testplan' => 'exclude_me',
        'requirement_spec' => 'exclude_me',
        'requirement' => 'exclude_me'
      ),
    'exclude_children_of' =>
      array(
        'testcase' => 'exclude_my_children',
        'requirement_spec' => 'exclude_my_children'
      )
  );

  if (isset($objFilters->filter_toplevel_testsuite) && is_array($objFilters->filter_toplevel_testsuite)) {
    $my['filters']['exclude_branches'] = $objFilters->filter_toplevel_testsuite;
  }

  if (isset($objFilters->filter_custom_fields) && is_array($objFilters->filter_custom_fields)) {
    $my['filters']['filter_custom_fields'] = $objFilters->filter_custom_fields;
  }

  $tplan_tcases = null;
  $linkedTestCasesSet = null;

  if (is_null($filters['tcase_id']) || $filters['tcase_id'] > 0) {
    $applyTCCAlgo = false;
    $tcc = null;
    if (!is_null($sql2do = $tplan_mgr->getLinkedForExecTree($context['tplan_id'], $filters, $options))) {

      $applyTCCAlgo =
        ($objOptions->exec_tree_counters_logic == USE_LATEST_EXEC_ON_TESTPLAN_FOR_COUNTERS ||
          $objOptions->exec_tree_counters_logic ==
          USE_LATEST_EXEC_ON_TESTPLAN_PLAT_FOR_COUNTERS);

      $kmethod = "fetchRowsIntoMap";
      if (is_array($sql2do)) {
        if ($filters['keyword_filter_type'] == 'And') {
          $kmethod = "fetchRowsIntoMapAddRC";
          $unionClause = " UNION ALL ";
        } else {
          $kmethod = "fetchRowsIntoMap";
          $unionClause = ' UNION ';
        }
        $sql2run = $sql2do['exec'] . $unionClause . $sql2do['not_run'];
      } else {
        $sql2run = $sql2do;
      }
      $tplan_tcases = $dbHandler->$kmethod($sql2run, 'tcase_id');
    }

    if ($applyTCCAlgo) {
      switch ($objOptions->exec_tree_counters_logic) {
        case USE_LATEST_EXEC_ON_TESTPLAN_FOR_COUNTERS:
          $n3 = $tplan_mgr->getLinkedForExecTreeCross($context['tplan_id'], $filters, $options);
          break;

        case USE_LATEST_EXEC_ON_TESTPLAN_PLAT_FOR_COUNTERS:
          $n3 = $tplan_mgr->getLinkedForExecTreeIVU($context['tplan_id'], $filters, $options);
          break;
      }
      $ssx = $n3['exec'];
      if (is_array($n3)) {
        $ssx .= ' UNION ' . $n3['not_run'];
      }
      $tcc = $dbHandler->fetchRowsIntoMap($ssx, 'tcase_id');
    }
  }

  if ($filters['keyword_filter_type'] == 'And' && !is_null($tplan_tcases)) {
    $kwc = count($filters['keyword_id']);
    $ak = array_keys($tplan_tcases);
    $mx = null;
    foreach ($ak as $tk) {
      if ($tplan_tcases[$tk]['recordcount'] == $kwc) {
        $mx[$tk] = $tplan_tcases[$tk];
      }
    }
    $tplan_tcases = null;
    $tplan_tcases = $mx;
  }
  $setTestCaseStatus = $tplan_tcases;

  if (!is_null($tplan_tcases) && count($tplan_tcases) > 0) {
    $leaf_ids = array_keys($tplan_tcases);
    $spec = $tplan_mgr->getTreeFromLeaves($leaf_ids, $context['tproject_id']);
  } else {
    $spec = $tplan_mgr->getSkeleton(
      $context['tplan_id'],
      $context['tproject_id'],
      $my['filters'],
      $my['options']
    );
  }

  $test_spec = $spec[0];
  $test_spec['name'] = $context['tproject_name'] . " / " . $context['tplan_name'];
  $test_spec['id'] = $context['tproject_id'];
  $test_spec['node_type_id'] = $hash_descr_id['testproject'];
  $test_spec['node_type'] = 'testproject';
  $map_node_tccount = array();

  if (!is_null($tplan_tcases)) {
      $targetExecStatus = (array) (isset($objFilters->filter_result_result) ?
        $objFilters->filter_result_result : null);

      if (!is_null($targetExecStatus) && (!in_array($resultsCfg['status_code']['all'], $targetExecStatus))) {
        applyStatusFilters($context['tplan_id'], $tplan_tcases, $objFilters, $tplan_mgr, $resultsCfg['status_code']);
      }

      if (isset($my['filters']['filter_custom_fields']) && isset($test_spec['childNodes'])) {
        $cfx = cfForDesign($dbHandler, $my['filters']['filter_custom_fields']);
        if (!is_null($cfx)) {
          $test_spec['childNodes'] = filter_by_cf_values($dbHandler, $test_spec['childNodes'], $cfx, $hash_descr_id);
        }
      }

      if (null !== $tcc && count($tcc) > 0) {
        $tcIDSet = array_keys($tplan_tcases);
        foreach ($tcIDSet as $iID) {
          if (isset($tcc[$iID])) {
            $tplan_tcases[$iID]['exec_status'] = $tcc[$iID]['exec_status'];
          }
        }
      }

    $linkedTestCasesSet = null;
    if (isset($spec[1]['nindex'])) {
      $ltcs = $spec[1]['nindex'];
      $lt = array_keys((array) $tplan_tcases);
      $tl = array_flip($lt);
      foreach ($ltcs as &$ele) {
        if (isset($tl[$ele])) {
          $linkedTestCasesSet[] = $ele;
        }
      }
    }

    // 2. BYPASS RECURSION IF DISABLED
    if (!$g_disable_execution_counters) {
      $pnOptions = array('hideTestCases' => $options['hideTestCases'], 'viewType' => 'executionTree');
      $pnFilters = null;
      $testcase_counters = prepareExecTreeNode(
        $dbHandler,
        $test_spec,
        $map_node_tccount,
        $tplan_tcases,
        $pnFilters,
        $pnOptions
      );

      foreach ($testcase_counters as $key => $value) {
        $test_spec[$key] = $testcase_counters[$key];
      }
    } else {
      // Initialize dummy counters to avoid undefined index errors
      $testcase_counters = helperInitCounters();
      foreach ($testcase_counters as $key => $value) {
        $test_spec[$key] = $testcase_counters[$key];
      }
    }

    $renderTreeNodeOpt['hideTestCases'] = $options['hideTestCases'];
    $renderTreeNodeOpt['tc_action_enabled'] = 1;
    
    // 3. OVERRIDE RENDER OPTIONS
    $originalUseCounters = $renderTreeNodeOpt['useCounters'];
    $renderTreeNodeOpt['useCounters'] = !$g_disable_execution_counters && $originalUseCounters;

    renderExecTreeNode(
      1,
      $test_spec,
      $tplan_tcases,
      $hash_id_descr,
      $menuUrl,
      $tcase_prefix,
      $renderTreeNodeOpt
    );
  }

  $treeMenu->rootnode = new stdClass();
  $treeMenu->rootnode->name = $test_spec['text'];
  $treeMenu->rootnode->id = $test_spec['id'];
  $treeMenu->rootnode->leaf = $test_spec['leaf'];
  $treeMenu->rootnode->text = $test_spec['text'];
  $treeMenu->rootnode->position = $test_spec['position'];
  $treeMenu->rootnode->href = $test_spec['href'];

  $menustring = '';
  if (isset($test_spec['childNodes'])) {
    $menustring = str_ireplace('childNodes', 'children', json_encode($test_spec['childNodes']));
  }

  $target = array(',"' . REMOVEME . '"', '"' . REMOVEME . '",');
  $menustring = str_ireplace($target, array('', ''), $menustring);

  $target = array(':' . REMOVEME, '"' . REMOVEME . '"');
  $menustring = str_ireplace($target, array(':[]', ''), $menustring);

  $treeMenu->menustring = $menustring;

  return array($treeMenu, $linkedTestCasesSet);
}

// ... (rest of file remains unchanged) ...
?>

```

---

### 3. Tree Rendering Logic

**File:** `lib/functions/treeMenu.inc.php`

**Changes applied:**

1. Updated `renderExecTreeNode` to check the global flag before rendering counter string.
2. Updated `create_counters_info` to return early if disabled (extra safety).

```php
<?php
// ... (header comments and imports) ...

// ... (previous functions generateTestSpecTree, prepareNode, renderTreeNode remain unchanged) ...

function renderExecTreeNode($level,&$node,&$tcase_node,$hash_id_descr,$linkto,$testCasePrefix,$opt)
{
  static $resultsCfg;
  static $l18n; 
  static $pf; 
  static $doColouringOn;
  static $cssClasses;

  $node_type = $hash_id_descr[$node['node_type_id']];

  // ... (static initialization block unchanged) ...
  if(!$resultsCfg) {
     // ... (initialization logic) ...
     // ensure this block is preserved from your original file
     // ...
     $resultsCfg = config_get('results');
     $status_descr_code = $resultsCfg['status_code'];
     foreach($resultsCfg['status_label'] as $key => $value) {
       $l18n[$status_descr_code[$key]] = lang_get($value);
       $cssClasses[$status_descr_code[$key]] = $doColouringOn['testcase'] ? ('class="light_' . $key . '"') : ''; 
     }
     $pf['testsuite'] = $opt['hideTestCases'] ? 'TPLAN_PTS' : ($opt['showTestSuiteContents'] ? 'STS' : null); 
     $pf['testproject'] = $opt['hideTestCases'] ? 'TPLAN_PTP' : 'SP';
     // ... (rest of static init) ...
  }

  $name = htmlspecialchars($node['name'], ENT_QUOTES);
  $node['testlink_node_name'] = $name;
  $node['testlink_node_type'] = $node_type;

  switch($node_type) {
    case 'testproject':
    case 'testsuite':
      $node['leaf'] = false;

      $testcase_count = isset($node['testcase_count']) ? $node['testcase_count'] : 0; 
      $node['text'] = $name ." (" . $testcase_count . ")";
      
      // 1. CONDITIONAL COUNTER RENDERING
      if($opt['useCounters'] && !$GLOBALS['g_disable_execution_counters'])
      {
        $node['text'] .= create_counters_info($node,$doColouringOn['counters']);
      }

      if( isset($opt['nodeHelpText'][$node_type]) ) {
        $node['text'] = '<span title="' . $opt['nodeHelpText'][$node_type] . '">' . $node['text'] . '</span>';
      }  

      $pfn = !is_null($pf[$node_type]) ? $pf[$node_type] . "({$node['id']})" : null;
      if( 'testsuite' == $node_type && ($opt['alertOnTestSuiteTCQty'] >0) ) {
        if( $testcase_count > $opt['alertOnTestSuiteTCQty'] ) {
          $jfn = config_get('jsAlertOnTestSuiteTCQty');
          $pfn = $jfn;
        }
      }
    break;
      
    case 'testcase':
      // ... (testcase logic remains unchanged) ...
      $node['leaf'] = true;
      $pfn = null;
      if($opt['tc_action_enabled']) {
        $pfx = "ST";
        if(isset($pf[$node_type])) {
          $pfx = "$pf[$node_type]";
        }
        $pfn = $pfx . "({$node['id']},{$node['tcversion_id']})";
      }

      $node['text'] = "<span ";
      if( isset($tcase_node[$node['id']]) ) {
        if($opt['showTestCaseExecStatus']) {
          $status_code = $tcase_node[$node['id']]['exec_status'];
          $node['text'] .= "{$cssClasses[$status_code]} " . '  title="' .  $l18n[$status_code] . 
                           '" alt="' . $l18n[$status_code] . '">';
        }
      }  
  
      if($opt['showTestCaseID']) {
        $node['text'] .= "<b>" . htmlspecialchars($testCasePrefix . $node['external_id']) . "</b>:";
      } 
      $node['text'] .= "{$name}</span>";
    break;

    // ... (rest of switch cases unchanged) ...
    default:
       $pfn = "ST({$node['id']})"; 
       // ... 
    break; 
  }
  
  $node['position'] = isset($node['node_order']) ? $node['node_order'] : 0;
  $node['href'] = is_null($pfn)? '' : "javascript:{$pfn}";

  if( isset($tcase_node[$node['id']]) ) {
    unset($tcase_node[$node['id']]);
  }

  if (isset($node['childNodes']) && $node['childNodes']) {
    $nodes_qty = sizeof($node['childNodes']);
    for($idx = 0;$idx <$nodes_qty ;$idx++) {
      if(is_null($node['childNodes'][$idx]) || $node['childNodes'][$idx]==REMOVEME) {
        continue;
      }
      renderExecTreeNode($level+1,$node['childNodes'][$idx],$tcase_node,
                         $hash_id_descr,$linkto,$testCasePrefix,$opt);
    }
  }
  return;
}

function create_counters_info(&$node,$useColors)
{
  // 2. SAFETY CHECK IN COUNTER FUNCTION
  if ($GLOBALS['g_disable_execution_counters']) {
    return '';
  }
  
  static $keys2display;
  static $labelCache;

  if(!$labelCache)
  {
    $resultsCfg = config_get('results');
    $status_label = $resultsCfg['status_label'];
    $keys2display = array('not_run' => 'not_run');
    foreach( $resultsCfg['status_label_for_exec_ui'] as $key => $value)
    {
      if( $key != 'not_run') {
        $keys2display[$key]=$key;  
      }  
      $labelCache[$key] = lang_get($status_label[$key]);
    }
  } 

  $add_html='';
  foreach($keys2display as $key)
  {
    if( isset($node[$key]) ) {
      $css_class = $useColors ? (" class=\"light_{$key}\" ") : '';   
      $add_html .= "<span {$css_class} " . ' title="' . $labelCache[$key] . 
             '">' . $node[$key] . ",</span>";
    }
  }

  $add_html = "(" . rtrim($add_html,",</span>") . "</span>)"; 
  return $add_html;
}

// ... (rest of file) ...
?>

```

### Risks and Considerations

1. **Broken Filters:** When `$g_disable_execution_counters` is TRUE, the function `prepareExecTreeNode` is skipped. This function is responsible for:
* **Pruning empty folders:** Folders with no test cases will now appear in the tree.
* **"Hide Test Cases" Option:** This option relies on `prepareExecTreeNode` to remove leaf nodes. If enabled, this option will stop working (test cases will always show).
* **Keyword/Build filtering:** If you use filters that rely on cleaning up the tree *after* the initial SQL query, they might behave inconsistently.


2. **Performance Note (SQL vs PHP):**
* This modification disables the **PHP** recursion processing (iterating through your 90,000 nodes and summing integers). This is significant for CPU usage.
* It **does not** disable the initial SQL query (`$tplan_mgr->getLinkedForExecTree`) that fetches the execution status of the 90,000 rows.
* *Why?* We still need that data to determine which nodes are valid leaves and to color them (Pass/Fail icons).
* If you wanted to kill the SQL query too, the tree would lose all status coloring (everything would look like "Not Run"), which usually defeats the purpose of the Execution page.


3. **Memory Usage:**
* Loading 90k rows into the `$tplan_tcases` array consumes significant PHP memory. If you hit memory limits, increase `memory_limit` in your `php.ini`.