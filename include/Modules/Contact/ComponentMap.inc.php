<?php

require_once( "AMP/System/ComponentMap.inc.php");

class ComponentMap_Contact extends AMPSystem_ComponentMap {

    var $paths = array( 
        'form' => 'Modules/Contact/Form.inc.php',
        'fields' => 'Modules/Contact/Fields.xml'
        );


    var $components = array( 
        'form' => 'ContactForm' );
}

?>
