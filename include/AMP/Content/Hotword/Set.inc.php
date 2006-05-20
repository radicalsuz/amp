<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( 'AMP/Content/Hotword/Hotword.php');

class HotwordSet extends AMPSystem_Data_Set {
    var $datatable = 'hotwords';
    var $sort = array( "word");

    function HotwordSet ( &$dbcon ){
        $this->init( $dbcon );
    }
}

?>
