<?php

require_once('AMP/UserData/Plugin.inc.php');
require_once('DIA/API.php');

class UserDataPlugin_Read_DIAEvent extends UserDataPlugin {

    // We take one option, a DIA event key, and no fields.
    var $cal;
    var $options     = array( 'dia_event_key' => array( 'available' => false,
														'value' => null) );

//XXX: set this dynamically by getting it from the calendar plugin
	var $_field_prefix = "plugin_AMPCalendar";

	function UserDataPlugin_Read_DIAEvent(&$udm, $plugin_instance=null) {
        $this->cal = &new Calendar( $udm->dbcon, null, $udm->admin );
		$this->init($udm, $plugin_instance);
	}

	function execute($options=null) {
//XXX: tell austin what key is being used here
		$options = array_merge($this->getOptions(), $options);
		if (!(isset( $options['dia_event_key'] )&&$options['dia_event_key'])) return false;
		$key = $options['dia_event_key'];

		$api =& DIA_API::create();
		if($event = $api->getEvent($key)) {
			$this->setData($this->translate($event));
			return true;
		}

		return false;
    }

	function translate($data) {
        //this is totally gonna hurt
		$translation = array (
			'event_KEY' => 'dia_key',
			'Event_Name' => 'event',
			'Ticket_Price' => 'cost',
			'Contact_Email' => 'email1',
			'Directions' => 'location',
			'Address' => 'laddress',
			'City' => 'lcity',
			'State' => 'lstate',
			'Zip' => 'lzip',
			'Description' => 'fulldesc');

		foreach($data as $key => $value) {
			if(isset($translation[$key])) {
				$return[$translation[$key]] = $value;
			} else {
				$return[$key] = $value;
			}
		}

		$recurring_options = array( 'None' => 0,
									'YEARLY' => 4,
									'MONTHLY' => 3,
									'DAILY' => 1);
		if(isset($data['Recurrence_Frequency']) && $freq = $data['Recurrence_Frequency']) {
			if(isset($recurring_options[$freq])) {
				$return['recurring_options'] = $recurring_options[$freq];
			} else {
				$return['recurring_options'] = 0;
			}
		}

		if($data['Status'] == 'Active') {
			$return['publish'] = 1;
		} else {
			$return['publish'] = 0;
		}

		if(isset($data['Start']) && $data['Start']) {
			$start = strtotime($data['Start']);
			if(!($start && ($start != -1))) {
				$start = $data['Start'];
			}
			if(isset($start) && $start) {
				$return['date'] = strftime('%D', $start);
				$return['time'] = strftime('%T', $start);
			}
		}

		if(isset($data['End']) && $data['End']) {
			$end = strtotime($data['End']);
			if(!($end && ($end != -1))) {
				$end = $data['End'];
			}
			if(isset($end) && $end) {
				$return['endtime'] = strftime('%D %T', $end);
			}
		}

		return $return;
	}
		
}

?>
