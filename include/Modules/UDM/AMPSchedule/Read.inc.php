<?php

require_once( 'AMP/UserData/Plugin.inc.php' );
require_once( 'Modules/Schedule/Item/List.inc.php' );

class UserDataPlugin_Read_AMPSchedule extends UserDataPlugin {
    var $options = array(
        '_userid' => array(   
                'available' => false,
                    'value' => null) 
        );

    function UserDataPlugin_Read_AMPSchedule( &$udm, $plugin_instance = null) {
        $this->init( $udm, $plugin_instance) ;
    }

    function _register_fields_dynamic() {
        $this->fields = array(

            'schedule_list' => array(
                'type'=>'html',
                'public'=>false,
                'enabled'=>true
                )
            );
        $this->insertAfterFieldOrder( array_keys( $this->fields ) );
    }

    function execute( $options = null ) {
        $options = array_merge ($this->getOptions(), $options);
        if (!isset( $options['_userid'] ) ) return false;
        $uid = $options['_userid'];
        
        $schedulelist = &new ScheduleItem_List ( $this->dbcon );
        $schedulelist->getPersonalSchedule( $uid );

        $this->udm->fields[ $this->addPrefix('schedule_list') ]['values'] = $this->inForm($schedulelist->output());
    }
}
?>
