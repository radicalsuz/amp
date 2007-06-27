<?php
require_once ( 'AMP/Form/SearchForm.inc.php' );
require_once ( 'AMP/Content/Article/ComponentMap.inc.php');

class ContentSearch_Form_User extends AMPSearchForm {
    var $xml_pathtype = 'search_fields_user';

    function ContentSearch_Form_User( ) {
        $name = 'User_ContentSearch';
        $this->init( $name );
    }

    function submitted() {
        if ( isset( $_REQUEST['action']) && $_REQUEST['action'] != AMP_TEXT_SEARCH ) {
            $search_request = false;
        }
        if ( $search_request ) return 'search';
        return parent::submitted( );
    }
}
?>
