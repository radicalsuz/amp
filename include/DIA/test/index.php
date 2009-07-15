<?php
if(!defined('DIR_SEP')) {
    define('DIR_SEP', DIRECTORY_SEPARATOR);
}

if(!defined('DIA_TEST_DIR')) {
	define('DIA_TEST_DIR', dirname(__FILE__).DIR_SEP);
}

require_once(DIA_TEST_DIR.'config.php');

define('DIA_TEST_RUNNER', true);
require_once(DIA_TEST_DIR.'GroupTestDIA.php');

$test =& new GroupTest_DIA();
$test->run(new HtmlReporter());
?>
