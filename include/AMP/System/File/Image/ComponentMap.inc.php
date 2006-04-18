<?php

require_once ( 'AMP/System/ComponentMap.inc.php' );
require_once ( 'AMP/Content/Page/Urls.inc.php');

class ComponentMap_Image extends AMPSystem_ComponentMap {

    var $heading = "Image";
    var $nav_name = "content";
    var $_allow_inline_update = true;
    var $_path_controller = 'AMP/System/Component/Controller.php';
    var $_component_controller = 'AMP_System_Component_Controller_Map';
    var $_action_displays = array( 'list' => 'list', 'upload' => 'form');


    var $paths = array(
        'list'   => 'AMP/Content/Image/List.inc.php',
        'source' => 'AMP/System/File/Image.php' );

    var $components = array (
        'list'   => 'AMP_Content_Image_List',
        'source' => 'AMP_System_File_Image' );

    function ComponentMap_Image( ){
        $this->_path_source = AMP_LOCAL_PATH . AMP_CONTENT_URL_DOCUMENTS;
    }


    function &getComponent( $component_type, $passthru_value=null ){
        if ( $component_type != 'source' ) return PARENT::getComponent( $component_type, $passthru_value );
        return PARENT::getComponent( $component_type, $this->_path_source );
    }

}

?>
