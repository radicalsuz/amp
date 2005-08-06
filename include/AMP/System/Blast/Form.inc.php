<?php

require_once( 'AMP/System/Form/XML.inc.php' );
require_once( 'AMP/System/Blast/ComponentMap.inc.php' );

class Blast_Form extends AMPSystem_Form_XML {

    var $submit_button = array( 'submitAction' => array(
        'type' => 'group',
        'elements'=> array(
            'send' => array(
                'type' => 'submit',
                'label' => 'Send Email'),
            'cancel' => array(
                'type' => 'submit',
                'label' => 'Cancel')
        )));

    function Blast_Form () {
        $name = "EnterEmail";
        $this->init( $name );
    }

    function getComponentHeader() {
        return "Send System Email";
    }

    function setDynamicValues() {
        $this->setFieldValueSet( 'modin', AMPSystem_Lookup::instance( 'Forms' ) );
        $this->setDefaultValue(  'from_email', AMP_SYSTEM_BLAST_EMAIL_SENDER );
        $this->setDefaultValue(  'from_name', AMP_SYSTEM_BLAST_EMAIL_SENDER_NAME );
        $this->setDefaultValue(  'replyto_email', AMP_SYSTEM_BLAST_EMAIL_SENDER );
    }

}
?>
