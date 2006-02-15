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
