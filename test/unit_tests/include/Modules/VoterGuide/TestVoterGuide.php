<?php
require_once( 'unit_tests/config.php' );

require_once( 'AMP/BaseDB.php' );
require_once( 'Modules/VoterGuide/VoterGuide.php' );
require_once( 'Modules/VoterGuide/Controller.php' );

class TestVoterGuide extends UnitTestCase {

	var $dbcon;
	var $guide;
	var $_request = array (
  'action' => 'new',
  'plugin_AMPVoterGuide_id' => '',
  'plugin_AMPVoterGuide_picture_value' => '',
  'MAX_FILE_SIZE' => '1048576',
  'plugin_AMPVoterGuide_filelink_value' => '',
  'modin' => '200',
  'First_Name' => 'Staple',
  'Last_Name' => 'Gunn',
  'Email' => 'staple@gunn.com',
  'Phone' => '123 456 7890',
  'Cell_Phone' => '123 456 7891',
  'Phone_Provider' => 'verizon',
  'Work_Phone' => '123 456 7892',
  'Street' => '123 Sesame St',
  'Street_2' => 'Apt 1',
  'City' => 'San Francisco',
  'State' => 'CA',
  'Zip' => '94110',
  'plugin_AMPVoterGuide_name' => 'Testeroo Voter Guide',
  'plugin_AMPVoterGuide_short_name' => 'testeroo',
  'plugin_AMPVoterGuide_city' => 'San Francisco',
  'plugin_AMPVoterGuide_state' => 'CA',
  'plugin_AMPVoterGuide_election_date' => 
  array (
    'd' => '8',
    'M' => '11',
    'Y' => '2005',
  ),
  'plugin_AMPVoterGuide_blurb' => 'Testeroo Intro',
  'plugin_AMPVoterGuide_footer' => 'Testeroo Outro',
  'plugin_AMPVoterGuide_accurate_checkbox' => '1',
  'plugin_AMPVoterGuide_trust_checkbox' => '1',
  'btnUdmSubmit' => 'Submit My Voter Guide',
  'plugin_AMPVoterGuide_voterguidePositions_id' => 
  array (
    1 => '',
    2 => '',
  ),
  'plugin_AMPVoterGuide_voterguidePositions_headline' => 
  array (
    1 => 'Testeroo First Endorsement Category',
    2 => 'Testeroo Second Endorsement Category',
  ),
  'plugin_AMPVoterGuide_voterguidePositions_item' => 
  array (
    1 => 'Testeroo First Endorsement Candidate/Issue',
    2 => 'Testeroo Second Endorsement Candidate/Issue',
  ),
  'plugin_AMPVoterGuide_voterguidePositions_position' => 
  array (
    1 => '1',
    2 => '2',
  ),
  'plugin_AMPVoterGuide_voterguidePositions_comments' => 
  array (
    1 => 'Testeroo First Endorsement Reason',
    2 => 'Testeroo Second Endorsement Reason',
  ),
  'plugin_AMPVoterGuide_voterguidePositions_voterguide_id' => 
  array (
    1 => '',
    2 => '',
  ),
  'plugin_AMPVoterGuide_voterguidePositions_textorder' => 
  array (
    1 => '',
    2 => '',
  ),
);

	var $_files = array (
  'plugin_AMPVoterGuide_picture' => 
  array (
    'name' => 'experts_small.gif',
    'type' => 'image/gif',
    'tmp_name' => '/var/tmp/phplUJ8pe',
    'error' => 0,
    'size' => 5368,
  ),
  'plugin_AMPVoterGuide_filelink' => 
  array (
    'name' => 'voterguide05 Portland.pdf',
    'type' => '',
    'tmp_name' => '',
    'error' => 2,
    'size' => 0,
  ),
);

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

	function testAutoPublish() {

	}
/*
	function testGetBlocID() {
		$guide =& new VoterGuide($this->dbcon);
		$id = $guide->getBlocGroupIDByName('Smackdown 2005');
		$this->assertEqual($id, 22092);

		$id = $guide->getBlocGroupIDByName('no such group');
		$this->assertFalse($id);

		$id = $guide->getBlocGroupIDByName('no such group');
		$this->assertFalse($id);
	}

	function testUniqueShortName() {
		$this->assertTrue($this->guide->isUniqueShortName('thisnamecannotpossiblyalreadyexist', true));
		$id = $this->guide->getBlocGroupIDByName('california');
		$this->assertTrue($id);
		$this->assertFalse($this->guide->isUniqueShortName('california', true));
	}
*/

		
}

UnitRunner_instantiate( __FILE__ );
?>
