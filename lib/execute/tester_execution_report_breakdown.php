<?php
/**
 * Tester Execution Report - Breakdown Version
 * 
 * This version shows individual execution details for each user,
 * rather than consolidated summary rows.
 * 
 * Features:
 * - Shows each execution as a separate row
 * - Includes execution date, status, and duration
 * - Maintains all filters from professional report
 * - Uses latest execution only logic
 */

// Include TestLink API and database connection
define('NOCRYPT', true);
require_once('../../config.inc.php');
require_once('common.php');

/**
 * Get detailed execution breakdown for testers
 */
function getTesterExecutionBreakdown($projectId, $userId, $startDate, $endDate, $includeNonAssigned, $db) {
    error_log("=== getTesterExecutionBreakdown START ===");
    error_log("Filters: project_id=$projectId, user_id=$userId, start_date=$startDate, end_date=$endDate, include_non_assigned=$includeNonAssigned");
    
    try {
        // Build query for individual executions with latest execution logic
        $sql = "
            SELECT 
                u.id AS tester_id,
                CONCAT(u.first, ' ', u.last) AS tester_name,
                e.id AS execution_id,
                tc.id AS testcase_id,
                tc.name AS testcase_name,
                tc.external_id AS testcase_external_id,
                tc.importance AS testcase_importance,
                e.status AS execution_status,
                e.execution_ts AS execution_date,
                e.execution_duration AS execution_duration,
                e.notes AS execution_notes,
                tp.name AS testplan_name,
                b.name AS build_name,
                pl.name AS platform_name,
                tptc.version AS testcase_version,
                
                -- Assignment info
                IFNULL(a.feature_id, 0) AS is_assigned,
                ua.type AS assignment_type,
                ua.status AS assignment_status
                
            FROM users u
            
            LEFT JOIN executions e ON u.id = e.tester_id
            LEFT JOIN testplans tp ON e.testplan_id = tp.id
            LEFT JOIN builds b ON e.build_id = b.id
            LEFT JOIN platforms pl ON e.platform_id = pl.id
            LEFT JOIN tcversions tptc ON e.tcversion_id = tptc.id
            LEFT JOIN testcases tc ON tptc.tc_id = tc.id
            LEFT JOIN user_assignments ua ON u.id = ua.user_id AND ua.feature_id = tptc.id
            LEFT JOIN (
                -- Get only the latest execution for each test case per tester
                SELECT 
                    e1.tester_id,
                    e1.tcversion_id,
                    ROW_NUMBER() OVER (
                        PARTITION BY e1.tester_id, e1.tcversion_id 
                        ORDER BY e1.execution_ts DESC
                    ) AS rn
                FROM executions e1
                JOIN testplans tp1 ON e1.testplan_id = tp1.id
            ) latest_exec ON e.tester_id = latest_exec.tester_id 
                          AND e.tcversion_id = latest_exec.tcversion_id 
                          AND latest_exec.rn = 1
            
            WHERE u.active = 1
                AND latest_exec.rn = 1  -- Only latest executions
                " . ($projectId > 0 ? "AND tp.testproject_id = $projectId" : "") . "
                " . ($userId > 0 ? "AND u.id = $userId" : "") . "
                " . (!empty($startDate) || !empty($endDate) ? "AND " : "") . "
                " . (!empty($startDate) ? "e.execution_ts >= '$startDate'" : "") . "
                " . (!empty($startDate) && !empty($endDate) ? " AND " : "") . "
                " . (!empty($endDate) ? "e.execution_ts <= '$endDate'" : "") . "
                " . (!$includeNonAssigned ? "HAVING is_assigned = 1" : "") . "
            
            ORDER BY u.first, u.last, e.execution_ts DESC
        ";
        
        error_log("Executing BREAKDOWN query: " . $sql);
        
        $result = $db->exec_query($sql);
        
        if (!$result) {
            error_log("Query failed: " . $db->error_msg());
            throw new Exception("Database query failed: " . $db->error_msg());
        }
        
        $data = array();
        
        while ($row = $db->fetch_array($result)) {
            // Format execution status with color coding
            $statusClass = '';
            $statusText = '';
            
            switch ($row['execution_status']) {
                case 'p':
                    $statusClass = 'badge-passed';
                    $statusText = 'Passed';
                    break;
                case 'f':
                    $statusClass = 'badge-failed';
                    $statusText = 'Failed';
                    break;
                case 'b':
                    $statusClass = 'badge-blocked';
                    $statusText = 'Blocked';
                    break;
                case 'n':
                case 'i':
                    $statusClass = 'badge-not-run';
                    $statusText = 'Not Run';
                    break;
                default:
                    $statusClass = 'badge-executed';
                    $statusText = $row['execution_status'];
                    break;
            }
            
            $row['status_class'] = $statusClass;
            $row['status_text'] = $statusText;
            
            // Format duration
            $row['execution_duration_formatted'] = $row['execution_duration'] ? number_format($row['execution_duration'], 2) . 's' : 'N/A';
            
            // Format date
            $row['execution_date_formatted'] = date('Y-m-d H:i:s', strtotime($row['execution_date']));
            
            $data[] = $row;
        }
        
        error_log("Breakdown query returned " . count($data) . " rows");
        error_log("=== getTesterExecutionBreakdown END SUCCESS ===");
        return $data;
        
    } catch (Exception $e) {
        error_log("=== getTesterExecutionBreakdown END ERROR ===");
        error_log("Exception: " . $e->getMessage());
        error_log("Stack trace: " . $e->getTraceAsString());
        throw $e;
    }
}

