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

    function prepareMessage ( $options = array( )) {
        return false;
    }

    function getEmailTarget( $options ){
        $field_prefix = $this->_field_prefix;
        
        $this->_field_prefix = '';
        $answer = $this->getData( array('Cell_Phone', 'Phone_Provider') );
        if ( !( isset( $answer['Cell_Phone']) && $answer['Cell_Phone'] && isset( $answer['Phone_Provider']) && $answer['Phone_Provider'])) return false;

        $this->_field_prefix = $field_prefix;
        $provider_lookups = AMPSystem_Lookup::instance( 'cellProviderDomains' );
        $account = AMP_cleanPhoneNumber( $answer['Cell_Phone']);
        $domain = false;
        if ( isset( $provider_lookups[ $answer[ 'Phone_Provider']] )){
            $domain = $provider_lookups[ $answer['Phone_Provider']] ;
        }
        if ( !( $account && $domain )) return false;
        
        return $account . '@' . $domain;

    }
}
?>
