<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/Link/ComponentMap.inc.php');

class AMP_Content_Link_Form extends AMPSystem_Form_XML {

    var $name_field = 'linkname';

    function AMP_Content_Link_Form( ) {
        $name = 'Links';
        $this->init( $name );
    }

    function setDynamicValues( ){
        /*auto scaffolded items here  auto scaffold items end */
    }
}
?>
