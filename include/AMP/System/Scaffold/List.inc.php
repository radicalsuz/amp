<?php

require_once( 'AMP/Display/System/List.php');
require_once( '%4\$s%1\$s/%1\$s.php');

class %1\$s_List extends AMP_Display_System_List {

    var $_source_object = '%1\$s';
    var $columns = array( 'select', 'controls', 'name', 'id' ) ;
    var $column_headers = array( );
    var $_actions = array( 'publish', 'unpublish', 'delete' );

    function %1\$s_List( &$source, $criteria = array( ) ) {
        $this->__construct( $source, $criteria );
    }

}

?>
