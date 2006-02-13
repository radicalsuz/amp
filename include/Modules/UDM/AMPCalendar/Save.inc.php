<?php

require_once ('Modules/Calendar/Calendar.inc.php');
require_once ('AMP/UserData/Plugin/Save.inc.php');

class UserDataPlugin_Save_AMPCalendar extends UserDataPlugin_Save {
    var $name = 'Save Calendar Data';
    var $description = 'Save event data into the AMP database';

    var $options = array(
        'reg_modin' => array(
            'description'=>'Registration Form',
            'name'=>'Registration',
            //'values'=>'Lookup(userdata_fields, name, id)',
            'default'=>51,
            'type'=>'select',
            'available'=>true),
        'recurring_events' =>array(
            'name'=>'Use Recurring Events',
            'type'=>'checkbox',
            'default'=>false,
            'available'=>true));

    var $available = true;
    var $cal; #the Calendar Object
    var $_field_prefix = "plugin_AMPCalendar";

    function UserdataPlugin_Save_AMPCalendar ( &$udm , $plugin_instance=null){
        $this->cal =new Calendar( $udm->dbcon, null, $udm->admin );
        $this->init( $udm, $plugin_instance );
    }


    function getSaveFields() {
		return $this->getAllDataFields();
    }

    function _register_fields_dynamic() {
        $options=$this->getOptions();
        if (isset($options['reg_modin']) ) {
            $this->cal->allowRegistration( $options['reg_modin'] );
        }
        if (isset ($options['recurring_events']) && $options['recurring_events']) {
            $this->cal->allowRecurringEvents( true );
        }
        $this->fields=$this->cal->getFields();

        $this->insertBeforeFieldOrder( array_keys($this->fields) );

    }

    function save ( $data ) {
        $options=$this->getOptions();
        if (!isset($this->udm->uid)) return false;
        $data['uid'] = $this->udm->uid;

        if ($this->cal->saveEvent( $data )) {
            $this->setData(array('id'=> $this->cal->id));
            return true;
        }

        return false;
	
	}

	function updateDIAKey($key) {
		return $this->cal->updateEvent(
							array('id' 		=> $this->cal->id,
								  'dia_key' => $key));
	}

}	



?>
