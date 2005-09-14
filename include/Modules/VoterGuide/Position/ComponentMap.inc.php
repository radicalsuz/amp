<?php

require_once ( 'AMP/System/ComponentMap.inc.php' );

class ComponentMap_VoterGuidePosition extends AMPSystem_ComponentMap {

    var $heading = "Voter Guide";
    var $nav_name = "voterguide";

    var $paths = array( 
        'fields' => 'Modules/VoterGuide/Position/Fields.xml',
        'form'   => 'Modules/VoterGuide/Position/Form.inc.php',
        'source' => 'Modules/VoterGuide/Position.php' );

    var $components = array(
        'form' => 'VoterGuidePosition_Form',
        'source ' => 'VoterGuide_Position' );

}
?>
