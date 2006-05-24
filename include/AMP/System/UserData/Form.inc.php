<?php

require_once( 'AMP/System/Form/XML.inc.php');

class AMP_System_UserData_Form extends AMPSystem_Form_XML {
    function AMP_System_UserData_Form( ){
        $name = 'AMP_System_UserData_Form';
        $this->init( $name , 'POST', AMP_SYSTEM_URL_FORMS );
    }

}

?>
