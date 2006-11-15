<?php

require_once( 'AMP/UserData/Plugin/Save.inc.php');

class UserDataPlugin_Save_Listserve extends UserDataPlugin_Save {
    
    var $available = true;
    var $name = 'Listserve subscription Save';

    var $_listfield_template = array( 
            'public'   => true,
            'enabled'  => true,
            'type'     => 'checkbox',
            'required' => false,
            'default'  => 1 );

    var $_listfield_header = array( 
            'label'     => 'Subscribe to the following lists:',
            'public'    => true,
            'enabled'   => true,
            'type'      => 'header' );

    var $options = array( 
    
        "lists" => array( 
            "type"  => "text",
            "label" => "Available Lists"
            ));
    var $_field_prefix = "";

    function UserDataPlugin_Save_Listserve( &$udm, $plugin_instance=null){
        $this->init( $udm, $plugin_instance );
    }

    function _register_fields_dynamic( ) {
        if( !( $lists = $this->udm->getRegisteredLists( ))) return;
        $this->fields[ 'list_header' ] = $this->_listfield_header;
        $list_addresses = &AMPSystem_Lookup::instance( 'listHosts');

        foreach ( $lists as $list_id => $list_name ){
            if ( !( isset( $list_addresses[$list_id]) && $list_addresses[$list_id])) continue;
            $listField = array( 'label'    => $list_name );
            $this->fields[ 'list_' . $list_id] = $listField + $this->_listfield_template;
        }
    }

    function getSaveFields( ){
        return $this->getAllDataFields( );
    }

    function save( $data , $options = null ) {
		$options = array_merge( $this->getOptions(), $options );
        $contact_info = $this->udm->getData( );
        if ( !isset( $contact_info['Email'])) return true;
        if( !( $lists = $this->udm->getRegisteredLists( ))) return true;
        $list_addresses = &AMPSystem_Lookup::instance( 'listHosts');

        foreach( $lists as $list_id => $list_name ) {
           if( !$new_status = $data[ 'list_' . $list_id ]) continue;
           if( $this->_send_save_email( $contact_info['Email'], $list_addresses[ $list_id ] )){
               trigger_error( 'send was successful '.$list_addresses[$list_id]);
           }
        }

        return true;

    }

    function _send_save_email( $from, $target ){
        require_once( 'AMP/System/Email.inc.php');
        $message = &new AMPSystem_Email( );
        $message->setSender( $from );
        $message->setRecipient( $target );
        $message->setMessage( "subscribe");
        return $message->execute( );
        
    }
}
