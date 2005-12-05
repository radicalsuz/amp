<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_User extends AMPSystem_ComponentMap {
    var $heading = "User";
    var $nav_name = "system";

    var $paths = array( 
        'fields' => 'AMP/System/User/Fields.xml',
        'list'   => 'AMP/System/User/List.inc.php',
        'form'   => 'AMP/System/User/Form.inc.php',
        'source' => 'AMP/System/User/User.php');
    
    var $components = array( 
        'form'  => 'User_Form',
        'list'  => 'User_List',
        'source'=> 'AMPSystem_User');
}

?>
