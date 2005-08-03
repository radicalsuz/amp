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

        $this->addTranslation( 'schedule_id', '_locateActionSchedule', 'set' );
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

    function _locateActionSchedule ($data, $fieldname ) {
        $schedset = $this->_getScheduleItemLookup();
        if (!isset($data['action_id'])) return false;
        if (!isset($schedset[ $data[ 'action_id' ] ])) return false;
        $schedule_id = $schedset[ $data[ 'action_id' ] ];
        $this->swapper->setInitialValue( $schedule_id, 'schedule_id' );
        return $schedule_id;
    }

    /*
    function setDefaultValue( $fieldname, $value ) {
        if ($fieldname == 'action_id' ) {
            $schedule_id = $this->_locateActionSchedule( array($fieldname => $value), $fieldname );
            PARENT::setDefaultValue( 'schedule_id', $schedule_id );
            PARENT::setDefaultValue( 'action_id_'.$schedule_id, $value );
            return true;
        }
        PARENT::setDefaultValue( $fieldname, $value );
    }*/

    function _getFieldIncrement( $fieldname ) {
        $last_dash = strrpos( $fieldname, "_" );
        if ($last_dash === FALSE) return false;
        return intval( substr($fieldname, $last_dash+1));
    }

    function _placeInScheduleSet( $data, $fieldname ) {
        $itemset = $this->_getScheduleItemLookup();
        if (!isset($data['action_id'])) return false;
        if (!(isset($itemset[ $data['action_id'] ]) && ($schedule_id = $itemset[ $data['action_id'] ]))) return false;
        if ($schedule_id != $this->_getFieldIncrement( $fieldname )) return false;
        return $data['action_id'];
    }

    function setValues( $data ) {
        $result = PARENT::setValues( $data ); 
        if (!isset($data['action_id'])) return $result; 
        $action = &new ScheduleItem( AMP_Registry::getDbcon(), $data['action_id'] );
        if ($action->isOpen()) return $result;
        if (!($schedule_id = $this->_locateActionSchedule( $data, 'schedule_id' ))) return $result;

        $form_field = 'action_id_' . $schedule_id;
        $current_set = $this->_getValueSet( $form_field );
        $this->addToFieldValueSet( $form_field, array( $data['action_id']=>$action->describeSlot() ));
        return $result;
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
        $current = (isset( $_GET['action_id']) && $_GET['action_id'])?$_GET['action_id']:null; 
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
    }

    function output() {
        $this->registerJavascript( $this->swapper->output() );
        $this->setJavascript();
        return PARENT::output();
    }

}

?>
