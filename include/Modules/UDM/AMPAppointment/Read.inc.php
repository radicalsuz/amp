<?php

require_once( 'AMP/UserData/Plugin.inc.php' );
require_once( 'Modules/Schedule/Appointment/List.inc.php' );

class UserDataPlugin_Read_AMPAppointment extends UserDataPlugin {
    var $options = array(
        '_userid' => array(   
                'available' => false,
                    'value' => null) 
        );

    function UserDataPlugin_Read_AMPAppointment( &$udm, $plugin_instance = null) {
        $this->init( $udm, $plugin_instance) ;
    }

    function _register_fields_dynamic() {
        $this->fields = array(

            'appointment_list' => array(
                'type'=>'html',
                'public'=>false,
                'enabled'=>true
                )
            );
        $this->insertAfterFieldOrder( array_keys( $this->fields ) );
    }

    function execute( $options = array( )) {
        $options = array_merge ($this->getOptions(), $options);
        if (!isset( $options['_userid'] ) ) return false;
        $uid = $options['_userid'];
        
        $appointmentlist = &new Appointment_List ( $this->dbcon );
        $appointmentlist->getPersonalSchedule( $uid );

        $this->udm->fields[ $this->addPrefix('appointment_list') ]['values'] = $this->inForm($appointmentlist->output());
    }
}
?>
