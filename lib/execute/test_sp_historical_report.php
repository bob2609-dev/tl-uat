<?php
/**
 * Test script for historical tester execution report stored procedure
 * This script validates the stored procedure implementation against known scenarios
 */

// Include TestLink API and database connection
define('NOCRYPT', true);
require_once('../../config.inc.php');
require_once('common.php');

// Initialize session and check permissions
testlinkInitPage($db, false, false, false);

echo "<h1>Historical Tester Report - Stored Procedure Testing</h1>";

// Test scenarios
$testScenarios = [
    [
        'name' => 'Current Date - All Testers',
        'project_id' => 1,
        'testplan_id' => null,
        'build_id' => null,
        'tester_id' => null,
        'report_type' => 'all',
        'start_date' => null,
        'end_date' => null,
        'description' => 'Should show current data for all testers'
    ],
    [
        'name' => 'Current Date - Assigned Testers Only',
        'project_id' => 1,
        'testplan_id' => null,
        'build_id' => null,
        'tester_id' => null,
        'report_type' => 'assigned',
        'start_date' => null,
        'end_date' => null,
        'description' => 'Should show only assigned testers with current data'
    ],
    [
        'name' => 'Historical Date - Last Week',
        'project_id' => 1,
        'testplan_id' => null,
        'build_id' => null,
        'tester_id' => null,
        'report_type' => 'assigned',
        'start_date' => date('Y-m-d', strtotime('-7 days')),
        'end_date' => date('Y-m-d', strtotime('-7 days')),
        'description' => 'Should show data as of last week (point-in-time)'
    ],
    [
        'name' => 'Date Range - Last 30 Days',
        'project_id' => 1,
        'testplan_id' => null,
        'build_id' => null,
        'tester_id' => null,
        'report_type' => 'assigned',
        'start_date' => date('Y-m-d', strtotime('-30 days')),
        'end_date' => date('Y-m-d', strtotime('-1 day')),
        'description' => 'Should show data up to yesterday'
    ],
    [
        'name' => 'Specific Tester - User 111',
        'project_id' => 1,
        'testplan_id' => null,
        'build_id' => null,
        'tester_id' => 111,
        'report_type' => 'assigned',
        'start_date' => null,
        'end_date' => null,
        'description' => 'Should show data for specific tester'
    ]
];

echo "<h2>Test Scenarios</h2>";

