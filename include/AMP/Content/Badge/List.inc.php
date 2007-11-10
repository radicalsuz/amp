<?php

require_once( 'AMP/Display/System/List.php');
require_once( 'AMP/Content/Badge/Badge.php');

class AMP_Content_Badge_List extends AMP_Display_System_List {
    var $columns = array( 'select', 'controls', 'name', 'status', 'id');
    var $name_field = 'name';
    var $_source_object = 'AMP_Content_Badge';
    var $_actions = array( 'publish', 'unpublish', 'delete', 'navify');

    function AMP_Content_Badge_List( &$dbcon, $criteria = array( ) ) {
        $this->__construct( false, $criteria );
    }

}
?>
