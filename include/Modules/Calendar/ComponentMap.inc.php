<?php

require_once( 'AMP/System/ComponentMap.inc.php');
require_once( 'AMP/System/Permission/Observer/Section.php');

class ComponentMap_Calendar extends AMPSystem_ComponentMap {
    var $heading = "event";
    var $nav_name = "calendar";
    var $_action_default = 'list';
    var $_allow_search = true;

    var $_allow_add = AMP_PERMISSION_CALENDAR_ACCESS;
    var $_allow_edit = AMP_PERMISSION_CALENDAR_ACCESS;
    var $_allow_list = AMP_PERMISSION_CALENDAR_ACCESS;
    var $_allow_delete = AMP_PERMISSION_CALENDAR_DELETE;
    var $_allow_publish = AMP_PERMISSION_CALENDAR_PUBLISH;
    var $_allow_unpublish = AMP_PERMISSION_CALENDAR_PUBLISH;

    var $paths = array( 
        'fields' => 'Modules/Calendar/Fields.xml',
        'list'   => 'Modules/Calendar/List.inc.php',
        'form'   => 'Modules/Calendar/Form.inc.php',
        'source' => 'Modules/Calendar/Event.php',
        'search' => 'Modules/Calendar/Search/Form.php',
        'search_fields' => 'Modules/Calendar/Search/Fields.xml',
        );
    
    var $components = array( 
        'form'  => 'Calendar_Form',
        'list'  => 'Calendar_List',
        'source'=> 'Calendar_Event',
        'search'=> 'Calendar_Search_Form' 
        );

    var $_observers = array( 'AMP_System_Permission_Observer_Section');
}

?>
