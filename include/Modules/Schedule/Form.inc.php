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

	function retainForm( $form_id, $namespace ) {
		print '<BR>setting form' . $form_id;
		$this->initial_forms_links[ $namespace ] = $form_id;
	}

	function getFormLink( $namespace ) {
		if (empty( $this->initial_form_links )) return false;
		if (!isset( $this->initial_form_links[ $namespace ] )) return false;
		print '<BR>returning form' . $form_id;
		return $this->initial_form_links[ $namespace ];
	}


    function postSave( $values ) {

        if (!isset($values['id'])) return false;
    }


}

?> 
