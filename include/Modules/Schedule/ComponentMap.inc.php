<?php

require_once ( 'AMP/System/ComponentMap.inc.php' );

class ComponentMap_Schedule extends AMPSystem_ComponentMap {

	var $heading = "Schedule";
	var $nav_name = "schedule";

	var $paths = array(
		'fields' => 'Modules/Schedule/Fields.xml',
		'list' => 'Modules/Schedule/List.inc.php',
		'form' => 'Modules/Schedule/Form.inc.php',
		'source' => 'Modules/Schedule/Schedule.php' );


	var $components = array(
		'list' => 'Schedule_List',
		'form' => 'Schedule_Form',
		'source' => 'Schedule' );
}
?>
