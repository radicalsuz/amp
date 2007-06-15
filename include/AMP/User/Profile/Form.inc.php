<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/User/Profile/Profile.php');

class AMP_User_Profile_Form extends AMPSystem_Form_XML {

    var $name_field = 'name';

    function AMP_User_Profile_Form( ) {
        $name = 'AMP_User_Profile';
        $this->init( $name, 'POST', $_SERVER['PHP_SELF'] );
    }


    function adjustFields( $fields ) {
        return $fields;
    }

}
?>
