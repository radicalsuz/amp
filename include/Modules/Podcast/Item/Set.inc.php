<?php

require_once ('AMP/System/Data/Set.inc.php' );

class PodcastItemSet extends AMPSystem_Data_Set {

	var $datatable = "podcast_item";

	function PodcastItemSet ( &$dbcon ) {
		$this->init ($dbcon );
	}
}
?>
