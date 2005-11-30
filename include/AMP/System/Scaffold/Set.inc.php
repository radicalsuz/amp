<?php

require_once( 'AMP/System/Data/Set.inc.php');
require_once( '%4\$s%1\$s/%1\$s.php');

class %1\$sSet extends AMPSystem_Data_Set {
    var $datatable = '%2\$s';
    var $sort = array( "%3\$s");

    function %1\$sSet ( &$dbcon ){
        $this->init( $dbcon );
    }
}

?>
