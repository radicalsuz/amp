<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Content/Template.inc.php');

class AMP_Content_Template_List extends AMPSystem_List {
    var $name = "Template";
    var $col_headers = array( 
        'Name' => 'name',
        'ID'    => 'id');
    var $editlink = 'template.php';
    var $name_field = 'name';
    var $_source_object = 'AMPContent_Template';
    var $_observers_source = array( 'AMP_System_List_Observer');

    function AMP_Content_Template_List( &$dbcon ) {
        $this->init( $this->_init_source( $dbcon ) );
    }
}
?>
