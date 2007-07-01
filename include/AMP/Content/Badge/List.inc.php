<?php

require_once( 'AMP/Display/System/List.php');
require_once( 'AMP/Content/Badge/Badge.php');

class AMP_Content_Badge_List extends AMP_Display_System_List {
    var $columns = array( 'select', 'edit', 'name', 'status', 'id');
    var $name_field = 'name';
    var $_source_object = 'AMP_Content_Badge';

    function AMP_Content_Badge_List( &$dbcon, $criteria = array( ) ) {
        $this->__construct( false, $criteria );
    }

}
?>
