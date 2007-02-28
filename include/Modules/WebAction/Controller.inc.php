<?php

require_once( 'AMP/System/Component/Controller.php');
require_once( 'Modules/WebAction/WebAction.php');

class WebAction_Controller extends AMP_System_Component_Controller_Standard {

    function WebAction_Controller(){
        $this->init( );
    }

    function commit_update( ){
        require_once( 'Modules/WebAction/Deprecated.php');
        $old_webActions = &new WebAction_Deprecated( AMP_Registry::getDbcon( ));
        $actions_set = $old_webActions->search( );
        if ( !$actions_set ) {
            ampredirect( AMP_SYSTEM_URL_WEBACTION );
            return false;
        }
        foreach( $actions_set as $action ){
            if ( $action->update( ) ) {
                $this->message( $action->getName( ) . ' updated');
                //$action->delete( );
            }
        }
        $this->display_default( );
    }

}
?>
