<?php
define('RUNNER', true);
require_once('GroupTestDIA.php');

$test =& new GroupTestDIA();
$test->run(new HtmlReporter());
?>
