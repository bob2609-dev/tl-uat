<?php
require_once 'C:/xampp/htdocs/tl-uat/config.inc.php';
require_once 'C:/xampp/htdocs/tl-uat/lib/functions/common.php';

echo "TL_WEB_PATH: " . TL_WEB_PATH . "\n";
$tc_id = 123;
$tplan_id = 456;

// Simulating how TestLink builds a direct link to a test case
$url = rtrim(TL_WEB_PATH, '/') . "/linkto.php?tproject_id=0&testplan_id={$tplan_id}&testcase_id={$tc_id}";
echo "URL: $url\n";
