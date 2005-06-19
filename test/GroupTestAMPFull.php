<?php
#Defines the full AMP test suite
require_once('unit_tests/config.php');

#AMPTest is the top level grouping for the AMP test suite
#it includes the second level groupings of unit tests and 
#functional tests
class GroupTestAMPFull extends GroupTest {
	function GroupTestAMPFull($name = 'Full AMP Test Suite') {
		$this->GroupTest($name);
#		$this->addTestFile('unit_tests/AMPUnitTests.php');
#		$this->addTestFile('web_tests.php');
#		$this->addTestFile('unit_tests/userdata_test.php');
		$this->addTestFile('unit_tests/include/Modules/Payment/TestPayment.php');
		$this->addTestFile('unit_tests/include/Modules/Payment/TestCreditCard.php');
		$this->addTestFile('unit_tests/include/AMP/Article/TestArticleInc.php');
	}
}

UnitRunner_instantiate(__FILE__);
?>
