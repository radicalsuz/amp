<?php

require_once ('AMP/System/Data/Set.inc.php' );

class PodcastSet extends AMPSystem_Data_Set {

	var $datatable = "podcast";

	function PodcastSet ( &$dbcon ) {
		$this->init ($dbcon );
	}
}
?>
