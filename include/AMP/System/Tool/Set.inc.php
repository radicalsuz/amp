<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'AMP/System/Tool.inc.php');

class ToolSet extends AMPSystem_Data_Set {
    var $datatable = 'modules';
    var $sort = array( "name");

    function ToolSet( &$dbcon ){
        $this->init( $dbcon );
    }
}

?>
