<?php

require_once ('Modules/Podcast/Set.inc.php' );
require_once ('AMP/System/List.inc.php');

class Podcast_List extends AMPSystem_List {

	var $col_headers = array( "ID" => "id", "Podcast" => "title" );
	var $editlink = "podcast.php";
	var $name = "Podcasts";

	function Podcast_List ( &$dbcon ) {
		$source = & new PodcastSet( $dbcon );
		$this->init ($source );
	}

}
?>
