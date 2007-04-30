<?php

require_once('AMP/UserData/Plugin.inc.php');
require_once('Modules/Calendar/ComponentMap.inc.php');
require_once( 'Modules/Calendar/Lookups.inc.php');

class UserDataPlugin_Read_Event extends UserDataPlugin {

    // Basic descriptive data.
    var $long_name   = 'Event Read Plugin';
    var $description = 'Reads Event Data from the AMP database';
    var $options     = array( 'calid' => array(   'available' => false,
                                                    'value' => null),
							  'dia_event_key' => array( 'available' => false,
													'value' => null) );

    // Available for use in forms.
    var $available   = true;

    var $_field_prefix = "plugin_Event";

    var $_event;
    var $_event_map;
    var $_event_controller;
    var $_event_form;


    function UserDataPlugin_Read_Event ( &$udm, $plugin_instance=null ) {
  //      $this->cal = &new Calendar( $udm->dbcon, null, $udm->admin );
        $this->init( $udm, $plugin_instance );
    }

    function execute( $options = array( )) {
        $options = array_merge($this->getOptions(), $options);

        $calid = $this->find_calendar_id( $options );
        if ( !$calid ) return $this->_read_failure( ) ;

        $this->_register_event_setup( );
        $this->_event = $this->_event_map->getComponent( 'source' );
        $this->_event->read( $calid );
        if ( !$this->_event->hasData( )) return $this->_read_failure( );
        $data = $this->_event->getData( );

        $this->setData( $this->_event->getData( ));
        return false;
    }

    function _read_failure( ) {
        if ( $this->udm->uid && AMP_lookup( 'eventsByOwner', $this->udm->uid )) {
            $this->udm->unregisterPlugin( 'Event', 'Save');
        }
        return false;

    }

    function _register_fields_dynamic() {
        $this->_make_event_list_field( );
    }


    function _make_event_list_field( ) {
        $uid = $this->udm->uid;
        if ( !$uid ) $uid = ( isset( $_REQUEST['uid']) ? $_REQUEST['uid'] : false ); 
        if ( !$uid ) return;
        $events = AMP_lookup( 'eventsByOwner', $uid );
        if ( !$events ) return;

        $event_list = '';
        $renderer = AMP_get_renderer( );

        foreach( $events as $event_id => $event_name ) {

            $event_list .= 
                $renderer->link( 
                    AMP_url_update( AMP_SYSTEM_URL_EVENT, array( 'id' => $event_id )), 
                    $event_name )
                . $renderer->newline( );
        }

        $this->fields['events_list'] = array( 
                'type' => 'static',
                'enabled' => true,
                'default' => $event_list,
                'public' => false
            );

        $this->fields['events_list_header'] = array( 
                'type' => 'header',
                'label' => 'All Events Created By this User',
                'public' => false,
                'enabled' => true
            );

        $this->insertAfterFieldOrder( array( 'events_list_header', 'events_list'));

    }

    function find_calendar_id( $options ) {
        //basic
        if ( isset( $options['calid']) && $options['calid']) {
            return $options['calid'];
        }

        //dia lookup
		if(isset($options['dia_event_key']) && $options['dia_event_key']) {
            $dia_calid = $this->lookup_calid_thru_dia( $options['dia_event_key']);
            if ( $dia_calid ) return $dia_calid;
		}

        // Check for the existence of a userid.
        if ( !$this->udm->uid ) return false;

        $user_events = AMP_lookup( 'eventsByOwner', $this->udm->uid );
        if ( !$user_events || empty( $user_events )) {
            return false;
        }
        if ( count( $user_events) == 1 ) {
            return key( $user_events );
        } elseif ( isset( $_REQUEST['calid']) && $_REQUEST['calid']) {
            $request_calid = $_REQUEST['calid'];
            if ( isset( $user_events[$request_calid])) return $request_calid;
        } 
        return false;

    }

    function lookup_calid_thru_dia( $foreign_key, $type = 'CalendarDiaKey'){
        $keys = AMPSystem_Lookup::instance($type);
            
	    if(isset($keys[$foreign_key])) return $keys[$foreign_key];
        return false;

    }

    function _register_event_setup(  ) {
        if ( isset( $this->_event_map ) ) return;
        $options = $this->getOptions(  );
        if (( ( isset( $options['allow_registration_admin'] ) && $options['allow_registration_admin']  && $this->admin )
           || ( isset( $options['allow_registration'] ) && $options['allow_registration']  ))
           && ( !defined( 'AMP_CALENDAR_ALLOW_RSVP' ) )) { 
            define( 'AMP_CALENDAR_ALLOW_RSVP', true );
        }

        if ( $this->udm->admin ) {
            $this->_event_map = new ComponentMap_Calendar(  );
        } else {
            $this->_event_map = new ComponentMap_Calendar_Public(  );
        }
        $this->_event_controller = $this->_event_map->get_controller(  );
        $this->_event_form = $this->_event_controller->get_form(  );

    }

}


?>
