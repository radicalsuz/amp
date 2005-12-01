<?php

require_once ('Modules/Podcast/Item/Set.inc.php' );
require_once ('AMP/System/List.inc.php');

class PodcastItem_List extends AMPSystem_List {

	var $col_headers = array( "ID" => "id", "File" => "podcast", "Item" => "title" );
	var $editlink = "podcast_item.php";
	var $name = "Podcast Items";

	function PodcastItem_List ( &$dbcon ) {
		$source = & new PodcastItemSet( $dbcon );
		$this->init ($source );
	}

}
?>
