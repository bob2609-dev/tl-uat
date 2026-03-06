<?php
/**
 * Test Bug Display
 * 
 * This is a standalone test file to debug bug display issues
 */

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Create a log file for debugging
file_put_contents('test_bug_display_log.txt', date('Y-m-d H:i:s') . " - Test page accessed\n", FILE_APPEND);

// Function to get the bug status (simplified version)
function getBugStatus($bugID) {
    // Log the attempt
    file_put_contents('test_bug_display_log.txt', date('Y-m-d H:i:s') . " - Getting status for bug ID: {$bugID}\n", FILE_APPEND);
    
    // URL for the Redmine API
    $url = "https://support.profinch.com/issues/{$bugID}.json";
    
    // Log the URL
    file_put_contents('test_bug_display_log.txt', date('Y-m-d H:i:s') . " - API URL: {$url}\n", FILE_APPEND);
    
    // Try to get the status
    try {
        // Use curl to get the data
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        
        // Execute the request
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        // Log the response
        file_put_contents('test_bug_display_log.txt', date('Y-m-d H:i:s') . " - API response code: {$httpCode}\n", FILE_APPEND);
        
        // Check if we got a valid response
        if ($httpCode == 200) {
            // Parse the JSON response
            $data = json_decode($response, true);
            
            // Check if we have a status
            if (isset($data['issue']) && isset($data['issue']['status']) && isset($data['issue']['status']['name'])) {
                $status = $data['issue']['status']['name'];
                file_put_contents('test_bug_display_log.txt', date('Y-m-d H:i:s') . " - Got status: {$status}\n", FILE_APPEND);
                return $status;
            }
        }
    } catch (Exception $e) {
        file_put_contents('test_bug_display_log.txt', date('Y-m-d H:i:s') . " - Error: " . $e->getMessage() . "\n", FILE_APPEND);
    }
    
    // Default to 'Unknown' if we couldn't get the status
    return 'Unknown';
}

// Create some sample bug data
$bugs = array(
    '64812' => array(
        'summary' => 'Sample bug 1',
        'status' => getBugStatus('64812')
    ),
    '64813' => array(
        'summary' => 'Sample bug 2',
        'status' => getBugStatus('64813')
    )
);

// Start the HTML output
?>
<!DOCTYPE html>
<html>
<head>
    <title>TestLink Bug Display Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        h1 { color: #333; }
        .bug-container { margin: 10px 0; padding: 10px; border: 1px solid #ccc; }
        .bug-id { font-weight: bold; color: #cc0000; }
        .bug-status { font-weight: bold; }
        .bug-link { margin-left: 10px; }
    </style>
</head>
<body>
    <h1>TestLink Bug Display Test</h1>
    
    <div id="debug-info" style="background-color: #f0f0f0; padding: 10px; margin-bottom: 20px;">
        <h2>Debug Information</h2>
        <p>Page loaded at: <?php echo date('Y-m-d H:i:s'); ?></p>
        <p>PHP Version: <?php echo phpversion(); ?></p>
        <button onclick="showBugInfo()">Show Bug Info</button>
    </div>
    
    <h2>Sample Bug Display</h2>
    
    <?php foreach ($bugs as $bugID => $bugInfo): ?>
    <div class="bug-container">
        <span class="bug-id">Bug #<?php echo $bugID; ?></span>
        <span class="bug-status">[Status: <?php echo $bugInfo['status']; ?>]</span>
        <a class="bug-link" href="https://support.profinch.com/issues/<?php echo $bugID; ?>" target="_blank"><?php echo $bugInfo['summary']; ?></a>
    </div>
    <?php endforeach; ?>
    
    <h2>JavaScript Test</h2>
    <div id="js-test-area">
        <p>This text contains a bug reference: 64812</p>
        <p>This text contains another bug reference: 64813</p>
    </div>
    
    <script>
    // Function to show information about bug links on the page
    function showBugInfo() {
        console.log('Showing bug info...');
        
        // Find all links that might be bug links
        var links = document.getElementsByTagName('a');
        var bugLinks = [];
        
        for (var i = 0; i < links.length; i++) {
            if (links[i].href.indexOf('issues') !== -1) {
                bugLinks.push(links[i]);
            }
        }
        
        // Create a message with the bug link information
        var message = 'Found ' + bugLinks.length + ' bug links:\n';
        
        for (var i = 0; i < bugLinks.length; i++) {
            var link = bugLinks[i];
            var parent = link.parentNode;
            
            message += '\nBug Link #' + (i+1) + ':\n';
            message += '- URL: ' + link.href + '\n';
            message += '- Text: ' + link.textContent + '\n';
            message += '- Parent HTML: ' + parent.innerHTML + '\n';
        }
        
        // Display the message
        alert(message);
        console.log(message);
        
        // Create a visible display on the page
        var debugDiv = document.createElement('div');
        debugDiv.style.backgroundColor = '#ffffcc';
        debugDiv.style.padding = '10px';
        debugDiv.style.margin = '20px 0';
        debugDiv.style.border = '1px solid #cccc00';
        
        var heading = document.createElement('h3');
        heading.textContent = 'Bug Link Debug Info';
        debugDiv.appendChild(heading);
        
        var pre = document.createElement('pre');
        pre.textContent = message;
        debugDiv.appendChild(pre);
        
        // Add to the page
        document.body.appendChild(debugDiv);
    }
    
    // Log that the script has loaded
    console.log('Bug display test script loaded at ' + new Date().toISOString());
    </script>
</body>
</html>
