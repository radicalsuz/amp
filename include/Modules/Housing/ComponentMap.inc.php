<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Housing extends AMPSystem_ComponentMap {

    var $heading = 'housing post';
    var $_action_default = 'list';
    var $_allow_search = true;
    var $_allow_add = AMP_PERMISSION_FORM_BOARD_HOUSING;
    var $_allow_edit = AMP_PERMISSION_FORM_BOARD_HOUSING;
    var $_allow_list = AMP_PERMISSION_FORM_BOARD_HOUSING;
    var $_allow_delete = AMP_PERMISSION_FORM_BOARD_HOUSING;
    var $_allow_publish = AMP_PERMISSION_FORM_BOARD_HOUSING;
    var $_allow_unpublish = AMP_PERMISSION_FORM_BOARD_HOUSING;

    var $paths = array( 
        'fields' => 'Modules/Housing/Fields.xml',
        'list'   => 'Modules/Housing/List.php',
        'form'   => 'Modules/Housing/Form.php',
        'source' => 'Modules/Housing/Post.php',
        
        //'search' => 'Modules/Housing/Search/Form.php',
        //'search_fields' => 'Modules/Housing/Search/Fields.xml',
        );
    
    var $components = array( 
        'form'  => 'Housing_Form',
        'list'  => 'Housing_List',
        'source'=> 'Housing_Post',
//        'search'=> 'Housing_Search_Form' 
        );
}


?>
