<?php
require_once('unit_tests/config.php');

class GroupTestDIAEvent extends GroupTest {
	function GroupTestDIAEvent($name = 'DIAEvent Test Suite') {
		$this->GroupTest($name);
		$this->addTestFile('TestEventSave.php');
		$this->addTestFile('TestEventRead.php');
	}

}

UnitRunner_instantiate( __FILE__ );
?>
