<?php
/**
 * Optimized Tree Menu for Test Execution
 * 
 * This file provides performance-optimized versions of the tree menu functions
 * with caching, lazy loading, and efficient database queries.
 */

ini_set('memory_limit', '1024M'); // Reduced from 2048M

require_once('performance_optimizations.php');

/**
 * Optimized execTree function with caching and pagination
 */
function execTree_optimized(&$dbHandler, &$menuUrl, $context, $objFilters, $objOptions) {
    $chronos[] = microtime(true);
    
    $treeMenu = new stdClass(); 
    $treeMenu->rootnode = null;
    $treeMenu->menustring = '';
    
    $resultsCfg = config_get('results');
    $glueChar = config_get('testcase_cfg')->glue_character;
    
    // Initialize optimized components
    $treeGenerator = new OptimizedTreeGenerator($dbHandler);
    $cfManager = new LazyCustomFieldManager($dbHandler);
    
    $renderTreeNodeOpt = array();
    $renderTreeNodeOpt['showTestCaseID'] = config_get('treemenu_show_testcase_id');
    $renderTreeNodeOpt['alertOnTestSuiteTCQty'] = 0;
    
    if (property_exists($objOptions, 'alertOnTestSuiteTCQty')) {
        $renderTreeNodeOpt['alertOnTestSuiteTCQty'] = $objOptions->alertOnTestSuiteTCQty;
    }

    list($filters, $options,
         $renderTreeNodeOpt['showTestSuiteContents'],
         $renderTreeNodeOpt['useCounters'],
         $renderTreeNodeOpt['useColors'], $colorBySelectedBuild) = initExecTree($objFilters, $objOptions);

    $renderTreeNodeOpt['showTestCaseExecStatus'] = $options['showTestCaseExecStatus'];

    if (property_exists($objOptions, 'actionJS')) {
        if (isset($objOptions->actionJS['testproject'])) {
            $renderTreeNodeOpt['actionJS']['testproject'] = $objOptions->actionJS['testproject'];
        }  
    }  

    // Use cached testplan manager
    $tplan_mgr = new CachedTestPlan($dbHandler);
    $tproject_mgr = new testproject($dbHandler);
    $tcase_node_type = $tplan_mgr->tree_manager->node_descr_id['testcase'];

    $hash_descr_id = $tplan_mgr->tree_manager->get_available_node_types();
    $hash_id_descr = array_flip($hash_descr_id);      
    
    $tcase_prefix = $tproject_mgr->getTestCasePrefix($context['tproject_id']) . $glueChar;
    
    // Optimized tree generation with pagination
    $limit = isset($_GET['tree_limit']) ? intval($_GET['tree_limit']) : 1000;
    $offset = isset($_GET['tree_offset']) ? intval($_GET['tree_offset']) : 0;
    
    // Build optimized filters
    $my['options'] = array(
        'recursive' => true, 
        'remove_empty_nodes_of_type' => $tplan_mgr->tree_manager->node_descr_id['testsuite'],
        'order_cfg' => array("type" => 'exec_order', "tplan_id" => $context['tplan_id']),
        'limit' => $limit,
        'offset' => $offset
    );

    $my['filters'] = array(
        'exclude_node_types' => array(
            'testplan' => 'exclude_me',
            'requirement_spec'=> 'exclude_me',
            'requirement'=> 'exclude_me'
        ),
        'exclude_children_of' => array(
            'testcase' => 'exclude_my_children',
            'requirement_spec'=> 'exclude_my_children'
        )
    );

    // Add filtering for toplevel testsuite
    if (isset($objFilters->filter_toplevel_testsuite) && is_array($objFilters->filter_toplevel_testsuite)) {
        $my['filters']['exclude_branches'] = $objFilters->filter_toplevel_testsuite;
    }

    if (isset($objFilters->filter_custom_fields) && is_array($objFilters->filter_custom_fields)) {
        $my['filters']['filter_custom_fields'] = $objFilters->filter_custom_fields;
    }
    
    // Get optimized skeleton
    $spec = getOptimizedSkeleton($tplan_mgr, $context['tplan_id'], $context['tproject_id'], $my['filters'], $my['options']);
    $test_spec = $spec[0];
    
    $test_spec['name'] = $context['tproject_name'] . " / " . $context['tplan_name'];  
    $test_spec['id'] = $context['tproject_id'];
    $test_spec['node_type_id'] = $hash_descr_id['testproject'];
    $test_spec['node_type'] = 'testproject';
    $map_node_tccount = array();
    
    $tplan_tcases = null;
    $linkedTestCasesSet = null;

    if ($test_spec) {
        // Optimized test case retrieval
        if (is_null($filters['tcase_id']) || $filters['tcase_id'] > 0) {
            $applyTCCAlgo = false;
            $tcc = null;
            
            // Use optimized query with caching
            $sql2do = getOptimizedLinkedForExecTree($tplan_mgr, $context['tplan_id'], $filters, $options, $limit, $offset);
            
            if (!is_null($sql2do)) {
                $applyTCCAlgo = (
                    $objOptions->exec_tree_counters_logic == USE_LATEST_EXEC_ON_TESTPLAN_FOR_COUNTERS || 
                    $objOptions->exec_tree_counters_logic == USE_LATEST_EXEC_ON_TESTPLAN_PLAT_FOR_COUNTERS
                );

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
                
                $start_time = microtime(true);
                $tplan_tcases = $dbHandler->$kmethod($sql2run, 'tcase_id');
                $query_time = microtime(true) - $start_time;
                PerformanceMonitor::logQuery($sql2run, $query_time);
            }

            // Handle cross-platform counters if needed
            if ($applyTCCAlgo) {
                switch ($objOptions->exec_tree_counters_logic) {
                    case USE_LATEST_EXEC_ON_TESTPLAN_FOR_COUNTERS:
                        $n3 = getOptimizedLinkedForExecTreeCross($tplan_mgr, $context['tplan_id'], $filters, $options);
                        break;
                    
                    case USE_LATEST_EXEC_ON_TESTPLAN_PLAT_FOR_COUNTERS:
                        $n3 = getOptimizedLinkedForExecTreeIVU($tplan_mgr, $context['tplan_id'], $filters, $options);
                        break;
                }
                
                $ssx = $n3['exec'];
                if (is_array($n3)) {
                    $ssx .= ' UNION ' . $n3['not_run'];
                }
                
                $start_time = microtime(true);
                $tcc = $dbHandler->fetchRowsIntoMap($ssx, 'tcase_id');
                $query_time = microtime(true) - $start_time;
                PerformanceMonitor::logQuery($ssx, $query_time);
            }
        }   

        // Optimized keyword filtering
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

        if (!is_null($tplan_tcases)) {
            // Optimized status filtering
            $targetExecStatus = (array)(isset($objFilters->filter_result_result) ? 
                $objFilters->filter_result_result : null);
            
            if (!is_null($targetExecStatus) && (!in_array($resultsCfg['status_code']['all'], $targetExecStatus))) {
                applyStatusFilters($context['tplan_id'], $tplan_tcases, $objFilters, $tplan_mgr, $resultsCfg['status_code']);       
            }

            // Optimized custom field filtering
            if (isset($my['filters']['filter_custom_fields']) && isset($test_spec['childNodes'])) {
                $cfx = cfForDesign($dbHandler, $my['filters']['filter_custom_fields']);
                if (!is_null($cfx)) {
                    $test_spec['childNodes'] = filter_by_cf_values($dbHandler, $test_spec['childNodes'], $cfx, $hash_descr_id);
                }  
            }

            // Apply cross-platform counters
            if (null !== $tcc && count($tcc) > 0) {
                $tcIDSet = array_keys($tplan_tcases);
                foreach ($tcIDSet as $iID) {
                    if (isset($tcc[$iID])) {
                        $tplan_tcases[$iID]['exec_status'] = $tcc[$iID]['exec_status'];
                    }
                }
            }

            // Optimized tree node preparation
            $pnOptions = array(
                'hideTestCases' => $options['hideTestCases'], 
                'viewType' => 'executionTree'
            );
            $pnFilters = null;    
            
            $start_time = microtime(true);
            $testcase_counters = prepareExecTreeNode($dbHandler, $test_spec,
                                 $map_node_tccount, $tplan_tcases, $pnFilters, $pnOptions);
            $query_time = microtime(true) - $start_time;
            PerformanceMonitor::logQuery('prepareExecTreeNode', $query_time);

            foreach ($testcase_counters as $key => $value) {
                $test_spec[$key] = $testcase_counters[$key];
            }
        }
    }

    // Create optimized tree menu
    $treeMenu = createOptimizedTreeMenu($test_spec, $renderTreeNodeOpt, $tcase_prefix, $glueChar);
    
    return array($treeMenu, $testcases_to_show ?? []);
}

