<?php
require_once( 'AMP/System/Data/Set.inc.php' );

class AMPSystem_IntroText_Set extends AMPSystem_Data_Set {

    var $datatable = "moduletext";
    var $sort = array('name');

    function AMPSystem_IntroText_Set( &$dbcon ) {
        $this->init($dbcon);
    }
}

?>
