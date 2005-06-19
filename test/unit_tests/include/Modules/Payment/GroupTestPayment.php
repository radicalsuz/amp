<?php

//require_once('unit_tests/config.php');

#AMPTest is the top level grouping for the AMP test suite
#it includes the second level groupings of unit tests and 
#functional tests
class GroupTestPayment extends GroupTest {
    function GroupTestPayment($name = 'AMP include/Modules/Payment Directory Unit Tests') {
        $this->GroupTest($name);
        $this->addTestFile('TestPayment.php');
//        $this->addTestFile('CreditCardTest.php');
    }
}

if(! defined('RUNNER')) {
    define('RUNNER', true);
    $test = &new GroupTestPayment();
    $test->run(new HtmlReporter());
}

?>
