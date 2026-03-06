<?php
/**
 * Test script for getting test plans
 */
define('NOCRYPT', true);
require_once('../../config.inc.php');
require_once('common.php');
require_once('users.inc.php');

// Set content type to JSON
header('Content-Type: application/json');
header('Cache-Control: no-cache, must-revalidate');

// Initialize database using TestLink's standard way
testlinkInitPage($db);
$currentUser = $_SESSION['currentUser'];

// Check permissions
if (!is_object($currentUser) || !$currentUser->hasRight($db, 'testplan_metrics')) {
    echo json_encode(['success' => false, 'error' => 'Access denied']);
    exit;
}

$projectId = isset($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;

if ($projectId <= 0) {
    echo json_encode(['success' => false, 'error' => 'Invalid project ID']);
    exit;
}

try {
    $tplanSql = 'SELECT id, notes FROM testplans WHERE testproject_id = ' . $projectId . ' AND active = 1 ORDER BY notes';
    $tplanResult = $db->exec_query($tplanSql);
    
    $plans = array();
    $debugInfo = array();
    
    if ($tplanResult) {
        while ($row = $db->fetch_array($tplanResult)) {
            $debugInfo[] = array(
                'original_notes' => $row['notes'],
                'strip_tags' => strip_tags($row['notes']),
                'html_decode' => html_entity_decode(strip_tags($row['notes']), ENT_QUOTES, 'UTF-8'),
                'trim' => trim(html_entity_decode(strip_tags($row['notes']), ENT_QUOTES, 'UTF-8'))
            );
            
            // Clean HTML tags and format properly
            $planName = strip_tags($row['notes']);
            $planName = html_entity_decode($planName, ENT_QUOTES, 'UTF-8');
            $planName = trim($planName);
            if (empty($planName)) {
                $planName = 'Test Plan #' . $row['id'];
            }
            $plans[] = array(
                'id' => $row['id'],
                'name' => $planName,
                'original' => $row['notes']
            );
        }
    }
    
    echo json_encode([
        'success' => true, 
        'plans' => $plans,
        'debug_info' => $debugInfo,
        'project_id' => $projectId,
        'sql' => $tplanSql,
        'result_count' => count($plans)
    ]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Exception: ' . $e->getMessage()]);
}
?>
