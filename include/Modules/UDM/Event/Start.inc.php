<?php

require_once( 'AMP/UserData/Plugin.inc.php');
require_once( 'Modules/UDM/Event/Read.inc.php');
require_once( 'Modules/UDM/Event/Save.inc.php');

class UserDataPlugin_Start_Event extends UserDataPlugin {
    var $short_name = 'udm_event_start';
    var $long_name = 'Event Plugin';
    var $description = 'Loads Event Code';

    var $options    = array( 
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
            'use_dia' => array( 
                'type' => 'checkbox',
                'label' => 'Save events to DIA',
                'default' => '',
                'available' => true,
            ),
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
			),
        'components'    => array( 
            'type'      => 'text',
            'default'   => 'Messages,EventList',
            'label'     => 'List Components',
            'available' => true ),
        'component_order' =>  array( 
            'type'      => 'text',
            'default'   => 'Messages,EventList',
            'label'     => 'List Component Order',
            'available' => true ),

        );
    var $available   = true;
    var $_save_plugin;
    var $_read_plugin;

    function UserDataPlugin_Start_Event( &$udm, $plugin_instance_id ){
        $this->init( $udm, $plugin_instance_id );

        $this->_save_plugin = &$udm->registerPlugin( 'Event', 'Save', $plugin_instance_id );
        $this->_read_plugin = &$udm->registerPlugin( 'Event', 'Read', $plugin_instance_id );
        $this->_system_list_plugin = &$udm->registerPlugin( 'Output', 'ListAdmin' );
        $this->_system_list_event_plugin = &$udm->registerPlugin( 'Output', 'EventList' );

        $options = $this->getOptions( );
        if ( isset( $options['use_dia']) && $options['use_dia']) {
            $this->_load_dia_plugins( );
        }
        $this->_verifyBasics( 2, 12 );
    }

    function setOptions( $options ){
        if ( !$options ) $options = array( );
        $options = array_merge( $this->getOptions( ), $options );
        if ( isset( $this->_save_plugin )) {
            $this->_save_plugin->setOptions( $options ) ;
        }
        if ( isset( $this->_read_plugin )) {
            $this->_read_plugin->setOptions( $options ) ;
        }
        if ( isset( $this->_dia_read_event_plugin )) {
            $this->_dia_read_event_plugin->setOptions( $options );
        }
        if ( isset( $this->_dia_save_event_plugin )) {
            $this->_dia_save_event_plugin->setOptions( $options );
        }
        if ( isset( $this->_system_list_plugin )) {
            $this->_system_list_plugin->setOptions( $options );
        }
    }

    function _load_dia_plugins( $instance_id ) {
        $this->_dia_save_plugin = &$this->udm->registerPlugin( 'DIA', 'Read', $instance_id  );
        $this->_dia_save_event_plugin = &$this->udm->registerPlugin( 'DIAEvent', 'Read', $instance_id  );

        $this->_dia_read_plugin = &$this->udm->registerPlugin( 'DIA', 'Save', $instance_id  );
        $this->_dia_read_event_plugin = &$this->udm->registerPlugin( 'DIAEvent', 'Save', $instance_id  );
    }
   
    function execute( ){
        //do nothing
    }
}

?>
