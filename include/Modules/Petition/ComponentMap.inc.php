<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Petition extends AMPSystem_ComponentMap {
    var $heading = "Petition";
    var $nav_name = "petition";
    var $_action_default = 'list';

    var $paths = array( 
        'fields' => 'Modules/Petition/Fields.xml',
        'list'   => 'Modules/Petition/List.inc.php',
        'form'   => 'Modules/Petition/Form.inc.php',
        'source' => 'Modules/Petition/Petition.php');
    
    var $components = array( 
        'form'  => 'Petition_Form',
        'list'  => 'Petition_List',
        'source'=> 'Petition');
}

?>
