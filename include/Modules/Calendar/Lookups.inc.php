<?php
require_once('AMP/System/Lookups.inc.php');

class AMPSystemLookup_CalendarUid extends AMPSystem_Lookup {
	var $datatable = "calendar";
	var $result_field = "uid";
}

class AMPSystemLookup_CalendarDiaKey extends AMPSystem_Lookup {
	var $datatable = "calendar";
	var $id_field = "dia_key";
	var $result_field = "id";
	var $criteria = 'dia_key != FALSE AND !isNull(dia_key)';

	function AMPSystemLookup_CalendarDiaKey() {
		$this->init();
	}
}
class AMPSystemLookup_CalendarEventOwner extends AMPSystem_Lookup {
	var $datatable = "calendar";
	var $result_field = "uid";
	var $criteria = 'uid != "" AND !isNull(uid)';

    function AMPSystemLookup_CalendarEventOwner() {
		$this->init();
	}
}

class AMPSystemLookup_EventsByOwner extends AMPSystem_Lookup {
	var $datatable = "calendar";
	var $result_field = "event";
	var $criteria = 'uid != "" AND !isNull(uid)';

    function AMPSystemLookup_EventsByOwner( $owner_id = null ) {
        if ( isset( $owner_id )) $this->_add_criteria_owner( $owner_id );
		$this->init();
    }

    function _add_criteria_owner( $owner_id ) {
        $this->criteria = 'uid=' . $owner_id;
    }

}

class AMPSystemLookup_DistributedEvent extends AMPSystem_Lookup {
	var $datatable = "eventtype";
	var $result_field = "distributed_event_key";
	var $criteria = 'distributed_event_key != FALSE AND !isNull(distributed_event_key)';

	function AMPSystemLookup_CalendarDiaKey() {
		$this->init();
	}
}

class AMPSystemLookup_FormsWithEvents extends AMPSystem_Lookup {
    var $datatable = 'userdata';
    var $result_field = 'count( id ) as qty';
    var $id_field = 'modin';
    var $criteria = '1 GROUP BY modin';

    function AMPSystemLookup_FormsWithEvents( ) {
        $owned_events = AMP_lookup( 'calendarEventOwner');
        if ( !$owned_events ) return;
        $this->criteria = 'id in( '.join( ',', $owned_events ).') GROUP BY modin ';

        $this->init( );

        $allowed_forms = array_keys( $this->dataset );
        $this->dataset = array_combine_key( $allowed_forms, AMP_lookup( 'forms'));
    }
}
?>
