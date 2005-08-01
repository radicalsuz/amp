<?php

require_once ('AMP/System/Data/Set.inc.php' );


class NavigationLocationSet extends AMPSystem_Data_Set {

    var $datatable = 'nav';
    var $sort = array('position');

    function NavigationLocationSet( &$dbcon ) {
        $this->init( $dbcon );
    }

}
?>
