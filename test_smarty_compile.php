<?php
/**
 * Quick test script to compile the problematic Smarty template.
 * Run from web: http://localhost:8086/test_smarty_compile.php
 * Or from CLI: php test_smarty_compile.php
 */
define('TL_TPL_TEST', true);

// Bootstrap minimum needed for Smarty
require_once dirname(__FILE__) . '/vendor/autoload.php';

$smarty = new Smarty();
$smarty->setTemplateDir(dirname(__FILE__) . '/gui/templates/tl-classic/');
$smarty->setCompileDir(dirname(__FILE__) . '/templates_c/');
$smarty->setForceCompile(true);

// Register dummy plugins so compilation doesn't fail on unknown functions
$smarty->registerPlugin('function', 'lang_get', function($p, $s) { return ''; });
$smarty->registerPlugin('function', 'localize_timestamp', function($p, $s) { return ''; });
$smarty->registerPlugin('function', 'localize_tc_status', function($p, $s) { return ''; });
$smarty->registerPlugin('function', 'load_notes', function($p, $s) { return ''; });
$smarty->registerPlugin('modifier', 'escape', function($s) { return htmlspecialchars((string)$s); });
$smarty->registerPlugin('modifier', 'nl2br', function($s) { return nl2br((string)$s); });
$smarty->registerPlugin('modifier', 'json_encode', function($s) { return json_encode($s); });

echo "<pre>Attempting to compile inc_exec_show_tc_exec.tpl...\n";
try {
    $tpl = $smarty->createTemplate('execute/inc_exec_show_tc_exec.tpl');
    $compiler = new Smarty_Internal_TemplateCompilerBase($smarty);
    // Force compile
    $compiled = $smarty->compileAllTemplates('execute/inc_exec_show_tc_exec.tpl', true);
    echo "Compiled OK!\n";
} catch (SmartyCompilerException $e) {
    echo "COMPILER ERROR:\n" . $e->getMessage() . "\n";
} catch (SmartyException $e) {
    echo "SMARTY ERROR:\n" . $e->getMessage() . "\n";
} catch (Exception $e) {
    echo "EXCEPTION:\n" . $e->getMessage() . "\n";
}
echo "</pre>";
?>