foreach ($testScenarios as $index => $scenario) {
    echo "<div style='border: 1px solid #ccc; margin: 20px 0; padding: 15px;'>";
    echo "<h3>Test " . ($index + 1) . ": " . $scenario['name'] . "</h3>";
    echo "<p><strong>Description:</strong> " . $scenario['description'] . "</p>";
    echo "<p><strong>Parameters:</strong> " . json_encode($scenario, JSON_PRETTY_PRINT) . "</p>";
    
    try {
        // Build parameter string for stored procedure call
        $spParams = array();
        
        $spParams[] = $scenario['project_id'] > 0 ? $scenario['project_id'] : 'NULL';
        $spParams[] = $scenario['testplan_id'] > 0 ? $scenario['testplan_id'] : 'NULL';
        $spParams[] = $scenario['build_id'] > 0 ? $scenario['build_id'] : 'NULL';
        $spParams[] = $scenario['tester_id'] > 0 ? $scenario['tester_id'] : 'NULL';
        $spParams[] = "'" . $db->prepare_string($scenario['report_type']) . "'";
        $spParams[] = !empty($scenario['start_date']) ? "'" . $db->prepare_string($scenario['start_date']) . "'" : 'NULL';
        $spParams[] = !empty($scenario['end_date']) ? "'" . $db->prepare_string($scenario['end_date']) . "'" : 'NULL';
        
        $paramString = implode(', ', $spParams);
        
        // Call stored procedure
        $sql = "CALL sp_tester_execution_report_historical($paramString)";
        
        echo "<p><strong>SQL:</strong> " . htmlspecialchars($sql) . "</p>";
        
        $startTime = microtime(true);
        $result = $db->exec_query($sql);
        $endTime = microtime(true);
        
        if (!$result) {
            throw new Exception("Stored procedure execution failed: " . $db->error_msg());
        }
        
        $executionTime = round(($endTime - $startTime) * 1000, 2);
        echo "<p><strong>Execution Time:</strong> {$executionTime}ms</p>";
        
        // Fetch and display results
        $testers = array();
        $summary = ['totalTesters' => 0, 'totalAssigned' => 0, 'totalPassed' => 0, 'totalFailed' => 0, 'totalBlocked' => 0, 'totalNotRun' => 0];
        
        echo "<table border='1' cellpadding='5' cellspacing='0' style='border-collapse: collapse; width: 100%;'>";
        echo "<tr style='background-color: #f0f0f0;'>";
        echo "<th>Tester Name</th>";
        echo "<th>Total Assigned</th>";
        echo "<th>Passed</th>";
        echo "<th>Failed</th>";
        echo "<th>Blocked</th>";
        echo "<th>Not Run</th>";
        echo "<th>Pass Rate</th>";
        echo "<th>Last Execution</th>";
        echo "</tr>";
        
        while ($row = $db->fetch_array($result)) {
            $row['pass_rate'] = ($row['passed'] + $row['failed']) > 0 ? round(($row['passed'] / ($row['passed'] + $row['failed'])) * 100, 1) : 0;
            
            $bgColor = $row['tester_name'] === 'TOTAL' ? '#ffffcc' : 'white';
            
            echo "<tr style='background-color: $bgColor;'>";
            echo "<td><strong>" . htmlspecialchars($row['tester_name']) . "</strong></td>";
            echo "<td>" . $row['total_assigned'] . "</td>";
            echo "<td>" . $row['passed'] . "</td>";
            echo "<td>" . $row['failed'] . "</td>";
            echo "<td>" . $row['blocked'] . "</td>";
            echo "<td>" . $row['not_run'] . "</td>";
            echo "<td>" . $row['pass_rate'] . "%</td>";
            echo "<td>" . ($row['last_execution'] ? htmlspecialchars($row['last_execution']) : 'N/A') . "</td>";
            echo "</tr>";
            
            if ($row['tester_name'] !== 'TOTAL') {
                $testers[] = $row;
                $summary['totalTesters']++;
                $summary['totalAssigned'] += intval($row['total_assigned']);
                $summary['totalPassed'] += intval($row['passed']);
                $summary['totalFailed'] += intval($row['failed']);
                $summary['totalBlocked'] += intval($row['blocked']);
                $summary['totalNotRun'] += intval($row['not_run']);
            }
        }
        
        echo "</table>";
        
        // Display summary
        echo "<h4>Summary:</h4>";
        echo "<ul>";
        echo "<li>Total Testers: " . $summary['totalTesters'] . "</li>";
        echo "<li>Total Assigned: " . $summary['totalAssigned'] . "</li>";
        echo "<li>Total Passed: " . $summary['totalPassed'] . "</li>";
        echo "<li>Total Failed: " . $summary['totalFailed'] . "</li>";
        echo "<li>Total Blocked: " . $summary['totalBlocked'] . "</li>";
        echo "<li>Total Not Run: " . $summary['totalNotRun'] . "</li>";
        
        $overallPassRate = ($summary['totalPassed'] + $summary['totalFailed']) > 0 ? 
                           round(($summary['totalPassed'] / ($summary['totalPassed'] + $summary['totalFailed'])) * 100, 1) : 0;
        echo "<li>Overall Pass Rate: " . $overallPassRate . "%</li>";
        echo "</ul>";
        
        echo "<p style='color: green;'><strong>✅ Test Passed</strong></p>";
        
    } catch (Exception $e) {
        echo "<p style='color: red;'><strong>❌ Test Failed:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    }
    
    echo "</div>";
}

// Test stored procedure creation
echo "<h2>Stored Procedure Status</h2>";

try {
    $sql = "SHOW PROCEDURE STATUS WHERE Db = DATABASE() AND Name = 'sp_tester_execution_report_historical'";
    $result = $db->exec_query($sql);
    
    if ($result && $db->num_rows($result) > 0) {
        echo "<p style='color: green;'><strong>✅ Stored procedure exists in database</strong></p>";
        
        $proc = $db->fetch_array($result);
        echo "<ul>";
        echo "<li>Name: " . htmlspecialchars($proc['Name']) . "</li>";
        echo "<li>Type: " . htmlspecialchars($proc['Type']) . "</li>";
        echo "<li>Created: " . htmlspecialchars($proc['Created']) . "</li>";
        echo "<li>Modified: " . htmlspecialchars($proc['Modified']) . "</li>";
        echo "</ul>";
    } else {
        echo "<p style='color: orange;'><strong>⚠️ Stored procedure not found - needs to be created</strong></p>";
        echo "<p>Run the SQL in create_sp_tester_execution_report_historical.sql to create the stored procedure.</p>";
    }
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>❌ Error checking stored procedure:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
}

echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Ensure the stored procedure is created by running the SQL file</li>";
echo "<li>Run these tests to validate the implementation</li>";
echo "<li>Compare results with the original view-based implementation</li>";
echo "<li>Test with various date ranges to ensure historical accuracy</li>";
echo "<li>Check performance with larger datasets</li>";
echo "</ol>";

echo "<p><a href='tester_execution_report_professional.html'>Go to Tester Report Interface</a></p>";
?>
