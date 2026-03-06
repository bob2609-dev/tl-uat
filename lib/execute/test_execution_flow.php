<?php
/**
 * Test script to understand execution flow
 */

require_once('../../config.inc.php');
require_once('../functions/common.php');

// Simulate the exact same data structure as execSetResults
$_POST = array(
    'save_button_clicked' => '431185',
    'createIssue' => 'on',
    'tc_version' => array('431185' => '431184'),
    'statusSingle' => array('431185' => 'f'),
    'bug_summary' => 'Test Case: /ARCHIVE/WORKING D/TESTING/ARC-994:ATM-TC110 -   2026-02-24CET18:30',
    'bug_notes' => 'Function ID: STDCRDMS
Action: New
Test scenario: Negative test
Test Data: [
N/A
]
Expected result: Cards > Maintenance > Card Master
Test result: [
TEST EXECUTION FLOW
]

TestLink URLs:
- View Test Case: https://test-management.nmbtz.com:9443/lib/execute/execSetResults.php?tcversion_id=431185
- Test Case Print View: https://test-management.nmbtz.com:9443/lib/execute/execSetResultsPrint.php?tcversion_id=431185'
);

echo json_encode(array(
    'success' => true,
    'message' => 'Test execution flow',
    'post_data' => $_POST,
    'execution_id_available' => isset($_POST['execution_id']),
    'execution_id_value' => $_POST['execution_id'] ?? 'NOT_SET'
));
?>
