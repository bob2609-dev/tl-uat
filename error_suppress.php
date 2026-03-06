<?php
// Include PHP 8 compatibility layer
require_once('custom/inc/php8_init.php');
// Disable all PHP deprecation warnings
// Specifically suppress dynamic property creation warnings
error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
ini_set('display_errors', 0);
