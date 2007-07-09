<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Nav_Location extends AMPSystem_ComponentMap {
    var $heading = "Navigation Location";
    var $nav_name = "nav";
    var $_action_default = 'list';

    var $paths = array( 
        'fields' => 'AMP/Content/Nav/Location/Fields.xml',
        'list'   => 'AMP/Content/Nav/Location/List.inc.php',
        'form'   => 'AMP/Content/Nav/Location/Form.inc.php',
        'source' => 'AMP/Content/Nav/Location/Location.php');
    
    var $components = array( 
        'form'  => 'AMP_Content_Nav_Location_Form',
        'list'  => 'AMP_Content_Nav_Location_List',
        'source'=> 'AMP_Content_Nav_Location');

    var $_allow_list = AMP_PERMISSION_CONTENT_NAVIGATION;
    var $_allow_save = AMP_PERMISSION_CONTENT_NAVIGATION;
    var $_allow_publish = AMP_PERMISSION_CONTENT_NAVIGATION;
    var $_allow_unpublish = AMP_PERMISSION_CONTENT_NAVIGATION;
    var $_allow_delete = AMP_PERMISSION_CONTENT_NAVIGATION;
    var $_allow_edit = AMP_PERMISSION_CONTENT_NAVIGATION;

    function onSave( &$controller, $args = array( ) ) {
        $model = $controller->get_model( );
        $nav_layout = $model->getLayoutId( );
        if ( $nav_layout ) {
            ampredirect( AMP_url_update( AMP_SYSTEM_URL_NAV_LAYOUT, array( 'id' => $nav_layout )) );
        }
    }

    function onDelete( &$controller, $args = array( ) ) {
        $model = $controller->get_model( );
        $nav_layout = $model->getLayoutId( );
        if ( $nav_layout ) {
            ampredirect( AMP_url_update( AMP_SYSTEM_URL_NAV_LAYOUT, array( 'id' => $nav_layout )) );
        }
    }
}

?>
