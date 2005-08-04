<?php

require_once ('AMP/Geo/FlashMap/Set.inc.php' );
require_once ('AMP/System/List.inc.php');

class FlashMap_List extends AMPSystem_List {

	var $col_headers = array( "ID" => "id", "Name" => "name" );
	var $editlink = "maps.php";
	var $name = "FlashMap";

	function FlashMap_List ( &$dbcon ) {
		$source = & new FlashMapSet( $dbcon );
		$this->init ($source );
	}

}
?>
