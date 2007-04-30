<?php
require_once( 'DIA/API.php' );

class Calendar_DIA_Event {

	var	$translation = array(
			'dia_key' =>    'key',
            'event'   =>    'Event_Name',
            'cost'    =>    'Ticket_Price',
            'email1'  =>    'Contact_Email',
            'location' =>   'Directions',
            'laddress' =>   'Address',
            'lcity'     =>  'City',
            'lstate'    =>  'State',
            'lzip'      =>  'Zip',
            'fulldesc'  =>  'Description',
            'uid'       =>  'supporter_KEY',
            );

    var $_api;
    var $data = array( );
    var $id;

    var $capacity = 250;

    function Calendar_DIA_Event ( $item_id = null ) {
        $this->__construct( $item_id );
    }

    function __construct( $item_id = null ) {
        $this->_init_api( );
        if ( isset( $item_id )) {
            $this->read( $item_id );
        }
    }

    function _init_api( ) {
        $api_options = array( );

        if ( defined( 'DIA_API_ORGANIZATION_KEY')) $api_options['organization_key'] = DIA_API_ORGANIZATION_KEY;
        if ( defined( 'DIA_API_USERNAME')) $api_options['user'] = DIA_API_USERNAME;
        if ( defined( 'DIA_API_PASSWORD')) $api_options['password'] = DIA_API_PASSWORD;
        
        $this->_api =  DIA_API::create( null, $api_options );
    }

    function read( $item_id ) {
        $this->clear( );
        $this->data = $this->_api->getEvent( $item_id );
        if ( !$this->data ) return false;

        $this->id = $item_id;
        return $this->data;
    }

    function save( ) {
        $event_key = $this->_api->addEvent( $this->data  );
        if ( $event_key ) {
            $this->id = $event_key;
        }
    }

    function set( $data, $translation = 'amp' ){
        $translate_method = 'translate_from_' . $translation;
        if ( !( $translation && method_exists( $this, $translate_method ) )) {
            $this->data = $data;
            return;
        }

        $this->data = array_merge( $this->data, $this->$translate_method( $data ));
        return count( $this->data );
    }

    function clear( ) {
        $this->data = array( );
    }

    function get( $translation = 'amp' ) {
        $translate_method = 'translate_to_' . $translation;
        if ( !( $translation && method_exists( $this, $translate_method ) )) {
            return $this->data;
        }
        return $this->$translate_method( $this->data );

    }

	function translate_from_amp( $data ) {

		$translation = $this->translation;
		
		foreach($data as $key => $value) {
			if(isset($translation[$key])) {
				$return[$translation[$key]] = $value;
			} else {
				//$return[$key] = $value;
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

        if ( isset( $this->capacity )) {
            $return['Maximum_Attendees'] = $this->capacity;
        }
        if ( isset( $this->rsvp_request_fields )) {
            $return['Request'] = $this->rsvp_request_fields;
        }
        if ( isset( $this->rsvp_required_fields )) {
            $return['Required'] = $this->rsvp_required_fields;
        }

		return $return;
	}

    function translate_to_amp( $data ) {

		$translation = array_flip( $this->translation );
        $return = array( );

		foreach($data as $key => $value) {
			if(isset($translation[$key]) && $data[$key]) {
				$return[$translation[$key]] = $value;
			} 
            //else {
            //  $return[$key] = $value;
			//}
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
			if($type_lookup && $type = array_search($data['distributed_event_KEY'], $type_lookup)) {
				$return['typeid'] = $type;
			}
		}
        
        if ( $return['cost'] == '0.00') {
            unset( $return['cost']);
        }

        if ( $return['time'] == '12:00 AM') {
            unset( $return['time']);
        }
        if ( $return['endtime'] == '12:00 AM') {
            unset( $return['endtime']);
        }
		return $return;
    }

}


?>
