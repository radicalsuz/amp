<?php

require_once( 'AMP/System/Form/XML.inc.php');

class Share_Form extends AMPSystem_Form_XML {

    function Share_Form( ) {
        $name = get_class( $this );
        $this->init( $name, 'POST', AMP_CONTENT_URL_SHARE );
    }

}

?>
