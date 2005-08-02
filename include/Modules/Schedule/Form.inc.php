<?php

require_once ('AMP/System/Form/XML.inc.php');
require_once ('Modules/Schedule/Schedule.php' );
require_once ('Modules/Schedule/ComponentMap.inc.php' );
require_once ('AMP/UserData/Input.inc.php');

class Schedule_Form extends AMPSystem_Form_XML {

	var $inital_form_links = array();

	function Schedule_Form() {
		$name = "Schedules";
		$this->init( $name );
	}

    function setDynamicValues() {
        $formlist = AMPSystem_Lookup::instance("forms");
        $this->setFieldValueSet( 'form_id_AMPSchedule' , $formlist );
        $this->setFieldValueSet( 'form_id_AMPAppointment' , $formlist );
    }

}

?> 
