<?php
require_once( 'AMP/System/ComponentMap.inc.php');
if ( !defined( 'AMP_MODULE_ID_GALLERY')) define( 'AMP_MODULE_ID_GALLERY', 8 );

class ComponentMap_Gallery extends AMPSystem_ComponentMap {
    var $heading = "Photo Gallery"    ;
    var $nav_name = "gallery";

    var $paths = array( 
        'fields' => 'Modules/Gallery/Fields.xml',
        'list'   => 'Modules/Gallery/List.inc.php',
        'form'   => 'Modules/Gallery/Form.inc.php',
        'source' => 'Modules/Gallery/Gallery.php');

    var $components = array( 
        'form'  => 'Gallery_Form',
        'list'  => 'Gallery_List',
        'source'=> 'Gallery');
}

?>
