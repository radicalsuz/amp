<?php

require_once ('AMP/System/Data/Set.inc.php' );

class PodcastItemSet extends AMPSystem_Data_Set {

	var $datatable = "podcast_item";
	var $podcast = null;

	function PodcastItemSet ( &$dbcon, $podcast=null ) {
		$this->init ($dbcon, $podcast );
	}

	function init(&$dbcon, $podcast=null) {
		$this->podcast = $podcast;
		PARENT::init($dbcon);
	}

	function _register_criteria_dynamic() {
		if(isset($this->podcast)) {
			$this->addCriteria('podcast='.$this->podcast);
		}
	}
}
?>
