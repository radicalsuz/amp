<?php
define('RUNNER', true);
require_once('GroupTestAMPVoterGuide.php');

$test =& new GroupTestAMPVoterGuide();
$test->run(new HtmlReporter());
?>
