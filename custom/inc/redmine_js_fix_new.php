<?php
/**
 * Redmine JavaScript Fix for TestLink
 * 
 * This file provides a JavaScript solution to make bug IDs clickable in TestLink
 */

/**
 * Function to inject JavaScript to make bug IDs clickable
 */
function redmine_js_fix() {
    // Get the Redmine URL from configuration
    global $tlCfg;
    $redmineUrl = isset($tlCfg->issueTracker->toolsDefaultValues['redmine']['url']) ? 
                 $tlCfg->issueTracker->toolsDefaultValues['redmine']['url'] : 'https://support.profinch.com';
    
    // Log that the function was called
    error_log('redmine_js_fix() function was called at ' . date('Y-m-d H:i:s'));
    
    // Create a log file for debugging
    file_put_contents(dirname(__FILE__) . '/../../redmine_js_fix_debug.txt', 
                      date('Y-m-d H:i:s') . " - redmine_js_fix() function was called\n", 
                      FILE_APPEND);
    
    // Output a visible HTML comment to verify the script is running
    echo "<!-- Redmine JS fix loaded at " . date('Y-m-d H:i:s') . " -->\n";
    
    // Add a small notification div that can be seen on the page
    echo "<div id='redmine-fix-notification' style='position:fixed;bottom:10px;right:10px;background:rgba(0,0,0,0.7);color:white;padding:5px;font-size:10px;z-index:9999;'>Redmine Fix Active</div>\n";
    
    // Output a simple script tag with minimal JavaScript
    echo "<script>\n";
    echo "console.log('Redmine bug fix script loaded at " . date('Y-m-d H:i:s') . "');\n";
    echo "var redmineUrl = '{$redmineUrl}';\n";
    echo "function fixBugLinks() {\n";
    echo "  console.log('Scanning for bug IDs');\n";
    echo "  var pattern = /\\b(64\\d{3,5})\\b/g;\n";
    echo "  var found = 0;\n";
    echo "  var walker = document.createTreeWalker(document.body, NodeFilter.SHOW_TEXT);\n";
    echo "  var node;\n";
    echo "  var nodesToReplace = [];\n";
    echo "  while(node = walker.nextNode()) {\n";
    echo "    if(pattern.test(node.nodeValue)) {\n";
    echo "      nodesToReplace.push(node);\n";
    echo "    }\n";
    echo "  }\n";
    echo "  console.log('Found ' + nodesToReplace.length + ' nodes with bug IDs');\n";
    echo "  for(var i=0; i<nodesToReplace.length; i++) {\n";
    echo "    var node = nodesToReplace[i];\n";
    echo "    var text = node.nodeValue;\n";
    echo "    var span = document.createElement('span');\n";
    echo "    span.innerHTML = text.replace(pattern, '<a href=\\\"' + redmineUrl + '/issues/$1\\\"' +\n";
    echo "                                   ' target=\\\"_blank\\\"' +\n";
    echo "                                   ' style=\\\"color:#0066cc;font-weight:bold;\\\"' +\n";
    echo "                                   '>$1</a>');\n";
    echo "    if(node.parentNode) {\n";
    echo "      node.parentNode.replaceChild(span, node);\n";
    echo "      found++;\n";
    echo "    }\n";
    echo "  }\n";
    echo "  console.log('Replaced ' + found + ' bug IDs');\n";
    echo "}\n";
    echo "fixBugLinks();\n";
    echo "setInterval(fixBugLinks, 2000);\n";
    echo "</script>\n";
    
    return true;
}
