<?php
/**
 * Custom header inclusion to fix image display issues
 * Place this in custom/inc/ directory
 */

// Only add once per page load
static $imageFixIncluded = false;

if (!$imageFixIncluded) {
    // Output script tag to include our fixes - use the simpler version
    echo "<script type='text/javascript' src='" . $_SESSION['basehref'] . "simple_image_fix.js'></script>";
    
    // Add a style to force image reloads
    echo "<style>
        .image-refreshed { min-width: 10px; min-height: 10px; }
    </style>";
    
    $imageFixIncluded = true;
}
?>
