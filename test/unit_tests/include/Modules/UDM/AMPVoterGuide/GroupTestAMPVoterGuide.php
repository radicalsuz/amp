<?php
require_once('unit_tests/config.php');
//require_once('TestOrganizerSave.php');
//require_once('TestSugarData.php');


class GroupTestAMPVoterGuide extends GroupTest {
	function GroupTestAMPVoterGuide($name = 'AMP Voter Guide Test Suite') {
		$this->GroupTest($name);
//		$this->addTestFile('TestSugarData.php');
//		$this->addTestFile('TestOrganizerSave.php');
		$this->addTestFile('TestSugarPlugin.php');
	}

}

UnitRunner_instantiate( __FILE__ );
?>
