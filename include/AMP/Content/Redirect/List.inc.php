<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Content/Redirect/Redirect.php');

class AMP_Content_Redirect_List extends AMP_System_List_Form {
    var $name = "AMP_Content_Redirect";
    var $col_headers = array( 
        'Alias' => 'alias',
        'Target' => 'target',
        'Status' => 'publish',
        'ID'    => 'id');
    var $editlink = 'redirect.php';
    var $name_field = 'old';
    var $_source_object = 'AMP_Content_Redirect';
    var $_observers_source = array( 'AMP_System_List_Observer');

    function AMP_Content_Redirect_List( &$dbcon ) {
        $this->init( $this->_init_source( $dbcon ) );
    }
}
?>
