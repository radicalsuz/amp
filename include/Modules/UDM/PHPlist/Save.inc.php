<?php

/* * * * * * *
 *  PhpList Plugin
 *
 *  @author Austin Putman <austin@radicaldesigns.org> 
 *  @date 2005-11-07
 */
 
require_once ('AMP/UserData/Plugin/Save.inc.php');
require_once ('Modules/Blast/API.inc.php');

class UserDataPlugin_Save_PHPlist extends UserDataPlugin_Save {

    var $name = 'PHPlist';
	var $long_name   = 'PHP List Subscription ';
    var $description = 'Subscribes users to PHPlist Lists';
    var $available = true;

    var $_field_prefix = 'PHPlist';
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
    var $_listfield_footer = array( 
            'label'     => 'Email preferences:',
            'public'    => true,
            'enabled'   => true,
            'type'      => 'header' );

    function UserDataPlugin_Save_PHPlist ( &$udm , $plugin_instance=null){
        $this->init( $udm, $plugin_instance );
    }

    function _register_fields_dynamic( ) {
        if( !( $lists = $this->udm->getRegisteredLists( ))) return;
        $this->fields[ 'list_header' ] = $this->_listfield_header;

        foreach ( $lists as $list_id => $list_name ){
            $listField = array( 'label'    => $list_name );
            $this->fields[ 'list_' . $list_id] = $listField + $this->_listfield_template;
        }

        $this->fields[ 'list_footer' ] = $this->_listfield_footer;
        $this->fields[ 'list_htmlemail_ok'] = array( 'label' => 'I can receive email formatted as HTML' ) + $this->_listfield_template;
        
		$this->_PHPlist = &new PHPlist_API ( $this->dbcon ) ;
    }

    function getSaveFields( ){
        return $this->getAllDataFields( );
    }
	 
    function save ( $data, $options = null ) {
		$options = array_merge( $this->getOptions(), $options );

        if( !( $lists = $this->udm->getRegisteredLists( ))) return true;
        $contact_info = $this->udm->getData( );
        if( $subscriber = &$this->_PHPlist->get_subscriber_by_email( $contact_info['Email'] )) {
            if ( $subscriber->getHtmlFlag( ) != $data['list_htmlemail_ok'] ){
                $subscriber->setHtmlFlag( $data['list_htmlemail_ok']);
                $subscriber->save( );
            }
        } else {
            $subscriber = &$this->_PHPlist->create_subscriber( $contact_info['Email'], $data['list_htmlemail_ok'], $this->udm->uid );
        }

        if ( !$subscriber ) {
            $this->udm->errorMessage( 'Failed to create subcriber');
            return false;
        }
        if ( !$subscriber->setAttributes( $contact_info )) {
            $this->udm->errorMessage( 'Failed to update subcriber');
            return false;
        }
        
        foreach( $lists as $list_id => $list_name ) {
           $new_status = $data[ 'list_' . $list_id ];
           if( $this->_PHPlist->is_subscribed( $subscriber->id, $list_id) == ( $new_status )) continue;
           $this->_PHPlist->set_subscriber( $subscriber->id, $list_id, $new_status ) ;
        }

        return true;
    }

}

?>
