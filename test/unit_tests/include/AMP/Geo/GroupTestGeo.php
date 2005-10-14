<?php
require_once('unit_tests/config.php');

class GroupTestGeo extends GroupTest {
	function GroupTestGeo($name = 'AMP Geo Test Suite') {
		$this->GroupTest($name);
		$this->addTestFile('TestGeo.php');
	}

}

UnitRunner_instantiate( __FILE__ );
?>
