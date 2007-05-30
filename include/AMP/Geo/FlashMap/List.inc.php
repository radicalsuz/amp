<?php

require_once ('AMP/Geo/FlashMap/Set.inc.php' );
require_once ('AMP/Display/System/List.php');
require_once ( 'AMP/Geo/FlashMap/FlashMap.php');

class FlashMap_List extends AMP_Display_System_List {

//	var $col_headers = array( "ID" => "id", "Name" => "name" );
	var $editlink = "maps.php";
	var $name = "FlashMap";
    var $link_list_preview = 'flashmap.php';
    var $_source_object = 'AMPSystem_FlashMap';
    var $columns = array( 'select', 'edit', 'preview', 'name', 'id' );
    var $_actions = array( 'delete');

	function FlashMap_List ( &$source, $criteria = array( ) ) {
        $this->__construct( $source, $criteria );
		//$source = & new FlashMapSet( $source);
		//$this->init ($source );
	}

}
?>
