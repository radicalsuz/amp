<?php

require_once ( 'AMP/System/Data/Set.inc.php' );

class AMPSystem_RegionSet extends AMPSystem_Data_Set {

    var $datatable = "region";

    function AMPSystem_RegionSet( &$dbcon ) {
        $this->init ( $dbcon );
    }
}
?>
