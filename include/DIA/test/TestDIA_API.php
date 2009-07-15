<?php
if(!defined('DIR_SEP')) define('DIR_SEP', DIRECTORY_SEPARATOR);
if(!defined('DIA_TEST_DIR')) define('DIA_TEST_DIR', dirname(__FILE__).DIR_SEP);
require_once( DIA_TEST_DIR.'config.php' );

require_once( DIA_DIR.'API.php' );

class TestDIA_API extends UnitTestCase {

	var $api;

	var $test_string;
	var $test_email_domain;

    function TestDIA_API () {
        $this->UnitTestCase('DIA API Test');
    }

	function setUp() {
		$this->api =& DIA_API::create();

		$now = date('mdHi');
		$this->test_string = DIA_TEST_DATA_PREFIX.$now;
		$this->test_email_domain = '@radicaldesigns.org';
	}

	function tearDown() {
		unset($this->api);
	}

	function testSupporter() {
		$email = $this->test_string.$this->test_email_domain;
		$key = @$this->api->addSupporter($email, array('First_Name' => $this->test_string));

		//preferred
		$supporter = @$this->api->getSupporter($key);
		$this->assertEqual($supporter['Email'],$email);

		//getSupporter is a just a shortcut to get('supporter', $criteria)
		$supporter = @$this->api->get('supporter', array('key'=>$key));
		$this->assertEqual($supporter['Email'],$email);

		//can also specify criteria
		$supporter = @$this->api->getSupporter(array('key'=>$key));
		$this->assertEqual($supporter['Email'],$email);

		$supporter = @$this->api->get('supporter', array('where'=>'Email="'.$email.'"'));
		$this->assertEqual($supporter['Email'],$email);

		//and use similar interface for multiples
		$email2 = $this->test_string.'_2'.$this->test_email_domain;
		$key2 = @$this->api->addSupporter($email2);

		$results = @$this->api->get('supporter', array('key' => array($key, $key2)));

	}

	function testGroup() {
		$group_name = $this->test_string.'Group';
		$key = @$this->api->addGroup($group_name);

		$group = @$this->api->getGroup($key);
		$this->assertEqual($group['Group_Name'],$group_name);

		$result = @$this->api->getRecordKey('groups', array('Group_Name' => "doesnotexists123"));
		$this->assertFalse($result);
	}

	function testLinkSupporter() {
		$email = $this->test_string.$this->test_email_domain;
		$group_name = $this->test_string.'Group';
		$group_key = @$this->api->addGroup($group_name);
		
		$data = array("Email" => $email,
					  "link" => array('groups' => $group_key)); 
		$supporter = @$this->api->addSupporter($email,$data);

		$supporter_groups = @$this->api->get('supporter_groups', array('where' => '(supporter_KEY='.$supporter.')AND(groups_KEY='.$group_key.')'));
		$this->assertEqual($supporter_groups['supporter_KEY'], $supporter);
		$this->assertTrue(@$this->api->isMember($supporter, $group_key));
		$this->assertTrue(@$this->api->isMemberByEmail($email, $group_key));

		$email2 = $this->test_string.'_2'.$this->test_email_domain;
		$group2_name = $this->test_string.'_2'.'Group';
		$group2_key = @$this->api->addGroup($group_name);

		$supporter2 = @$this->api->addSupporter(array(
			'Email' => $email2,
			'link'  => array('groups' => array($group_key, $group2_key))));
		$this->assertTrue(@$this->api->isMember($supporter2, $group_key));
		$this->assertTrue(@$this->api->isMember($supporter2, $group2_key));
	}

	//TODO: write better tests here
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

	function testSupporterGroups() {
		$email = $this->test_string.$this->test_email_domain;
		$group_name = $this->test_string.'Group';

		$supporter_key = @$this->api->addSupporter($email);
		$group_key = @$this->api->addGroup($group_name);

		$properties = 'Allowed to send Email,Moderator';
		$result = @$this->api->process('supporter_groups', array('supporter_KEY' => $supporter_key,
													  'groups_KEY' => $group_key,
													  'Properties' => $properties));
		$this->assertTrue(@$this->api->isMember($supporter_key, $group_key));
		$supporter_groups = @$this->api->get('supporter_groups', $result);
		$this->assertEqual($supporter_groups['Properties'], $properties);
	}

	function testShortName() {
		$this->assertEqual('radicaldesigns', @$this->api->getShortName());
	}

	function testBaseURL() {
		$this->assertEqual('http://dia.devel.radicaldesigns.org/', @$this->api->getBaseURL());
	}
/*
	XXX: the big lesson here is that for large data transfers we may need to use
		 set_time_limit so as to not time out

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
}

dia_test_run(__FILE__);
?>
