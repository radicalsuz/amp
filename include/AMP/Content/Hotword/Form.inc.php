<?php

require_once( 'AMP/System/Form/XML.inc.php');
require_once( 'AMP/Content/Hotword/ComponentMap.inc.php');

class Hotword_Form extends AMPSystem_Form_XML {

    var $name_field = 'word';

    function Hotword_Form( ) {
        $name = 'hotwords';
        $this->init( $name );
    }

}
?>