/**
 * Get optimized skeleton with caching
 */
function getOptimizedSkeleton($tplan_mgr, $tplan_id, $tproject_id, $filters, $options) {
    $cache_key = 'skeleton_' . $tplan_id . '_' . md5(serialize($filters) . serialize($options));
    
    $cached = CustomFieldCache::get($cache_key);
    if ($cached) {
        return $cached;
    }
    
    $result = $tplan_mgr->getSkeleton($tplan_id, $tproject_id, $filters, $options);
    
    CustomFieldCache::set($cache_key, $result);
    
    return $result;
}

/**
 * Get optimized linked test cases for execution tree
 */
function getOptimizedLinkedForExecTree($tplan_mgr, $tplan_id, $filters, $options, $limit = 1000, $offset = 0) {
    $cache_key = 'linked_exec_' . $tplan_id . '_' . md5(serialize($filters)) . '_' . $limit . '_' . $offset;
    
    $cached = CustomFieldCache::get($cache_key);
    if ($cached) {
        return $cached;
    }
    
    // Build optimized query with proper indexing hints
    $sql = buildOptimizedExecTreeQuery($tplan_id, $filters, $options, $limit, $offset);
    
    $result = $sql;
    
    CustomFieldCache::set($cache_key, $result);
    
    return $result;
}

