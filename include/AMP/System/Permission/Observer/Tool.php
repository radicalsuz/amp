<?php

require_once( 'AMP/System/Observer.php');

class AMP_System_Permission_Observer_Tool extends AMP_System_Observer {

    var $_saved_tool_id;

    function AMP_System_Permission_Observer_Tool( ) {

    }

    function onInitForm( &$controller ) {
        $model = $controller->get_model( );
        $tool_id = $model->getToolId( );
        if ( !$tool_id) return false;
        $allowed_tools = AMP_lookup( 'tools');
        if ( !isset( $allowed_tools[ $tool_id])) {
            $this->_saved_tool_id = $tool_id;
            $form = $controller->get_form( );
            $form->dropField( 'modid') ;
        }
    }

    function onBeforeSave( &$controller ) {
        if ( !isset( $this->_saved_tool_id)) return;
        $model = $controller->get_model( );
        $model->mergeData( array( 'modid' => $this->_saved_tool_id ));

    }
}


?>
