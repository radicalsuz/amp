<?php
define('RUNNER', true);
require_once('GroupTestVoterGuide.php');

$test =& new GroupTestVoterGuide();
$test->run(new HtmlReporter());
?>
