<?php

require_once( 'AMP/UserData/Plugin.inc.php' );
require_once( 'Modules/Calendar/Calendar.inc.php' );
require_once( 'DIA/API.php' );

class UserDataPlugin_Read_DIAEvent extends UserDataPlugin {

    // We take one option, a DIA event key, and no fields.
    //var $cal;
    var $options     = array( 
            'dia_event_key' => array(   'available' => false,
                                        'value' => null),
            'organization_key' => array(
                'type'=>'text',
                'size'=>'5',
                'default' => '',
                'available'=>true,
                'label'=>'DIA Organization Key'
                ),
            'user' => array(
                'type'=>'text',
                'size'=>'5',
                'default' => '',
                'available'=>true,
                'label'=>'DIA AMP User Name'
                ),
            'password' => array(
                'type'=>'text',
                'size'=>'5',
                'default' => '',
                'available'=>true,
                'label'=>'DIA AMP User Password'
                )
            );

//XXX: set this dynamically by getting it from the calendar plugin
	var $_field_prefix = "plugin_AMPCalendar";
    var $available = true;

	var	$translation = array (
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

	function UserDataPlugin_Read_DIAEvent(&$udm, $plugin_instance=null) {
        //$this->cal = &new Calendar( $udm->dbcon, null, $udm->admin );

		$this->init($udm, $plugin_instance);
        //$this->_register_event_setup( );
	}

    /*
    function _register_event_setup( ) {
        if ( isset( $this->_calendar_plugin )) return;
        if ( !( $plugin = $this->udm->getPlugin( 'Event', 'Read' )) ){
            $this->_calendar_plugin = $this->udm->registerPlugin( 'AMPCalendar', 'Read');
        } else {
            $this->_calendar_plugin = &$plugin;
        }
        $this->_field_prefix = $this->_calendar_plugin->getPrefix( );
    }
    */


	function execute($options=array( )) {
		$options = array_merge($this->getOptions(), $options);
		if (!(isset( $options['dia_event_key'] )&&$options['dia_event_key'])) return false;
		$key = $options['dia_event_key'];

        //accepts passed API options for testing purposes
        $api = &DIA_API::create( null, $options );
        
		if($event = $api->getEvent($key)) {
            $local_data = $this->translate($event);
			$this->setData($local_data);
            #AMP_varDump( $this->getData( ));
			return true;
		}

		return false;
    }

	function translate($data) {
		$translation = $this->translation;

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
									'WEEKLY' => 2,
									'DAILY' => 1);
		if(isset($data['Recurrence_Frequency']) && $freq = $data['Recurrence_Frequency']) {
			if(isset($recurring_options[$freq])) {
                if ( isset( $data['Recurrence_Interval']) && $data['Recurrence_Interval'] == 7) {
                    $return['recurring_options'] = $recurring_options['WEEKLY'];
                } else {
                    $return['recurring_options'] = $recurring_options[$freq];

                }
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
			$start = dia_datetotime($data['Start']);
			if(isset($start) && $start) {
				$return['date'] = date('Y-m-d', $start);
				$return['time'] = date('g:i A', $start);
			}
		}

		if(isset($data['End']) && $data['End']) {
			$end = dia_datetotime($data['End']);
			if(isset($end) && $end) {
				$return['endtime'] = date('g:i A', $end);
			}
		}

		if(isset($data['distributed_event_KEY'])) {
			$type_lookup = AMPSystem_Lookup::instance('DistributedEvent');
			if($type = array_search($data['distributed_event_KEY'], $type_lookup)) {
				$return['typeid'] = $type;
			}
		}
		return $return;
	}
		
}

?>
