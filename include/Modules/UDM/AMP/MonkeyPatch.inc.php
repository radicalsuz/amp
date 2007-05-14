<?php

require_once( 'AMP/UserData/Plugin.inc.php');

class UserDataPlugin_MonkeyPatch_AMP extends UserDataPlugin {
    var $available = true;

    var $options = array( 
        'action_function' => array( 
            'type'  =>  'text',
            'default' => '',
            'available' => true,
            'label'     => 'Action Function'
            )
        );

    function UserDataPlugin_MonkeyPatch_AMP( &$udm, $plugin_instance = null ){
        $this->init( $udm, $plugin_instance );
    }

    function _register_fields_dynamic ( ){
        $this->do_something( );
    }

    function execute( ){
        //do nothing
    }

    function do_something( ){
        $options = $this->getOptions( );
        $action_function= $options['action_function'];
        if ( !function_exists( $options['action_function'])) {
            trigger_error( 'Function does not exist:' . $action_function);
            return;
        }
        return $action_function( $this->udm );
    }
}

?>
