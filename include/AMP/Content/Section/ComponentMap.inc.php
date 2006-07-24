<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Section extends AMPSystem_ComponentMap {
    var $heading = "Section";
    var $nav_name = "content";

    var $paths = array( 
        'fields' => 'AMP/Content/Section/Fields.xml',
        'list'   => 'AMP/Content/Section/List.inc.php',
        'form'   => 'AMP/Content/Section/Form.inc.php',
        'source' => 'AMP/Content/Section.inc.php');
    
    var $components = array( 
        'form'  => 'Section_Form',
        'list'  => 'Section_List',
        'source'=> 'Section');

    var $_allow_add  = AMP_PERMISSION_CONTENT_SECTION_EDIT;
    var $_allow_edit = AMP_PERMISSION_CONTENT_SECTION_EDIT;
    var $_allow_save = AMP_PERMISSION_CONTENT_SECTION_EDIT;
    var $_allow_delete    = AMP_PERMISSION_CONTENT_SECTION_DELETE;
    var $_allow_publish   = AMP_PERMISSION_CONTENT_PUBLISH;
    var $_allow_unpublish = AMP_PERMISSION_CONTENT_PUBLISH;
}

?>
