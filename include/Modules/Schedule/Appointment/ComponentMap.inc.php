<?php

require_once( 'AMP/System/ComponentMap.inc.php' );
require_once ( 'Modules/Schedule/Lookups.inc.php' );

class ComponentMap_Appointment extends AMPSystem_ComponentMap {

    var $heading = "Appointment";
    var $nav_name = "schedule";

    var $paths = array(
        "list" => "Modules/Schedule/Appointment/List.inc.php",
        "form" => "Modules/Schedule/Appointment/Form.inc.php",
        "fields" => "Modules/Schedule/Appointment/Fields.xml",
        "source" => "Modules/Schedule/Appointment.inc.php" );

    var $components = array(
        'list' => 'Appointment_List',
        'form' => 'Appointment_Form',
        'source' => 'Appointment' );
}
?>
