<?php
define('RUNNER', true);
define('DIA_TEST_RUNNER', true);
require_once('GroupTestDIAEvent.php');

$test =& new GroupTestDIAEvent();
$test->run(new HtmlReporter());
?>
