<?php
require_once( 'unit_tests/config.php' );
require_once( 'AMP/BaseDB.php' );
require_once( 'AMP/UserData/Input.inc.php' );
require_once( 'Modules/UDM/AMPVoterGuide/OrganizerSave.inc.php' );

if(!defined('AMP_VOTER_ORGANIZER_UDM')) define ('AMP_VOTER_ORGANIZER_UDM', 200);

if(!defined('AMP_SUGAR_LOGIN_USERNAME_ADMIN')) define('AMP_SUGAR_LOGIN_USERNAME_ADMIN', 'AMP');
if(!defined('AMP_SUGAR_LOGIN_PASSWORD_ADMIN')) define('AMP_SUGAR_LOGIN_PASSWORD_ADMIN', 'changeme');

class TestOrganizerSave extends UnitTestCase {
	var $_udm;
	var $_plugin;

    function TestOrganizerSave () {
        $this->UnitTestCase('Voter Organizer -> Sugar Module Test');
    }

	function setUp() {
		$dbcon =& AMP_Registry::getDbcon();
		$this->_udm =& new UserDataInput($dbcon, AMP_VOTER_ORGANIZER_UDM);
		$this->_plugin =& new UserDataPlugin_OrganizerSave_AMPVoterGuide( $this->_udm );
	}

	function tearDown() {
		unset( $this->_udm );
		unset( $this->_plugin );
	}

	function testLoginCredentials() {
		$this->assertEqual(AMP_SUGAR_LOGIN_USERNAME_ADMIN, 'AMP');
		$this->assertEqual(AMP_SUGAR_LOGIN_PASSWORD_ADMIN, 'changeme');
	}

	function testNewPlugin() {
		$plugin =& new UserDataPlugin_OrganizerSave_AMPVoterGuide( $this->_udm );
		$this->assertNotNull($plugin);
	}

	function testSugarDataItem() {
		$plugin =& $this->_plugin;
	}

}

UnitRunner_instantiate( __FILE__ );
?>
