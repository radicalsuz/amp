<?php

require_once( 'DIA/API.php' );
require_once( 'AMP/UserData/Plugin/Save.inc.php' );

class UserDataPlugin_Save_DIAEvent extends UserDataPlugin_Save {
    var $available = true;
    var $options = array(
        'orgKey' => array(
            'type'=>'text',
            'size'=>'5',
            'available'=>true,
            'default' => '',
            'label'=>'DIA Organization Key'
            ),
		'user' => array(
            'type'=>'text',
            'size'=>'5',
            'available'=>true,
            'default' => '',
            'label'=>'DIA AMP User Name'
			),
		'password' => array(
            'type'=>'text',
            'size'=>'5',
            'available'=>true,
            'default' => '',
            'label'=>'DIA AMP User Password'
			),
		'capacity' => array(
            'type'=>'text',
            'size'=>'4',
            'available'=>true,
            'label'=>'Max Capacity',
			'default'=>250
			),
		'rsvp_request_fields' => array(
            'type'=>'text',
            'size'=>'40',
            'available'=>true,
            'label'=>'RSVP Fields',
			'default'=> 'First_Name,Last_Name,Email,Phone,Zip'
			),
		'rsvp_required_fields' => array(
            'type'=>'text',
            'size'=>'40',
            'available'=>true,
            'label'=>'RSVP Required Fields',
			'default'=> 'First_Name,Last_Name,Email'
			)

        );

    var $_field_prefix;
    var $_calendar_plugin;
	var $_event_key;

	var	$translation = array(
	#		'dia_key' =>	'event_KEY', //or maybe just 'key'
            'event'   =>    'Event_Name',
            'cost'    =>    'Ticket_Price',
            'email1'  =>    'Contact_Email',
            'location' =>   'Directions',
            'laddress' =>   'Address',
            'lcity'     =>  'City',
            'lstate'    =>  'State',
            'lzip'      =>  'Zip',
            'fulldesc'  =>  'Description');

    function UserDataPlugin_Save_DIAEvent( &$udm, $plugin_instance ) {

        $this->init($udm, $plugin_instance);
        $this->_register_event_setup( );
    }

    function _register_event_setup( ) {
        if ( isset( $this->_calendar_plugin )) return;
        if ( !( $plugin = &$this->udm->getPlugin( 'Event', 'Save' )) ){
            $this->_calendar_plugin = $this->udm->registerPlugin( 'AMPCalendar', 'Save');
        } else {
            $this->_calendar_plugin = &$plugin;
        }
        $this->_field_prefix = $this->_calendar_plugin->getPrefix( );
    }

    function getSaveFields() {
        return $this->_calendar_plugin->getAllDataFields( );
    }

    function save ( $data, $options=array( )) {
		#$this->notice('entering diaevent save plugin');
        $options=array_merge($options, $this->getOptions());

		if(!defined('DIA_API_ORGCODE') && isset($options[ 'orgKey' ])) {
			define('DIA_API_ORGCODE', $options[ 'orgKey' ]);
		}
		if(!defined('DIA_API_USERNAME') && isset($options[ 'user' ])) {
			define('DIA_API_USERNAME', $options[ 'user' ]);
		}
		if(!defined('DIA_API_PASSWORD') && isset($options[ 'password' ])) {
			define('DIA_API_PASSWORD', $options[ 'password' ]);
		}

        $supporter_save =& $this->udm->registerPlugin( 'DIA', 'Save');
        if ( !($supporter_key = $supporter_save->getSupporterKey( ) )) {
			$this->error("couldn't retrieve supporter key", E_USER_ERROR);
			return false;
		}
        $data['supporter_KEY'] = $supporter_key;

		$data = $this->translate($data);
        $data = $this->_addDIAOptions( $data, $options );


        $api_options = $options; 
        if ( isset( $options['orgKey'])) $api_options['organization_key'] = $options['orgKey'];

		$api =& DIA_API::create( null, $api_options );
		if ( !($event_key = $api->addEvent( $data ) )) {
			$this->error('api failed to save event', E_USER_ERROR);
			return false;
		}

        $this->setEventKey( $event_key );
        $event_id = isset( $data['id']) ? $data['id'] : false;
        $this->_calendar_plugin->updateDIAKey( $event_key, $event_id );

		$this->error('returning event key: '.$this->getEventKey(), E_USER_NOTICE);
        return $this->getEventKey( );
    }

    function getEventKey( ) {
        return $this->_event_key;
    }

    function setEventKey($key) {
        $this->_event_key = $key;
    }

    function _addDIAOptions( &$data, $options ){
		if(!isset($data['Maximum_Attendees'])) {
			$data['Maximum_Attendees'] = $options['capacity'];
		}
        if ( isset( $options['rsvp_request_fields']) && $options['rsvp_request_fields']){
            $data['Request'] = $options['rsvp_request_fields'];
        }
        if ( isset( $options['rsvp_required_fields']) && $options['rsvp_required_fields']){
            $data['Required'] = $options['rsvp_required_fields'];
        }
        return $data;


    }

	function translate( $data ) {

		$translation = $this->translation;
		
		foreach($data as $key => $value) {
			if(isset($translation[$key])) {
				$return[$translation[$key]] = $value;
			} else {
				$return[$key] = $value;
			}
		}

        if ( isset( $data['recurring_options'])) {
            $recurring_options = array( 0 => 'None',
                                        4 => 'YEARLY',
                                        3 => 'MONTHLY',
                                        1 => 'DAILY');
            if($data['recurring_options'] == 2) {
                $return['Recurrence_Interval'] = 7;
                $return['Recurrence_Frequency'] = 'DAILY';
            } else {
                $return['Recurrence_Frequency'] = $recurring_options[$data['recurring_options']];
            }

        }

        if ( isset( $data['publish'])) {
            if($data['publish']) {
                $return['Status'] = 'Active';
            } else {
                $return['Status'] = 'Inactive';
            }

        }
		
        if ( !isset( $data['date'])) $data['date'] = '';
        if ( !isset( $data['time'])) $data['time'] = '';
        if ( !isset( $data['enddate'])) $data['enddate'] = '';
        if ( !isset( $data['endtime'])) $data['endtime'] = '';

		$start = strtotime($data['date'].' '.$data['time']);
		if(!$start || (-1 == $start)) {
			$start = strtotime($data['date']);
		}
		if($start && (-1 != $start)) {
			$return['Start'] = dia_formatdate($start);
		}

        $simple_end = strtotime( $data['enddate']);
        if ( $simple_end < $start ){
            $end = strtotime( $data['date'] . ' ' . $data['endtime']);
            if(!$end || (-1 == $end)) {
                $end = strtotime($data['date']);
            }
        } else {
            $end = strtotime( $data['enddate'] . ' ' . $data['endtime']);
            if(!$end || (-1 == $end)) {
                $end = $simple_end;
            }
        }
		if($end && (-1 != $end)) {
			$return['End'] = dia_formatdate($end);
		}
        if ( isset( $return['Ticket_Price'])) $return['This_Event_Costs_Money'] = true;

		if(isset($data['typeid'])) {
			$type_lookup = AMPSystem_Lookup::instance('DistributedEvent');
			if(($key = $type_lookup[$data['typeid']])) {
				$return['distributed_event_KEY'] = $key;
			}
		}	
        if ( isset( $data['dia_key'])){
            $return['key'] = $data['dia_key'];
        }

		return $return;
	}
		
}

?>
