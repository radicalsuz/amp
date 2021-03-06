<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/System/User/ComponentMap.inc.php');

class User_Form extends AMPSystem_Form_XML {

    var $name_field = 'name';

    function User_Form( ) {
        $name = 'users';
        $this->init( $name );
    }

    function _after_init( ) {
        $this->addTranslation( 'password', 'show_blank', 'set');
    }

    function show_blank( $values, $fieldname ) {
        return false;
    }

}
?>
