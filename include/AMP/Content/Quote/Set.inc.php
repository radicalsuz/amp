<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'AMP/Content/Quote/Quote.php');

class QuoteSet extends AMPSystem_Data_Set {
    var $datatable = 'quotes';
    var $sort = array( "quote");

    function QuoteSet ( &$dbcon ){
        $this->init( $dbcon );
    }
}

?>
