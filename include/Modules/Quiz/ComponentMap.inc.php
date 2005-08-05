<?php

require_once ( 'AMP/System/ComponentMap.inc.php' );

class ComponentMap_Quiz extends AMPSystem_ComponentMap {

	var $heading = "Quiz";
	var $nav_name = "quiz";

	var $paths = array(
		'fields' => 'Modules/Quiz/Fields.xml',
		'list' => 'Modules/Quiz/List.inc.php',
		'form' => 'Modules/Quiz/Form.inc.php',
		'source' => 'Modules/Quiz/Quiz.php' );


	var $components = array(
		'list' => 'Quiz_List',
		'form' => 'Quiz_Form',
		'source' => 'AMPSystem_Quiz' );
}
?>
