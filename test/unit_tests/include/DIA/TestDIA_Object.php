<?php
require_once( 'unit_tests/config.php' );

require_once( 'DIA/API.php' );
require_once( 'DIA/Object.php' );

class TestDIA_Object extends UnitTestCase {

	var $api;
	var $orgKey = 'pLxGeID1N0t4mAsoHTRA3CqPsfU/EsU8EuvTaUFa/wwDzkADR5zl1g==';

    function TestDIA_Object () {
        $this->UnitTestCase('DIA Object Test');
    }

	function setUP() {
		$this->api =& DIA_API::create('HTTP_Request');
	}

	function tearDown() {
		unset($this->api);
	}

/*
	function testFactoryCreate() {
		$supporter =& DIA_Object::create('supporter');
//		$supporter->interface('HTTP_Request');
		$this->assertNotNull($supporter);
//		$supporter->read();
		$this->dump($supporter);
	}

	function testGetSeth() {
		$supporter =& DIA_Object::create('supporter',3088498);
		$this->assertNotNull($supporter);
		$supporter->read();
		$this->dump($supporter);
	}

	function testGetMultiple() {
		$results = $this->api->get('supporter', array(3088498, 4447457));
		$this->assertNotNull($results);
		$this->dump($results);
	}

	function testCreateGroup() {
		$now = time();
		$data = array(
			"Group_Name" => "TestGroup".$now,
			"parent_KEY" => 24642,
			"external_ID" => "voterguideid-".$now,
			"Display_To_User" => 0);
		$group =& new DIA_Group($data);
		$result = $group->save();
	}

	function testCreateSupporter() {
	}

	function testLinkSupporter() {
		$group =& new DIA_Group(array("key"=> 24642));
		$supporter =& new DIA_Supporter(array("key" => 3088498));
		$supporter->linkToGroup($group);
	}

	function testCreateLinkedSupporter() {
	}
*/

}

UnitRunner_instantiate( __FILE__ );
?>
