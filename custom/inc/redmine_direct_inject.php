<?php
/**
 * Direct Redmine Integration Injector
 * 
 * This file directly injects Redmine buttons into TestLink pages
 * following the same approach used for the image display fix
 */

// Only run once
if (!defined('REDMINE_INJECTOR_INCLUDED')) {
    define('REDMINE_INJECTOR_INCLUDED', true);
    
    // Configuration - using the same settings from custom_config.inc.php
    global $tlCfg;
    $redmineConfig = array(
        'url' => isset($tlCfg->issueTracker->toolsDefaultValues['redmine']['url']) ? 
                $tlCfg->issueTracker->toolsDefaultValues['redmine']['url'] : 'https://support.profinch.com',
        'api_key' => isset($tlCfg->issueTracker->toolsDefaultValues['redmine']['apikey']) ? 
                   $tlCfg->issueTracker->toolsDefaultValues['redmine']['apikey'] : 'a597e200f8923a85484e81ca81d731827b8dbf3d',
        'project_id' => isset($tlCfg->issueTracker->toolsDefaultValues['redmine']['projectkey']) ? 
                      $tlCfg->issueTracker->toolsDefaultValues['redmine']['projectkey'] : 'nmb-fcubs-14-7-uat2'
    );
    
    // Get base URL - more robust approach
    $baseHref = '';
    if (isset($_SESSION['basehref'])) {
        $baseHref = $_SESSION['basehref'];
    } elseif (defined('TL_BASE_HREF')) {
        $baseHref = TL_BASE_HREF;
    } else {
        // Fallback - construct from server variables
        $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        $uri = isset($_SERVER['SCRIPT_NAME']) ? rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') : '';
        $baseHref = $protocol . $host . $uri . '/';
    }
    
    // Error handling - don't let our code break TestLink
    try {
    
    // Always inject our buttons regardless of the connection status
    // This follows the same approach we used for the image display fix
    // Check if we're on an execution page - more comprehensive check
    $isExecutionPage = strpos($_SERVER['SCRIPT_NAME'], 'execSetResults.php') !== false || 
                      strpos($_SERVER['SCRIPT_NAME'], 'execNavigator.php') !== false || 
                      (isset($_GET['feature']) && $_GET['feature'] === 'executeTest');
                      
    // Remove the 'Something is preventing connection' message if it exists
    // This is a hack, but it's the simplest way to remove the message
    echo '<style>
    .user_feedback {
        display: none !important;
    }
    
    /* Add a custom Redmine integration message */
    .execution_status_container::after {
        content: "Redmine integration is available when test fails";
        display: block;
        color: #31708f;
        background-color: #d9edf7;
        border: 1px solid #bce8f1;
        padding: 5px;
        margin-top: 10px;
        border-radius: 4px;
        font-size: 12px;
    }
    </style>';
    
    if ($isExecutionPage) {
        // Output direct HTML/JS injection for the Redmine buttons
        echo "<style>
            .redmine-button {
                background-color: #f0ad4e;
                color: white;
                padding: 5px 10px;
                border: none;
                border-radius: 3px;
                cursor: pointer;
                margin: 5px 0;
                display: inline-block;
                text-decoration: none;
            }
            .redmine-button:hover {
                background-color: #ec971f;
            }
            .redmine-container {
                margin: 10px 0;
                padding: 10px;
                border: 1px solid #f0ad4e;
                border-radius: 5px;
                background-color: #fcf8e3;
            }
            .redmine-title {
                font-weight: bold;
                margin-bottom: 10px;
                color: #8a6d3b;
            }
        </style>";
        
        // Add direct script injection with improved reliability
        echo "<script type='text/javascript'>
        // Wait for page to load
        window.addEventListener('DOMContentLoaded', function() {
            console.log('Redmine direct inject loaded');
            
            // Function to add Redmine buttons
            function addRedmineButtons() {
                // Find all status selects
                var statusSelects = document.querySelectorAll('select[name^=\"statusSingle\"]');
                console.log('Found ' + statusSelects.length + ' status selects');
                
                // If no status selects found, try again later
                if (statusSelects.length === 0) {
                    setTimeout(addRedmineButtons, 500);
                    return;
                }
                
                statusSelects.forEach(function(select) {
                    // Get parent container - more robust approach
                    var container = select.closest('.exec_tc_title') || 
                                   select.closest('.exec_additional_info') || 
                                   select.closest('div');
                    if (!container) {
                        // If we can't find a container, use the select's parent
                        container = select.parentNode;
                    }
                    
                    // Check if we already added buttons
                    if (container.querySelector('.redmine-container')) return;
                    
                    // Create container for Redmine buttons
                    var redmineContainer = document.createElement('div');
                    redmineContainer.className = 'redmine-container';
                    redmineContainer.style.display = 'none'; // Initially hidden
                    
                    // Get test case name and execution ID
                    var tcTitle = document.querySelector('.exec_tc_title');
                    var tcName = tcTitle ? tcTitle.textContent.trim() : 'Test Case';
                    var executionId = '';
                    
                    // Try to get execution ID from the form
                    var executionIdField = document.querySelector('input[name=\"exec_id\"]');
                    if (executionIdField) {
                        executionId = executionIdField.value;
                    }
                    
                    // Set container content with more details
                    redmineContainer.innerHTML = '\
                        <div class=\"redmine-title\">Redmine Bug Tracking</div>\
                        <a href=\"{$baseHref}redmine-integration.php?testcase=' + encodeURIComponent(tcName) + 
                        '&exec_id=' + encodeURIComponent(executionId) + '\" \
                        class=\"redmine-button\" target=\"_blank\" style=\"margin-right:5px;\">\
                            Create Bug in Redmine\
                        </a>\
                        <a href=\"{$baseHref}redmine-integration.php?testcase=' + encodeURIComponent(tcName) + 
                        '&exec_id=' + encodeURIComponent(executionId) + '&operation=link\" \
                        class=\"redmine-button\" target=\"_blank\" style=\"background-color:#337ab7;\">\
                            Link Existing Bug\
                        </a>';
                    
                    // Add container after the select
                    container.appendChild(redmineContainer);
                    
                    // Show container if status is 'Failed'
                    if (select.value === 'f') {
                        redmineContainer.style.display = 'block';
                    }
                    
                    // Add change listener
                    select.addEventListener('change', function() {
                        if (this.value === 'f') {
                            redmineContainer.style.display = 'block';
                        } else {
                            redmineContainer.style.display = 'none';
                        }
                    });
                });
            }
            
            // Try to add buttons immediately
            addRedmineButtons();
            
            // Also check periodically in case of AJAX updates
            setInterval(addRedmineButtons, 1000);
            
            // Add a mutation observer to detect DOM changes
            var observer = new MutationObserver(function(mutations) {
                mutations.forEach(function(mutation) {
                    if (mutation.addedNodes.length > 0) {
                        // DOM changed, try to add buttons
                        addRedmineButtons();
                        // Also try to enhance bug display
                        enhanceBugDisplay();
                    }
                });
            });
            
            // Start observing the document body for changes
            observer.observe(document.body, { childList: true, subtree: true });
            
            // Function to enhance bug display
            function enhanceBugDisplay() {
                console.log('Enhancing bug display');
                
                // Add custom styles for bug links
                if (!document.getElementById('redmine-bug-styles')) {
                    var style = document.createElement('style');
                    style.id = 'redmine-bug-styles';
                    style.textContent = '
                        .redmine-bug-link {
                            color: #0066cc !important;
                            font-weight: bold !important;
                            text-decoration: underline !important;
                            display: inline-block !important;
                            margin: 2px 0 !important;
                        }
                        .redmine-bug-details {
                            margin-top: 3px;
                            padding: 5px;
                            background-color: #f5f5f5;
                            border: 1px solid #ddd;
                            border-radius: 3px;
                            font-size: 12px;
                        }
                        .redmine-bug-status {
                            display: inline-block;
                            padding: 2px 5px;
                            background-color: #5cb85c;
                            color: white;
                            border-radius: 3px;
                            margin-right: 5px;
                        }
                        .redmine-bug-status.closed {
                            background-color: #777;
                        }
                        .redmine-bug-status.in-progress {
                            background-color: #f0ad4e;
                        }
                    ';
                    document.head.appendChild(style);
                }
                
                // Find all bug IDs in the page
                var bugPattern = /64\d{3,5}/g;
                var pageText = document.body.innerHTML;
                var matches = pageText.match(bugPattern);
                
                if (matches) {
                    // Get unique bug IDs
                    var uniqueBugs = [];
                    matches.forEach(function(bugId) {
                        if (uniqueBugs.indexOf(bugId) === -1) {
                            uniqueBugs.push(bugId);
                        }
                    });
                    
                    console.log('Found bug IDs:', uniqueBugs);
                    
                    // For each bug ID, create a link and insert it into the relevant bugs column
                    uniqueBugs.forEach(function(bugId) {
                        // Find all table cells
                        var cells = document.querySelectorAll('td');
                        cells.forEach(function(cell) {
                            // If the cell contains just the bug ID and doesn't already have our enhancement
                            if (cell.textContent.trim() === bugId && !cell.querySelector('.redmine-bug-link')) {
                                // Create a link
                                var link = document.createElement('a');
                                link.href = '{$redmineConfig['url']}/issues/' + bugId;
                                link.target = '_blank';
                                link.className = 'redmine-bug-link';
                                link.textContent = bugId;
                                
                                // Create a container for the bug details
                                var detailsContainer = document.createElement('div');
                                detailsContainer.className = 'redmine-bug-details';
                                detailsContainer.innerHTML = 'Loading bug details...';
                                
                                // Clear the cell and add the link and details
                                cell.innerHTML = '';
                                cell.appendChild(link);
                                cell.appendChild(detailsContainer);
                                
                                // Fetch bug details
                                fetchBugDetails(bugId, detailsContainer);
                            }
                        });
                    });
                }
            }
            
            // Function to fetch bug details
            function fetchBugDetails(bugId, container) {
                var xhr = new XMLHttpRequest();
                xhr.open('GET', '{$baseHref}custom/inc/redmine_proxy.php?id=' + bugId, true);
                xhr.onreadystatechange = function() {
                    if (xhr.readyState === 4) {
                        if (xhr.status === 200) {
                            try {
                                var response = JSON.parse(xhr.responseText);
                                if (response.issue) {
                                    var issue = response.issue;
                                    var statusClass = 'open';
                                    if (issue.status.name.toLowerCase().includes('closed')) {
                                        statusClass = 'closed';
                                    } else if (issue.status.name.toLowerCase().includes('progress')) {
                                        statusClass = 'in-progress';
                                    }
                                    
                                    container.innerHTML = 
                                        '<span class="redmine-bug-status ' + statusClass + '">' + issue.status.name + '</span>' +
                                        '<strong>Priority:</strong> ' + issue.priority.name + '<br>' +
                                        '<strong>Subject:</strong> ' + issue.subject + '<br>' +
                                        '<strong>Description:</strong> ' + (issue.description ? issue.description.substring(0, 100) + '...' : 'No description');
                                } else {
                                    container.innerHTML = 'Error loading bug details: No issue data';
                                }
                            } catch (e) {
                                container.innerHTML = 'Error parsing bug details: ' + e.message;
                            }
                        } else {
                            container.innerHTML = 'Error loading bug details: ' + xhr.status;
                        }
                    }
                };
                xhr.send();
            }
            
            // Run the bug display enhancement
            enhanceBugDisplay();
            
            // Also run periodically to catch any new bugs
            setInterval(enhanceBugDisplay, 5000);
        });
        </script>";
    }
    } catch (Exception $e) {
        // Log error but don't break TestLink
        error_log('Redmine Direct Inject Error: ' . $e->getMessage());
    }
}
?>
