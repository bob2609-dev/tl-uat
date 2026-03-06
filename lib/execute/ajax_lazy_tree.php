<?php
/**
 * AJAX endpoint for lazy loading tree children
 * Called when user expands a tree node
 */

header('Content-Type: application/json');

function logMessage($msg) {
    $timestamp = date('H:i:s') . '.' . substr(microtime(), 2, 3);
    error_log("ajax_lazy_tree: $msg");
}

try {
    require_once('../../config.inc.php');
    require_once('common.php');
    
    testlinkInitPage($db);
    
    $parent_id = intval($_GET['parent_id'] ?? 0);
    $tplan_id = intval($_GET['tplan_id'] ?? 0);
    
    if (!$parent_id || !$tplan_id) {
        throw new Exception("Missing required parameters");
    }
    
    logMessage("Loading children for parent: {$parent_id}");
    
    $start_time = microtime(true);
    
    // Get children with limited depth
    $sql = "SELECT h.id, h.name, h.node_type_id,
                   (CASE WHEN EXISTS(
                       SELECT 1 FROM nodes_hierarchy nh2 
                       WHERE nh2.parent_id = h.id
                   ) THEN 1 ELSE 0 END) as has_children
            FROM nodes_hierarchy h
            WHERE h.parent_id = " . $parent_id . "
            AND h.node_type_id IN (1, 2, 3)
            ORDER BY h.name
            LIMIT 50";
    
    $result = $db->exec_query($sql);
    $children = array();
    
    while ($row = $db->fetch_array($result)) {
        $child = array(
            'id' => $row['id'],
            'name' => $row['name'],
            'node_type_id' => $row['node_type_id'],
            'has_children' => $row['has_children'],
            'leaf' => ($row['node_type_id'] == 3),  -- Test case = leaf
            'children_loaded' => false
        );
        $children[] = $child;
    }
    
    $load_time = microtime(true) - $start_time;
    
    logMessage("Loaded " . count($children) . " children in {$load_time}s");
    
    echo json_encode(array(
        'success' => true,
        'children' => $children,
        'load_time' => $load_time,
        'parent_id' => $parent_id
    ));
    
} catch (Exception $e) {
    logMessage("ERROR: " . $e->getMessage());
    echo json_encode(array(
        'success' => false,
        'error' => $e->getMessage()
    ));
}
?>
