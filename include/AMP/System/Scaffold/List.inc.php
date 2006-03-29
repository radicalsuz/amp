<?php

require_once( 'AMP/System/List.inc.php');
require_once( '%4\$s%1\$s/%1\$s.php');

class %1\$s_List extends AMPSystem_List {
    var $name = "%1\$s";
    var $col_headers = array( 
        '%3\$s' => '%3\$s',
        'ID'    => 'id');
    var $editlink = '%5\$s';
    var $name_field = '%3\$s';
    var $_source_object = '%1\$s';

    function %1\$s_List( &$dbcon ) {
        $this->init( $this->_init_source( ) );
    }
}
?>
