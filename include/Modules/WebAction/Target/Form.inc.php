<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'Modules/WebAction/Target/ComponentMap.inc.php');

class WebAction_Target_Form extends AMPSystem_Form_XML {

    var $name_field = 'Last_Name';

    function WebAction_Target_Form( ) {
        $name = 'webaction_targets';
        $this->init( $name );
    }

    function setDynamicValues( ){
        /*auto scaffolded items here  auto scaffold items end */
    }
}
?>
