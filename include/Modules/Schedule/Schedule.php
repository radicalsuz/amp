<?php

/*/Schedule object holds all the slots

pulls data from db to populate itself

determines characteristics of schedule such as availability

this is a schedule table wrapper
*/
require_once ('AMP/UserData/Lookups.inc.php');
require_once ("Modules/Schedule/Item/Set.inc.php");
require_once ("AMP/System/Data/Item.inc.php");
require_once ("AMP/UserData/Set.inc.php");

class Schedule extends AMPSystem_Data_Item {

	//var $_scheduleItems = array();
	var $_scheduleItems;

	var $_owner;
	//var $datatable = "scheduleitems";
	var $datatable = "schedules";
    var $adjust_fields =  array( "form_id_AMPAppointment", "form_id_AMPSchedule" );

	function Schedule( &$dbcon, $id = null) {
		$this->init( $dbcon, $id );
	}

	function readScheduleItems() {
		$this->_scheduleItems =& new ScheduleItemSet($this->dbcon, $this->id);
		$this->_scheduleItems->readData();
		return $this->_scheduleItems;
	}

	function readData( $schedule_id ) {
		$result =  PARENT::readData ($schedule_id );
		$this->_readSchedulePlugins( 'AMPSchedule' );
		$this->_readSchedulePlugins( 'AMPAppointment' );
		return $result;
	}

	function _readSchedulePlugins( $namespace ) {
		if ($form_id = $this->_seekFormPlugin( $namespace, $this->getData() )) {
			$this->mergeData( array( ('form_id_'.$namespace)  => $form_id ) );
		}
	}

	function &_getFormRef( $namespace ) {
		if ($form_id = $this->_seekFormPlugin( $namespace, $this->getData() )) {
			return new UserDataInput( $this->dbcon, $form_id );
		}
		return false;
	}


    function _seekFormPlugin( $namespace, $values ) {
        if (!isset($values['id'])) return false;

        return $this->getFormByOption( $namespace, $values['id']);

    }


	function _adjustSetData( $data ) {
		$allowed_data = array_combine_key( $this->adjust_fields, $data );
		if (empty( $allowed_data )) return false;
		$this->itemdata = array_merge( $this->itemdata, $allowed_data );
	}

	function getOpenItems_Options_OwnerTime() {
		$slots = $this->_scheduleItems->getOpenItems();
		$options = array();

		if (!($udm= &$this->_getFormRef('AMPSchedule'))) return false;
		
		$lookup = FormLookup_Names::instance( $udm->instance ); 

		foreach( $slots as $item ) {
			$useful_time = date( 'm/d/y \a\t h:i a', strtotime($item->getData('start_time')));
			$options[ $item->id ] = $lookup[$item->getData('owner_id')] . " : " . $useful_time;
		} 
		return $options;
	}
		
	function makeAppointment($user, $scheduleitem_id) {
		$appointment = Appointment::createAppointment($user, $scheduleitem_id);
		return $appointment->save();
	}

    function getFormByOption( $namespace, $schedule_id ) {
        if( $result = $this->getPluginsByOption( $namespace, $schedule_id ) ) {
            $formlist = &FormLookup::instance('FormsbyPlugin');
            return $formlist[current( $result )];
        }

        return false;
    }

    function getPluginsByOption( $namespace, $schedule_id ) {
        $option_plugins = FormLookup_PluginsbyOptionDef::instance( 'schedule_id', $schedule_id );
        if (empty( $option_plugins )) return false;

        $namespace_plugins = FormLookup_StartPluginsbyNamespace::instance( $namespace );
        if (empty( $namespace_plugins )) return false;

        $result = array_intersect( $option_plugins, $namespace_plugins ) ;
        return $result;
    }

	function _afterSave() {
		$this->_updateFormRef( 'AMPSchedule' );
		$this->_updateFormRef( 'AMPAppointment' );
	}

	function getFormId( $namespace ) {
		return $this->getData( 'form_id_' . $namespace  );
	}
		

	function _updateFormRef( $namespace ) {
		$form_id = $this->getFormId( $namespace );
	
		if ($udm = &$this->_getFormRef( $namespace ) ) {
			if ($udm->instance == $form_id ) return true; 
			$this->_deleteSchedulePlugin( $udm , $namespace );
		}
		return $this->_saveSchedulePlugin( $namespace, $form_id );
		
	}

	function _deleteSchedulePlugin ( &$udm, $namespace ) {
		if ($plugin =& $udm->getPlugin( $namespace, 'Start' )) {
			$plugin->deleteRegistration( $namespace, 'Start' );
		}
	}

    function _saveSchedulePlugin( $namespace, $form_id ) {
        if (! $form_id ) return false;

        $udm = &new UserDataInput( $this->dbcon, $form_id );

        if (!$plugin = $udm->getPlugin( $namespace, 'Start' )) {
            $plugin = $udm->saveRegisteredPlugin( $namespace, 'Start' );
			return $plugin->saveOption( 'schedule_id', $this->id );
        }

    }

}

class Appointment extends AMPSystem_Data_Item {

	var $_action;
	var $_userId;
	var $_actionId;
	var $_timestamp;
	var $_created;
	var $_status;

	var $datatable = "userdata_actions";

	function Appointment ( &$dbcon, $id=null) {
		$this->init( $dbcon, $id );
	}

	function init( &$dbcon, $id=null ) {
		PARENT::init( $dbcon, $id );
		$this->setService();
	}

	function &createAppointment( $user, $scheduleItem ) {
		$dbcon =& AMP_Registry::getDbcon();
		$appointment =& new Appointment( $dbcon );

		$appointment->setParticipant( $user );
		$appointment->setScheduleItem( $scheduleItem );

		return $appointment;
	}

	function setService() {
		$service = array( "service" => AMP_USERDATA_ACTION_SCHEDULE );
		$this->setData( $service );
	}
		
	function setParticipant( $userdata_id ) {
		$person = array( "userdata_id" => $userdata_id );
		$this->setData( $person );
	}

	function setScheduleItem( $scheduleitem_id ) {
		$scheduleItem = array( "action_id" => $scheduleitem_id );
		$this->setData( $scheduleItem );
	}	

	function save() {
		$status = PARENT::save();

		if ($status) {
			$scheduleItem =& new ScheduleItem($this->dbcon, $this->getData("action_id"));
			if( !$scheduleItem->update($this) ) return false;

			//-----
			$status = false;
			if($scheduleItem->containsAppointment($this) || $scheduleItem->isOpen()) {
				$status = PARENT::save();
			}
			$scheduleItem->updateStatus();
		}
	
	}
}

define( 'AMP_USERDATA_ACTION_SCHEDULE', 'schedule' );
class AppointmentSet extends AMPSystem_Data_Set {

	var $datatable = "userdata_action";

	function AppointmentSet ( &$dbcon ) {
		$this->init( $dbcon );
	}

    function _register_criteria_dynamic() {
        $this->addCriteria( "action="
                    . $this->dbcon->qstr(AMP_USERDATA_ACTION_SCHEDULE));
    }

    function getParticpantCounts() {
        return $this->getGroupedIndex('userdata_id');
    }

    function getUserdataId() {
        return "userdata_id";
    }
}
?>
