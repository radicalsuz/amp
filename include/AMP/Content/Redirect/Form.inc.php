<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/Redirect/ComponentMap.inc.php');

class AMP_Content_Redirect_Form extends AMPSystem_Form_XML {

    var $name_field = 'old';

    function AMP_Content_Redirect_Form( ) {
        $name = 'redirect';
        $this->init( $name );
    }

    function setDynamicValues( ){
        /*auto scaffolded items here  auto scaffold items end */
    }
}
?>
