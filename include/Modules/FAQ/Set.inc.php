<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'Modules/FAQ/FAQ.php');

class FAQSet extends AMPSystem_Data_Set {
    var $datatable = 'faq';
    var $sort = array( "question");

    function FAQSet ( &$dbcon ){
        $this->init( $dbcon );
    }
}

?>
