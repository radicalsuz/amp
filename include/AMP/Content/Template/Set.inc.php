<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'AMP/Content/Template/Template.php');

class AMP_Content_Template_Set extends AMPSystem_Data_Set {
    var $datatable = 'template';
    var $sort = array( "name");

    function AMP_Content_Template_Set( &$dbcon ){
        $this->init( $dbcon );
    }
}

?>
