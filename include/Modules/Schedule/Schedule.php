<?php

/*/Schedule object holds all the slots

pulls data from db to populate itself

determines characteristics of schedule such as availability

this is a schedule table wrapper
*/
require_once ('AMP/UserData/Lookups.inc.php');
require_once ("Modules/Schedule/Item/Set.inc.php");
require_once ("Modules/Schedule/Item.inc.php");
require_once ("AMP/System/Data/Item.inc.php");
require_once ("AMP/UserData/Input.inc.php");
require_once ('Modules/Schedule/Appointment.inc.php' );

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

    function &getScheduleItem( $item_id ) {
        return $this->_scheduleItems->getItem( $item_id );
    }

	function readData( $schedule_id ) {
		$result =  PARENT::readData ($schedule_id );
		$this->_readSchedulePlugins( 'AMPSchedule' );
		$this->_readSchedulePlugins( 'AMPAppointment' );
		return $result;
	}

	function _readSchedulePlugins( $namespace ) {
		if ($form_id = $this->seekFormPlugin( $namespace )) {
			$this->mergeData( array( ('form_id_'.$namespace)  => $form_id ) );
		}
	}

	function _adjustSetData( $data ) {
		$allowed_data = array_combine_key( $this->adjust_fields, $data );
		if (empty( $allowed_data )) return false;
		$this->itemdata = array_merge( $this->itemdata, $allowed_data );
	}

	function describeOpenItems() {
        if (!isset($this->_scheduleItemList)) $this->readScheduleItems();

		$slots = $this->_scheduleItems->getOpenItems();
		$options = array();

		foreach( $slots as $item ) {
            $options[$item->id ] = $item->describeSlot();
		} 

		return $options;
	}
		
	function makeAppointment($user, $scheduleitem_id) {
       //get schedule item and verify it and update the darn status 
        if (!isset($this->_scheduleItemList)) $this->readScheduleItems();
        $this->dbcon->StartTrans();
        if (!$item = $this->getScheduleItem( $scheduleitem_id )) return false;
        if (! $item->isOpen() )  {
            $this->dbcon->FailTrans();
            $this->dbcon->CompleteTrans();
            return false;
        }
        $appointment = &new Appointment( $this->dbcon );
        $data = array( 'userdata_id' => $user, 'action_id'=>$scheduleitem_id );
        $appointment->setData( $data );
		#$appointment = &Appointment::createAppointment($user, $scheduleitem_id);
		if (!$appointment->save()) {
            $this->dbcon->CompleteTrans();
            return false;
        }
        print 'appointment saved';
        $item->updateStatus();
        return $this->dbcon->CompleteTrans();
    }

/*

    function getFormByOption( $namespace ) {
        if( $result = $this->getPluginsByOption( $namespace ) ) {
            $formlist = &FormLookup::instance('FormsbyPlugin');
            return $formlist[current( $result )];
        }

        return false;
    }

    function getPluginsByOption( $namespace ) {
        if (!isset($this->id)) return false;
        $option_plugins = FormLookup_PluginsbyOptionDef::instance( 'schedule_id', $this->id );
        if (empty( $option_plugins )) return false;

        $namespace_plugins = FormLookup_StartPluginsbyNamespace::instance( $namespace );
        if (empty( $namespace_plugins )) return false;

        $result = array_intersect( $option_plugins, $namespace_plugins ) ;
        return $result;
    }
    */

	function _afterSave() {
		$this->_updateFormRef( 'AMPSchedule' );
		$this->_updateFormRef( 'AMPAppointment' );
	}

	function getFormId( $namespace ) {
		return $this->getData( 'form_id_' . $namespace  );
	}


    function seekFormPlugin( $namespace ) {
        if (!isset($this->id)) return false;
        $lookup_class = "FormLookup_Find" . substr( $namespace, 3 ). "Form";
        $lookup = &new $lookup_class( $this->id );

        return $lookup->getResult();

    }

		
	function &_getFormRef( $namespace ) {
		if ($form_id = $this->seekFormPlugin( $namespace )) {
			return new UserDataInput( $this->dbcon, $form_id );
		}
		return false;
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


?>
