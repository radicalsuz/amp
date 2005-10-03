<?php
if(!defined('AMP_VOTER_ORGANIZER_UDM')) define ('AMP_VOTER_ORGANIZER_UDM', 200);

if(!defined('AMP_SUGAR_LOGIN_USERNAME_ADMIN')) define('AMP_SUGAR_LOGIN_USERNAME_ADMIN', 'AMP');
if(!defined('AMP_SUGAR_LOGIN_PASSWORD_ADMIN')) define('AMP_SUGAR_LOGIN_PASSWORD_ADMIN', 'changeme');

require_once( 'unit_tests/config.php' );
require_once( 'AMP/BaseDB.php' );
require_once( 'AMP/UserData/Input.inc.php' );
//require_once( 'Modules/UDM/AMPVoterGuide/OrganizerSave.inc.php' );
require_once( 'Modules/UDM/Sugar/Save.inc.php' );


class TestSugarPlugin extends UnitTestCase {
	var $_udm;
	var $_plugin;

    function TestSugarPlugin() {
        $this->UnitTestCase('Sugar Save Plugin Module Test');
    }

	function setUp() {
		$dbcon =& AMP_Registry::getDbcon();
		$this->_udm =& new UserDataInput($dbcon, AMP_VOTER_ORGANIZER_UDM);
		$this->_plugin =& new UserDataPlugin_Save_Sugar( $this->_udm );
	}

	function tearDown() {
		unset( $this->_udm );
		unset( $this->_plugin );
	}

	function testLoginCredentials() {
		$this->assertEqual(AMP_SUGAR_LOGIN_USERNAME_ADMIN, 'AMP');
		$this->assertEqual(AMP_SUGAR_LOGIN_PASSWORD_ADMIN, 'changeme');
	}

	function testSave() {
		require_once( 'Sugar/Data/Item.inc.php' );
		$sugarDaddy =& new Sugar_Data_Item( 'Users' );

		$now = date('mdHi');
		$data = array(
			'First_Name'	=> 'TestFirst'.$now,
            'Last_Name'	=> 'TestLast'.$now,
            'occupation' => 'Test Occupation',
            'Phone' => 'Test Phone',
            'Cell_Phone' => 'Test Cell Phone',
            'Work_Phone' => 'Test Work Phone',
            'Work_Fax' => 'Test Work Fax',
            'Email' => 'Test Email',
            'Notes' => 'Notes.  fields that should be here:\n'.
						'First_Name, Last_Name, occupation, Phone,\n'.
						'Cell_Phone, Work_Phone, Work_Fax, Email, Notes,\n'.
						'Street, City, State, Zip, Country',
            'Street' => 'Test Street',
            'City' => 'Test City',
            'State' => 'Test State',
            'Zip' => 'Test Zip',
            'Country' => 'Test Country',
			'user_name' => 'user'.$now,
			'user_hash' => strtolower(md5('password')),
			'user_password' => $sugarDaddy->sugar_encrypt_password('user'.$now,strtolower(md5('password'))),
			'mail_fromname' => 'Test Mail From Name',
			'mail_fromaddress' => 'test@mailfromaddress.com',
			'is_admin' => 'off',
			'status' => 'Active',
			'portal_app' => 'AMP');

		$data['First_Name'] = 'TestOrganizer'.$now;
		$result = $this->_plugin->save($data, array('module' => 'Users'));
		$this->assertTrue($result);
		$this->dump(array($result));

		$result = $sugarDaddy->_source->call('set_preference', 
									array('user_name'=>$data['user_name'],
										'password'=>$data['user_hash'],
										'preference_name'=>'mail_fromname',
										'preference_value'=>'TestMailFromName'));
		$this->assertTrue($result);

		$result = $sugarDaddy->_source->call('set_preference', 
									array('user_name'=>$data['user_name'],
										'password'=>$data['user_hash'],
										'preference_name'=>'mail_fromaddress',
										'preference_value'=>'test@mailfromaddress.com'));
		$this->assertTrue($result);

		$data['First_Name'] = 'TestVoter'.$now;
		$data['assigned_user_id'] = $result;
		$result = $this->_plugin->save($data, array('module' => 'Contacts'));
		$this->assertTrue($result);
		$this->dump(array($result));
	}
}

UnitRunner_instantiate( __FILE__ );
?>
