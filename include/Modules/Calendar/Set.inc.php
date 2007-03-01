<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'Modules/Calendar/Event.php');

class CalendarSet extends AMPSystem_Data_Set {
    var $datatable = 'calendar';
    var $sort = array( "event");

    function CalendarSet ( &$dbcon ){
        $this->init( $dbcon );
    }
}

?>
