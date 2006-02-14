<?php
require_once('unit_tests/config.php');

class GroupTestDIAEvent extends GroupTest {
	function GroupTestDIAEvent($name = 'DIAEvent Test Suite') {
		$this->GroupTest($name);
		$this->addTestFile('TestEventSave.php');
	}

}

UnitRunner_instantiate( __FILE__ );
?>
