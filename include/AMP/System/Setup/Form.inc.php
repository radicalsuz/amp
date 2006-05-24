<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/System/Setup/ComponentMap.inc.php');

class AMP_System_Setup_Form extends AMPSystem_Form_XML {

    var $name_field = 'websitename';
    var $submit_button = array( 'submitAction' => array(
        'type' => 'group',
        'elements'=> array(
            'save' => array(
                'type' => 'submit',
                'label' => 'Save Changes'))
        ));

    function AMP_System_Setup_Form( ) {
        $name = 'sysvar';
        $this->init( $name );
    }

}
?>
