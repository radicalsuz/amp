<?php

require_once( 'AMP/System/List.inc.php');
require_once( 'AMP/System/Tool/Set.inc.php' );
require_once( 'AMP/System/Tool/Module.php' );

class Tool_List extends AMPSystem_List {
    var $name = "Tool";
    var $col_headers = array( 
        'name' => 'name',
        'ID'    => 'id');
    var $editlink = 'module.php';
    var $_url_add = 'module.php?action=add';

    function Tool_List( &$dbcon, $criteria = array( ) ) {
        $source = &new ToolSet( $dbcon );
        $this->init( $source );
    }
}
?>
