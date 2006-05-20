<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'AMP/Content/Redirect/Redirect.php');

class AMP_Content_RedirectSet extends AMPSystem_Data_Set {
    var $datatable = 'redirect';
    var $sort = array( "old");

    function AMP_Content_RedirectSet ( &$dbcon ){
        $this->init( $dbcon );
    }
}

?>
