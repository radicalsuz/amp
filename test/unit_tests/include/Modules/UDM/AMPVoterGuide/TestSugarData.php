<?php
require_once( 'unit_tests/config.php' );
/*require_once( 'AMP/BaseDB.php' );
require_once( 'Modules/UDM/AMPVoterGuide/OrganizerSave.inc.php' );
require_once( 'AMP/UserData/Input.inc.php' );
*/

if(!defined('AMP_VOTER_ORGANIZER_UDM')) define ('AMP_VOTER_ORGANIZER_UDM', 200);

if(!defined('AMP_SUGAR_LOGIN_USERNAME_ADMIN')) define('AMP_SUGAR_LOGIN_USERNAME_ADMIN', 'AMP');
if(!defined('AMP_SUGAR_LOGIN_PASSWORD_ADMIN')) define('AMP_SUGAR_LOGIN_PASSWORD_ADMIN', 'changeme');

require_once( 'Sugar/Data/Item.inc.php' );
require_once ( 'nuSoap/nusoap.php' );

class TestSugarData extends UnitTestCase {
//	var $_udm;
//	var $_plugin;
	var $_sugar_data_item;

    function TestSugarData () {
        $this->UnitTestCase('Voter Organizer (Sugar Data) Test');
    }

	function &getItem() {
		return $this->_sugar_data_item; 
	}

	function setUp() {
/*
		global $dbcon;
		$this->_udm =& new UserDataInput($dbcon, AMP_VOTER_ORGANIZER_UDM);
		$this->_plugin =& new UserDataPlugin_OrganizerSave_AMPVoterGuide( $this->_udm );
*/
		$this->_sugar_data_item =& new Sugar_Data_Item();
	}

	function tearDown() {
/*
		unset( $this->_udm );
		unset( $this->_plugin );
*/
		$item = $this->getItem();
		$item->doLogout();
		unset( $this->_sugar_data_item );
	}

/*
	function testLoginCredentials() {
		$this->assertEqual(AMP_SUGAR_LOGIN_USERNAME_ADMIN, 'AMP');
		$this->assertEqual(AMP_SUGAR_LOGIN_PASSWORD_ADMIN, 'changeme');
	}

	function testSoapClient() {
		$soap =& new soapclient( SUGAR_URL_SOAP, true );
		$this->assertNotNull($soap);
	}

	function testSugarTest() {
		$item =& $this->getItem();
		$item->doLogin();
		$source =& $item->getSource();
		$string = "AMP Testing";
		$test = $source->call( 'test', $string );
		$this->assertEqual($test, $string);

		$this->dump(array($item->getSession()));
		$uid = $source->call( 'get_user_id', $item->getSession );
		$this->dump(array($uid));
		$this->assertNotEqual($uid, 2);
	}


	function testGetSource() {
		$item =& $this->getItem();
		$source = $item->getSource();
		$this->assertNotNull($source);
		$result = $source->call('login', $item->_getLoginArgs());
//		$this->dump($item->_getLoginArgs());
//		$this->dump($result);
	}

	function testDoLogin() {
		$item =& $this->getItem();
		$result = $item->doLogin();
		$this->assertTrue($result);
	}

	function testDoLogout() {
		$item =& $this->getItem();
		$result = $item->doLogout();
		$this->assertTrue($result);
	}

	function testSetContactSource() {
		$item =& $this->getItem();
		$result = $item->setSource('contacts');
		$this->assertTrue($result);
	}

	function testSetUserSource() {
		$item =& $this->getItem();
		$this->assertTrue($item->init('users'));
		$this->assertFalse($item->getErrors());

		//Dummy Organizer
		$item->id = 'da009e2a-69b5-4737-388a-431111e893f6';
		$result = $item->read(array('id', 'date_entered', 'name'));
		$this->assertNotNull($item->getSession());
		$this->assertNotNull($item->_module);
		$this->assertTrue($result);
		$this->assertFalse($item->getErrors());
	}

*/
	function testAddOrganizer() {
		$item =& $this->getItem();
		$this->assertTrue($item->init('users'));

		$data = array('First_Name' => 'TestFirst'.time(), 'Last_Name' =>'TestLast'.time());
		$item->setData($data);

		$id = $item->save();
		$this->dump(array($id));
		$this->assertTrue($id);

		$new =& new Sugar_Data_Item('Users');
		$new->id = $id;
		
		$result = $new->read(array('first_name', 'last_name'));
	}
}

UnitRunner_instantiate( __FILE__ );
?>
