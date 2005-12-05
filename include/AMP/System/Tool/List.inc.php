<?php

require_once( 'AMP/System/List.inc.php');
require_once( 'AMP/System/Tool/Set.inc.php' );

class Tool_List extends AMPSystem_List {
    var $name = "Tool";
    var $col_headers = array( 
        'name' => 'name',
        'ID'    => 'id');
    var $editlink = 'module.php';

    function Tool_List( &$dbcon ) {
        $source = & new ToolSet( $dbcon );
        $this->init( $source );
    }
}
?>
