<?php
#Defines the AMP unit test suite
if(! defined('SIMPLE_TEST')) {
    define('SIMPLE_TEST', '../../../../simpletest/');
}
require_once(SIMPLE_TEST.'unit_tester.php');
require_once(SIMPLE_TEST.'reporter.php');
#require_once('UserData/AMPUnitTestsIncludeAMPUserData.php');

#require_once('unit_tests/config.php');

require_once('GroupTestPayment.php');

if(! defined('RUNNER')) {
    define('RUNNER', true);

    $test = &new GroupTestPayment('AMP include/Modules/Payment Directory Unit Tests');
    $test->run(new HtmlReporter());
}
?>
