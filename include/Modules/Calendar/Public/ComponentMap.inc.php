<?php

require_once( 'AMP/System/ComponentMap.inc.php');

class ComponentMap_Calendar_Public extends AMPSystem_ComponentMap {
    var $heading = "event";
    var $_action_default = 'list';
    var $_allow_search = true;
    var $_path_controller = 'Modules/Calendar/Public/Controller.php';
    var $_component_controller = 'Calendar_Public_Controller';
    var $_public_page_id_input = AMP_CONTENT_PUBLICPAGE_ID_CALENDAR_INPUT;
    var $_public_page_id_response = AMP_CONTENT_PUBLICPAGE_ID_CALENDAR_RESPONSE;
    var $_public_page_id_list = AMP_CONTENT_PUBLICPAGE_ID_CALENDAR_DISPLAY;
    var $_public_page_id_detail = AMP_CONTENT_PUBLICPAGE_ID_CALENDAR_DISPLAY;

    var $paths = array( 
        'fields' => 'Modules/Calendar/Public/Fields.xml',
        'list'   => 'Modules/Calendar/Public/List.inc.php',
        'list_repeat'   => 'Modules/Calendar/Public/List_Repeating.inc.php',
        'form'   => 'Modules/Calendar/Public/Form.inc.php',
        'source' => 'Modules/Calendar/Event.php',
        'search' => 'Modules/Calendar/Public/Search/Form.inc.php',
        'search_fields' => 'Modules/Calendar/Public/Search/Fields.xml',
        'view'   => 'Modules/Calendar/Public/Display.php'
        );
    
    var $components = array( 
        'form'  => 'Calendar_Public_Form',
        'list'  => 'Calendar_Public_List',
        'list_repeat'  => 'Calendar_Public_List_Repeating',
        'source'=> 'Calendar_Event',
        'search'=> 'Calendar_Public_Search_Form',
        'view'  => 'Calendar_Public_Display'
        );
}

?>
