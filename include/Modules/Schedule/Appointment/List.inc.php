<?php

require_once ('AMP/System/List.inc.php' );
require_once( 'Modules/Schedule/Lookups.inc.php' );
require_once( 'AMP/System/Lookups.inc.php' );
require_once( 'Modules/Schedule/Appointment/Set.inc.php' );

class Appointment_List extends AMPSystem_List {
    var $name = "Appointments";
    var $col_headers = array( "Schedule Item" => "action_id", "attendee"=> "userdata_id" );
    var $editlink = "schedule_appointment.php";
    var $schedule_id;
   # var $extra_columns = array( 'Schedule'=>'schedule_item.php?schedule_id=');
   # var $extra_column_maps = array('Schedule'=>'schedule_id' );

    function Appointment_List (&$dbcon, $id = null ) {
        $source = & new AppointmentSet( $dbcon );
        $this->init( $source );
        $this->addLookup( 'action_id' , AMPSystem_Lookup::instance( 'scheduleItemDesc' ) ); 
        $this->addLookup( 'userdata_id' , AMPSystem_Lookup::instance( 'userDataNames' ) ); 
    }

    function getPersonalSchedule( $userdata_id ) {
        $this->source->addCriteria( "userdata_id" . "=" . $userdata_id );
        $this->source->readData();
    }

}

?>    
