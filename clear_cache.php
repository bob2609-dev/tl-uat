<?php
// Clear PHP opcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    echo "Opcache cleared successfully";
} else {
    echo "Opcache not available";
}

// Also clear any file stat cache
clearstatcache();
echo "<br>File stat cache cleared";
?>
