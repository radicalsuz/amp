<?php
require_once( 'unit_tests/config.php' );
require_once( 'Modules/Schedule/TimeSlot.inc.php' );

Mock::generate( 'Schedule' );

class TestTimeSlot extends UnitTestCase {
    var $dbcon;
    var $rs;

    function TestTimeSlot () {
        $this->UnitTestCase('Scheduling Module Test');
    }

	function setUp() {
		$this->dbcon = &new MockADODB_mysql($this);	
	}


    function test_getStatus() {
        $timeslot = & new TimeSlot( $this->dbcon );
        $data = array( "status" => "poop");
        $timeslot->setData( $data );
        $this->assertEqual( $timeslot->getStatus(), "poop" );
    }

UnitRunner_instantiate( __FILE__ );
?>
