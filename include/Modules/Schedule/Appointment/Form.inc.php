<?php

if (!defined ('AMP_SCHEDULE_APPOINTMENT_FORM_TEXT_AVAILABLE')) 
    define ('AMP_SCHEDULE_APPOINTMENT_FORM_TEXT_AVAILABLE', "Select %s Appointment");

if (!defined ('AMP_SCHEDULE_APPOINTMENT_FORM_TEXT_UNAVAILABLE')) 
    define ('AMP_SCHEDULE_APPOINTMENT_FORM_TEXT_UNAVAILABLE',"No %s Appointments Available");

if (!defined ('AMP_SCHEDULE_APPOINTMENT_FORM_TEXT_REQUESTED_UNAVAILABLE')) 
    define ('AMP_SCHEDULE_APPOINTMENT_FORM_TEXT_REQUESTED_UNAVAILABLE',"The Requested %s Appointment is no longer available");
    
require_once ('AMP/System/Form/XML.inc.php' );
require_once ('AMP/System/Form/XML.inc.php' );
require_once ('Modules/Schedule/Appointment/ComponentMap.inc.php' );
require_once ('AMP/Form/ElementSwapScript.inc.php');
require_once ('Modules/Schedule/Lookups.inc.php' );
require_once ('Modules/Schedule/Schedule.php' );

class Appointment_Form extends AMPSystem_Form_XML {
    
    function Appointment_Form() {
        $name = "Appointments";
        $this->swapper = &ElementSwapScript::instance();
        $this->init( $name );
        $schedule_set = AMPSystem_Lookup::instance( 'scheduleNames' ) ;
        foreach( $schedule_set as $schedule_id => $schedule_name ) {
            $this->addTranslation( 'action_id_'.$schedule_id , '_placeInScheduleSet', 'set' );
        }
        $this->addTranslation( 'action_id', '_pullfromScheduleSet', 'get' );
    }

    function _getScheduleItemLookup() {
        if (isset($this->_scheduleItemLookup)) return $this->_scheduleItemLookup;
        $this->_scheduleItemLookup = &AMPSystem_Lookup::instance( 'SchedulesbyItem');
        return $this->_scheduleItemLookup;
    }

    function _placeInScheduleSet( $data, $fieldname ) {
        $itemset = $this->_getScheduleItemLookup();
        if (isset($itemset[ $data['action_id'] ]) && ($schedule_id = $itemset[ $data['action_id'] ])) {
            $data['action_id_'.$schedule_id] = $data['action_id'];
            $data['schedule_id'] = $schedule_id;
        }
    }

    function _pullFromScheduleSet( $data, $fieldname ) {
        if (!(isset($data['schedule_id']) && isset($data[  'action_id_'.$data['schedule_id']  ])))  return false;
        return $data[ 'action_id_'.$data['schedule_id'] ];
    }
            



    function addSwapFieldSet( $swapname, $fields, $id ) {
        $set_fields = $this->incrementFields( $fields, $id );
        $this->swapper->addSet($id, $set_fields, $swapname );
        $this->addFields( $set_fields );
    }

    function incrementFields( $fields, $id ) {
        $result = array();
        foreach( $fields as $fieldname => $fDef ) {
            $result[ $fieldname .'_' .$id ] = $fDef;
        }
        return $result;
    }

    function addAppointmentSet( $appts, &$schedule ) {
        $hasAppts = ($appts && count($appts));
        
        if ($hasAppts) {
            $fields['action_id'] = array(
				'type' => 'select',
				'public' => true,
                'label' => sprintf( ($hasAppts ? AMP_SCHEDULE_APPOINTMENT_FORM_TEXT_AVAILABLE :
                                                    AMP_SCHEDULE_APPOINTMENT_FORM_TEXT_UNAVAILABLE )
                                    , $schedule->getName() ),
				'enabled' => true,
				'values'  => $appts );
        }
            
        $this->addSwapFieldSet( 'schedule_id', $fields, $schedule->id );
    }

    function setDynamicValues() {
        $form_set = AMPSystem_Lookup::instance( 'userDataFormalNames' );
        $schedule_set = AMPSystem_Lookup::instance( 'scheduleNames' ) ;
        $this->setFieldValueSet( 'schedule_id', $schedule_set );
        $this->setFieldValueSet( 'userdata_id', $form_set );
        $schedule = & new Schedule( AMP_Registry::getDbcon() );
        $this->swapper->addSwapper( 'schedule_id' );
        $this->swapper->setForm( $this->formname, 'schedule_id' );

        foreach ($schedule_set as $schedule_id => $schedule_name ) {
            $schedule->readData( $schedule_id );
            $open_items = $schedule->describeOpenItems();   
            $this->addAppointmentSet( $open_items, $schedule );
        }

        $this->addFieldAttr( 'schedule_id', array( 'onChange' => $this->swapper->js_swapAction( 'schedule_id' ) ));
        $this->registerJavascript( $this->swapper->output() );
    }

}

?>
