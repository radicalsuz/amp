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

class AMPSystemLookup_DistributedEvent extends AMPSystem_Lookup {
	var $datatable = "eventtype";
	var $result_field = "distributed_event_key";
	var $criteria = 'distributed_event_key != FALSE AND !isNull(distributed_event_key)';

	function AMPSystemLookup_CalendarDiaKey() {
		$this->init();
	}
}
