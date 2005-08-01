<?php
require_once( 'AMP/System/Lookups.inc.php' );

class AMPSystemLookup_ScheduleNames extends AMPSystem_Lookup {

    var $datatable = "schedules";
    var $result_field = "name";

    function AMPSystemLookup_ScheduleNames() {
        $this->init();
    }
}
?>
