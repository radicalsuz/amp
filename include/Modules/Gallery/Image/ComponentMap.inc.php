<?php
require_once( 'AMP/System/ComponentMap.inc.php');
require_once( 'AMP/System/Permission/Observer/Section.php');
if ( !defined( 'AMP_MODULE_ID_GALLERY')) define( 'AMP_MODULE_ID_GALLERY', 8 );

class ComponentMap_GalleryImage extends AMPSystem_ComponentMap {
    var $heading = "Photo Gallery Image"    ;
    var $nav_name = "gallery";
    var $_action_default = 'list';
    var $_allow_search = true;

    var $paths = array( 
        'search_fields' => 'Modules/Gallery/Image/SearchFields.xml',
        'search'   => 'Modules/Gallery/Image/SearchForm.inc.php',
        'fields' => 'Modules/Gallery/Image/Fields.xml',
        #'list'   => 'Modules/Gallery/Image/List.inc.php',
        'list'   => 'Modules/Gallery/Image/List.php',
        'form'   => 'Modules/Gallery/Image/Form.inc.php',
        'source' => 'Modules/Gallery/Image.inc.php'
        );

    var $components = array( 
        'form'  => 'GalleryImage_Form',
        #'list'  => 'GalleryImage_List',
        'list'  => 'Gallery_Image_List',
        'search'  => 'GalleryImageSearch',
        'source'=> 'GalleryImage'
        );

    var $_url_system = AMP_SYSTEM_URL_GALLERY_IMAGE;
    var $_component_controller = 'AMP_System_Component_Controller_Bookmark';

    var $_observers = array( 'AMP_System_Permission_Observer_Section');

    var $_allow_list = AMP_PERMISSION_GALLERY_ACCESS ;
    var $_allow_edit = AMP_PERMISSION_GALLERY_ACCESS ;
    var $_allow_save = AMP_PERMISSION_GALLERY_ACCESS;
    var $_allow_publish = AMP_PERMISSION_GALLERY_IMAGE_PUBLISH;
    var $_allow_unpublish = AMP_PERMISSION_GALLERY_IMAGE_PUBLISH;
    var $_allow_delete = AMP_PERMISSION_GALLERY_IMAGE_DELETE;
}

?>
