<?php
/**
 * Script to list attachments for an execution
 */
// Include TestLink configuration
require_once('config.inc.php');

// Get execution ID
$exec_id = isset($_GET['exec_id']) ? intval($_GET['exec_id']) : 0;
if (!$exec_id) {
    die("No execution ID specified");
}

// Connect to database
$db = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
if ($db->connect_error) {
    die("Database connection failed");
}

// Get attachments for this execution
$sql = "SELECT * FROM attachments WHERE fk_table = 'executions' AND fk_id = $exec_id ORDER BY id DESC";
$result = $db->query($sql);

// Output HTML header
header('Content-Type: text/html; charset=UTF-8');
?>
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; margin: 0; padding: 5px; }
        .attachment-row { display: flex; align-items: center; margin-bottom: 5px; }
        .attachment-row img { margin-right: 5px; border: none; }
        .attachment-info { font-style: italic; color: #666; }
        .icon { vertical-align: middle; }
    </style>
</head>
<body>
<?php
// Check if we found any attachments
if ($result && $result->num_rows > 0) {
    echo "<div class='attachments-list'>";
    while ($attachment = $result->fetch_assoc()) {
        // Determine if it's an image
        $isImage = (strpos($attachment['file_type'], 'image/') === 0);
        $inlineViewable = $isImage;
        
        echo "<div class='attachment-row'>";
        
        // Download link
        echo "<a href='attachment_download_fixed.php?id={$attachment['id']}' target='_blank' class='attachment-link'>";
        echo "<img src='gui/themes/default/images/new_f2_16.png' class='icon' title='Download attachment' />";
        echo "</a> ";
        
        // Inline view icon (for images)
        if ($inlineViewable) {
            echo "<img src='gui/themes/default/images/eye.png' class='icon' style='cursor:pointer;' ";
            echo "title='Display inline' onclick=\"parent.showInlineAttachment({$attachment['id']})\" /> ";
        }
        
        // Attachment info
        echo "<span class='attachment-info'>{$attachment['file_name']} ({$attachment['file_size']} bytes, ";
        echo "{$attachment['file_type']}) " . date('d/m/Y', strtotime($attachment['date_added'])) . "</span>";
        
        echo "</div>";
    }
    echo "</div>";
    
    // Add JavaScript function for inline display
    echo "<script>
    function showInlineAttachment(id) {
        parent.fixedToogleImageURL('inline_img_container_' + id, id);
    }
    </script>";
} else {
    echo "<p>No attachments found for this execution.</p>";
}

$db->close();
?>
</body>
</html>