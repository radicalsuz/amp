<?php
require_once( 'AMP/System/Form/XML.inc.php');

class PublicPage_Form extends AMPSystem_Form_XML {
    var $name = 'PublicPages';
    var $name_field = 'title';

    function PublicPage_Form( ){
        $this->init( $this->name );
    }
}

?>
