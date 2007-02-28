<?php

require_once( 'AMP/System/ComponentMap.inc.php');
require_once( 'AMP/System/Permission/Observer/Header.php');

class ComponentMap_Class extends AMPSystem_ComponentMap {
    var $heading = "Class";
    var $nav_name = "class";

    var $paths = array( 
        'fields' => 'AMP/Content/Class/Fields.xml',
        'list'   => 'AMP/Content/Class/List.inc.php',
        'form'   => 'AMP/Content/Class/Form.inc.php',
        'source' => 'AMP/Content/Class.inc.php');
    
    var $components = array( 
        'form'  => 'Class_Form',
        'list'  => 'Class_List',
        'source'=> 'ContentClass');

    var $_observers = array( 'AMP_System_Permission_Observer_Header' );

    var $_allow_edit = AMP_PERMISSION_CONTENT_CLASS_EDIT;
    var $_allow_delete = AMP_PERMISSION_CONTENT_CLASS_DELETE;
}

?>
