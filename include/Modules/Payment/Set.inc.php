<?php

require_once ('AMP/System/Data/Set.inc.php');

class AMPSystem_Payment_Set extends AMPSystem_Data_Set {

    var $datatable = "payment";
    
    function AMPSystem_Payment_Set ( &$dbcon ) {
        $this->init( $dbcon );
    }
}
?>
