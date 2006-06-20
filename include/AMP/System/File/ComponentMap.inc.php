<?php

require_once ( 'AMP/System/ComponentMap.inc.php' );
require_once ( 'AMP/Content/Page/Urls.inc.php');

class ComponentMap_File extends AMPSystem_ComponentMap {

    var $heading = "Document";
    var $nav_name = "content";
    var $_allow_inline_update = true;

    var $_path_controller = 'AMP/System/File/Controller.php';
    var $_component_controller = 'AMP_System_File_Controller';
    
    var $_action_displays = array( 'list' => 'list', 'upload' => 'form');
    var $_action_default = 'list';


    var $paths = array (
        'form'   => 'AMP/System/File/Form.inc.php',
        'fields' => 'AMP/System/File/Fields.xml',
        'list'   => 'AMP/System/File/List.inc.php',
        'source' => 'AMP/System/File/File.php' 
    );

    var $components = array (
        'form'   => 'AMP_System_File_Form',
        'list'   => 'AMP_System_File_List',
        'source' => 'AMP_System_File' 
    );

    function ComponentMap_File( ){
        $this->_path_source = AMP_LOCAL_PATH . AMP_CONTENT_URL_DOCUMENTS;
    }


    function &getComponent( $component_type, $passthru_value=null ){
        if ( $component_type != 'source' ) return PARENT::getComponent( $component_type, $passthru_value );
        return PARENT::getComponent( $component_type, $this->_path_source );
    }

}

?>
