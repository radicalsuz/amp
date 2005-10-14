<?php
require_once( 'unit_tests/config.php' );

if(!defined('AMP_CALENDAR_LOCAL_ZIP')) {
    define('AMP_CALENDAR_LOCAL_ZIP', 94110);
}
if (!defined('AMP_CALENDAR_LOCAL_DISTANCE')) {
    define('AMP_CALENDAR_LOCAL_DISTANCE', 7);
}

require_once( 'AMP/Geo/Geo.php' );
require_once( 'AMP/BaseDB.php' );

class TestGeo extends UnitTestCase {

	var $dbcon;

    function TestGeo () {
        $this->UnitTestCase('DIA API Test');
    }

	function setUP() {
		$this->dbcon =& AMP_Registry::getDbcon();
	}

	function tearDown() {
		unset($this->dbcon);
	}

	function testNewFromZip() {
		$geo =& new Geo($this->dbcon, null, null, null, AMP_CALENDAR_LOCAL_ZIP);
		$this->assertNotNull($geo->lat);
		$this->assertNotNull($geo->long);

		$geo->zip_lookup();
		$this->assertNotNull($geo->lat);
		$this->assertNotNull($geo->long);
	}

	function testNewFromSanFrancisco() {
		$geo =& new Geo($this->dbcon, null, 'San Francisco', 'CA');
		$this->assertEqual($geo->lat, 37.784827);
		$this->assertEqual($geo->long, -122.727802);
	}

	function testFullText() {
		$geo =& new Geo($this->dbcon, null, 'St. Paul', 'MN', null, 'city_fulltext');
		$this->assertNotNull($geo->lat);
		$this->assertNotNull($geo->long);
	}

	function testSoundex() {
		$geo =& new Geo($this->dbcon, null, 'minapolis', 'MN', null, 'city_soundex');
		$this->assertNotNull($geo->lat);
		$this->assertNotNull($geo->long);
	}

	function testZipFromCityState() {
		$geo =& new Geo($this->dbcon, null, 'San Francisco', 'CA');
		$zips = $geo->zip_radius(0);
		$this->assertNotNull(array_shift($zips));
	}

	function testZipRadius() {
		$geo =& new Geo($this->dbcon, null, null, null, AMP_CALENDAR_LOCAL_ZIP);
		$zips = $geo->zip_radius(15);
		$this->assertIsA($zips, 'array');
	}
		
}

UnitRunner_instantiate( __FILE__ );
?>
