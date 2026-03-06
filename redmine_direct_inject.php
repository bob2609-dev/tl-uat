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
    
    // Configuration
    $redmineConfig = array(
        'url' => 'https://support.profinch.com',
        'api_key' => 'a597e200f8923a85484e81ca81d731827b8dbf3d',
        'project_id' => 'nmb-fcubs-14-7-uat2'
    );
    
    // Get base URL
    $baseHref = isset($_SESSION['basehref']) ? $_SESSION['basehref'] : './';
    
    // Check if we're on an execution page
    $isExecutionPage = strpos($_SERVER['SCRIPT_NAME'], 'execSetResults.php') !== false;
    
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
        
        // Add direct script injection
        echo "<script type='text/javascript'>
        // Wait for page to load
        window.addEventListener('DOMContentLoaded', function() {
            console.log('Redmine direct inject loaded');
            
            // Function to add Redmine buttons
            function addRedmineButtons() {
                // Find all status selects
                var statusSelects = document.querySelectorAll('select[name^=\"statusSingle\"]');
                console.log('Found ' + statusSelects.length + ' status selects');
                
                statusSelects.forEach(function(select) {
                    // Get parent container
                    var container = select.closest('div');
                    if (!container) return;
                    
                    // Check if we already added buttons
                    if (container.querySelector('.redmine-container')) return;
                    
                    // Create container for Redmine buttons
                    var redmineContainer = document.createElement('div');
                    redmineContainer.className = 'redmine-container';
                    redmineContainer.style.display = 'none'; // Initially hidden
                    
                    // Get test case name
                    var tcTitle = document.querySelector('.exec_tc_title');
                    var tcName = tcTitle ? tcTitle.textContent.trim() : 'Test Case';
                    
                    // Set container content
                    redmineContainer.innerHTML = '\
                        <div class=\"redmine-title\">Redmine Bug Tracking</div>\
                        <a href=\"{$baseHref}redmine-integration.php?testcase=' + encodeURIComponent(tcName) + '\" \
                           class=\"redmine-button\" target=\"_blank\" style=\"margin-right:5px;\">\
                            Create Bug in Redmine\
                        </a>\
                        <a href=\"{$baseHref}redmine-integration.php?testcase=' + encodeURIComponent(tcName) + '&operation=link\" \
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
            
            // Add buttons immediately
            addRedmineButtons();
            
            // Also check periodically in case of AJAX updates
            setInterval(addRedmineButtons, 2000);
        });
        </script>";
    }
}
?>
