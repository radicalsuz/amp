<?php

require_once( 'AMP/UserData/Plugin.inc.php');
if ( !defined( 'AMP_MODULE_BLAST')) define( 'AMP_MODULE_BLAST', 'AMP');

class UserDataPlugin_Start_AMPlist extends UserDataPlugin {
    var $available = true;
    var $name = 'Use Mailing Lists';

    function UserDataPlugin_Start_AMPlist( &$udm, $plugin_instance ) {
        $this->init( $udm, $plugin_instance );
    }

    function init( &$udm, $plugin_instance = null) {
        PARENT::init( $udm, $plugin_instance );
        $init_blaster_method = 'init'. AMP_MODULE_BLAST;
        if (method_exists( $this, $init_blaster_method) ) $this->$init_blaster_method( $udm, $plugin_instance ) ;
    }
    function initPHPlist( &$udm, $plugin_instance ){
        $save = & $udm->registerPlugin( 'PHPlist', 'Save', $plugin_instance );
        $read = & $udm->registerPlugin( 'PHPlist', 'Read', $plugin_instance );
    }

    function execute() {
        //do nothing
    }
}
?>
