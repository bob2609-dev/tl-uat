<?php
// Simple test version to debug the issue
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include TestLink API and database connection
define('NOCRYPT', true);
require_once('../../config.inc.php');
require_once('common.php');

// Initialize session and check permissions
testlinkInitPage($db, false, false, false);
$currentUser = $_SESSION['currentUser'];

// Handle AJAX requests
if (isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == '1') {
    header('Content-Type: application/json');
    
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
    
    try {
        switch ($action) {
            case 'get_initial_data':
                // Load test projects
                $testProjectMgr = new testproject($db);
                $testprojects = $testProjectMgr->get_accessible_for_user($currentUser->dbID);
                
                // Load users
                $sql = "SELECT id, login, first, last FROM users WHERE active = 1 ORDER BY first, last";
                $result = $db->exec_query($sql);
                $users = array();
                while ($row = $db->fetch_array($result)) {
                    $users[] = $row;
                }
                
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'projects' => $testprojects,
                        'testers' => $users
                    ]
                ]);
                break;
                
            case 'run_report':
                // Simple test data
                $testData = array(
                    array(
                        'tester_id' => 1,
                        'tester_name' => 'Test User',
                        'execution_id' => 123,
                        'testcase_id' => 456,
                        'testcase_name' => 'Test Case',
                        'execution_status' => 'p',
                        'status_class' => 'badge-passed',
                        'status_text' => 'Passed',
                        'execution_date_formatted' => '2025-01-01 12:00:00',
                        'execution_duration_formatted' => '5.00s'
                    )
                );
                
                echo json_encode(['success' => true, 'data' => $testData]);
                break;
                
            default:
                echo json_encode(['success' => false, 'error' => 'Unknown action: ' . $action]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// If not AJAX, render simple HTML
?>
<!DOCTYPE html>
<html>
<head>
    <title>Test Breakdown Report</title>
    <script src="../../gui/javascript/jquery-3.6.0.min.js"></script>
    <script src="../../gui/javascript/select2.min.js"></script>
    <link href="../../gui/css/select2.min.css" rel="stylesheet" />
</head>
<body>
    <h1>Test Breakdown Report</h1>
    
    <button onclick="testLoadData()">Load Data</button>
    <button onclick="testGenerateReport()">Generate Report</button>
    
    <div id="results"></div>
    
    <script>
        function testLoadData() {
            $.ajax({
                url: 'tester_execution_report_breakdown_test.php',
                data: { ajax: '1', action: 'get_initial_data' },
                dataType: 'json',
                success: function(response) {
                    $('#results').html('<pre>' + JSON.stringify(response, null, 2) + '</pre>');
                },
                error: function(xhr, status, error) {
                    $('#results').html('ERROR: ' + error + '<br><pre>' + xhr.responseText + '</pre>');
                }
            });
        }
        
        function testGenerateReport() {
            $.ajax({
                url: 'tester_execution_report_breakdown_test.php',
                data: { ajax: '1', action: 'run_report' },
                dataType: 'json',
                success: function(response) {
                    $('#results').html('<pre>' + JSON.stringify(response, null, 2) + '</pre>');
                },
                error: function(xhr, status, error) {
                    $('#results').html('ERROR: ' + error + '<br><pre>' + xhr.responseText + '</pre>');
                }
            });
        }
    </script>
</body>
</html>
