<?php

require_once ('Modules/Calendar/Calendar.inc.php');
require_once ('AMP/UserData/Plugin/Save.inc.php');
if ( !defined( 'AMP_FORM_ID_EVENT_REGISTRATION')) define( 'AMP_FORM_ID_EVENT_REGISTRATION', '' );

class UserDataPlugin_Save_AMPCalendar extends UserDataPlugin_Save {
    var $name = 'Save Calendar Data';
    var $description = 'Save event data into the AMP database';

    var $options = array(
        'reg_modin' => array(
            'description'=>'Registration Form',
            'label'=>'RSVP Form',
            //'values'=>'Lookup(userdata_fields, name, id)',
            'default'=> AMP_FORM_ID_EVENT_REGISTRATION,
            'type'=>'select',
            'available'=>true),
        'recurring_events' =>array(
            'label'=>'Use Recurring Events',
            'type'=>'checkbox',
            'default'=>false,
            'available'=>true));

    var $available = true;
    var $cal; #the Calendar Object
    var $_field_prefix = "plugin_AMPCalendar";
    var $_event_id;

    function UserdataPlugin_Save_AMPCalendar ( &$udm , $plugin_instance=null){
        $this->cal =new Calendar( $udm->dbcon, null, $udm->admin );
        $this->init( $udm, $plugin_instance );
    }


    function getSaveFields() {
		return $this->getAllDataFields();
    }

    function _register_fields_dynamic() {
        $options=$this->getOptions();
        if (isset($options['reg_modin']) && $options['reg_modin'] ) {
            $this->cal->allowRegistration( $options['reg_modin'] );
        }
        if (isset ($options['recurring_events']) && $options['recurring_events']) {
            $this->cal->allowRecurringEvents( true );
        }
        $this->fields=$this->cal->getFields();

        $this->insertBeforeFieldOrder( array_keys($this->fields) );

    }

    function _register_options_dynamic() {
        if ( !$this->udm->admin ) return;
        $forms = &AMPSystem_Lookup::instance( 'forms' );
        $this->options['reg_modin']['values']    = array( '' => 'None selected') + $forms;
    }

    function save ( $data, $options = null ) {
        $options = array_merge( $this->getOptions(), $options );
        if (!isset($this->udm->uid)) return false;
        $data['uid'] = $this->udm->uid;

        if ($this->cal->saveEvent( $data )) {
            $this->setData(array('id'=> $this->cal->id));
            $this->_event_id = $this->cal->id;
            return true;
        }

        return false;
	
	}

	function updateDIAKey($key, $event_id = null) {
        $data = $this->getData( );
        if ( !( isset( $event_id) && $event_id )) $event_id = $this->_event_id;
		return $this->cal->updateEvent(
							array('id' 		=> $event_id,
								  'dia_key' => $key));
	}

}	



?>
