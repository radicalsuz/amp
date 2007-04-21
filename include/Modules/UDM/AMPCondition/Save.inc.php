<?php

require_once( 'AMP/UserData/Plugin.inc.php');
if ( file_exists_incpath( 'custom.translations.inc.php')){
    require_once( 'custom.translations.inc.php');
}

class UserDataPlugin_Save_AMPCondition extends UserDataPlugin {

    var $available = true;
    var $options = array( 
        'condition_function' => array( 
            'type' => 'text',
            'label' => 'Condition Function',
            'available' => true,
            'default' => ''
        )
    );

    function UserDataPlugin_Save_AMPCondition ( &$udm, $plugin_instance = null ){
        $this->init( $udm, $plugin_instance );
    }

    function execute( $options = array( )){
        $options = array_merge( $this->getOptions( ), $options );
        $save_flag = true;
        if ( isset( $options['condition_function']) && $options['condition_function']){
            $condition_function = $options['condition_function'];
            if ( !function_exists( $condition_function )) return false;
            $save_flag = call_user_func( $condition_function, $this->udm->getData( ));
        }
        if ( !$save_flag ) return true;

        $save_plugin = $this->udm->registerPlugin( 'AMP', 'Save');
        return $save_plugin->execute( );
        
    }

}

?>
