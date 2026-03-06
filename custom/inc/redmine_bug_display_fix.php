<?php
/**
 * Redmine Bug Display Fix for TestLink
 * 
 * This file provides a JavaScript solution to enhance the display of Redmine bugs
 * in TestLink's test execution page by adding the bug ID and status.
 */

/**
 * Function to inject JavaScript to enhance bug display
 */
function redmine_bug_display_fix() {
    // Get the Redmine URL from configuration
    global $tlCfg;
    $redmineUrl = isset($tlCfg->issueTracker->toolsDefaultValues['redmine']['url']) ? 
                 $tlCfg->issueTracker->toolsDefaultValues['redmine']['url'] : 'https://support.profinch.com';
    
    // Log that the function was called
    error_log('redmine_bug_display_fix() function was called at ' . date('Y-m-d H:i:s'));
    
    // Create a log file for debugging
    file_put_contents(dirname(__FILE__) . '/../../redmine_bug_display_debug.txt', 
                      date('Y-m-d H:i:s') . " - redmine_bug_display_fix() function was called\n", 
                      FILE_APPEND);
    
    // Output a visible HTML comment to verify the script is running
    echo "<!-- Redmine Bug Display Fix loaded at " . date('Y-m-d H:i:s') . " -->\n";
    
    // Add a visible notification
    echo "<div id='redmine-bug-fix-notification' style='position:fixed;bottom:30px;right:10px;background:rgba(0,0,0,0.7);color:white;padding:5px;font-size:10px;z-index:9999;'>Bug Display Fix Active</div>\n";
    
    // Output the JavaScript to enhance bug display
    echo "<script type='text/javascript'>\n";
    echo "console.log('Redmine bug display fix script loaded at " . date('Y-m-d H:i:s') . "');\n";
    echo "var redmineUrl = '{$redmineUrl}';\n";
    
    // Function to extract bug ID from a link
    echo "function extractBugId(url) {\n";
    echo "  var match = url.match(/issues\/(\\d+)/);
";
    echo "  return match ? match[1] : null;\n";
    echo "}\n";
    
    // Function to fetch bug status from our API
    echo "function fetchBugStatus(bugId, element) {\n";
    echo "  console.log('Fetching status for bug ID: ' + bugId);\n";
    echo "  fetch('/redmine_bug_status.php?id=' + bugId)\n";
    echo "    .then(response => response.json())\n";
    echo "    .then(data => {\n";
    echo "      if (data && data.status) {\n";
    echo "        // Create status display\n";
    echo "        var statusSpan = document.createElement('span');\n";
    echo "        statusSpan.style.marginLeft = '5px';\n";
    echo "        statusSpan.style.fontWeight = 'bold';\n";
    echo "        statusSpan.textContent = '[Status: ' + data.status + ']';\n";
    echo "        \n";
    echo "        // Add color based on status\n";
    echo "        if (data.status.toLowerCase() === 'open') {\n";
    echo "          statusSpan.style.color = '#cc0000';\n";
    echo "        } else if (data.status.toLowerCase() === 'in progress') {\n";
    echo "          statusSpan.style.color = '#ff9900';\n";
    echo "        } else if (data.status.toLowerCase() === 'resolved' || data.status.toLowerCase() === 'closed') {\n";
    echo "          statusSpan.style.color = '#009900';\n";
    echo "        }\n";
    echo "        \n";
    echo "        // Add ID display\n";
    echo "        var idSpan = document.createElement('span');\n";
    echo "        idSpan.style.marginLeft = '5px';\n";
    echo "        idSpan.style.fontWeight = 'bold';\n";
    echo "        idSpan.style.color = '#0066cc';\n";
    echo "        idSpan.textContent = '[ID: ' + bugId + ']';\n";
    echo "        \n";
    echo "        // Insert the spans after the link\n";
    echo "        element.parentNode.insertBefore(statusSpan, element.nextSibling);\n";
    echo "        element.parentNode.insertBefore(idSpan, element.nextSibling);\n";
    echo "      }\n";
    echo "    })\n";
    echo "    .catch(error => {\n";
    echo "      console.error('Error fetching bug status:', error);\n";
    echo "    });\n";
    echo "}\n";
    
    // Function to enhance bug links
    echo "function enhanceBugLinks() {\n";
    echo "  // Find all bug links in the table\n";
    echo "  var bugCells = document.querySelectorAll('table.tl_table_bugs_per_test_case td:nth-child(3)');\n";
    echo "  \n";
    echo "  bugCells.forEach(function(cell) {\n";
    echo "    var links = cell.querySelectorAll('a');\n";
    echo "    links.forEach(function(link) {\n";
    echo "      var bugId = extractBugId(link.href);\n";
    echo "      if (bugId) {\n";
    echo "        // Mark this link as processed\n";
    echo "        if (!link.hasAttribute('data-bug-processed')) {\n";
    echo "          link.setAttribute('data-bug-processed', 'true');\n";
    echo "          // Fetch and display bug status\n";
    echo "          fetchBugStatus(bugId, link);\n";
    echo "        }\n";
    echo "      }\n";
    echo "    });\n";
    echo "  });\n";
    echo "}\n";
    
    // Run the enhancement when the page is loaded
    echo "document.addEventListener('DOMContentLoaded', function() {\n";
    echo "  console.log('DOM loaded, enhancing bug links');\n";
    echo "  // Run initially\n";
    echo "  enhanceBugLinks();\n";
    echo "  // Run again after a short delay to catch any dynamically loaded content\n";
    echo "  setTimeout(enhanceBugLinks, 1000);\n";
    echo "});\n";
    
    echo "</script>\n";
    
    return true;
}
