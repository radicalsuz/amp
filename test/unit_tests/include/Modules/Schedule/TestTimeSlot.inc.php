<?php
require_once( 'unit_tests/config.php' );
require_once( 'Modules/Schedule.inc.php' );

class TestScheduleTimeSlot extends UnitTestCase {
    var $dbcon;
    var $rs;

    function TestScheduleTimeSlot () {
        $this->UnitTestCase('Scheduling Module Test');
    }

	function setUp() {
		$this->dbcon = &new MockADODB_mysql($this);	
	}


    function test_getStatus() {
        $timeslot = & new ScheduleTimeSlot( $this->dbcon );
        $data = array( "status" => "poop");
        $timeslot->setData( $data );
        $this->assertEqual( $timeslot->getStatus(), "poop" );
    }

}

UnitRunner_instantiate( __FILE__ );
?>
