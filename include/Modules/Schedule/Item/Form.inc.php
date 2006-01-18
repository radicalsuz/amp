<?php
require_once ( 'AMP/System/Form/XML.inc.php' );
require_once ( 'AMP/UserData/Lookups.inc.php');
require_once ( 'Modules/Schedule/Item.inc.php' );
require_once ( 'Modules/Schedule/Set.inc.php' );
require_once ( 'Modules/Schedule/Schedule.php' );
require_once ( 'Modules/Schedule/Item/ComponentMap.inc.php' );

class ScheduleItem_Form extends AMPSystem_Form_XML {
    var $name_field = "title";
    var $schedule_id;
    var $schedule;
    var $owner_id_field = array(
        'owner_id' => array( 
            'label' => 'Contact',
            'type'  =>  'select' )
        );

    function ScheduleItem_Form( $schedule_id = null ) {
        $name = "AMP_ScheduleItem";
        $this->init( $name );
        if (isset($schedule_id)) $this->setSchedule( $schedule_id );
		$this->addTranslation( 'start_time', '_dateArrayToString', 'get' );
		$this->addTranslation( 'stop_time', '_dateArrayToString', 'get' );
		$this->addTranslation( 'start_date', '_selectBaseDate', 'set' );
    }

    function setDynamicValues() {

        $this->setStatusOptionValues();
        $this->setScheduleValues();
    }

    function setValues( $data ) {
        $result = PARENT::setValues( $data );
        if ($data['schedule_id']) $this->setSchedule( $data['schedule_id'] );
        return $result;
    }

    function setSchedule( $schedule_id ) {
        if (!is_numeric($schedule_id)) return false;
        $this->schedule_id = $schedule_id ;
        $this->schedule = &new Schedule( AMP_Registry::getDbcon(), $schedule_id );
        if (!( $form_id  = $this->schedule->seekFormPlugin( 'AMPSchedule' ))) return false;
        $this->setOwnerNames( $form_id );
    }

    function verifyOwnedItem() {
        if (isset( $this->schedule_id )) return true;

        $this->dropField( 'owner_id' );
        $this->form->removeElement( 'owner_id' );
        return false;
    }

    function setOwnerNames( $form_id ) {
        $userset = FormLookup_Names::instance( $form_id );
		$this->setFieldValueSet( 'owner_id', $userset );
    }

    function setScheduleValues() {
		$scheduleset =& new ScheduleSet( AMP_Registry::getDbcon() );
		$schedulenames = $scheduleset->getLookup('name');
		$this->setFieldValueSet( 'schedule_id', $schedulenames );
    }

    function setStatusOptionValues() {
		$scheduleItem =& new ScheduleItem( AMP_Registry::getDbcon() );
		$statums = $scheduleItem->getStatusOptions();
		$this->setFieldValueSet( 'status', $statums );
    }


	function _selectBaseDate( $values, $fieldname = "start_date" ) {
		if (isset( $values[$fieldname]) ) return $values[$fieldname];
		if (isset( $values['start_time']) ) return $values['start_time'];
		if (isset( $values['stop_time']) ) return $values['stop_time'];
		return false;
	}

	function _dateArrayToString( $values, $fieldname = "start_time" ) {
		if (isset( $values['start_date'] )) {
            $values[ 'start_datetime' ] = array_merge( $values['start_date'], $values[$fieldname] );
            $fieldname = 'start_datetime';
        }
		return $this->_makeDbDateTime( $values, $fieldname );
	}

    function output() {
        $this->verifyOwnedItem();
        return PARENT::output();
    }

}
?>
