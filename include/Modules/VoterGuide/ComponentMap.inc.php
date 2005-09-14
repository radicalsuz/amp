<?php

require_once ( 'AMP/System/ComponentMap.inc.php' );

class ComponentMap_VoterGuide extends AMPSystem_ComponentMap {

    var $heading = "Voter Guide";
    var $nav_name = "voterguide";

    var $paths = array( 
        'fields' => 'Modules/VoterGuide/Fields.xml',
        'form'   => 'Modules/VoterGuide/Form.inc.php',
        'source' => 'Modules/VoterGuide/VoterGuide.php' );

    var $components = array(
        'form' => 'VoterGuide_Form',
        'source ' => 'VoterGuide' );

}
?>
