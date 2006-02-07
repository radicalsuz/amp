<?php

require_once( 'Modules/Contact/ComponentMap.inc.php');
require_once( 'AMP/Form/XML.inc.php');

class ContactForm extends AMPForm_XML {

    function ContactForm( ){
        $this->init( 'ContactForm');
        $this->defineSubmit( 'AMP_Submit', 'Send E-mail');
    }

}

?>