// Initialize session and check permissions
testlinkInitPage($db, false, false, false);
$currentUser = $_SESSION['currentUser'];

// Initialize GUI object
$gui = new stdClass();

// Get filter parameters - handle both old and new parameter names
$selectedProject = isset($_REQUEST['project_id']) ? intval($_REQUEST['project_id']) : 0;
$selectedUser = isset($_REQUEST['user_id']) ? intval($_REQUEST['user_id']) : 
               (isset($_REQUEST['tester_id']) ? intval($_REQUEST['tester_id']) : 0);
$startDate = isset($_REQUEST['start_date']) ? $_REQUEST['start_date'] : '';
$endDate = isset($_REQUEST['end_date']) ? $_REQUEST['end_date'] : '';
$includeNonAssigned = isset($_REQUEST['include_non_assigned']) ? ($_REQUEST['include_non_assigned'] === 'true' || $_REQUEST['include_non_assigned'] === '1') : 
                     (isset($_REQUEST['report_type']) && $_REQUEST['report_type'] === 'all');

// Load test projects for dropdown
$testProjectMgr = new testproject($db);
$gui->testprojects = $testProjectMgr->get_accessible_for_user($currentUser->dbID);

// Clean project notes (remove HTML tags)
foreach ($gui->testprojects as &$project) {
    $project->name = strip_tags($project->name);
}

// Load users for dropdown
try {
    $sql = "SELECT id, login, first, last FROM users WHERE active = 1 ORDER BY first, last";
    $result = $db->exec_query($sql);
    
    $gui->users = array();
    while ($row = $db->fetch_array($result)) {
        $gui->users[] = $row;
    }
} catch (Exception $e) {
    error_log("Error loading users: " . $e->getMessage());
    $gui->users = array();
}

