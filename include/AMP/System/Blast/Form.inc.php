<?php

require_once( 'AMP/Form/System/XML.inc.php' );
require_once( 'AMP/System/Blast/ComponentMap.inc.php' );

class Blast_Form extends AMPSystemForm_XML {

    function Blast_Form () {
        $name = "EnterEmail";
        $this->init( $name );
    }

    function getComponentHeader() {
        return "Send System Email";
    }

    function setDynamicValues() {
        $blast_form->setDefaultValue( 'MM_recordId', $_GET['id'] );
        $blast_form->setDefaultValue( 'passedsql', stripslashes($_POST['sqlp'] ));
        $blast_form->setDefaultValue( 'modin', $_REQUEST['modin'] );
    }
}
?>
