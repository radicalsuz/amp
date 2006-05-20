<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'Modules/FAQ/Type/Type.php');

class FAQ_Type_List extends AMP_System_List_Form {
    var $name = "FAQ_Type";
    var $col_headers = array( 
        'Type' => 'name',
        'Status' => 'publish',
        'ID'    => 'id');
    var $editlink = 'faq_type.php';
    var $name_field = 'type';
    var $_source_object = 'FAQ_Type';
    var $_observers_source = array( 'AMP_System_List_Observer');

    function FAQ_Type_List( &$dbcon ) {
        $this->init( $this->_init_source( $dbcon ) );
    }
}
?>
