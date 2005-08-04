<?php

require_once ('AMP/System/Data/Set.inc.php' );

class FlashMapSet extends AMPSystem_Data_Set {

	var $datatable = "maps";

	function FlashMapSet ( &$dbcon ) {
		$this->init ($dbcon );
	}
}
?>
