<?php

require_once( 'AMP/System/List.inc.php');
require_once( 'Modules/Calendar/Type/Set.inc.php' );

class Calendar_Type_List extends AMPSystem_List {
    var $name = "Calendar Type";
    var $col_headers = array( 
        'name' => 'name',
        'ID'    => 'id');
    var $editlink = 'calendar_type.php';

    function Calendar_Type_List( &$dbcon ) {
        $source = & new Calendar_Type_Set( $dbcon );
        $this->init( $source );
    }
}
?>
