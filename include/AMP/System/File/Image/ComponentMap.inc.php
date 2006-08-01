<?php

require_once ( 'AMP/System/ComponentMap.inc.php' );
require_once ( 'AMP/Content/Page/Urls.inc.php');

class ComponentMap_Image extends AMPSystem_ComponentMap {

    var $heading = "Image";
    var $nav_name = "content";
    var $_allow_inline_update = true;
    
    var $_path_controller = 'AMP/System/File/Image/Controller.php';
    var $_component_controller = 'AMP_System_File_Image_Controller';
    
    var $_action_displays = array( 'list' => 'list', 'upload' => 'form');
    var $_action_default = 'list';


    var $paths = array(
        'crop'   => 'AMP/Content/Image/Crop/Form.inc.php',
        'crop_fields' => 'AMP/Content/Image/Crop/Fields.xml',
        'form'   => 'AMP/System/File/Image/Form.inc.php',
        'list'   => 'AMP/Content/Image/List.inc.php',
        'fields' => 'AMP/System/File/Image/Fields.xml',
        'source' => 'AMP/System/File/Image.php' 
        );

    var $components = array (
        'crop'   => 'AMP_Content_Image_Crop_Form',
        'form'   => 'AMP_System_File_Image_Form',
        'list'   => 'AMP_Content_Image_List',
        'source' => 'AMP_System_File_Image' 
        );

    function ComponentMap_Image( ){
        $this->_path_source = AMP_LOCAL_PATH . AMP_CONTENT_URL_DOCUMENTS;
    }


    function &getComponent( $component_type, $passthru_value=null ){
        if ( $component_type != 'source' ) return parent::getComponent( $component_type, $passthru_value );
        return parent::getComponent( $component_type, $this->_path_source );
    }

}

?>
