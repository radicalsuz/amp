<?php

require_once ('AMP/System/Data/Set.inc.php' );


class NavigationSet extends AMPSystem_Data_Set {

    var $datatable = 'navtbl';

    function NavigationSet( &$dbcon ) {
        $this->init( $dbcon );
    }

}
?>
