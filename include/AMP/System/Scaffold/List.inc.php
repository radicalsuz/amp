<?php

require_once( 'AMP/System/List.inc.php');
require_once( '%4\$s%1\$s/Set.inc.php' );

class %1\$s_List extends AMPSystem_List {
    var $name = "%1\$s";
    var $col_headers = array( 
        '%3\$s' => '%3\$s',
        'ID'    => 'id');
    var $editlink = '%5\$s';

    function %1\$s_List( &$dbcon ) {
        $source = & new %1\$sSet( $dbcon );
        $this->init( $source );
    }
}
?>
