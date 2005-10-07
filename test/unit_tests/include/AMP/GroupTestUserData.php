<?php
require_once('unit_tests/config.php');

class GroupTestUserData extends GroupTest {
	function GroupTestUserData($name = 'UserData Test Suite') {
		$this->GroupTest($name);
		$this->addTestFile('TestUserData.php');
	}

}

UnitRunner_instantiate( __FILE__ );
?>
