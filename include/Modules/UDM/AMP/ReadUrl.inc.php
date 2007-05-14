<?php

require_once( 'AMP/UserData/Plugin.inc.php');

class UserDataPlugin_ReadUrl_AMP extends UserDataPlugin {
    var $available = true;

    var $options = array( 
        'translate_function' => array( 
            'type'  =>  'text',
            'default' => '',
            'available' => true,
            'label'     => 'Translation Function'
            )
        );

    function UserDataPlugin_ReadUrl_AMP( &$udm, $plugin_instance = null ){
        $this->init( $udm, $plugin_instance );
    }

    function _register_fields_dynamic ( ){
        $data = AMP_URL_Read( );
        if ( !$data ) return;
        $this->udm->setData( $this->translate( $data ) );

    }

    function execute( ){
        //do nothing
    }

    function translate( $data ){
        $options = $this->getOptions( );
        if ( isset( $data['id' ])) {
            $otp = isset( $_REQUEST['otp']) ? $_REQUEST['otp'] : false;
            $auth = $this->udm->authenticate( $data['id'], $otp );
            if ( !$auth ) {
                unset( $data['id']);
            }
        }
        if ( !( isset( $options['translate_function']) && $options['translate_function'])) return $data;
        $translate_function = $options['translate_function'];
        if ( !function_exists( $options['translate_function'])) {
            trigger_error( 'Function does not exist:' . $translate_function);
            return $data;
        }
        return $translate_function( $data, $this->udm );
    }
}

?>
