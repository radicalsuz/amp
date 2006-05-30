<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Content/Nav/Layout/Layout.php');

class AMP_Content_Nav_Layout_List extends AMPSystem_List{
    var $name = "Nav_Layout";
    var $col_headers = array( 
        'Name' => 'name',
        'ID'    => 'id');
    var $editlink = 'nav_layouts.php';
    var $name_field = 'name';
    var $_source_object = 'AMP_Content_Nav_Layout';
    var $_observers_source = array( 'AMP_System_List_Observer');
    var $_actions = array( 'delete');

    function AMP_Content_Nav_Layout_List( &$dbcon ) {
        $this->init( $this->_init_source( $dbcon ) );
    }
}
?>
