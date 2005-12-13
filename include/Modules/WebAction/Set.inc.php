<?php

require_once( 'AMP/System/Data/Set.inc.php');

class WebActionSet extends AMPSystem_Data_Set {

    var $datatable = 'webactions';

    function WebActionSet( &$dbcon ){
        $this->init( $dbcon );
    }

    function addCriteriaLive() {
        $this->addCriteria( 'status=1' );
        $this->addCriteria( '( ( !enddate ) OR enddate>CURRENT_DATE)' );
    }

}
?>
