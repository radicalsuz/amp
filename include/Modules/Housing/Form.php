<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'Modules/Housing/ComponentMap.inc.php');

class Housing_Form extends AMPSystem_Form_XML {

    function Housing_Form( ) {
        $name = 'Housing';
        $this->init( $name, 'POST', $_SERVER['PHP_SELF'] );
    }

}


?>
