<?php
if(!defined('DIA_TEST_DIR')) define('DIA_TEST_DIR', dirname(__FILE__).DIR_SEP);
require_once( DIA_TEST_DIR.'config.php' );

require_once( DIA_DIR.'API.php' );

require_once(SIMPLE_TEST.'mock_objects.php');
require_once(DIA_PEAR_DIR.'HTTP/Request.php');
Mock::generate('HTTP_Request', 'MockHttpRequest');

class TestDIA_API extends UnitTestCase {

	var $api;

	//mock request object, to simulate DIA responses
	var $request;

	var $test_string;
	var $test_email_domain;

    function TestDIA_API () {
        $this->UnitTestCase('DIA API Test');
    }

	function setUp() {
		$this->api =& DIA_API::create();
		$this->request =& new MockHttpRequest($this);

		$now = date('mdHi');
		$this->test_string = DIA_TEST_DATA_PREFIX.$now;
		$this->test_email_domain = '@radicaldesigns.org';
	}

	function tearDown() {
		unset($this->api);
	}

	function testGetSupporterByKey() {
		$results = @$this->api->get('supporter', array('key'=>7561369));
		$this->assertNotNull($results);
	}

	function testGetSupporterFunc() {
		$results = @$this->api->getSupporter(array('key'=>7561369));
		$this->assertNotNull($results);

		$results = @$this->api->getSupporter(7561369);
		$this->assertNotNull($results);
	}

	function testGetSupporterByEmail() {
		$results = @$this->api->get('supporter', array('where'=>'Email="seth@indyvoter.org"'));
		$this->assertNotNull($results);
	}

	function testGetMultiple() {
		$results = @$this->api->get('supporter', array('key' => array(7561369, 7818225)));
		$this->assertNotNull($results);
	}

	function testGroupExists() {
		$key = @$this->api->getRecordKey('groups', array('Group_Name'=>"testeroo"));
		$this->assertTrue($key);
	}

	function testGroupNoExists() {
		$key = @$this->api->getRecordKey('groups', array('Group_Name' => "doesnotexists123"));
		$this->assertFalse($results);
	}

	function testAddSupporter() {
		$email = $this->test_string.$this->test_email_domain;
		$key = @$this->api->addSupporter($email);

		$supporter = @$this->api->getSupporter($key);
		$this->assertEqual($supporter['Email'],$email);
	}

	function testAddLinkSupporter() {
		$email = $this->test_string.$this->test_email_domain;
		
		$data = array("Email" => $email,
					  "link" => array('groups' => DIA_TEST_GROUP_KEY)); 
		$supporter = @$this->api->addSupporter($email,$data);

		$supporter_groups = @$this->api->get('supporter_groups', array('where' => '(supporter_KEY='.$supporter.')AND(groups_KEY='.DIA_TEST_GROUP_KEY.')'));
		$this->assertEqual($supporter_groups['supporter_KEY'], $supporter);
		$this->assertTrue(@$this->api->isMember($supporter, DIA_TEST_GROUP_KEY));
	}

	function testAddSupportersToGroup() {
		for($i = 0; $i < 3; ++$i) {
			$string = $this->test_string."_$i";
			$supporter = array("First_Name" => $string,
								  "Email" => $string.$this->test_email_domain,
								  "link" => array('groups' => DIA_TEST_GROUP_KEY));
			$supporters[] = $supporter;
			$result = @$this->api->addSupporter($supporter);
		}
		foreach($supporters as $supporter) {
			$this->assertTrue(@$this->api->isMemberByEmail($supporter['Email'], DIA_TEST_GROUP_KEY));
		}
	}

	function testAddSupportersToGroups() {
		for($i = 0; $i < 3; ++$i) {
			$string = $this->test_string."_$i";
			$supporter = array("First_Name" => $string,
							   "Email" => $string.$this->test_email_domain,
							   "link" => array('groups' => array(DIA_TEST_GROUP_KEY,
																 DIA_TEST_GROUP2_KEY)));
			$supporters[] = $supporter;
			$result = @$this->api->addSupporter($supporter);
		}
		foreach($supporters as $supporter) {
			$this->assertTrue(@$this->api->isMemberByEmail($supporter['Email'], DIA_TEST_GROUP_KEY));
			$this->assertTrue(@$this->api->isMemberByEmail($supporter['Email'], DIA_TEST_GROUP2_KEY));
		}
	}


