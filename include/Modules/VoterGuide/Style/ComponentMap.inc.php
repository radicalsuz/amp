<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_VoterGuide_Style extends AMPSystem_ComponentMap {
    var $heading = "VoterGuide_Style";
    var $nav_name = "voterguide_styles";

    var $paths = array( 
        'fields' => 'Modules/VoterGuide/Style/Fields.xml',
        'list'   => 'Modules/VoterGuide/Style/List.inc.php',
        'form'   => 'Modules/VoterGuide/Style/Form.inc.php',
        'source' => 'Modules/VoterGuide/Style/VoterGuide_Style.php');
    
    var $components = array( 
        'form'  => 'VoterGuide_Style_Form',
        'list'  => 'VoterGuide_Style_List',
        'source'=> 'VoterGuide_Style');
}

?>
