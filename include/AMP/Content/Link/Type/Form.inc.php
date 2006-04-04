<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/Link/Type/ComponentMap.inc.php');

class Link_Type_Form extends AMPSystem_Form_XML {

    var $name_field = 'name';

    function Link_Type_Form( ) {
        $name = 'linktype';
        $this->init( $name );
    }

    function setDynamicValues( ){
        /*auto scaffolded items here  auto scaffold items end */
    }
}
?>