	function testGetAllGroups() {
		$result = @$this->api->getGroups();
		$this->assertIsA($result, 'array');
	}

	function testGroupNames() {
		$result = @$this->api->getGroupNames();
		$this->assertIsA($result, 'array');
	}

	function testGroupNamesAssoc() {
		$result = @$this->api->getGroupNamesAssoc();
		$this->assertIsA($result, 'array');
	}


/*
	function testAppendFooter() {
		$results = $this->api->get('groups', array('key' => '25669'));
		$xmlparser =& new XML_Unserializer();
		$status = $xmlparser->unserialize($results);
		$this->dump($xmlparser->getUnserializedData());
	}
		
	function testGetLimit() {
//		$big_bloc_xml = $this->api->get('supporter_groups', array('where' => 'groups_KEY=22291'));
		$big_bloc_xml = $this->api->get('supporter_groups', array('where' => 'groups_KEY=24222'));
//		$big_bloc_xml = $this->api->get('supporter_groups', array('where' => 'groups_KEY=24917'));
		$xmlparser =& new XML_Unserializer();
		$status = $xmlparser->unserialize($big_bloc_xml);
		$big_bloc = $xmlparser->getUnserializedData();

		foreach ($big_bloc['supporter_groups']['item'] as $item) {
			if($key = intval($item['supporter_KEY'])) {
				$supporters[] = $key;
			}
		}
print "count: ".count($supporters);

//		for($subset = 100; $subset += 100; $subset < count($supporters)) {
set_time_limit(200);
			$little_bloc_xml = $this->api->get('supporter', array('key' => $supporters));
			$parser =& new XML_Unserializer();
			$status = $parser->unserialize($little_bloc_xml);
			$this->assertFalse(PEAR::isError($status));
//$this->dump($parser->getUnserializedData());
//			if(PEAR::isError($status)) break;
//		}
	}
*/

/*
works.  trust me.
	function testProcessSupporter() {
		$now = date('mdHi');
		$data = array("First_Name" => "TestAMP".$now,
					  "Last_Name" => "TestAMPLast_Name",
					  "Email" => "test".$now."@radicaldesigns.org",
					  "simple" => true);
//		$this->api->addSupporter($data);
		$supporter_id = $this->api->process('supporter', $data);
		$this->assertNotNull($supporter_id);
		$result = $this->api->get('supporter', array('key' => $supporter_id));
		$xmlparser =& new XML_Unserializer();
		$status = $xmlparser->unserialize($result);
		$unserialized = $xmlparser->getUnserializedData();
		$properties = $unserialized['supporter']['item'];
		$this->assertEqual($properties['First_Name'], $data['First_Name']);
		return $supporter_id;
	}

	function testProcessGroup() {
		$now = date('mdHi');
//organization_KEY is required
		$data = array("Group_Name" => "TestAMP".$now,
					  "parent_KEY" => 24642,
					  "external_ID" => "testampgroupid-".$now,
					  "Display_To_User" => 0,
					  "organization_KEY" => 315,
					  "Listserve_Type" => 'Restrict Posts to Allowed Users',
					  "simple" => true);
		$group_id = $this->api->process('groups', $data);
//		print "new group id: " . $group_id . "<br/>";
		$result = $this->api->get('groups', array('key' => $group_id));
		$xmlparser =& new XML_Unserializer();
		$status = $xmlparser->unserialize($result);
		$unserialized = $xmlparser->getUnserializedData();
		$properties = $unserialized['groups']['item'];
		$this->assertEqual($properties['Group_Name'], $data['Group_Name']);
		return $group_id;
	}

	function testLinkSupporter() {
		$supporter_id = $this->testProcessSupporter();
		$group_id = $this->testProcessGroup();
		$result = $this->api->process('supporter_groups', array('supporter_KEY' => $supporter_id,
													  'groups_KEY' => $group_id,
													  'organization_KEY' => 315,
													  'Properties' => 'Allowed to send Email,Moderator',
													  'simple' => true));
		$this->dump($result);
	}
*/
}

dia_test_run(__FILE__);
?>
