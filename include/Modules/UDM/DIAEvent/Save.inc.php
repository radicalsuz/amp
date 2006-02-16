<?php

require_once( 'DIA/API.php' );
require_once( 'AMP/UserData/Plugin/Save.inc.php' );

class UserDataPlugin_Save_DIAEvent extends UserDataPlugin_Save {
    var $options = array(
        'orgKey' => array(
            'type'=>'text',
            'size'=>'5',
            'available'=>true,
            'label'=>'DIA Organization Key'
            ),
		'user' => array(
            'type'=>'text',
            'size'=>'5',
            'available'=>true,
            'label'=>'DIA AMP User Name'
			),
		'password' => array(
            'type'=>'text',
            'size'=>'5',
            'available'=>true,
            'label'=>'DIA AMP User Password'
			)
        );

    var $_field_prefix;
    var $_calendar_plugin;
	var $_event_key;

	var	$translation = array(
			'dia_key' =>	'event_KEY', //or maybe just 'key'
            'event'   =>    'Event_Name',
            'cost'    =>    'Ticket_Price',
            'email1'  =>    'Contact_Email',
            'location' =>   'Directions',
            'laddress' =>   'Address',
            'lcity'     =>  'City',
            'lstate'    =>  'State',
            'lzip'      =>  'Zip',
            'fulldesc'  =>  'Description');

    function UserDataPlugin_Save_DIAEvent(&$udm, $plugin_instance) {
        $this->_calendar_plugin =& $udm->registerPlugin( 'AMPCalendar', 'Save');
        $this->_field_prefix = $this->_calendar_plugin->getPrefix( );
        $this->init($udm, $plugin_instance);
    }

    function getSaveFields() {
        return $this->_calendar_plugin->getAllDataFields( );
    }

    function save ( $data ) {
		$this->notice('entering diaevent save plugin');
        $options=$this->getOptions();

		if(!defined('DIA_API_ORGCODE') && isset($options[ 'orgKey' ])) {
			define('DIA_API_ORGCODE', $options[ 'orgKey' ]);
		}
		if(!defined('DIA_API_USERNAME') && isset($options[ 'user' ])) {
			define('DIA_API_USERNAME', $options[ 'user' ]);
		}
		if(!defined('DIA_API_PASSWORD') && isset($options[ 'password' ])) {
			define('DIA_API_PASSWORD', $options[ 'password' ]);
		}

        $supporter_save =& $this->udm->getPlugin( 'DIA', 'Save');
        if ( !($supporter_key = $supporter_save->getSupporterKey( ) )) {
			$this->error("couldn't retrieve supporter key", E_USER_ERROR);
			return false;
		}
        $data['supporter_KEY'] = $supporter_key;

		$data = $this->translate($data);

		$api =& DIA_API::create();
		if ( !($event_key = $api->addEvent( $data ) )) {
			$this->error('api failed to save event', E_USER_ERROR);
			return false;
		}
        $this->setEventKey( $event_key );
        $this->_calendar_plugin->updateDIAKey( $event_key );

		$this->error('returning event key: '.$this->getEventKey(), E_USER_NOTICE);
        return $this->getEventKey( );
    }

    function getEventKey( ) {
        return $this->_event_key;
    }

    function setEventKey($key) {
        $this->_event_key = $key;
    }

	function translate( $data ) {
        //this is totally gonna hurt

		$translation = $this->translation;
		
		foreach($data as $key => $value) {
			if(isset($translation[$key])) {
				$return[$translation[$key]] = $value;
			} else {
				$return[$key] = $value;
			}
		}

		$recurring_options = array( 0 => 'None',
									4 => 'YEARLY',
									3 => 'MONTHLY',
									1 => 'DAILY');
		if($data['recurring_options'] = 2) {
			$return['Recurrence_Interval'] = 7;
			$return['Recurrence_Frequency'] = 'DAILY';
		} else {
			$return['Recurrence_Frequency'] = $recurring_options[$data['recurring_options']];
		}

		if($data['publish']) {
			$return['Status'] = 'Active';
		} else {
			$return['Status'] = 'Inactive';
		}
		
		$start = strtotime($data['date'].' '.$data['time']);
		if(!$start || (-1 == $start)) {
			trigger_error('couldnot strtotime date and time concatenated');
			$start = strtotime($data['date']);
		}
		if($start && (-1 != $start)) {
			$return['Start'] = dia_formatdate($start);
		}

		$end = strtotime( $data['endtime']);
		if($end && (-1 != $end)) {
			$return['End'] = dia_formatdate($end);
		}
        if ( isset( $return['Ticket_Price'])) $return['This_Event_Costs_Money'] = true;

		return $return;
	}
		
}

?>
