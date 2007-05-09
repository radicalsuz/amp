<?php

require_once( 'AMP/UserData/Plugin.inc.php');
require_once( 'Modules/Calendar/List.inc.php');

class UserDataPlugin_EventList_Output extends UserDataPlugin {

    /*
    var $options = array( 
        'test' => array( 
            'type' => 'text',
            'label' =>  ' Stuff',
            'available' => true,
            'default' => ''
            )
    );
    */

    var $available = true;

    function UserDataPlugin_EventList_Output ( &$udm, $plugin_instance = null ) {
        $this->init( $udm, $plugin_instance );
    }

    function execute( ) {
        //$list = new Calendar_List( false, array( 'modin' => $this->udm->instance( )));
        $list = new Calendar_List( $this->udm->dbcon );
        $list->editlink_uid = true ;
        $list->_url_add = 'modinput4_view.php?modin='. $this->udm->instance( );
        return $list->execute( );
    }

}


?>
