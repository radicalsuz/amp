<?php

require_once ('AMP/System/Data/Set.inc.php');

class Payment_Set extends AMPSystem_Data_Set {

    var $datatable = "payment";
    
    function Payment_Set ( &$dbcon ) {
        $this->init( $dbcon );
    }
}
?>
