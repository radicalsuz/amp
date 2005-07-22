<?php

require_once ( 'AMP/System/List.inc.php' );
require_once ( 'AMP/System/Region/Set.inc.php' );

class AMPSystem_Region_List extends AMPSystem_List {
    var $name = "Region";
    var $col_headers = array( 'ID' => "id", "Name" => "title" );
    var $editlink = "region.php";

    function AMPSystem_Region_List (&$dbcon) {
        $source = &new AMPSystem_RegionSet ( $dbcon );
        $this->init( $source );
    }
}
?>
