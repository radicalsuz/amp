<?php
require_once( 'unit_tests/config.php' );

require_once( 'AMP/BaseDB.php' );
require_once( 'Modules/VoterGuide/VoterGuide.php' );

class TestVoterGuide extends UnitTestCase {

	var $dbcon;
	var $guide;

    function TestVoterGuide () {
        $this->UnitTestCase('AMP VoterGuide Test');
    }

	function setUP() {
		$this->dbcon =& AMP_Registry::getDbcon();	
		$this->guide =& new VoterGuide($this->dbcon);
	}

	function tearDown() {
		unset($this->dbcon);
		unset($this->guide);
	}

	function testGetBlocID() {
		$guide =& new VoterGuide($this->dbcon);
		$id = $guide->getBlocGroupIDByName('Smackdown 2005');
		$this->assertEqual($id, 22092);

		$id = $guide->getBlocGroupIDByName('no such group');
		$this->assertFalse($id);
	}

	function testUniqueShortName() {
		$this->assertTrue($this->guide->isUniqueShortName('thisnamecannotpossiblyalreadyexist', true));
		$id = $this->guide->getBlocGroupIDByName('california');
		$this->assertTrue($id);
		$this->assertFalse($this->guide->isUniqueShortName('california', true));
	}
		
}

UnitRunner_instantiate( __FILE__ );
?>
