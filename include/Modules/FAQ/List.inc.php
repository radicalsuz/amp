<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'Modules/FAQ/FAQ.php');

class FAQ_List extends AMP_System_List_Form {
    var $name = "FAQ";
    var $col_headers = array( 
        'Question' => 'name',
        'Type'      => '_lookupType',
        'ID'    => 'id');
    var $editlink = 'faq.php';
    var $name_field = 'question';
    var $_source_object = 'FAQ';
    var $_observers_source = array( 'AMP_System_List_Observer');

    function FAQ_List( &$dbcon ) {
        $this->init( $this->_init_source( $dbcon ) );
    }

    function _lookupType( &$source, $fieldname ){
        $faq_types = &AMPContent_Lookup::instance( 'faqTypes');
        if ( !isset( $faq_types[ $source->getType( )])) return false;
        return $faq_types[$source->getType( )];
    }
}
?>
