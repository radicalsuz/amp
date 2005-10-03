<?php
require_once( 'unit_tests/config.php' );

require_once( 'DIA/API.php' );

require_once( 'XML/Unserializer.php' );

class TestDIA_API extends UnitTestCase {

	var $api;
	var $orgKey = 'pLxGeID1N0t4mAsoHTRA3CqPsfU/EsU8EuvTaUFa/wwDzkADR5zl1g==';

    function TestDIA_API () {
        $this->UnitTestCase('DIA API Test');
    }

	function setUP() {
		$this->api =& DIA_API::create('HTTP_Request');
	}

	function tearDown() {
		unset($this->api);
	}

	function testGetSeth() {
		$results = $this->api->get('supporter', array('key'=>3088498));
		$this->assertNotNull($results);
//		$this->dump($results);
	}

	function testGetMultiple() {
		$results = $this->api->get('supporter', array('key' => array(3088498, 4447457)));
		$this->assertNotNull($results);
//		$this->dump($results);
	}

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
}

UnitRunner_instantiate( __FILE__ );
?>
