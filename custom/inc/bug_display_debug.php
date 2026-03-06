<?php
/**
 * Bug Display Debug for TestLink
 * 
 * This file provides a simple JavaScript solution to debug bug display issues
 */

/**
 * Function to inject JavaScript for debugging bug display
 */
function bug_display_debug() {
    // Log that the function was called
    error_log('bug_display_debug() function was called at ' . date('Y-m-d H:i:s'));
    
    // Create a log file for debugging
    file_put_contents(dirname(__FILE__) . '/../../bug_display_debug.txt', 
                      date('Y-m-d H:i:s') . " - bug_display_debug() function was called\n", 
                      FILE_APPEND);
    
    // Add a visible notification div
    echo "<div id='bug-debug-notification' style='position:fixed;top:10px;right:10px;background:rgba(255,0,0,0.8);color:white;padding:10px;font-size:14px;z-index:9999;border:2px solid black;'>";
    echo "Bug Display Debug Active - " . date('H:i:s');
    echo "</div>\n";
    
    // Add JavaScript to show bug information
    echo "<script>\n";
    echo "console.log('Bug display debug script loaded at " . date('Y-m-d H:i:s') . "');\n";
    
    // Add code to check for bug display issues
    echo "// Check for bug links every 2 seconds\n";
    echo "setInterval(function() {\n";
    echo "  // Look for bug links\n";
    echo "  var links = document.getElementsByTagName('a');\n";
    echo "  var bugLinks = [];\n";
    echo "  for(var i=0; i<links.length; i++) {\n";
    echo "    if(links[i].href.indexOf('issues') !== -1) {\n";
    echo "      bugLinks.push(links[i]);\n";
    echo "    }\n";
    echo "  }\n";
    echo "  \n";
    echo "  // If bug links found, update the notification\n";
    echo "  if(bugLinks.length > 0) {\n";
    echo "    var notification = document.getElementById('bug-debug-notification');\n";
    echo "    if(notification) {\n";
    echo "      notification.innerHTML = 'Found ' + bugLinks.length + ' bug links';\n";
    echo "      notification.style.backgroundColor = 'rgba(0,128,0,0.8)';\n";
    echo "    }\n";
    echo "    \n";
    echo "    // Check for bug ID and status text\n";
    echo "    var hasIdStatus = false;\n";
    echo "    for(var i=0; i<bugLinks.length; i++) {\n";
    echo "      var parentText = bugLinks[i].parentNode.textContent;\n";
    echo "      if(parentText.indexOf('[ID:') !== -1 && parentText.indexOf('[Status:') !== -1) {\n";
    echo "        hasIdStatus = true;\n";
    echo "        break;\n";
    echo "      }\n";
    echo "    }\n";
    echo "    \n";
    echo "    // Update notification with ID/Status info\n";
    echo "    if(notification) {\n";
    echo "      if(hasIdStatus) {\n";
    echo "        notification.innerHTML += ' with ID and Status info';\n";
    echo "      } else {\n";
    echo "        notification.innerHTML += ' WITHOUT ID and Status info';\n";
    echo "        notification.style.backgroundColor = 'rgba(255,128,0,0.8)';\n";
    echo "      }\n";
    echo "    }\n";
    echo "  }\n";
    echo "}, 2000);\n";
    
    echo "</script>\n";
    
    return true;
}
