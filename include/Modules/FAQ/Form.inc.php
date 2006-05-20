<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'Modules/FAQ/ComponentMap.inc.php');

class FAQ_Form extends AMPSystem_Form_XML {

    var $name_field = 'question';

    function FAQ_Form( ) {
        $name = 'faq';
        $this->init( $name );
    }

    function setDynamicValues( ){
        /*auto scaffolded items here  auto scaffold items end */
    }
}
?>
