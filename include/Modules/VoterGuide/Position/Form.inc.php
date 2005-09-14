<?php

require_once( 'AMP/Form/XML.inc.php' );
require_once( 'Modules/VoterGuide/Position/ComponentMap.inc.php' );
require_once( 'Modules/VoterGuide/Position.php' );

class VoterGuidePosition_Form extends AMPForm_XML {

    function VoterGuidePosition_Form () {
        $name = "VoterGuide_Positions";
        $this->init( $name );
    }
}
?>
