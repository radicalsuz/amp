<?php
require_once( 'AMP/System/Lookups.inc.php' );

class AMPSystemLookup_ScheduleNames extends AMPSystem_Lookup {

    var $datatable = "schedules";
    var $result_field = "name";

    function AMPSystemLookup_ScheduleNames() {
        $this->init();
    }
}

class AMPSystemLookup_ItemScheduleNames extends AMPSystem_Lookup {
    var $datatable = "schedules, scheduleitems";
    var $result_field = "schedules.name";
    var $id_field = "scheduleitems.id";
    var $criteria = " schedules.id = scheduleitems.schedule_id ";

    function AMPSystemLookup_ItemScheduleNames() {
        $this->init();
    }
}
class AMPSystemLookup_SchedulesByItem extends AMPSystem_Lookup {
    var $datatable = "scheduleitems";
    var $result_field = "schedule_id";
    var $sortby = "schedule_id, start_time";

    function AMPSystemLookup_SchedulesByItem() {
        $this->init();
    }
}

class ScheduleLookup_ItemSet {
    var $scheduleItems;

    function ScheduleLookup_ItemSet () {
        $this->init();
    }

    function init() {
        $this->scheduleItems = & AMPSystemLookup_SchedulesByItem::instance();
    }

    function &instance( $schedule_id ) {
        static $lookup = false;
        if (!$lookup ) $lookup = new ScheduleLookup_ItemSet(); 
        return $lookup->filter( $schedule_id );
    }

    function filter( $target_schedule_id ) {
        $results = array();
        foreach( $this->scheduleItems as $item_id => $schedule_id ) {
            if (!$schedule_id == $target_schedule_id) continue;
            $results[] = $item_id;
        }
        return $results;
    }
}
?>
