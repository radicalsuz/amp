<?php

require_once( 'AMP/System/List/Form.inc.php');
require_once( 'AMP/Display/System/List.php');
require_once( 'Modules/FAQ/FAQ.php');

class FAQ_List extends AMP_Display_System_List {

    var $name = "FAQ";
    var $col_headers = array( 
        'Question' => 'name',
        'Type'      => '_lookupType',
        'ID'    => 'id');
    var $editlink = 'faq.php';
    var $name_field = 'question';
    var $_source_object = 'FAQ';
    var $_observers_source = array( 'AMP_System_List_Observer');
    var $columns  = array( 'edit','name', 'type', 'id', 'status');
    var $column_headers = array( 'name' => 'Question' );
    var $link_list_preview = AMP_CONTENT_URL_FAQ;

    function FAQ_List( $source, $criteria = array( ) ) {
        $this->__construct( $source, $criteria );
    }

    function render_type( $source ) {
        $faq_types = &AMPContent_Lookup::instance( 'faqTypes');
        if ( !isset( $faq_types[ $source->getType( )])) return false;
        return $faq_types[$source->getType( )];
    }

}
?>
