<?php

require_once('AMP/UserData/Plugin/Email.inc.php');

class UserDataPlugin_SmsUser_AMP extends UserDataPlugin_Email {

    var $short_name  = 'SmsUser_AMP';
    var $long_name   = 'SMS User';
    var $description = 'Notifies the user via SMS';

    var $available = true;

    function UserDataPlugin_SmsUser_AMP ( &$udm, $plugin_instance=null ) {
        $this->init($udm, $plugin_instance);
    }

    function _register_options_dynamic() {
        $this->_registerIntroTextOptions( );
        $this->options['subject']['default']='Thank you for registering';
    }

    function prepareMessage ( $options = null ) {
        return false;
    }

    function getEmailTarget( $options ){
        $field_prefix = $this->_field_prefix;
        
        $this->_field_prefix = '';
        $answer = $this->getData( array('Cell_Phone', 'Phone_Provider') );

        $this->_field_prefix = $field_prefix;
        $provider_lookups = AMPSystem_Lookup::instance( 'cellphone_carriers');
        if ( isset( $provider_lookups[ $answer[ 'Phone_Provider']] )){
            $result_email = AMP_cleanPhoneNumber( $answer['Cell_Phone'] ) . '@' . $provider_lookups[ $answer['Phone_Provider']] ;
            return $result_email;
        }
        return false;

    }
}
?>
