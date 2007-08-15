<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Display/System/List.php');
require_once( 'AMP/Content/Redirect/Redirect.php');

//class AMP_Content_Redirect_List extends AMP_System_List_Form {
class AMP_Content_Redirect_List extends AMP_Display_System_List {
    var $name = "AMP_Content_Redirect";
    var $columns = array( 'select', 'controls', 'alias', 'target', 'publish', 'id');
    var $name_field = 'old';
    var $_source_object = 'AMP_Content_Redirect';
    /*
    var $col_headers = array( 
        'Alias' => 'alias',
        'Target' => 'target',
        'Status' => 'publish',
        'ID'    => 'id');
    var $editlink = 'redirect.php';
    */
//    var $_observers_source = array( 'AMP_System_List_Observer');

    function AMP_Content_Redirect_List( $source = false, $criteria = array( ) ) {
        //$this->init( $this->_init_source( $dbcon, $criteria  ) );
        $this->__construct( $source, $criteria );
    }

    function render_header_name( ) {
        return 'Alias';
    }
}
?>
