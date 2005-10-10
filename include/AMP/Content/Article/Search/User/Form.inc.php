<?php
require_once( 'AMP/Content/Article/Search/Form.inc.php');

class ContentSearch_Form_User extends ContentSearch_Form {
    var $xml_pathtype = 'search_fields_user';

    function ContentSearch_Form_User( ) {
        $name = 'User_ContentSearch';
        $this->init( $name );
    }

}
?>
