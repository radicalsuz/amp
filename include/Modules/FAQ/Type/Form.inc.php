<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'Modules/FAQ/Type/ComponentMap.inc.php');

class FAQ_Type_Form extends AMPSystem_Form_XML {

    var $name_field = 'type';

    function FAQ_Type_Form( ) {
        $name = 'faqtype';
        $this->init( $name );
    }

    function setDynamicValues( ){
        /*auto scaffolded items here  auto scaffold items end */
    }
}
?>
