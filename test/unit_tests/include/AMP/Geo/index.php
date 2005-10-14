<?php
define('RUNNER', true);
require_once('GroupTestGeo.php');

$test =& new GroupTestGeo();
$test->run(new HtmlReporter());
?>
