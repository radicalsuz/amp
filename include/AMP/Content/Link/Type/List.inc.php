<?php

require_once( 'AMP/System/List.inc.php');
require_once( 'AMP/Content/Link/Type/Type.php');

class Link_Type_List extends AMPSystem_List {
    var $name = "Link_Type";
    var $col_headers = array( 
        'Name' => 'name',
        'Status' => 'publish',
        'ID'    => 'id');
    var $editlink = 'link_type.php';
    var $name_field = 'name';
    var $_source_object = 'Link_Type';

    function Link_Type_List( &$dbcon ) {
        $this->init( $this->_init_source( $dbcon ) );
    }
}
?>
