<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'AMP/Content/Nav/Layout/Layout.php');

class AMP_Content_Nav_Layout_Set extends AMPSystem_Data_Set {
    var $datatable = 'nav_layouts';
    var $sort = array( "name");

    function AMP_Content_Nav_Layout_Set ( &$dbcon ){
        $this->init( $dbcon );
    }
}

?>
