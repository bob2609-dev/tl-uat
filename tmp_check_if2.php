<?php
$file = 'C:/xampp/htdocs/tl-uat/gui/templates/tl-classic/execute/inc_exec_show_tc_exec.tpl';
$content = file_get_contents($file);
$lines = explode("\n", $content);
$stack = [];
foreach($lines as $i => $line) {
    if (preg_match_all('/\{(if\b|\/if\b)/', $line, $matches)) {
        foreach($matches[1] as $match) {
            if(strpos($match, 'if') === 0) {
                $stack[] = $i + 1;
            } elseif($match === '/if') {
                if(empty($stack)) {
                    echo 'EXTRA /if at line ' . ($i + 1) . PHP_EOL;
                } else {
                    array_pop($stack);
                }
            }
        }
    }
}
echo 'Unclosed opening IFs at lines: ' . implode(', ', $stack) . PHP_EOL;
