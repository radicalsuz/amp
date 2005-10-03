<?php
require_once('unit_tests/config.php');

class GroupTestDIA extends GroupTest {
	function GroupTestDIA($name = 'AMP DIA Test Suite') {
		$this->GroupTest($name);
		$this->addTestFile('TestDIA_API.php');
		$this->addTestFile('TestDIA_Object.php');
	}

}

UnitRunner_instantiate( __FILE__ );
?>
