<?php
require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_GalleryImage extends AMPSystem_ComponentMap {
    var $heading = "Photo Gallery Image"    ;
    var $nav_name = "gallery";

    var $paths = array( 
        'search_fields' => 'Modules/Gallery/Image/SearchFields.xml',
        'search'   => 'Modules/Gallery/Image/SearchForm.inc.php',
        'fields' => 'Modules/Gallery/Image/Fields.xml',
        'list'   => 'Modules/Gallery/Image/List.inc.php',
        'form'   => 'Modules/Gallery/Image/Form.inc.php',
        'source' => 'Modules/Gallery/Image.inc.php');

    var $components = array( 
        'form'  => 'GalleryImage_Form',
        'list'  => 'GalleryImage_List',
        'search'  => 'GalleryImageSearch',
        'source'=> 'GalleryImage');
}

?>
