<?php

require_once ('AMP/System/Form/XML.inc.php');
require_once ('Modules/Schedule/Schedule.php' );

class Schedule_Form extends AMPSystem_Form_XML {

	function Schedule_Form() {
		$name = "Schedules";
		$this->init( $name );
	}
}

?> 
