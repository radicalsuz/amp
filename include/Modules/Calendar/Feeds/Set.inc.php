<?php

require_once ('AMP/System/Data/Set.inc.php' );

class CalendarFeedsSet extends AMPSystem_Data_Set {

	var $datatable = "px_feeds";

	function CalendarFeedsSet ( &$dbcon ) {
		$this->init ($dbcon );
		$this->addCriteria("service = 'Calendar'");
	}
}
?>
