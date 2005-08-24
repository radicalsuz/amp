<?php

require_once ('AMP/System/Data/Set.inc.php' );


class NavigationSet extends AMPSystem_Data_Set {

    var $datatable = 'navtbl';
    var $_debug_constant = "AMP_DISPLAYMODE_DEBUG_NAVS";

    function NavigationSet( &$dbcon ) {
        $this->init( $dbcon );
    }

}
?>
