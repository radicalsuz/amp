<?php

require_once( 'AMP/UserData/Plugin.inc.php');
require_once( 'Modules/UDM/AMPAction/Read.inc.php');
require_once( 'Modules/UDM/AMPAction/Save.inc.php');

class UserDataPlugin_Start_AMPAction extends UserDataPlugin {
    var $short_name = 'udm_ampaction_start';
    var $long_name = 'WebAction Plugin';
    var $description = 'Loads Action Center Code';

    var $options    = array( 
        'action_id'     =>  array( 
            'type'  =>  'select',
            'lookup'=>  'webactions',
            'label' =>  'Web Action')
        );
    var $available   = true;
    var $_save_plugin;
    var $_read_plugin;

    function UserDataPlugin_Start_AMPAction( &$udm, $plugin_instance_id ){
        $this->init( $udm, $plugin_instance_id );
        $this->_save_plugin = &$this->udm->registerPlugin( 'AMPAction', 'Save', $plugin_instance_id );
        $this->_read_plugin = &$this->udm->registerPlugin( 'AMPAction', 'Read', $plugin_instance_id );
    }

    function setOptions( $options ){
        $this->_save_plugin->setOptions( $options ) ;
        $this->_read_plugin->setOptions( $options ) ;
    }
   
    function execute( ){
        //do nothing
    }
}

?>
