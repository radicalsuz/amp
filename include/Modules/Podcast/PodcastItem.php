<?php

require_once ( 'AMP/System/Data/Item.inc.php' );

class AMPSystem_PodcastItem extends AMPSystem_Data_Item {

	var $datatable = "podcast_item";
	var $name_field = 'podcast';

	function AMPSystem_PodcastItem ( &$dbcon, $id=null ) {
		$this->init( $dbcon, $id );
	}

}

?>
