<?php

require_once ('AMP/UserData/Plugin/Save.inc.php');
require_once ('Modules/Calendar/ComponentMap.inc.php' );
require_once ('Modules/Calendar/Public/ComponentMap.inc.php' );

class UserDataPlugin_Save_Event extends UserDataPlugin_Save {

    var $name = 'Save Event Data';
    var $description = 'Save event data into the AMP database';
    var $available = true;
    var $_field_prefix = "plugin_Event";

    var $_event;
    var $_event_map;
    var $_event_controller;
    var $_event_form;
    var $options = array( 
            'allow_registration' => array( 
                'type' => 'checkbox',
                'label' => 'Allow Registration Setup to Public',
                'default' => '',
                'available'=>true,
            ),
            'allow_registration_admin' => array( 
                'type' => 'checkbox',
                'label' => 'Allow Registration Setup to Admin',
                'default' => '',
                'available'=>true,
            ),
        );

    function UserDataPlugin_Save_Event ( &$udm , $plugin_instance=null) {
        $this->init( $udm, $plugin_instance );
    }

    function _register_fields_dynamic(  ) {
        $this->_register_event_setup(  );
        $this->fields= $this->enable_all(  $this->_event_form->getFields() );

        $udm_fields = AMP_get_column_names( 'userdata' );
        $allowed_fields = array_diff( array_keys( $this->fields ), $udm_fields );
        $this->fields = array_combine_key( $allowed_fields, $this->fields );

        $captcha_def = $this->fields['humanity_test'];

        unset( $this->fields[ 'submit' ] );
        unset( $this->fields[ 'submitAction' ] );
        unset( $this->fields[ 'humanity_test' ] );
        
        $this->insertBeforeFieldOrder( array_keys($this->fields) );
        $this->insertAfterFieldOrder( array(  'humanity_test' ) );

        $this->fields[ 'humanity_test' ] = $captcha_def;
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

    function save( $data ) {
        $this->_register_event_setup(  );
        $this->_event = $this->_event_map->getComponent( 'source' );
        if ( !( isset( $data['uid'] ) && $data['uid'] ) ) {
            $data['uid'] = $this->udm->uid;
        }

        if ( isset( $data['id'] ) && $data['id'] ) {
            $this->_event->readData( $data['id'] );
        }
        $this->_event->mergeData( $data );
        return $this->_event->save(  );

    }

    function getSaveFields(  ) {
        return $this->getAllDataFields(  );
    }

}

?>
