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

    function _after_init( ) {
        $this->addTranslation( 'basepath', 'addTrailingSlash', 'get');
    }

    function addTrailingSlash( $data, $fieldname ) {
        if ( substr( $data[$fieldname], -1 ) != '/' ) {
            if ( isset( $_REQUEST[$fieldname])) $_REQUEST[$fieldname] .= '/';
            if ( isset( $_POST[$fieldname])) $_POST[$fieldname] .= '/';
            return $data[$fieldname] . '/';
        }
        return $data[ $fieldname ];
    }

}
?>
