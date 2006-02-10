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

    function UserDataPlugin_Save_DIA(&$udm, $plugin_instance) {
        $this->init($udm, $plugin_instance);
        $this->_calendar_plugin =& $udm->registerPlugin( 'AMPCalendar', 'Save');
        $this->_field_prefix = $this->_calendar_plugin->getPrefix( );
    }

    function getSaveFields() {
        return $this->getAllDataFields( );
    }

    function save ( $data ) {
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
        if ( !($supporter_key = $supporter_save->getSupporterKey( ) )) return false;
        $data['supporter_KEY'] = $supporter_key;

		$data = $this->translate($data);

		$api =& DIA_API::create();
		if ( !($event_key = $api->addEvent( $data ) )) return false;
        $this->setEventKey( $event_key );
        $this->_calendar_plugin->updateDIAKey( $event_key );

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
		$translation = array(
            'publish' =>    'Status',
            'event'   =>    'Event_Name',
            'cost'    =>    'Ticket_Price',
            'email1'  =>    'Contact_Email',
            'location' =>   'Directions',
            'laddress' =>   'Address',
            'lcity'     =>  'City',
            'lstate'    =>  'State',
            'lzip'      =>  'Zip',
            'fulldesc'  =>  'Description');


		foreach($data as $key => $value) {
			if(isset($translation[$key])) {
				$return[$translation[$key]] = $value;
			} else {
				$return[$key] = $value;
			}
		}

        $return['Start'] = strtotime( $data['date'] . ' ' . $data['time']);
        $return['End'] = strtotime( $data['endtime']);
        if ( isset( $data['cost'])) $return['This_Event_Costs_Money'] = true;

		return $return;
	}
		
}

?>
