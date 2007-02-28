<?php
require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Gallery extends AMPSystem_ComponentMap {
    var $heading = "Photo Gallery"    ;
    var $nav_name = "gallery";
    var $_action_default = 'list';

    var $paths = array( 
        'fields' => 'Modules/Gallery/Fields.xml',
        'list'   => 'Modules/Gallery/List.inc.php',
        'form'   => 'Modules/Gallery/Form.inc.php',
        'source' => 'Modules/Gallery/Gallery.php');

    var $components = array( 
        'form'  => 'Gallery_Form',
        'list'  => 'Gallery_List',
        'source'=> 'Gallery');

    var $_allow_list = AMP_PERMISSION_GALLERY_ACCESS;
    var $_allow_edit = AMP_PERMISSION_GALLERY_ADMIN;
    var $_allow_save = AMP_PERMISSION_GALLERY_ADMIN;
    var $_allow_publish = AMP_PERMISSION_GALLERY_ADMIN;
    var $_allow_unpublish = AMP_PERMISSION_GALLERY_ADMIN;
    var $_allow_delete = AMP_PERMISSION_GALLERY_ADMIN;
}

?>
