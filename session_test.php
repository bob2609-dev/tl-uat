<?php
session_start();
echo "Session ID: " . session_id() . "\n";
echo "Session save path: " . session_save_path() . "\n";
echo "Session cookie params: " . print_r(session_get_cookie_params(), true) . "\n";
?>
