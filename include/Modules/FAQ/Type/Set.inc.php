<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'Modules/FAQ/Type/Type.php');

class FAQ_TypeSet extends AMPSystem_Data_Set {
    var $datatable = 'faqtype';
    var $sort = array( "type");

    function FAQ_TypeSet ( &$dbcon ){
        $this->init( $dbcon );
    }
}

?>
