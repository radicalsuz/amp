<?php

require_once ( "AMP/System/ComponentMap.inc.php" );

class ComponentMap_ScheduleItem extends AMPSystem_ComponentMap {

    var $heading = "Schedule Item";
    var $nav_name = "schedule";

    var $paths = array(
        "list" => "Modules/Schedule/List.inc.php",
        "form" => "Modules/Schedule/Form.inc.php",
        "source" => "Modules/Schedule/Item.inc.php" );

    var $components = array(
        "list" => "Schedule_List",
        "form" => "ScheduleItem_Form",
        "source" => "ScheduleItem" );

}
?>
