<?php
if(!defined('DIA_TEST_DIR')) define('DIA_TEST_DIR', dirname(__FILE__).DIRECTORY_SEPARATOR);
require_once( DIA_TEST_DIR.'config.php' );

require_once( DIA_DIR.'API.php' );

class TestDIA_API_Events extends UnitTestCase {

	var $api;

	var $test_string;
	var $test_email_domain;

	var $test_event = array (
  'supporter_KEY' => '9396204',
  'Event_Name' => 'this is a test',
  'Description' => 'this is the description',
  'Address' => 'this is the address',
  'City' => 'san francisco',
  'State' => 'CA',
  'Zip' => '94110',
  'Directions' => '',
  'Header' => '',
  'Footer' => '',
  'PRIVATE_Zip_Plus_4' => '0000',
  'Start' => '2006-03-03 13:30:00.0',
  'End' => '',
  'Recurrence_Frequency' => 'None',
  'Recurrence_Interval' => '',
  'Contact_Email' => '',
  'Guests_allowed' => '0',
  'Maximum_Attendees' => '20',
  'Map_URL' => '',
  'Status' => 'Active',
  'This_Event_Costs_Money' => '1',
  'Ticket_Price' => '25.00',
  'PRIVATE_trigger' => '',
  'Class' => 'PUBLIC',
  'Request' => '0,First_Name,Last_Name,Email',
  'Required' => '0,Email',
  'Request_Additional_Attendees' => '0',
  'One_Column_Layout' => '0',
);
    function TestDIA_API_Events () {
        $this->UnitTestCase('DIA Events Test');
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

	function testGetEvent() {
		@$event = $this->api->getEvent(10915);
		$this->assertEqual($event['Event_Name'], 'this is a test');
	}

	function testAddEvent() {
		$test_event = $this->test_event;
		@$test_event['supporter_KEY'] = $this->api->getSupporterKeyByEmail('seth@radicaldesigns.org');
		@$event_key = $this->api->addEvent($test_event);
		$this->assertTrue($event_key);

		@$dia_event = $this->api->getEvent($event_key);
		$this->assertEqual($dia_event['Event_Name'], $test_event['Event_Name']);
	}

}

dia_test_run(__FILE__);
?>
