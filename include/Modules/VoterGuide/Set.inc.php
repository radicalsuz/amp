<?php

require_once ('AMP/System/Data/Set.inc.php' );

class VoterGuideSet extends AMPSystem_Data_Set {

	var $datatable = "voterguides";
    var $sort = array( "state", "city", "election_date" );

	function VoterGuideSet ( &$dbcon ) {
		$this->init ($dbcon );
	}
}
?>