/**
 * Build optimized execution tree query
 */
function buildOptimizedExecTreeQuery($tplan_id, $filters, $options, $limit, $offset) {
    $base_sql = "
        SELECT DISTINCT
            tc.id as tcase_id,
            tc.version,
            tc.tc_external_id,
            n.name,
            COALESCE(e.status, 'n') as exec_status,
            COALESCE(e.execution_ts, 0) as execution_ts,
            COALESCE(e.build_id, 0) as build_id,
            COALESCE(e.platform_id, 0) as platform_id,
            COALESCE(e.tester_id, 0) as tester_id,
            COUNT(DISTINCT e.id) as execution_count
        FROM testplan_tcversions tc
        INNER JOIN nodes_hierarchy n ON tc.tcversion_id = n.id
        LEFT JOIN executions e ON tc.id = e.tcversion_id
        WHERE tc.testplan_id = {$tplan_id}
    ";
    
    // Add build filter
    if (isset($filters->setting_build) && $filters->setting_build > 0) {
        $base_sql .= " AND (e.build_id = {$filters->setting_build} OR e.build_id IS NULL)";
    }
    
    // Add platform filter
    if (isset($filters->setting_platform) && $filters->setting_platform > 0) {
        $base_sql .= " AND (e.platform_id = {$filters->setting_platform} OR e.platform_id IS NULL)";
    }
    
    // Add keyword filter
    if (isset($filters->keyword_id) && is_array($filters->keyword_id) && !empty($filters->keyword_id)) {
        $keyword_ids = implode(',', $filters->keyword_id);
        $base_sql .= " AND tc.id IN (
            SELECT tcversion_id FROM testcase_keywords 
            WHERE keyword_id IN ({$keyword_ids})
        )";
    }
    
    $base_sql .= " GROUP BY tc.id, tc.version, tc.tc_external_id, n.name, e.status, e.execution_ts, e.build_id, e.platform_id, e.tester_id";
    $base_sql .= " ORDER BY n.name LIMIT {$limit} OFFSET {$offset}";
    
    return $base_sql;
}

/**
 * Create optimized tree menu structure
 */
function createOptimizedTreeMenu($test_spec, $renderOptions, $tcase_prefix, $glueChar) {
    $treeMenu = new stdClass();
    $treeMenu->rootnode = new stdClass();
    $treeMenu->rootnode->id = $test_spec['id'];
    $treeMenu->rootnode->name = $test_spec['name'];
    $treeMenu->rootnode->testlink_node_type = 'testproject';
    
    // Process children efficiently
    if (isset($test_spec['childNodes'])) {
        $children = [];
        foreach ($test_spec['childNodes'] as $child) {
            $children[] = processTreeNode($child, $renderOptions, $tcase_prefix, $glueChar);
        }
        $treeMenu->menustring = json_encode($children);
    } else {
        $treeMenu->menustring = "[]";
    }
    
    return $treeMenu;
}

/**
 * Process individual tree node
 */
function processTreeNode($node, $renderOptions, $tcase_prefix, $glueChar) {
    $processed = [
        'id' => $node['id'],
        'name' => $node['name'],
        'type' => $node['node_type']
    ];
    
    if ($node['node_type'] == 'testcase' && $renderOptions['showTestCaseID']) {
        $processed['name'] = $tcase_prefix . $node['id'] . ':' . $node['name'];
    }
    
    if (isset($node['childNodes'])) {
        $children = [];
        foreach ($node['childNodes'] as $child) {
            $children[] = processTreeNode($child, $renderOptions, $tcase_prefix, $glueChar);
        }
        $processed['children'] = $children;
    }
    
    return $processed;
}
?>
