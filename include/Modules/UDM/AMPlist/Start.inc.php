<?php

require_once( 'AMP/UserData/Plugin.inc.php');

class UserDataPlugin_Start_AMPlist extends UserDataPlugin {
    var $available = true;
    var $name = 'Use Mailing Lists';

    var $options = array( 
    
        "lists" => array( 
            "type"  => "text",
            "label" => "Available Lists",
            "available" => true
            ));
    function UserDataPlugin_Start_AMPlist( &$udm, $plugin_instance ) {
        $this->init( $udm, $plugin_instance );
    }

    function init( &$udm, $plugin_instance = null) {
        PARENT::init( $udm, $plugin_instance );
        $init_blaster_method = 'init'. AMP_MODULE_BLAST;
        if (method_exists( $this, $init_blaster_method) ) $this->$init_blaster_method( $udm, $plugin_instance ) ;
        if ( isset( $_GET['email']) && email_is_valid( $_GET['email']) && !$this->udm->uid) {
            $this->udm->setData( array( 'Email'=>$_GET['email']));
        }
    }
    function initPHPlist( &$udm, $plugin_instance ){
        $save = & $udm->registerPlugin( 'PHPlist', 'Save', $plugin_instance );
        $read = & $udm->registerPlugin( 'PHPlist', 'Read', $plugin_instance );
    }
    function initListserve( &$udm, $plugin_instance ){
        $save = & $udm->registerPlugin( 'Listserve', 'Save', $plugin_instance );
    }

	function initDIA( &$udm, $plugin_instance ) {
        $save = & $udm->registerPlugin( 'DIA', 'Save', $plugin_instance );
	}

    function execute() {
        //do nothing
    }
}
?>
