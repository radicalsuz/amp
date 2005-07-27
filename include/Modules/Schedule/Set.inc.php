<?php

require_once ('AMP/System/Data/Set.inc.php' );

class ScheduleSet extends AMPSystem_Data_Set {

	var $datatable = "schedules";

	function ScheduleSet ( &$dbcon ) {
		$this->init ($dbcon );
	}
}
?>
