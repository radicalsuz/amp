<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Content/Quote/Quote.php');

class Quote_List extends AMP_System_List_Form {
    var $name = "Quote";
    var $col_headers = array( 
        'Quote' => 'name',
        'Status' => 'statusText',
        'ID'    => 'id');
    var $editlink = 'quotes.php';
    var $name_field = 'quote';
    var $_source_object = 'Quote';
    var $_observers_source = array( 'AMP_System_List_Observer');

    function Quote_List( &$dbcon ) {
        $this->init( $this->_init_source( $dbcon ) );
    }
}
?>
