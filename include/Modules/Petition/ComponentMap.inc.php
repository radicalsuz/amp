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

    var $_allow_list = AMP_PERMISSION_PETITION_ADMIN ;
    var $_allow_edit = AMP_PERMISSION_PETITION_ADMIN ;
    var $_allow_save = AMP_PERMISSION_PETITION_ADMIN;
    var $_allow_publish = AMP_PERMISSION_PETITION_ADMIN;
    var $_allow_unpublish = AMP_PERMISSION_PETITION_ADMIN;
    var $_allow_delete = AMP_PERMISSION_PETITION_ADMIN;
}

?>
