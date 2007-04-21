<?php

require_once ( 'AMP/UserData/Plugin.inc.php');
require_once ( 'Modules/Blast/API.inc.php');

class UserDataPlugin_Read_PHPlist extends UserDataPlugin {

    var $_field_prefix = 'PHPlist';
    var $name = 'PHPlist';
	var $long_name   = 'PHP List Subscription ';
    var $description = 'Subscribes users to PHPlist Lists';
    var $available = true;


    function UserDataPlugin_Read_PHPlist ( &$udm, $plugin_instance ) {
        $this->init( $udm, $plugin_instance );
    }

    function execute( $options = array( )) {
        $this->_PHPlist = &new PHPlist_API( $this->dbcon );

        $all_lists = $this->udm->getRegisteredLists( ) ;
        if ( !$all_lists ) return true;

        $email = current( $this->udm->getData( array( 'Email') ));

        if ( !( $subscriber = &$this->_PHPlist->get_subscriber_by_email( $email ))) return true;
        $listSet = &$this->_PHPlist->get_subscriber_lists( $subscriber->id );
        $listSet->readData( );

        while( $list = $listSet->getData( )) {
            $this->setData( array( ( 'list_' . $list['id']) => true ));
            unset ( $all_lists[ $list['id']]);
        }
        foreach( $all_lists as $unsubscribed_id => $un_name ) {
            $this->setData( array(( 'list_' . $unsubscribed_id) => false ));
        }
        $this->setData( array(( 'list_htmlemail_ok' ) => $subscriber->getHtmlFlag( )));
        
        return true; 
    }
}

?>