// Handle AJAX requests
if (isset($_REQUEST['ajax']) && $_REQUEST['ajax'] == '1') {
    header('Content-Type: application/json');
    
    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
    
    // Debug logging
    error_log("AJAX request received - Action: " . $action);
    error_log("Request data: " . print_r($_REQUEST, true));
    
    try {
        switch ($action) {
            case 'get_initial_data':
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'projects' => $gui->testprojects,
                        'testers' => $gui->users
                    ]
                ]);
                break;
                
            case 'run_report':
                error_log("Running breakdown report with parameters: Project=$selectedProject, User=$selectedUser, Start=$startDate, End=$endDate, IncludeNonAssigned=$includeNonAssigned");
                $reportData = getTesterExecutionBreakdown($selectedProject, $selectedUser, $startDate, $endDate, $includeNonAssigned, $db);
                echo json_encode(['success' => true, 'data' => $reportData]);
                break;
                
            default:
                error_log("Unknown action: " . $action);
                echo json_encode(['success' => false, 'error' => 'Unknown action: ' . $action]);
        }
    } catch (Exception $e) {
        error_log("AJAX Exception: " . $e->getMessage());
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// Handle new simplified AJAX request
if (isset($_REQUEST['get_report']) && $_REQUEST['get_report'] == '1') {
    header('Content-Type: application/json');
    
    try {
        $reportData = getTesterExecutionBreakdown($selectedProject, $selectedUser, $startDate, $endDate, $includeNonAssigned, $db);
        echo json_encode(['success' => true, 'data' => $reportData]);
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}

// If not AJAX, render as HTML page
header('Content-Type: text/html; charset=UTF-8');

// Load data for HTML page
try {
    $testProjectMgr = new testproject($db);
    $gui->testprojects = $testProjectMgr->get_accessible_for_user($currentUser->dbID);

    // Clean project notes (remove HTML tags)
    foreach ($gui->testprojects as &$project) {
        $project->name = strip_tags($project->name);
    }
} catch (Exception $e) {
    $gui->testprojects = array();
}

// Load users for HTML page
try {
    $sql = "SELECT id, login, first, last FROM users WHERE active = 1 ORDER BY first, last";
    $result = $db->exec_query($sql);
    
    $gui->users = array();
    while ($row = $db->fetch_array($result)) {
        $gui->users[] = $row;
    }
} catch (Exception $e) {
    $gui->users = array();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tester Execution Report - Breakdown</title>
    
    <script src="../../gui/javascript/jquery-3.6.0.min.js"></script>
    <script src="../../gui/javascript/select2.min.js"></script>
    <link href="../../gui/css/select2.min.css" rel="stylesheet" />
    
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #1B263B 0%, #2D3748 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 300;
        }

        .filters {
            padding: 30px;
            background: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
        }

        .filter-row {
            display: flex;
            gap: 20px;
            margin-bottom: 20px;
            align-items: end;
        }

        .filter-group {
            flex: 1;
            min-width: 200px;
        }

        .filter-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #495057;
        }

        .filter-group select,
        .filter-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease;
        }

        .filter-group select:focus,
        .filter-group input:focus {
            outline: none;
            border-color: #415A77;
        }

        .checkbox-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .checkbox-group input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .button-group {
            padding: 20px 30px;
            text-align: center;
            background: white;
            border-top: 1px solid #e9ecef;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            margin: 0 10px;
        }

        .btn-primary {
            background: #415A77;
            color: white;
        }

        .btn-primary:hover {
            background: #34495e;
            transform: translateY(-2px);
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
        }

        .btn-success {
            background: #2D6A4F;
            color: white;
        }

        .btn-success:hover {
            background: #1e5123;
            transform: translateY(-2px);
        }

        .results {
            padding: 30px;
        }

        .table-container {
            overflow-x: auto;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th {
            background: #1B263B;
            color: white;
            padding: 15px 12px;
            text-align: left;
            font-weight: 600;
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        td {
            padding: 12px;
            border-bottom: 1px solid #e9ecef;
            vertical-align: middle;
        }

        tr:hover {
            background: #f8f9fa;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            min-width: 70px;
            text-align: center;
        }

        .badge-passed {
            background: #2D6A4F;
            color: white;
        }

        .badge-failed {
            background: #9B2226;
            color: white;
        }

        .badge-blocked {
            background: #E09F3E;
            color: #212529;
        }

        .badge-not-run {
            background: #E0E1DD;
            color: #495057;
        }

        .badge-executed {
            background: #778DA9;
            color: white;
        }

        .loading {
            text-align: center;
            padding: 60px;
            color: #6c757d;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #415A77;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 1s linear infinite;
            margin: 0 auto 20px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .error {
            background: #f8d7da;
            color: #721c24;
            padding: 20px;
            border-radius: 8px;
            margin: 20px;
        }

        .success {
            background: #d4edda;
            color: #155724;
            padding: 20px;
            border-radius: 8px;
            margin: 20px;
        }

        .no-data {
            text-align: center;
            padding: 60px;
            color: #6c757d;
            font-size: 18px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 Tester Execution Report - Breakdown</h1>
        </div>

        <div class="filters">
            <div class="filter-row">
                <div class="filter-group">
                    <label for="project_id">Project</label>
                    <select id="project_id" name="project_id">
                        <option value="">All Projects</option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="user_id">Tester</label>
                    <select id="user_id" name="user_id">
                        <option value="">All Testers</option>
                    </select>
                </div>
            </div>

            <div class="filter-row">
                <div class="filter-group">
                    <label for="start_date">Start Date</label>
                    <input type="date" id="start_date" name="start_date">
                </div>

                <div class="filter-group">
                    <label for="end_date">End Date</label>
                    <input type="date" id="end_date" name="end_date">
                </div>
            </div>

            <div class="checkbox-group">
                <input type="checkbox" id="include_non_assigned" name="include_non_assigned" checked>
                <label for="include_non_assigned">Include users with zero assignments</label>
            </div>
        </div>

        <div class="button-group">
            <button type="button" id="generate_report" class="btn btn-primary">🚀 Generate Report</button>
            <button type="button" id="clear_filters" class="btn btn-secondary">🔄 Clear Filters</button>
            <button type="button" id="export_csv" class="btn btn-success" style="display: none;">📊 Export to CSV</button>
        </div>

        <div class="results">
            <div id="loading" class="loading" style="display: none;">
                <div class="spinner"></div>
                <p>Loading execution details...</p>
            </div>
            
            <div id="error_message" class="error" style="display: none;"></div>
            <div id="success_message" class="success" style="display: none;"></div>
            
            <div id="results_table" class="table-container" style="display: none;">
                <table>
                    <thead>
                        <tr>
                            <th>Tester ID</th>
                            <th>Tester Name</th>
                            <th>Execution ID</th>
                            <th>Test Case ID</th>
                            <th>Test Case Name</th>
                            <th>External ID</th>
                            <th>Importance</th>
                            <th>Status</th>
                            <th>Execution Date</th>
                            <th>Duration</th>
                            <th>Test Plan</th>
                            <th>Build</th>
                            <th>Platform</th>
                            <th>Version</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody id="table_body">
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Initialize Select2 for dropdowns
            $('#project_id, #user_id').select2({
                width: '100%',
                placeholder: 'Select an option'
            });

            // Load initial data
            loadProjects();
            loadUsers();

            // Event handlers
            $('#generate_report').click(generateReport);
            $('#clear_filters').click(clearFilters);
            $('#export_csv').click(exportToCSV);
        });

        function loadProjects() {
            $.ajax({
                url: 'tester_execution_report_breakdown_test.php',
                type: 'GET',
                data: { ajax: '1', action: 'get_initial_data' },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data && response.data.projects) {
                        var $select = $('#project_id');
                        $select.find('option:not(:first)').remove();
                        
                        $.each(response.data.projects, function(index, project) {
                            $select.append('<option value="' + project.id + '">' + project.name + '</option>');
                        });
                    }
                },
                error: function(xhr, status, error) {
                    showError('Failed to load projects: ' + error);
                }
            });
        }

        function loadUsers() {
            $.ajax({
                url: 'tester_execution_report_breakdown_test.php',
                type: 'GET',
                data: { ajax: '1', action: 'get_initial_data' },
                dataType: 'json',
                success: function(response) {
                    if (response.success && response.data && response.data.testers) {
                        var $select = $('#user_id');
                        $select.find('option:not(:first)').remove();
                        
                        $.each(response.data.testers, function(index, user) {
                            $select.append('<option value="' + user.id + '">' + user.first + ' ' + user.last + '</option>');
                        });
                    }
                },
                error: function(xhr, status, error) {
                    showError('Failed to load users: ' + error);
                }
            });
        }

        function generateReport() {
            var params = {
                ajax: '1',
                action: 'run_report',
                project_id: $('#project_id').val(),
                user_id: $('#user_id').val(),
                start_date: $('#start_date').val(),
                end_date: $('#end_date').val(),
                include_non_assigned: $('#include_non_assigned').is(':checked')
            };

            showLoading();
            hideMessages();

            $.ajax({
                url: 'tester_execution_report_breakdown_test.php',
                type: 'POST',
                data: params,
                dataType: 'json',
                success: function(response) {
                    hideLoading();
                    
                    if (response.success) {
                        displayResults(response.data);
                        showSuccess('Execution breakdown loaded successfully');
                    } else {
                        showError('Failed to generate report: ' + (response.error || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    hideLoading();
                    // Try to extract error message from response if it's HTML
                    var responseText = xhr.responseText;
                    if (responseText && responseText.includes('<pre>')) {
                        var match = responseText.match(/<pre[^>]*>([\s\S]*?)<\/pre>/);
                        if (match) {
                            showError('Server error: ' + match[1].trim());
                            return;
                        }
                    }
                    showError('Failed to generate report: ' + error);
                }
            });
        }

        function displayResults(data) {
            var $tbody = $('#table_body');
            $tbody.empty();

            if (data.length === 0) {
                $tbody.append('<tr><td colspan="15" class="no-data">No execution data found for selected criteria.</td></tr>');
                $('#results_table').show();
                return;
            }

            $.each(data, function(index, row) {
                var $tr = $('<tr>');
                
                $tr.append('<td>' + (row.tester_id || '') + '</td>');
                $tr.append('<td>' + row.tester_name + '</td>');
                $tr.append('<td>' + row.execution_id + '</td>');
                $tr.append('<td>' + row.testcase_id + '</td>');
                $tr.append('<td>' + (row.testcase_name || '') + '</td>');
                $tr.append('<td>' + (row.testcase_external_id || '') + '</td>');
                $tr.append('<td>' + (row.testcase_importance || '') + '</td>');
                $tr.append('<td><span class="status-badge ' + row.status_class + '">' + row.status_text + '</span></td>');
                $tr.append('<td>' + row.execution_date_formatted + '</td>');
                $tr.append('<td>' + row.execution_duration_formatted + '</td>');
                $tr.append('<td>' + (row.testplan_name || '') + '</td>');
                $tr.append('<td>' + (row.build_name || '') + '</td>');
                $tr.append('<td>' + (row.platform_name || '') + '</td>');
                $tr.append('<td>' + (row.testcase_version || '') + '</td>');
                $tr.append('<td>' + (row.execution_notes || '') + '</td>');
                
                $tbody.append($tr);
            });

            $('#results_table').show();
            $('#export_csv').show();
        }

        function clearFilters() {
            $('#project_id').val('').trigger('change');
            $('#user_id').val('').trigger('change');
            $('#start_date').val('');
            $('#end_date').val('');
            $('#include_non_assigned').prop('checked', true);
            $('#results_table').hide();
            $('#export_csv').hide();
            hideMessages();
        }

        function exportToCSV() {
            // Get current table data
            var tableData = [];
            $('#table_body tr').each(function() {
                var row = [];
                $(this).find('td').each(function() {
                    row.push($(this).text());
                });
                tableData.push(row);
            });

            if (tableData.length === 0) {
                showError('No data to export');
                return;
            }

            // Create CSV content
            var headers = ['Tester ID', 'Tester Name', 'Execution ID', 'Test Case ID', 'Test Case Name', 'External ID', 'Importance', 'Status', 'Execution Date', 'Duration', 'Test Plan', 'Build', 'Platform', 'Version', 'Notes'];
            var csvContent = headers.join(',') + '\n';

            tableData.forEach(function(row) {
                // Escape commas and quotes in data
                var escapedRow = row.map(function(cell) {
                    cell = cell.toString().replace(/"/g, '""'); // Escape quotes
                    if (cell.includes(',') || cell.includes('"')) {
                        cell = '"' + cell + '"'; // Wrap in quotes if contains comma or quote
                    }
                    return cell;
                });
                csvContent += escapedRow.join(',') + '\n';
            });

            // Create download
            var blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            var link = document.createElement('a');
            var url = URL.createObjectURL(blob);
            
            // Generate filename with current date
            var today = new Date();
            var filename = 'Tester_Execution_Breakdown_' + today.getFullYear() + '-' + 
                          String(today.getMonth() + 1).padStart(2, '0') + '-' + 
                          String(today.getDate()).padStart(2, '0') + '.csv';
            
            link.setAttribute('href', url);
            link.setAttribute('download', filename);
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

        function showLoading() {
            $('#loading').show();
        }

        function hideLoading() {
            $('#loading').hide();
        }

        function showError(message) {
            $('#error_message').text(message).show();
            $('#success_message').hide();
        }

        function showSuccess(message) {
            $('#success_message').text(message).show();
            $('#error_message').hide();
        }

        function hideMessages() {
            $('#error_message').hide();
            $('#success_message').hide();
        }
    </script>
</body>
</html>
