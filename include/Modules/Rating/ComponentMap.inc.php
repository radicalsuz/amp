<?php
require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Rating extends AMPSystem_ComponentMap {

    var $heading = "Content Rating";
    var $_action_default = 'list';
    var $_path_controller = 'Modules/Rating/Controller.php';
    var $_component_controller = 'Rating_Controller';

    var $paths = array( 
        'list'   => 'Modules/Rating/List.php',
        'source' => 'Modules/Rating/Rating.php');

    var $components = array( 
        'list'  => 'Rating_List',
        'source'=> 'Rating');

    var $_allow_list = true;
    var $_allow_edit = false;
    var $_allow_save = false;
    var $_allow_publish = false;
    var $_allow_unpublish = false;
    var $_allow_delete = false;

}


?>
