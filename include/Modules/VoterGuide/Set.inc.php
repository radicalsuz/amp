<?php

require_once ('AMP/System/Data/Set.inc.php' );

class VoterGuideSet extends AMPSystem_Data_Set {

	var $datatable = "voterguides";

	function VoterGuideSet ( &$dbcon ) {
		$this->init ($dbcon );
	}
}
?>
