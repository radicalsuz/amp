<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Housing_Public extends AMPSystem_ComponentMap {

    var $heading = 'housing post';
    var $_action_default = 'list';
    var $_allow_search = true;
    /*
    var $_allow_add = AMP_PERMISSION_FORM_BOARD_HOUSING;
    var $_allow_edit = AMP_PERMISSION_FORM_BOARD_HOUSING;
    var $_allow_list = AMP_PERMISSION_FORM_BOARD_HOUSING;
    var $_allow_delete = AMP_PERMISSION_FORM_BOARD_HOUSING;
    var $_allow_publish = AMP_PERMISSION_FORM_BOARD_HOUSING;
    var $_allow_unpublish = AMP_PERMISSION_FORM_BOARD_HOUSING;
    */
    var $_path_controller = 'AMP/System/Component/Controller/Public.php';
    var $_component_controller = 'AMP_System_Component_Controller_Public';
    var $_public_page_id_input = AMP_CONTENT_PUBLICPAGE_ID_HOUSING_INPUT;
    var $_public_page_id_response = AMP_CONTENT_PUBLICPAGE_ID_HOUSING_RESPONSE;
    var $_public_page_id_list = AMP_CONTENT_PUBLICPAGE_ID_HOUSING_DISPLAY;
    //var $_public_page_id_detail = AMP_CONTENT_PUBLICPAGE_ID_HOUSING_DISPLAY;


    var $paths = array( 
        'fields' => 'Modules/Housing/Public/Fields.xml',
        'list'   => 'Modules/Housing/Public/List.php',
        'form'   => 'Modules/Housing/Form.php',
        'source' => 'Modules/Housing/Post.php',
        
        //'search' => 'Modules/Housing/Search/Form.php',
        //'search_fields' => 'Modules/Housing/Search/Fields.xml',
        );
    
    var $components = array( 
        'form'  => 'Housing_Form',
        'list'  => 'Housing_Public_List',
        'source'=> 'Housing_Post',
//        'search'=> 'Housing_Search_Form' 
        );
}


?>
