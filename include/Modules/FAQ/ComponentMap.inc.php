<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_FAQ extends AMPSystem_ComponentMap {
    var $heading = "FAQ";
    var $nav_name = "faq";

    var $paths = array( 
        'fields' => 'Modules/FAQ/Fields.xml',
        'list'   => 'Modules/FAQ/List.inc.php',
        'form'   => 'Modules/FAQ/Form.inc.php',
        'source' => 'Modules/FAQ/FAQ.php');
    
    var $components = array( 
        'form'  => 'FAQ_Form',
        'list'  => 'FAQ_List',
        'source'=> 'FAQ');

    var $_allow_list = AMP_PERMISSION_FAQ_ACCESS;
    var $_allow_edit = AMP_PERMISSION_FAQ_ACCESS ;
    var $_allow_save = AMP_PERMISSION_FAQ_ACCESS;
    var $_allow_publish = AMP_PERMISSION_FAQ_PUBLISH;
    var $_allow_unpublish = AMP_PERMISSION_FAQ_PUBLISH;
    var $_allow_delete = AMP_PERMISSION_FAQ_DELETE;
}

?>
