<?php

require_once( 'AMP/UserData/Plugin.inc.php');

class UserDataPlugin_Read_AMPAction extends UserDataPlugin {
    var $options = array( 
        '_userid'   => array( 
                'available'     => false,
                'value'         => null ));

    function UserDataPlugin_Read_AMPAction( &$udm,  $plugin_instance = null ) {
        $this->init( $udm, $plugin_instance );
    }

    function _register_fields_dynamic( ){
        $this->fields = array( 
            'action_list'   => array( 
                'type'  =>  'html',
                'public'=>  false,
                'enabled' => true ));
        $this->insertAfterFieldOrder( array_keys( $this->fields ));
    }

    function execute( $options = array( )){
        $options = array_merge ($this->getOptions(), $options);
        if (!isset( $options['_userid'] ) ) return false;
        $uid = $options['_userid'];

        require_once( 'Modules/WebAction/Message/List.inc.php');
        $actionlist = &new WebActionMessage_List ( $this->dbcon );
        $actionlist->setCriteriaSender( $uid );

        $this->udm->fields[ $this->addPrefix('action_list') ]['values'] = $this->inForm($actionlist->output());

    }
}
?>
