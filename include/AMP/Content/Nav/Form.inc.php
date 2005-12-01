<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/Nav/ComponentMap.inc.php');

class Nav_Form extends AMPSystem_Form_XML {

    var $name_field = 'name';

    function Nav_Form( ) {
        $name = 'NavForm';
        $this->init( $name );
    }

    function setDynamicValues( ){
        /*auto scaffolded items here  auto scaffold items end */
    }
}
?>
