<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'Modules/Calendar/Type/Type.php');

class Calendar_Type_Set extends AMPSystem_Data_Set {
    var $datatable = 'eventtype';
    var $sort = array( "name");

    function Calendar_Type_Set ( &$dbcon ){
        $this->init( $dbcon );
    }
}

?>
