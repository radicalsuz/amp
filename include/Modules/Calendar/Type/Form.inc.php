<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'Modules/Calendar/Type/ComponentMap.inc.php');

class Calendar_Type_Form extends AMPSystem_Form_XML {

    var $name_field = 'name';

    function Calendar_Type_Form( ) {
        $name = 'eventtype';
        $this->init( $name );
    }

    function setDynamicValues( ){
        /*auto scaffolded items here  auto scaffold items end */
    }
}
?>
