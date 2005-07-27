<?php

require_once ( "AMP/System/ComponentMap.inc.php" );

class ComponentMap_ScheduleItem extends AMPSystem_ComponentMap {

    var $heading = "Schedule Item";
    var $nav_name = "schedule";

    var $paths = array(
        "list" => "Modules/Schedule/Item/List.inc.php",
        "form" => "Modules/Schedule/Item/Form.inc.php",
        "source" => "Modules/Schedule/Item.inc.php" );

    var $components = array(
        "list" => "ScheduleItem_List",
        "form" => "ScheduleItem_Form",
        "source" => "ScheduleItem" );

}
?>
