<?php
define('RUNNER', true);
require_once('GroupTestUserData.php');

$test =& new GroupTestUserData();
$test->run(new HtmlReporter());
?>
