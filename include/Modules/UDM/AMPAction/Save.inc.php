<?php

require_once( 'AMP/UserData/Plugin/Save.inc.php');

class UserDataPlugin_Save_AMPAction extends UserDataPlugin_Save {
    var $short_name = 'udm_ampaction_save';
    var $long_name = 'WebAction Save Plugin';
    var $description = 'Sends Action Messages';

    var $options    = array( 
        'action_id'     =>  array( 
            'type'  =>  'select',
            'lookup'=>  'webactions',
            'label' =>  'Web Action')
        );
    var $available   = true;
    var $_field_prefix = 'WebAction_Save';
    var $_webaction;
    var $_header_field = array( 
            'start_message' => array( 
                'type'      =>      'header',
                'default'   =>      'Take Action',
                'enabled'   =>      true,
                'public'    =>      true ));
    var $_footer_field = array( 
            'end_message' => array( 
                'type'      =>      'header',
                'default'   =>      'Your Info',
                'enabled'   =>      true,
                'public'    =>      true ));

    function UserDataPlugin_Save_AMPAction( &$udm, $plugin_instance_id = null ){
        $this->init( $udm, $plugin_instance_id );
    }

    function _register_options_dynamic( ){
        if ( !class_exists( 'WebAction_Controller')) return false;
        $controller = &WebAction_Controller::instance( );
        $this->_webaction = &$controller->getActionObject( );
        
    }

    function _register_fields_dynamic( ){
        if ( !$action = &$this->_getWebAction( )) return false;
        $form = &$action->getMessageForm( );
        $this->fields = array_merge( $this->_header_field , $form->getFields( ) , $this->_footer_field );
        $this->insertBeforeFieldOrder( array_keys( $this->fields) );
    }

    function &_getWebAction( ){
        if ( isset( $this->_webaction )) return $this->_webaction; 
        $options = $this->getOptions( );
        if ( !isset( $options['action_id'])) return false;

        require_once( 'Modules/WebAction/WebAction.php');
        $action = &new WebAction( $this->dbcon, $options['action_id']);
        if ( !$action->hasData( )) return false;
        $this->_webaction = &$action;
        return $this->_webaction;
        
    }

    function getSaveFields( ){
        return $this->getAllDataFields( );
    }

    function save( $data ){
        $options = $this->getOptions( );
        if ( !$this->_performAction( $data )) return false;

        require_once( 'Modules/WebAction/Message/Message.php' );
        $message_record = &new WebActionMessage( $this->dbcon );

        $data['memberid'] = $this->udm->uid;
        $data['date'] = date( 'Y-m-d');
        $data['actionid'] = $this->options['action_id'];

        $message_record->setData( $data );
        return $message_record->save( );
    }


}
?>
