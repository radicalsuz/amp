<?php

require_once( 'AMP/System/Data/Item.inc.php' );

class ScheduleItem extends AMPSystem_Data_Item {

    var $name_field = "title";
    var $datatable = "timeslots";
    
    function ScheduleItem ( &$dbcon, $id = null ) {
        $this->init( $dbcon, $id );

    }
}
?>
