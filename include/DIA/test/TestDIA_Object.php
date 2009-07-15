<?php
if(!defined('DIA_TEST_DIR')) define('DIA_TEST_DIR', dirname(__FILE__).DIR_SEP);
require_once( DIA_TEST_DIR.'config.php' );

require_once( DIA_DIR.'API.php' );
require_once( DIA_DIR.'Object.php' );

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

dia_test_run( __FILE__ );
?>
