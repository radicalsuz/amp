<?php

require_once( 'AMP/Form/XML.inc.php' );
require_once( 'Modules/VoterGuide/ComponentMap.inc.php' );
require_once( 'Modules/VoterGuide/VoterGuide.php' );

class VoterGuide_Form extends AMPForm_XML {

    function VoterGuide_Form () {
        $name = "VoterGuides";
        $this->init( $name );
    }
}
?>
