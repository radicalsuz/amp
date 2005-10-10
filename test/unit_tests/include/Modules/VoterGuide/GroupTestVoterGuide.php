<?php
require_once('unit_tests/config.php');

class GroupTestVoterGuide extends GroupTest {
	function GroupTestVoterGuide($name = 'AMP VoterGuide Test Suite') {
		$this->GroupTest($name);
		$this->addTestFile('TestVoterGuide.php');
	}

}

UnitRunner_instantiate( __FILE__ );
?>
