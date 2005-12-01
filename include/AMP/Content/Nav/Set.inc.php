<?php

require_once ('AMP/System/Data/Set.inc.php' );
require_once( 'AMP/Content/Nav.inc.php');


class NavigationSet extends AMPSystem_Data_Set {

    var $datatable = 'navtbl';
    var $_debug_constant = "AMP_DISPLAYMODE_DEBUG_NAVS";
    var $sort = array( "name");

    function NavigationSet( &$dbcon ) {
        $this->init( $dbcon );
    }

}
?>
