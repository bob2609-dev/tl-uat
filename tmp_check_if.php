<?php
$file = 'C:/xampp/htdocs/tl-uat/gui/templates/tl-classic/execute/inc_exec_show_tc_exec.tpl';
$content = file_get_contents($file);
$lines = explode("\n", $content);
$stack = [];
foreach($lines as $i => $line) {
    if (preg_match_all('/\{(if[ \}]|else|elseif|\/if)/', $line, $matches)) {
        foreach($matches[1] as $match) {
            $match = trim($match);
            if(strpos($match, 'if') === 0) {
                $stack[] = $i + 1;
            } elseif($match === '/if') {
                $open = array_pop($stack);
            }
        }
    }
}
echo 'Unclosed lines: ' . implode(', ', $stack) . PHP_EOL;
