<?php

require_once ('AMP/System/Form/XML.inc.php');
require_once ('Modules/Schedule/Schedule.php' );
require_once ('Modules/Schedule/ComponentMap.inc.php' );
require_once ('AMP/UserData/Lookups.inc.php');
require_once ('AMP/UserData/Input.inc.php');

class Schedule_Form extends AMPSystem_Form_XML {

	function Schedule_Form() {
		$name = "Schedules";
		$this->init( $name );
        $this->addTranslation( 'form_id_AMPSchedule', '_seekScheduleOption', 'set' );
        $this->addTranslation( 'form_id_AMPAppointment', '_seekScheduleOption', 'set' );
	}

    function setDynamicValues() {
        $formlist = AMPSystem_Lookup::instance("forms");
        $this->setFieldValueSet( 'form_id_AMPSchedule' , $formlist );
        $this->setFieldValueSet( 'form_id_AMPAppointment' , $formlist );
    }

    function _seekScheduleOption( $values, $fieldname ) {
        if (!isset($values['id'])) return false;
        $desired_namespace = substr( $fieldname, strlen( "form_id_") );
        $schedule= &new Schedule( AMP_Registry::getDbcon() );

        return $schedule->getScheduleOptionForm( $desired_namespace, $values['id']);

    }


    function postSave( $values ) {

        if (!isset($values['id'])) return false;
        $this->saveScheduleOption( 'AMPSchedule',$values['id'] ); 
        $this->saveScheduleOption( 'AMPAppointment',$values['id'] ); 
    }

    function saveScheduleOption( $namespace, $schedule_id ) {
        if (! $form_id = current($this->getValues( 'form_id_' . $namespace ))) return false; 

        $udm = &new UserDataInput( AMP_Registry::getDbcon(), $form_id );

        if (!$plugin = $udm->getPlugin( $namespace, 'Save' )) {
            $plugin = $udm->saveRegisteredPlugin( $namespace, 'Save' );
        }
        if (!$plugin) return false;

        $plugin->saveOption( 'schedule_id', $schedule_id );
    }

        



}

?> 
